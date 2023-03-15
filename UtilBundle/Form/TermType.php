<?php

declare(strict_types=1);

namespace Nines\UtilBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

abstract class TermType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('label', TextType::class, [
            'label' => 'Label',
            'help' => 'A human-readable name.',
        ]);
        $builder->add('description', TextareaType::class, [
            'label' => 'Description',
            'required' => false,
            'help' => 'A simple description of the item.',
            'attr' => [
                'class' => 'tinymce',
            ],
        ]);
    }
}
