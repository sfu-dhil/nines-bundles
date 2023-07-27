<?php

declare(strict_types=1);

namespace Nines\MediaBundle\Form;

use Nines\MediaBundle\Entity\Audio;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Audio form.
 */
class AudioType extends AbstractFileType {
    /**
     * Add form fields to $builder.
     *
     * @param null|mixed $label
     */
    public function buildForm(FormBuilderInterface $builder, array $options, $label = null) : void {
        parent::buildForm($builder, $options, 'Audio File');

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) : void {
            $audio = $event->getData();
            $form = $event->getForm();

            if ($audio && $audio->getId()) {
                $this->existingFile = $audio->getFile();

                $options = $form->get('file')->getConfig()->getOptions();
                $options['label'] = 'Replacement Audio';
                $options['required'] = false;
                $options['attr']['html_block'] = '
                    <figure class="text-center w-100">
                        <audio controls src="' . $this->router->generate('nines_media_audio_play', ['id' => $audio->getId()]) . '" type="' . $audio->getMimeType() . '" class="w-100">
                            Your browser does not support playing audio.
                        </audio>
                        <figcaption class="figure-caption">' . $audio->getOriginalName() . '</figcaption>
                    </figure>
                ';
                $form->add('file', FileType::class, $options);
                $form->add('originalName', TextType::class, [
                    'label' => 'Rename file',
                    'required' => false,
                ]);
            }
        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) : void {
            $audio = $event->getData();
            $form = $event->getForm();

            if ($form->get('file')->getData()) {
                $filesystem = new Filesystem();
                if ($path = $this->existingFile?->getRealPath()) {
                    $filesystem->remove($path);
                }
                $audio->preUpdate();
            }
        });
    }

    /**
     * Define options for the form.
     *
     * Set default, optional, and required options passed to the
     * buildForm() method via the $options parameter.
     */
    public function configureOptions(OptionsResolver $resolver) : void {
        $resolver->setDefaults([
            'data_class' => Audio::class,
        ]);
    }
}
