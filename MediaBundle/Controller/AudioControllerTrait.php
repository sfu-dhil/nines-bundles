<?php

declare(strict_types=1);

namespace Nines\MediaBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Nines\MediaBundle\Entity\Audio;
use Nines\MediaBundle\Entity\AudioContainerInterface;
use Nines\MediaBundle\Form\AudioType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait AudioControllerTrait {
    abstract public function createForm(string $type, $data = null, array $options = []);

    abstract public function redirectToRoute(string $route, array $parameters = [], int $status = 302) : RedirectResponse;

    abstract public function addFlash(string $type, $message);

    abstract public function isCsrfTokenValid(string $id, ?string $token);

    /**
     * @throws Exception
     */
    protected function newAudioAction(Request $request, EntityManagerInterface $em, AudioContainerInterface $container, string $route, array $routeParams) : array|RedirectResponse {
        $audio = new Audio();
        $form = $this->createForm(AudioType::class, $audio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $audio->setEntity($container);
            $em->persist($audio);
            $em->flush();
            $this->addFlash('success', 'The new audio has been saved.');

            return $this->redirectToRoute($route, $routeParams);
        }

        return [
            'audio' => $audio,
            'form' => $form->createView(),
            'entity' => $container,
            'route' => $route,
            'routeParams' => $routeParams,
        ];
    }

    /**
     * @throws Exception
     */
    protected function editAudioAction(Request $request, EntityManagerInterface $em, AudioContainerInterface $container, Audio $audio, string $route, array $routeParams) : array|RedirectResponse {
        if ( ! $container->containsAudio($audio)) {
            throw new NotFoundHttpException('That audio is not associated.');
        }

        $form = $this->createForm(AudioType::class, $audio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($audio);
            $em->flush();
            $this->addFlash('success', 'The audio has been updated.');

            return $this->redirectToRoute($route, $routeParams);
        }

        return [
            'audio' => $audio,
            'form' => $form->createView(),
            'entity' => $container,
            'route' => $route,
            'routeParams' => $routeParams,
        ];
    }

    /**
     * @throws Exception
     */
    protected function deleteAudioAction(Request $request, EntityManagerInterface $em, AudioContainerInterface $container, Audio $audio, string $route, array $routeParams) : RedirectResponse {
        if ( ! $this->isCsrfTokenValid('delete_audio' . $audio->getId(), $request->request->get('_token'))) {
            $this->addFlash('warning', 'Invalid security token.');

            return $this->redirectToRoute($route, $routeParams);
        }
        if ( ! $container->containsAudio($audio)) {
            throw new NotFoundHttpException('That audio is not associated.');
        }
        $container->removeAudio($audio);
        $em->remove($audio);
        $em->flush();
        $this->addFlash('success', 'The audio has been removed.');

        return $this->redirectToRoute($route, $routeParams);
    }
}
