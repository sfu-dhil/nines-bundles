<?php

declare(strict_types=1);

namespace Nines\MediaBundle\Controller;

use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\MediaBundle\Entity\Link;
use Nines\MediaBundle\Repository\LinkRepository;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/link')]
class LinkController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    #[Route(path: '/', name: 'nines_media_link_index', methods: ['GET'])]
    #[IsGranted('ROLE_MEDIA_ADMIN')]
    #[Template('@NinesMedia/link/index.html.twig')]
    public function index(Request $request, LinkRepository $linkRepository) : array {
        $q = $request->query->get('q');
        $query = $q ? $linkRepository->searchQuery($q) : $linkRepository->indexQuery();

        return [
            'links' => $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'), ['wrap-queries' => true]),
            'q' => $q,
        ];
    }

    #[Route(path: '/{id}', name: 'nines_media_link_show', methods: ['GET'])]
    #[IsGranted('ROLE_MEDIA_ADMIN')]
    public function show(Link $link) : Response {
        return $this->render('@NinesMedia/link/show.html.twig', [
            'link' => $link,
        ]);
    }
}
