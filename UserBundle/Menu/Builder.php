<?php

declare(strict_types=1);

namespace Nines\UserBundle\Menu;

use Knp\Menu\ItemInterface;
use Nines\UtilBundle\Menu\AbstractBuilder;

/**
 * Class to build some menus for navigation.
 */
class Builder extends AbstractBuilder {
    /**
     * @param array<string,mixed> $options
     */
    public function userMenu(array $options) : ItemInterface {
        $name = $options['name'] ?? 'Login';

        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttributes([
            'class' => 'navbar-nav ms-auto',
        ]);

        $user = $this->getUser();
        if ( ! $this->hasRole('ROLE_USER')) {
            $menu->addChild($name, [
                'route' => 'nines_user_security_login',
                'linkAttributes' => [
                    'class' => 'dropdown-item',
                ],
            ]);

            return $menu;
        }

        $user = $menu->addChild('user', [
            'uri' => '#',
            'label' => $user->getUserIdentifier(),
            'attributes' => [
                'class' => 'nav-item dropdown',
            ],
            'linkAttributes' => [
                'class' => 'nav-link dropdown-toggle',
                'role' => 'button',
                'data-bs-toggle' => 'dropdown',
                'id' => 'user-dropdown',
            ],
            'childrenAttributes' => [
                'class' => 'dropdown-menu text-small shadow dropdown-menu-end',
                'aria-labelledby' => 'user-dropdown',
            ],
        ]);

        $user->addChild('Profile', [
            'route' => 'nines_user_profile_index',
            'linkAttributes' => [
                'class' => 'dropdown-item',
            ],
        ]);
        $user->addChild('Change password', [
            'route' => 'nines_user_profile_password',
            'linkAttributes' => [
                'class' => 'dropdown-item',
            ],
        ]);
        $user->addChild('Logout', [
            'route' => 'nines_user_security_logout',
            'linkAttributes' => [
                'class' => 'dropdown-item',
            ],
        ]);

        if ($this->hasRole('ROLE_USER_ADMIN')) {
            $user->addChild('divider', [
                'label' => '<hr class="dropdown-divider">',
                'extras' => [
                    'safe_label' => true,
                ],
            ]);

            $user->addChild('users', [
                'label' => 'Users',
                'route' => 'nines_user_admin_index',
                'linkAttributes' => [
                    'class' => 'dropdown-item',
                ],
            ]);
        }

        return $menu;
    }

    public function userSidebarMenu(array $options) : ItemInterface {
        $user = $this->getUser();
        $name = $options['name'] ?? 'Login';

        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttributes([
            'class' => 'list-unstyled ps-0 mb-0 mt-auto',
        ]);

        $menu->addChild('divider1', [
            'label' => '<hr>',
            'extras' => [
                'safe_label' => true,
            ],
        ]);
        if ( ! $this->hasRole('ROLE_USER')) {
            $menu->addChild($name, [
                'route' => 'nines_user_security_login',
                'linkAttributes' => [
                    'class' => 'dropdown-item',
                ],
            ]);

            return $menu;
        }

        $user = $menu->addChild('user', [
            'uri' => '#',
            'label' => "<i class='bi bi-person-circle h5'>&nbsp;</i>{$user->getUserIdentifier()}",
            'attributes' => [
                'class' => 'dropdown',
            ],
            'linkAttributes' => [
                'class' => 'link-dark d-block text-decoration-none rounded text-truncate dropdown-toggle h6',
                'role' => 'button',
                'data-bs-toggle' => 'dropdown',
                'id' => 'user-dropdown',
            ],
            'childrenAttributes' => [
                'class' => 'dropdown-menu text-small shadow',
                'aria-labelledby' => 'user-dropdown',
            ],
            'extras' => [
                'safe_label' => true,
            ],
        ]);

        $user->addChild('Profile', [
            'route' => 'nines_user_profile_index',
            'linkAttributes' => [
                'class' => 'dropdown-item',
            ],
        ]);
        $user->addChild('Change password', [
            'route' => 'nines_user_profile_password',
            'linkAttributes' => [
                'class' => 'dropdown-item',
            ],
        ]);
        if ($this->hasRole('ROLE_USER_ADMIN')) {
            $user->addChild('Manage Users', [
                'route' => 'nines_user_admin_index',
                'linkAttributes' => [
                    'class' => 'dropdown-item',
                ],
            ]);
        }
        $user->addChild('divider1', [
            'label' => '<hr class="dropdown-divider">',
            'extras' => [
                'safe_label' => true,
            ],
        ]);
        $user->addChild('Logout', [
            'route' => 'nines_user_security_logout',
            'linkAttributes' => [
                'class' => 'dropdown-item',
            ],
        ]);

        return $menu;
    }
}
