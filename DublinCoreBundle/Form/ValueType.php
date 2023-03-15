<?php

declare(strict_types=1);

namespace Nines\DublinCoreBundle\Form;

use Nines\DublinCoreBundle\Entity\Element;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Document form.
 */
abstract class ValueType extends AbstractType {
    /**
     * @param array<string,mixed> $options
     */
    public static function add(FormBuilderInterface $builder, array $options) : void {
        $repo = $options['repo'];
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
                'help' => $element->getDescription(),
                'attr' => [
                    'class' => 'collection-simple',
                ],
                'mapped' => false,
            ]);
        }
    }
}
