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
use Symfony\Contracts\Translation\TranslatorInterface;

class AbstractBuilder implements ContainerAwareInterface {
    use ContainerAwareTrait;

    public function __construct(
        protected TranslatorInterface $translatorInterface,
        protected FactoryInterface $factory,
        protected AuthorizationCheckerInterface $authChecker,
        protected TokenStorageInterface $tokenStorage,
    ) {}

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
            'label' => $this->translatorInterface->trans($name),
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
}
