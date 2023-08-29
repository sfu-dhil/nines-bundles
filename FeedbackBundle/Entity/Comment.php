<?php

declare(strict_types=1);

namespace Nines\FeedbackBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints as Recaptcha;
use Nines\UtilBundle\Entity\AbstractEntity;
use Nines\UtilBundle\Entity\LinkedEntityInterface;
use Nines\UtilBundle\Entity\LinkedEntityTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'nines_feedback_comment')]
#[ORM\Index(name: 'comment_ft', columns: ['fullname', 'content'], flags: ['fulltext'])]
#[ORM\Entity(repositoryClass: 'Nines\FeedbackBundle\Repository\CommentRepository')]
class Comment extends AbstractEntity implements LinkedEntityInterface {
    use LinkedEntityTrait;

    #[Recaptcha\IsTrue]
    public $recaptcha;

    #[ORM\Column(type: 'string', length: 120)]
    private ?string $fullname = null;

    #[ORM\Column(type: 'string', length: 120)]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(type: 'boolean')]
    private bool $followUp = false;

    #[ORM\Column(type: 'text')]
    private ?string $content = null;

    #[ORM\ManyToOne(targetEntity: 'CommentStatus', inversedBy: 'comments')]
    #[ORM\JoinColumn(name: 'status_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?CommentStatus $status = null;

    /**
     * @var Collection<int,CommentNote>|CommentNote[]
     */
    #[ORM\OneToMany(targetEntity: 'CommentNote', mappedBy: 'comment', orphanRemoval: true)]
    private $notes;

    public function __construct() {
        parent::__construct();
        $this->notes = new ArrayCollection();
    }

    public function __toString() : string {
        return $this->content;
    }

    public function getFullname() : ?string {
        return $this->fullname;
    }

    public function setFullname(string $fullname) : self {
        $this->fullname = $fullname;

        return $this;
    }

    public function getEmail() : ?string {
        return $this->email;
    }

    public function setEmail(string $email) : self {
        $this->email = $email;

        return $this;
    }

    public function getFollowUp() : ?bool {
        return $this->followUp;
    }

    public function setFollowUp(bool $followUp) : self {
        $this->followUp = $followUp;

        return $this;
    }

    public function getContent() : ?string {
        return $this->content;
    }

    public function setContent(string $content) : self {
        $this->content = $content;

        return $this;
    }

    public function getStatus() : ?CommentStatus {
        return $this->status;
    }

    public function setStatus(?CommentStatus $status) : self {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int,CommentNote>|CommentNote[]
     */
    public function getNotes() : Collection {
        return $this->notes;
    }

    public function addNote(CommentNote $note) : self {
        if ( ! $this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->setComment($this);
        }

        return $this;
    }

    public function removeNote(CommentNote $note) : self {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getComment() === $this) {
                $note->setComment(null);
            }
        }

        return $this;
    }
}
