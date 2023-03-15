<?php

declare(strict_types=1);

namespace Nines\DublinCoreBundle\Form\Mapper;

use Doctrine\ORM\EntityManagerInterface;
use Nines\DublinCoreBundle\Entity\Value;
use Nines\DublinCoreBundle\Entity\ValueInterface;
use Nines\DublinCoreBundle\Repository\ElementRepository;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\DataMapper\DataMapper;

class DublinCoreMapper extends DataMapper implements DataMapperInterface {
    private ?ElementRepository $elementRepo = null;

    private ?EntityManagerInterface $em = null;

    private bool $parentCall = true;

    public function setParentCall(bool $call) : void {
        $this->parentCall = $call;
    }

    public function mapDataToForms($viewData, $forms) : void {
        if ( ! $viewData instanceof ValueInterface) {
            return;
        }
        if ($this->parentCall) {
            parent::mapDataToForms($viewData, $forms);
        }
        $forms = iterator_to_array($forms);
        foreach ($this->elementRepo->findAll() as $element) {
            // @var Element $element
            $forms[$element->getName()]->setData($viewData->getValues($element->getName()));
        }
    }

    public function mapFormsToData($forms, &$viewData) : void {
        if ( ! $viewData instanceof ValueInterface) {
            return;
        }
        if ($this->parentCall) {
            parent::mapFormsToData($forms, $viewData);
        }
        if ( ! $this->em->contains($viewData)) {
            $this->em->persist($viewData);
            $this->em->flush();
        }
        $forms = iterator_to_array($forms);
        foreach ($viewData->getValues() as $value) {
            $this->em->remove($value);
        }
        foreach ($this->elementRepo->findAll() as $element) {
            foreach ($forms[$element->getName()]->getData() as $data) {
                $value = new Value();
                $value->setEntity($viewData);
                $value->setData($data);
                $value->setElement($element);
                $this->em->persist($value);
            }
        }
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setEntityManager(EntityManagerInterface $em) : void {
        $this->em = $em;
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setElementRepository(ElementRepository $repo) : void {
        $this->elementRepo = $repo;
    }
}
