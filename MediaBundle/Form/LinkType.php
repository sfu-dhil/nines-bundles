<?php

declare(strict_types=1);

namespace Nines\MediaBundle\Form;

use Nines\MediaBundle\Entity\Link;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Link form.
 */
class LinkType extends AbstractType {
    /**
     * Add form fields to $builder.
     */
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('url', UrlType::class, [
            'label' => 'Url',
            'required' => true,
        ]);
        $builder->add('text', TextType::class, [
            'label' => 'Text',
            'required' => false,
        ]);
    }

    /**
     * Define options for the form.
     *
     * Set default, optional, and required options passed to the
     * buildForm() method via the $options parameter.
     */
    public function configureOptions(OptionsResolver $resolver) : void {
        $resolver->setDefaults([
        ]);
    }
}
