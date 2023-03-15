<?php

declare(strict_types=1);

namespace Nines\UserBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Nines\UserBundle\Form\Profile\ChangePasswordType;
use Nines\UserBundle\Form\Profile\ProfileType;
use Nines\UserBundle\Services\UserManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController {
    #[Route(path: '/', name: 'nines_user_profile_index', methods: ['GET'])]
    public function index() : Response {
        $user = $this->getUser();

        return $this->render('@NinesUser/profile/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route(path: '/edit', name: 'nines_user_profile_edit', methods: ['GET', 'POST'])]
    public function edit(EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $passwordHasher) : Response {
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($passwordHasher->isPasswordValid($user, $form->get('password')->getData())) {
                $entityManager->flush();
                $this->addFlash('success', 'Your profile has been updated.');

                return $this->redirectToRoute('nines_user_profile_index');
            }
            $this->addFlash('danger', 'The password does not match.');
        }

        return $this->render('@NinesUser/profile/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/password', name: 'nines_user_profile_password', methods: ['GET', 'POST'])]
    public function password(EntityManagerInterface $entityManager, Request $request, UserManager $manager) : Response {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $current = $form->get('current_password')->getData();
            if ($manager->validatePassword($user, $current)) {
                $password = $form->get('new_password')->getData();
                $manager->changePassword($user, $password);
                $entityManager->flush();
                $this->addFlash('success', 'Your password has been updated.');

                return $this->redirectToRoute('nines_user_profile_index');
            }
            $this->addFlash('danger', 'The password does not match.');
        }

        return $this->render('@NinesUser/profile/password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
