<?php

declare(strict_types=1);

namespace Nines\MediaBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Nines\MediaBundle\Entity\Image;
use Nines\MediaBundle\Entity\ImageContainerInterface;
use Nines\MediaBundle\Form\ImageType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait ImageControllerTrait {
    abstract public function createForm(string $type, $data = null, array $options = []);

    abstract public function redirectToRoute(string $route, array $parameters = [], int $status = 302) : RedirectResponse;

    abstract public function addFlash(string $type, $message);

    abstract public function isCsrfTokenValid(string $id, ?string $token);

    /**
     * @throws Exception
     */
    protected function newImageAction(Request $request, EntityManagerInterface $em, ImageContainerInterface $container, string $route, array $routeParams) : array|RedirectResponse {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image->setEntity($container);
            $em->persist($image);
            $em->flush();
            $this->addFlash('success', 'The new image has been saved.');

            return $this->redirectToRoute($route, $routeParams);
        }

        return [
            'image' => $image,
            'form' => $form->createView(),
            'entity' => $container,
            'route' => $route,
            'routeParams' => $routeParams,
        ];
    }

    /**
     * @throws Exception
     */
    protected function editImageAction(Request $request, EntityManagerInterface $em, ImageContainerInterface $container, Image $image, string $route, array $routeParams) : array|RedirectResponse {
        if ( ! $container->containsImage($image)) {
            throw new NotFoundHttpException('That image is not associated.');
        }

        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($image);
            $em->flush();
            $this->addFlash('success', 'The image has been updated.');

            return $this->redirectToRoute($route, $routeParams);
        }

        return [
            'image' => $image,
            'form' => $form->createView(),
            'entity' => $container,
            'route' => $route,
            'routeParams' => $routeParams,
        ];
    }

    protected function deleteImageAction(Request $request, EntityManagerInterface $em, ImageContainerInterface $container, Image $image, string $route, array $routeParams) : RedirectResponse {
        if ( ! $this->isCsrfTokenValid('delete_image' . $image->getId(), $request->request->get('_token'))) {
            $this->addFlash('warning', 'Invalid security token.');

            return $this->redirectToRoute($route, $routeParams);
        }
        if ( ! $container->containsImage($image)) {
            throw new NotFoundHttpException('That image is not associated.');
        }
        $container->removeImage($image);
        $em->remove($image);
        $em->flush();
        $this->addFlash('success', 'The image has been removed.');

        return $this->redirectToRoute($route, $routeParams);
    }
}
