<?php

declare(strict_types=1);

namespace Nines\MediaBundle\Controller;

use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\MediaBundle\Entity\Audio;
use Nines\MediaBundle\Repository\AudioRepository;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/audio')]
class AudioController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    #[Route(path: '/', name: 'nines_media_audio_index', methods: ['GET'])]
    #[IsGranted('ROLE_MEDIA_ADMIN')]
    #[Template('@NinesMedia/audio/index.html.twig')]
    public function index(Request $request, AudioRepository $audioRepository) : array {
        $q = $request->query->get('q');
        $query = $q ? $audioRepository->searchQuery($q) : $audioRepository->indexQuery();

        return [
            'audios' => $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'), ['wrap-queries' => true]),
            'q' => $q,
        ];
    }

    #[Route(path: '/{id}', name: 'nines_media_audio_show', methods: ['GET'])]
    #[IsGranted('ROLE_MEDIA_ADMIN')]
    #[Template('@NinesMedia/audio/show.html.twig')]
    public function show(Audio $audio) : array {
        return [
            'audio' => $audio,
        ];
    }

    #[Route(path: '/{id}/play', name: 'nines_media_audio_play', methods: ['GET'])]
    public function play(Audio $audio) : BinaryFileResponse {
        return new BinaryFileResponse($audio->getFile());
    }
}
