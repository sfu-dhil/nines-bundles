<?php

declare(strict_types=1);

namespace Nines\UtilBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DatalistType extends TextType {
    public function configureOptions(OptionsResolver $resolver) : void {
        parent::configureOptions($resolver);
        $resolver->setRequired(['datalist']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options) : void {
        parent::buildView($view, $form, $options);
        $view->vars['datalist'] = $options['datalist'];
    }

    public function getBlockPrefix() : string {
        return 'datalist';
    }
}
