<?php

declare(strict_types=1);

namespace Nines\MediaBundle\Controller;

use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\MediaBundle\Entity\Pdf;
use Nines\MediaBundle\Repository\PdfRepository;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/pdf')]
class PdfController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    #[Route(path: '/', name: 'nines_media_pdf_index', methods: ['GET'])]
    #[IsGranted('ROLE_MEDIA_ADMIN')]
    #[Template('@NinesMedia/pdf/index.html.twig')]
    public function index(Request $request, PdfRepository $pdfRepository) : array {
        $q = $request->query->get('q');
        $query = $q ? $pdfRepository->searchQuery($q) : $pdfRepository->indexQuery();

        return [
            'pdfs' => $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'), ['wrap-queries' => true]),
            'q' => $q,
        ];
    }

    #[Route(path: '/{id}', name: 'nines_media_pdf_show', methods: ['GET'])]
    #[IsGranted('ROLE_MEDIA_ADMIN')]
    #[Template('@NinesMedia/pdf/show.html.twig')]
    public function show(Pdf $pdf) : array {
        return [
            'pdf' => $pdf,
        ];
    }

    #[Route(path: '/{id}/view', name: 'nines_media_pdf_view', methods: ['GET'])]
    public function view(Pdf $pdf) : BinaryFileResponse {
        $response = new BinaryFileResponse($pdf->getFile());
        $response->headers->set('Content-Type', 'application/pdf');

        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $pdf->getOriginalName()
        );

        return $response;
    }

    #[Route(path: '/{id}/thumb', name: 'nines_media_pdf_thumb', methods: ['GET'])]
    public function thumbnail(Pdf $pdf) : BinaryFileResponse {
        return new BinaryFileResponse($pdf->getThumbFile());
    }
}
