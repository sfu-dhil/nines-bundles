<?php

declare(strict_types=1);

namespace Nines\UserBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Nines\UserBundle\Entity\User;
use Nines\UserBundle\Form\Admin\UserPasswordType;
use Nines\UserBundle\Form\Admin\UserType;
use Nines\UserBundle\Repository\UserRepository;
use Nines\UserBundle\Services\UserManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER_ADMIN')]
class AdminController extends AbstractController {
    /**
     * @throws Exception
     */
    protected function generatePassword() : string {
        $bytes = random_bytes(UserManager::PASSWORD_BYTES);

        return base64_encode($bytes);
    }

    #[Route(path: '/', name: 'nines_user_admin_index', methods: ['GET'])]
    public function index(UserRepository $userRepository) : Response {
        return $this->render('@NinesUser/admin/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route(path: '/new', name: 'nines_user_admin_new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $passwordHasher) : Response {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordHasher->hashPassword($user, $this->generatePassword());
            $user->setPassword($password);
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'The user account has been created with a random password.');

            return $this->redirectToRoute('nines_user_admin_index');
        }

        return $this->render('@NinesUser/admin/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}', name: 'nines_user_admin_show', methods: ['GET'])]
    public function show(User $user) : Response {
        return $this->render('@NinesUser/admin/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route(path: '/{id}/edit', name: 'nines_user_admin_edit', methods: ['GET', 'POST'])]
    public function edit(EntityManagerInterface $entityManager, Request $request, User $user) : Response {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'The user account has been updated.');

            return $this->redirectToRoute('nines_user_admin_show', ['id' => $user->getId()]);
        }

        return $this->render('@NinesUser/admin/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}/password', name: 'nines_user_admin_password', methods: ['GET', 'POST'])]
    public function password(EntityManagerInterface $entityManager, Request $request, User $user, UserManager $manager) : Response {
        $form = $this->createForm(UserPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('new_password')->getData();
            $manager->changePassword($user, $password);
            $entityManager->flush();
            $this->addFlash('success', 'The user password has been updated.');

            return $this->redirectToRoute('nines_user_admin_index');
        }

        return $this->render('@NinesUser/admin/password.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}', name: 'nines_user_admin_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, Request $request, User $user) : Response {
        if ($this->isCsrfTokenValid('delete_user' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'The account has been removed.');
        } else {
            $this->addFlash('warning', 'The security token was not valid.');
        }

        return $this->redirectToRoute('nines_user_admin_index');
    }
}
