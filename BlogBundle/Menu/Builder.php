<?php

declare(strict_types=1);

namespace Nines\BlogBundle\Menu;

use Knp\Menu\ItemInterface;
use Nines\BlogBundle\Repository\PageRepository;
use Nines\BlogBundle\Repository\PostRepository;
use Nines\BlogBundle\Repository\PostStatusRepository;
use Nines\UtilBundle\Menu\AbstractBuilder;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class to build some menus for navigation.
 */
class Builder extends AbstractBuilder {
    use ContainerAwareTrait;

    private ?PostStatusRepository $postStatusRepository = null;

    private ?PostRepository $postRepository = null;

    private ?PageRepository $pageRepository = null;

    /**
     * @param array<string,string> $options
     */
    public function postMenu(array $options) : ItemInterface {
        $menu = $this->dropdown($options['title'] ?? 'Announcements');

        $public = $this->postStatusRepository->findBy(['public' => true]);

        // @TODO turn this into menuQuery() or something.
        $posts = $this->postRepository->findBy(
            ['status' => $public],
            ['id' => 'DESC'],
            2,
        );
        foreach ($posts as $post) {
            $menu->addChild($post->getTitle(), [
                'route' => 'nines_blog_post_show',
                'routeParameters' => [
                    'id' => $post->getId(),
                ],
            ]);
        }
        $this->addDivider($menu);

        $menu->addChild('All Announcements', [
            'route' => 'nines_blog_post_index',
            'linkAttributes' => [
                'class' => 'dropdown-item',
            ],
        ]);

        if ($this->hasRole('ROLE_BLOG_ADMIN')) {
            $this->addDivider($menu, 'divider2');

            $menu->addChild('Post Categories', [
                'route' => 'nines_blog_post_category_index',
                'linkAttributes' => [
                    'class' => 'dropdown-item',
                ],
            ]);
            $menu->addChild('Post Statuses', [
                'route' => 'nines_blog_post_status_index',
                'linkAttributes' => [
                    'class' => 'dropdown-item',
                ],
            ]);
        }

        return $menu->getParent();
    }

    /**
     * @param array<string,string> $options
     */
    public function pageMenu(array $options) : ItemInterface {
        $menu = $this->dropdown($options['title'] ?? 'About');

        // @TODO turn this into menuQuery().
        $pages = $this->pageRepository->findBy(
            ['public' => true, 'homepage' => false, 'inMenu' => true],
            ['weight' => 'ASC', 'title' => 'ASC'],
        );
        foreach ($pages as $page) {
            $menu->addChild($page->getTitle(), [
                'route' => 'nines_blog_page_show',
                'routeParameters' => [
                    'id' => $page->getId(),
                ],
                'linkAttributes' => [
                    'class' => 'dropdown-item',
                ],
            ]);
        }

        if ($this->hasRole('ROLE_BLOG_ADMIN')) {
            $this->addDivider($menu);
            $menu->addChild('All Pages', [
                'route' => 'nines_blog_page_index',
                'linkAttributes' => [
                    'class' => 'dropdown-item',
                ],
            ]);
        }

        return $menu->getParent();
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setPostStatusRepository(PostStatusRepository $postStatusRepository) : void {
        $this->postStatusRepository = $postStatusRepository;
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setPostRepository(PostRepository $postRepository) : void {
        $this->postRepository = $postRepository;
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setPageRepository(PageRepository $pageRepository) : void {
        $this->pageRepository = $pageRepository;
    }
}
