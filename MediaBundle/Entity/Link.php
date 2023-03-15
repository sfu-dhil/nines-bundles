<?php

declare(strict_types=1);

namespace Nines\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\MediaBundle\Repository\LinkRepository;
use Nines\UtilBundle\Entity\AbstractEntity;
use Nines\UtilBundle\Entity\LinkedEntityInterface;
use Nines\UtilBundle\Entity\LinkedEntityTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'nines_media_link')]
#[ORM\Index(name: 'nines_media_link_ft', columns: ['url', 'text'], flags: ['fulltext'])]
#[ORM\Index(columns: ['entity'])]
#[ORM\Entity(repositoryClass: LinkRepository::class)]
class Link extends AbstractEntity implements LinkedEntityInterface {
    use LinkedEntityTrait;

    #[Assert\Url]
    #[ORM\Column(type: 'string', length: 500, nullable: false)]
    private ?string $url = null;

    #[ORM\Column(type: 'string', length: 191, nullable: true)]
    private ?string $text = null;

    public function __construct() {
        parent::__construct();
    }

    public function __toString() : string {
        if ($this->text) {
            return "<a href='{$this->url}'>{$this->text}</a>";
        }
        $host = parse_url($this->url, PHP_URL_HOST);

        return "<a href='{$this->url}'>{$host}</a>";
    }

    public function getUrl() : ?string {
        return $this->url;
    }

    public function setUrl(string $url) : self {
        $this->url = $url;

        return $this;
    }

    public function getText() : ?string {
        return $this->text;
    }

    public function setText(?string $text) : self {
        $this->text = null === $text ? null : preg_replace('/(^\p{Z})|(\p{Z}$)/u', '', $text);

        return $this;
    }

    public function getScheme() : string {
        return parse_url($this->url, PHP_URL_SCHEME);
    }

    public function getHost() : string {
        return parse_url($this->url, PHP_URL_HOST);
    }

    public function getPort() : int {
        return parse_url($this->url, PHP_URL_PORT);
    }

    public function getUser() : string {
        return parse_url($this->url, PHP_URL_USER);
    }

    public function getPass() : string {
        return parse_url($this->url, PHP_URL_PASS);
    }

    public function getPath() : string {
        return parse_url($this->url, PHP_URL_PATH);
    }

    public function getQuery() : string {
        return parse_url($this->url, PHP_URL_QUERY);
    }

    public function getFragment() : string {
        return parse_url($this->url, PHP_URL_FRAGMENT);
    }
}
