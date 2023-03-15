<?php

declare(strict_types=1);

namespace Nines\MediaBundle\Menu;

use Knp\Menu\ItemInterface;
use Nines\UtilBundle\Menu\AbstractBuilder;

/**
 * Class to build some menus for navigation.
 */
class Builder extends AbstractBuilder {
    /**
     * @param array<string,mixed> $options
     */
    public function navMenu(array $options) : ItemInterface {
        $title = $options['title'] ?? 'Media';
        $menu = $this->dropdown($title);

        $menu->addChild('Audio Files', [
            'route' => 'nines_media_audio_index',
            'linkAttributes' => [
                'class' => 'dropdown-item',
            ],
        ]);
        $menu->addChild('Images', [
            'route' => 'nines_media_image_index',
            'linkAttributes' => [
                'class' => 'dropdown-item',
            ],
        ]);
        $menu->addChild('Pdfs', [
            'route' => 'nines_media_pdf_index',
            'linkAttributes' => [
                'class' => 'dropdown-item',
            ],
        ]);
        $this->addDivider($menu);
        $menu->addChild('Links', [
            'route' => 'nines_media_link_index',
            'linkAttributes' => [
                'class' => 'dropdown-item',
            ],
        ]);

        return $menu->getParent();
    }
}
