<?php

declare(strict_types=1);

namespace Nines\MediaBundle\Form;

use Nines\MediaBundle\Service\AbstractFileManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Audio form.
 */
class AbstractFileType extends AbstractType {
    public function __construct(
        protected UrlGeneratorInterface $router,
        protected ?File $existingFile = null,
    ) {
    }

    /**
     * Add form fields to $builder.
     */
    public function buildForm(FormBuilderInterface $builder, array $options, string $label = 'File') : void {
        $maxSize = AbstractFileManager::getMaxUploadSize(false);
        $maxBytes = AbstractFileManager::getMaxUploadSize(true);

        $builder->add('id', HiddenType::class, [
            'attr' => [
                'class' => 'position-id',
            ],
            'disabled' => true,
        ]);
        $builder->add('file', FileType::class, [
            'label' => $label,
            'required' => true,
            'help' => "Select a file to upload which is less than {$maxSize} in size.",
            'attr' => [
                'data-maxsize' => $maxBytes,
            ],
        ]);
        $builder->add('originalName', HiddenType::class, [
            'label' => 'File name',
            'required' => false,
            'disabled' => true,
        ]);
        $builder->add('description', TextareaType::class, [
            'label' => 'Description',
            'required' => false,
            'attr' => [
                'class' => 'tinymce',
            ],
        ]);
        $builder->add('license', TextareaType::class, [
            'label' => 'License',
            'required' => false,
            'attr' => [
                'class' => 'tinymce',
            ],
        ]);
    }
}
