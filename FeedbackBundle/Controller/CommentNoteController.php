<?php

declare(strict_types=1);

namespace Nines\FeedbackBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\FeedbackBundle\Entity\CommentNote;
use Nines\FeedbackBundle\Repository\CommentNoteRepository;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/comment_note')]
class CommentNoteController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    #[Route(path: '/', name: 'nines_feedback_comment_note_index', methods: ['GET'])]
    public function index(Request $request, CommentNoteRepository $commentNoteRepository) : Response {
        $query = $commentNoteRepository->indexQuery();
        $pageSize = (int) $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return $this->render('@NinesFeedback/comment_note/index.html.twig', [
            'comment_notes' => $this->paginator->paginate($query, $page, $pageSize),
        ]);
    }

    #[Route(path: '/search', name: 'nines_feedback_comment_note_search', methods: ['GET'])]
    public function search(Request $request, CommentNoteRepository $commentNoteRepository) : Response {
        $q = $request->query->get('q');
        if ($q) {
            $query = $commentNoteRepository->searchQuery($q);
            $commentNotes = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'), [
                'wrap-queries' => true,
            ]);
        } else {
            $commentNotes = [];
        }

        return $this->render('@NinesFeedback/comment_note/search.html.twig', [
            'comment_notes' => $commentNotes,
            'q' => $q,
        ]);
    }

    #[Route(path: '/{id}', name: 'nines_feedback_comment_note_show', methods: ['GET'])]
    public function show(CommentNote $commentNote) : Response {
        return $this->render('@NinesFeedback/comment_note/show.html.twig', [
            'comment_note' => $commentNote,
        ]);
    }

    #[IsGranted('ROLE_FEEDBACK_ADMIN')]
    #[Route(path: '/{id}', name: 'nines_feedback_comment_note_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, Request $request, CommentNote $commentNote) : RedirectResponse {
        if ($this->isCsrfTokenValid('delete' . $commentNote->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commentNote);
            $entityManager->flush();
            $this->addFlash('success', 'The commentNote has been deleted.');
        } else {
            $this->addFlash('warning', 'The security token was not valid.');
        }

        return $this->redirectToRoute('nines_feedback_comment_note_index');
    }
}
