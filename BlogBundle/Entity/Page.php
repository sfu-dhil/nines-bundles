<?php

declare(strict_types=1);

namespace Nines\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\UserBundle\Entity\User;
use Nines\UtilBundle\Entity\AbstractEntity;
use Nines\UtilBundle\Entity\ContentEntityInterface;
use Nines\UtilBundle\Entity\ContentExcerptTrait;

#[ORM\Table(name: 'nines_blog_page')]
#[ORM\Index(name: 'blog_page_ft', columns: ['title', 'searchable'], flags: ['fulltext'])]
#[ORM\Entity(repositoryClass: 'Nines\BlogBundle\Repository\PageRepository')]
#[ORM\HasLifecycleCallbacks]
class Page extends AbstractEntity implements ContentEntityInterface {
    use ContentExcerptTrait;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private bool $inMenu = false;

    #[ORM\Column(name: 'weight', type: 'integer', nullable: false)]
    private int $weight = 0;

    #[ORM\Column(name: 'public', type: 'boolean', nullable: false)]
    private bool $public = false;

    #[ORM\Column(name: 'homepage', type: 'boolean', options: ['default' => 0])]
    private bool $homepage = false;

    #[ORM\Column(name: 'include_comments', type: 'boolean', nullable: false)]
    private bool $includeComments = false;

    /**
     * Blog post title.
     */
    #[ORM\Column(name: 'title', type: 'string', nullable: false)]
    private ?string $title = null;

    /**
     * Searchable version of the content, with the tags stripped out.
     */
    #[ORM\Column(name: 'searchable', type: 'text', nullable: false)]
    private ?string $searchable = null;

    /**
     * User that created the post.
     */
    #[ORM\ManyToOne(targetEntity: 'Nines\UserBundle\Entity\User')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    /**
     * Build a page.
     */
    public function __construct() {
        parent::__construct();
    }

    public function __toString() : string {
        return $this->title;
    }

    public function getInMenu() : ?bool {
        return $this->inMenu;
    }

    public function setInMenu(bool $inMenu) : self {
        $this->inMenu = $inMenu;

        return $this;
    }

    public function getWeight() : ?int {
        return $this->weight;
    }

    public function setWeight(int $weight) : self {
        $this->weight = $weight;

        return $this;
    }

    public function getPublic() : ?bool {
        return $this->public;
    }

    public function setPublic(bool $public) : self {
        $this->public = $public;

        return $this;
    }

    public function getHomepage() : ?bool {
        return $this->homepage;
    }

    public function setHomepage(bool $homepage) : self {
        $this->homepage = $homepage;

        return $this;
    }

    public function getIncludeComments() : ?bool {
        return $this->includeComments;
    }

    public function setIncludeComments(bool $includeComments) : self {
        $this->includeComments = $includeComments;

        return $this;
    }

    public function getTitle() : ?string {
        return $this->title;
    }

    public function setTitle(string $title) : self {
        $this->title = $title;

        return $this;
    }

    public function getSearchable() : ?string {
        return $this->searchable;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setSearchable() : self {
        if ($this->content) {
            $this->searchable = strip_tags($this->content);
        }

        return $this;
    }

    public function getUser() : ?User {
        return $this->user;
    }

    /**
     * @param ?User $user
     */
    public function setUser(?User $user) : self {
        $this->user = $user;

        return $this;
    }
}
