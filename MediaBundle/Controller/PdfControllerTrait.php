<?php

declare(strict_types=1);

namespace Nines\MediaBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Nines\MediaBundle\Entity\Pdf;
use Nines\MediaBundle\Entity\PdfContainerInterface;
use Nines\MediaBundle\Form\PdfType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait PdfControllerTrait {
    abstract public function createForm(string $type, $data = null, array $options = []);

    abstract public function redirectToRoute(string $route, array $parameters = [], int $status = 302) : RedirectResponse;

    abstract public function addFlash(string $type, $message);

    abstract public function isCsrfTokenValid(string $id, ?string $token);

    /**
     * @throws Exception
     */
    protected function newPdfAction(Request $request, EntityManagerInterface $em, PdfContainerInterface $container, string $route, array $routeParams) : array|RedirectResponse {
        $pdf = new Pdf();
        $form = $this->createForm(PdfType::class, $pdf);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pdf->setEntity($container);
            $em->persist($pdf);
            $em->flush();
            $this->addFlash('success', 'The new pdf has been saved.');

            return $this->redirectToRoute($route, $routeParams);
        }

        return [
            'pdf' => $pdf,
            'form' => $form->createView(),
            'entity' => $container,
            'route' => $route,
            'routeParams' => $routeParams,
        ];
    }

    /**
     * @throws Exception
     */
    protected function editPdfAction(Request $request, EntityManagerInterface $em, PdfContainerInterface $container, Pdf $pdf, string $route, array $routeParams) : array|RedirectResponse {
        if ( ! $container->containsPdf($pdf)) {
            throw new NotFoundHttpException('That pdf is not associated.');
        }

        $form = $this->createForm(PdfType::class, $pdf);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($pdf);
            $em->flush();
            $this->addFlash('success', 'The pdf has been updated.');

            return $this->redirectToRoute($route, $routeParams);
        }

        return [
            'pdf' => $pdf,
            'form' => $form->createView(),
            'entity' => $container,
            'route' => $route,
            'routeParams' => $routeParams,
        ];
    }

    protected function deletePdfAction(Request $request, EntityManagerInterface $em, PdfContainerInterface $container, Pdf $pdf, string $route, array $routeParams) : RedirectResponse {
        if ( ! $this->isCsrfTokenValid('delete_pdf' . $pdf->getId(), $request->request->get('_token'))) {
            $this->addFlash('warning', 'Invalid security token.');

            return $this->redirectToRoute($route, $routeParams);
        }
        if ( ! $container->containsPdf($pdf)) {
            throw new NotFoundHttpException('That pdf is not associated.');
        }
        $container->removePdf($pdf);
        $em->remove($pdf);
        $em->flush();
        $this->addFlash('success', 'The pdf has been removed.');

        return $this->redirectToRoute($route, $routeParams);
    }
}
