<?php

declare(strict_types=1);

namespace Nines\UtilBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Nines\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

class AbstractBuilder implements ContainerAwareInterface {
    use ContainerAwareTrait;

    protected ?FactoryInterface $factory = null;

    protected ?AuthorizationCheckerInterface $authChecker = null;

    protected ?TokenStorageInterface $tokenStorage = null;

    protected function hasRole(string $role) : bool {
        if ( ! $this->tokenStorage->getToken()) {
            return false;
        }

        return $this->authChecker->isGranted($role);
    }

    protected function getUser() : ?User {
        if ( ! $this->hasRole('ROLE_USER')) {
            return null;
        }
        $user = $this->tokenStorage->getToken()->getUser();
        if ( ! $user instanceof User) {
            return null;
        }

        return $user;
    }

    protected function dropdown(string $name) : ItemInterface {
        $slugger = new AsciiSlugger();
        $slug = $slugger->slug($name);

        $root = $this->factory->createItem('root');
        $root->setChildrenAttributes([
            'class' => 'navbar-nav',
        ]);
        $slugger = new AsciiSlugger();

        return $root->addChild($slug, [
            'uri' => '#',
            'label' => $name,
            'attributes' => [
                'class' => 'nav-item dropdown',
            ],
            'linkAttributes' => [
                'class' => 'nav-link dropdown-toggle',
                'role' => 'button',
                'data-bs-toggle' => 'dropdown',
                'id' => "dropdown-{$slug}",
            ],
            'childrenAttributes' => [
                'class' => 'dropdown-menu text-small shadow',
                'aria-labelledby' => "dropdown-{$slug}",
            ],
        ]);
    }

    protected function addDivider(ItemInterface $item, ?string $name = 'divider') : void {
        $divider = $item->addChild($name, [
            'label' => '',
            'attributes' => [
                'class' => 'dropdown-divider',
            ],
        ]);
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setFactory(FactoryInterface $factory) : void {
        $this->factory = $factory;
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setAuthChecker(AuthorizationCheckerInterface $authChecker) : void {
        $this->authChecker = $authChecker;
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setTokenStorage(TokenStorageInterface $tokenStorage) : void {
        $this->tokenStorage = $tokenStorage;
    }
}
