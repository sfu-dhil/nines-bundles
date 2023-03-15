<?php

declare(strict_types=1);

namespace Nines\MediaBundle\Form;

use Nines\MediaBundle\Entity\Pdf;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Pdf form.
 */
class PdfType extends AbstractFileType {
    /**
     * Add form fields to $builder.
     *
     * @param null|mixed $label
     */
    public function buildForm(FormBuilderInterface $builder, array $options, $label = null) : void {
        parent::buildForm($builder, $options, 'PDF File');

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) : void {
            $pdf = $event->getData();
            $form = $event->getForm();

            if ($pdf && $pdf->getId()) {
                $this->existingFile = $pdf->getFile();

                $options = $form->get('file')->getConfig()->getOptions();
                $options['label'] = 'Replacement Pdf';
                $options['attr']['html_block'] = '
                    <figure class="text-center w-100">
                        <img src="' . $this->router->generate('nines_media_pdf_thumb', ['id' => $pdf->getId()]) . '" class="figure-img img-fluid rounded">
                        <figcaption class="figure-caption">' . $pdf->getOriginalName() . '</figcaption>
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
            $pdf = $event->getData();
            $form = $event->getForm();

            if ($form->get('file')->getData()) {
                $filesystem = new Filesystem();
                if ($path = $this->existingFile?->getRealPath()) {
                    $filesystem->remove($path);
                }
                if ($path = $pdf->getThumbFile()?->getRealPath()) {
                    $filesystem->remove($path);
                }
                $pdf->preUpdate();
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
            'data_class' => Pdf::class,
        ]);
    }
}
