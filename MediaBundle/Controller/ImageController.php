<?php

declare(strict_types=1);

namespace Nines\MediaBundle\Controller;

use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\MediaBundle\Entity\Image;
use Nines\MediaBundle\Repository\ImageRepository;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/image')]
class ImageController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    #[Route(path: '/', name: 'nines_media_image_index', methods: ['GET'])]
    #[IsGranted('ROLE_MEDIA_ADMIN')]
    #[Template('@NinesMedia/image/index.html.twig')]
    public function index(Request $request, ImageRepository $imageRepository) : array {
        $q = $request->query->get('q');
        $query = $q ? $imageRepository->searchQuery($q) : $imageRepository->indexQuery();

        return [
            'images' => $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'), ['wrap-queries' => true]),
            'q' => $q,
        ];
    }

    #[Route(path: '/{id}', name: 'nines_media_image_show', methods: ['GET'])]
    #[IsGranted('ROLE_MEDIA_ADMIN')]
    #[Template('@NinesMedia/image/show.html.twig')]
    public function show(Image $image) : array {
        return [
            'image' => $image,
        ];
    }

    #[Route(path: '/{id}/view', name: 'nines_media_image_view', methods: ['GET'])]
    public function view(Image $image) : BinaryFileResponse {
        return new BinaryFileResponse($image->getFile());
    }

    #[Route(path: '/{id}/thumb', name: 'nines_media_image_thumb', methods: ['GET'])]
    public function thumbnail(Image $image) : BinaryFileResponse {
        return new BinaryFileResponse($image->getThumbFile());
    }
}
