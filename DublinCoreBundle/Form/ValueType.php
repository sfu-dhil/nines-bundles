<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace Nines\DublinCoreBundle\Form;

use Nines\DublinCoreBundle\Entity\Element;
use Nines\DublinCoreBundle\Repository\ElementRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Document form.
 */
abstract class ValueType extends AbstractType {
    /**
     * Add form fields to $builder.
     */
    public static function add(FormBuilderInterface $builder, array $options, ElementRepository $repo) : void {
        foreach ($repo->indexQuery()->execute() as $element) {
            // @var Element $element
            $builder->add($element->getName(), CollectionType::class, [
                'label' => $element->getLabel(),
                'entry_type' => TextType::class,
                'required' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'entry_options' => [
                    'label' => false,
                ],
                'attr' => [
                    'help_block' => $element->getDescription(),
                    'class' => 'collection-simple',
                ],
                'mapped' => false,
            ]);
        }
    }
}
