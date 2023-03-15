<?php

declare(strict_types=1);

namespace Nines\UtilBundle\Services;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Nines\FeedbackBundle\Entity\Comment;
use Nines\UtilBundle\Entity\AbstractEntity;
use Nines\UtilBundle\Entity\LinkedEntityInterface;
use ReflectionClass;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EntityLinker {
    /**
     * Map of FQCN to the show route name.
     *
     * @var array<string,string>
     */
    private ?array $routing = null;

    private ?UrlGeneratorInterface $urlGenerator = null;

    private ?EntityManagerInterface $em = null;

    /**
     * @param array<string,string> $routing
     */
    public function __construct(array $routing = []) {
        $this->routing = $routing;
    }

    /**
     * @param array<string,string> $parameters
     * @param ?AbstractEntity $entity
     *
     * @throws Exception
     */
    public function link(?AbstractEntity $entity, array $parameters = [], int $type = UrlGeneratorInterface::ABSOLUTE_PATH) : ?string {
        if ( ! $entity) {
            return null;
        }
        $class = ClassUtils::getClass($entity);
        if ( ! isset($this->routing[$class])) {
            throw new Exception("Cannot link to unconfigured entity {$class}.");
        }
        $name = $this->routing[$class];
        $params = array_merge(['id' => $entity->getId()], $parameters);

        return $this->urlGenerator->generate($name, $params, $type);
    }

    /**
     * Find the entity corresponding to a comment.
     */
    public function findEntity(LinkedEntityInterface $linked) : ?object {
        list($class, $id) = explode(':', $linked->getEntity());

        try {
            return $this->em->getRepository($class)->find($id);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Return the short class name for the entity a comment refers to or null if
     * the entity cannot be found.
     */
    public function entityType(LinkedEntityInterface $linked) : ?string {
        $entity = $this->findEntity($linked);
        if ( ! $entity) {
            return 'unknown type ' . $linked->getEntity();
        }
        $reflection = new ReflectionClass($entity);

        return $reflection->getShortName();
    }

    /**
     * @param array<string,string> $routing
     */
    public function setRouting(array $routing = []) : void {
        $this->routing = $routing;
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setUrlGenerator(UrlGeneratorInterface $urlGenerator) : void {
        $this->urlGenerator = $urlGenerator;
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setEntityManager(EntityManagerInterface $em) : void {
        $this->em = $em;
    }
}
