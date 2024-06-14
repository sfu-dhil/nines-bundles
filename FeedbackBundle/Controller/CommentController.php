<?php

declare(strict_types=1);

namespace Nines\FeedbackBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\FeedbackBundle\Entity\Comment;
use Nines\FeedbackBundle\Entity\CommentNote;
use Nines\FeedbackBundle\Form\AdminCommentType;
use Nines\FeedbackBundle\Form\CommentNoteType;
use Nines\FeedbackBundle\Form\CommentType;
use Nines\FeedbackBundle\Repository\CommentRepository;
use Nines\FeedbackBundle\Repository\CommentStatusRepository;
use Nines\FeedbackBundle\Services\CommentService;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Nines\UtilBundle\Services\EntityLinker;
use Nines\UtilBundle\Services\Notifier;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/comment')]
class CommentController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    #[Route(path: '/', name: 'nines_feedback_comment_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request, CommentRepository $commentRepository, CommentStatusRepository $statusRepository) : Response {
        $query = $commentRepository->indexQuery($request->query->get('status'));
        $pageSize = (int) $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return $this->render('@NinesFeedback/comment/index.html.twig', [
            'comments' => $this->paginator->paginate($query, $page, $pageSize),
            'statuses' => $statusRepository->findBy([], ['label' => 'ASC']),
        ]);
    }

    #[Route(path: '/search', name: 'nines_feedback_comment_search', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function search(Request $request, CommentRepository $commentRepository) : Response {
        $q = $request->query->get('q');
        if ($q) {
            $query = $commentRepository->searchQuery($q);
            $comments = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'), [
                'wrap-queries' => true,
            ]);
        } else {
            $comments = [];
        }

        return $this->render('@NinesFeedback/comment/search.html.twig', [
            'comments' => $comments,
            'q' => $q,
        ]);
    }

    /**
     * @throws Exception
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws TransportExceptionInterface
     */
    #[Route(path: '/new', name: 'nines_feedback_comment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TranslatorInterface $translator, CommentService $service, EntityManagerInterface $em, EntityLinker $linker, Notifier $notifier) : Response {
        $entity_id = $request->request->get('entity_id', null);
        $entity_class = $request->request->get('entity_class', null);
        $repo = $em->getRepository($entity_class);
        $entity = $repo->find($entity_id);

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $service->addComment($entity, $comment);
            $em->flush();
            $this->addFlash('success', $translator->trans('The new comment has been saved.'));
            $recipients = $this->getParameter('nines_feedback.recipients');
            $notifier->notify($recipients, 'New Comment Received', '@NinesFeedback/notification/comment.html.twig', [
                'comment' => $comment,
            ]);

            return $this->redirect($linker->link($entity));
        }

        return $this->render('@NinesFeedback/comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
            'entity' => $entity,
        ]);
    }

    #[Route(path: '/{id}', name: 'nines_feedback_comment_show', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function show(Request $request, Comment $comment, EntityManagerInterface $em) : Response {
        $adminForm = $this->createForm(AdminCommentType::class, $comment);
        $adminForm->handleRequest($request);
        if ($adminForm->isSubmitted() && $adminForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'The comment has been updated.');

            return $this->redirectToRoute('nines_feedback_comment_show', ['id' => $comment->getId()]);
        }

        $note = new CommentNote();
        $note->setUser($this->getUser());
        $note->setComment($comment);
        $noteForm = $this->createForm(CommentNoteType::class, $note);
        $noteForm->handleRequest($request);
        if ($noteForm->isSubmitted() && $noteForm->isValid()) {
            $comment->addNote($note);
            $em->persist($note);
            $em->flush();
            $this->addFlash('success', 'The note has been saved.');

            return $this->redirectToRoute('nines_feedback_comment_show', ['id' => $comment->getId()]);
        }

        return $this->render('@NinesFeedback/comment/show.html.twig', [
            'comment' => $comment,
            'note_form' => $noteForm->createView(),
            'admin_form' => $adminForm->createView(),
        ]);
    }

    #[IsGranted('ROLE_FEEDBACK_ADMIN')]
    #[Route(path: '/{id}', name: 'nines_feedback_comment_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_FEEDBACK_ADMIN')]
    public function delete(EntityManagerInterface $entityManager, Request $request, Comment $comment) : RedirectResponse {
        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
            $this->addFlash('success', 'The comment has been deleted.');
        } else {
            $this->addFlash('warning', 'The security token was not valid.');
        }

        return $this->redirectToRoute('nines_feedback_comment_index');
    }
}
