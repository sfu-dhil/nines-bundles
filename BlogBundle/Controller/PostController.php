<?php

declare(strict_types=1);

namespace Nines\BlogBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\BlogBundle\Entity\Post;
use Nines\BlogBundle\Form\PostType;
use Nines\BlogBundle\Repository\PostRepository;
use Nines\UserBundle\Entity\User;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/post')]
class PostController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    #[Route(path: '/', name: 'nines_blog_post_index', methods: ['GET'])]
    public function index(Request $request, PostRepository $postRepository) : Response {
        $query = $postRepository->indexQuery($this->isGranted('ROLE_USER'));
        $pageSize = (int) $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return $this->render('@NinesBlog/post/index.html.twig', [
            'posts' => $this->paginator->paginate($query, $page, $pageSize),
        ]);
    }

    #[Route(path: '/search', name: 'nines_blog_post_search', methods: ['GET'])]
    public function search(Request $request, PostRepository $postRepository) : Response {
        $q = $request->query->get('q');
        if ($q) {
            $query = $postRepository->searchQuery($q);
            $posts = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'), [
                'wrap-queries' => true,
            ]);
        } else {
            $posts = [];
        }

        return $this->render('@NinesBlog/post/search.html.twig', [
            'posts' => $posts,
            'q' => $q,
        ]);
    }

    #[Route(path: '/new', name: 'nines_blog_post_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_BLOG_ADMIN')]
    public function new(EntityManagerInterface $entityManager, Request $request) : Response {
        /** @var User $user */
        $user = $this->getUser();
        $post = new Post();
        $post->setUser($user);

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($post);
            $entityManager->flush();
            $this->addFlash('success', 'The new post has been saved.');

            return $this->redirectToRoute('nines_blog_post_show', ['id' => $post->getId()]);
        }

        return $this->render('@NinesBlog/post/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}', name: 'nines_blog_post_show', methods: ['GET'])]
    public function show(Post $post) : Response {
        return $this->render('@NinesBlog/post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[IsGranted('ROLE_BLOG_ADMIN')]
    #[Route(path: '/{id}/edit', name: 'nines_blog_post_edit', methods: ['GET', 'POST'])]
    public function edit(EntityManagerInterface $entityManager, Request $request, Post $post) : Response {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'The updated post has been saved.');

            return $this->redirectToRoute('nines_blog_post_show', ['id' => $post->getId()]);
        }

        return $this->render('@NinesBlog/post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted('ROLE_BLOG_ADMIN')]
    #[Route(path: '/{id}', name: 'nines_blog_post_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, Request $request, Post $post) : RedirectResponse {
        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
            $this->addFlash('success', 'The post has been deleted.');
        } else {
            $this->addFlash('warning', 'The security token was not valid.');
        }

        return $this->redirectToRoute('nines_blog_post_index');
    }
}
