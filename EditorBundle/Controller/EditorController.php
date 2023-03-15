<?php

declare(strict_types=1);

namespace Nines\EditorBundle\Controller;

use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class EditorController extends AbstractController {
    public const FORBIDDEN = '/[^a-z0-9_. -]/i';

    private function getUploadDir() : string {
        $uploadDir = $this->getParameter('nines.editor.upload_dir');
        if ('/' !== $uploadDir[0]) {
            $uploadDir = $this->getParameter('kernel.project_dir') . '/' . $uploadDir;
        }

        return $uploadDir;
    }

    /**
     * @throws Exception
     */
    #[Route(path: '/upload/image', name: 'nines_editor_image_upload', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function editorUploadImageAction(Request $request) : JsonResponse {
        if (1 !== $request->files->count()) {
            throw new BadRequestHttpException('Expected one file parameter. Got ' . $request->files->count() . ' instead.');
        }

        $uploadDir = $this->getUploadDir();
        $uploadFile = $request->files->get('file');
        $name = preg_replace(self::FORBIDDEN, '_', $uploadFile->getClientOriginalName());
        $info = pathinfo($name);
        $slug = uniqid();

        $filename = $info['filename'] . '_' . $slug . '.' . $info['extension'];
        $uploadFile->move($uploadDir, $filename);

        return new JsonResponse([
            'location' => $this->generateUrl('nines_editor_image_view', ['filename' => $filename]),
        ]);
    }

    /**
     * @param mixed $filename
     */
    #[Route(path: '/upload/image/{filename}', name: 'nines_editor_image_view', methods: ['GET'])]
    public function editorViewImageAction(Request $request, $filename) : BinaryFileResponse {
        if (preg_match(self::FORBIDDEN, $filename)) {
            throw new BadRequestHttpException('Invalid file name: ' . $filename);
        }
        $uploadDir = $this->getUploadDir();
        $path = $uploadDir . '/' . $filename;
        if ( ! file_exists($path)) {
            throw new NotFoundHttpException("Requested file {$filename} could not be found.");
        }

        return new BinaryFileResponse($path);
    }
}
