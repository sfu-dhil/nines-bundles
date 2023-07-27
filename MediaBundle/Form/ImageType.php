<?php

declare(strict_types=1);

namespace Nines\MediaBundle\Form;

use Nines\MediaBundle\Entity\Image;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Image form.
 */
class ImageType extends AbstractFileType {
    /**
     * Add form fields to $builder.
     *
     * @param null|mixed $label
     */
    public function buildForm(FormBuilderInterface $builder, array $options, $label = 'Image File') : void {
        parent::buildForm($builder, $options, $label);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) : void {
            $image = $event->getData();
            $form = $event->getForm();

            if ($image && $image->getId()) {
                $this->existingFile = $image->getFile();

                $options = $form->get('file')->getConfig()->getOptions();
                $options['label'] = 'Replacement Image';
                $options['required'] = false;
                $options['attr']['html_block'] = '
                    <figure class="text-center w-100">
                        <img src="' . $this->router->generate('nines_media_image_thumb', ['id' => $image->getId()]) . '" class="figure-img img-fluid">
                        <figcaption class="figure-caption">' . $image->getOriginalName() . '</figcaption>
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
            $image = $event->getData();
            $form = $event->getForm();

            if ($form->get('file')->getData()) {
                $filesystem = new Filesystem();
                if ($path = $this->existingFile?->getRealPath()) {
                    $filesystem->remove($path);
                }
                if ($path = $image->getThumbFile()?->getRealPath()) {
                    $filesystem->remove($path);
                }
                $image->preUpdate();
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
            'data_class' => Image::class,
        ]);
    }
}
