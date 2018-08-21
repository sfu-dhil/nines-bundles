<?php

namespace Nines\FeedbackBundle\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Nines\FeedbackBundle\Entity\CommentStatus;
use Nines\UserBundle\Entity\User;

/**
 * Load some users for unit tests.
 */
class LoadCommentStatus extends Fixture {

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $em) {

        $unpublished = new CommentStatus();
        $unpublished->setName('unpublished');
        $unpublished->setLabel('Unpublished');
        $unpublished->setDescription('Comment has not been approved for publication.');
        $em->persist($unpublished);

        $published = new CommentStatus();
        $published->setName('published');
        $published->setLabel('Published');
        $published->setDescription('Comment has been approved for publication.');
        $em->persist($published);

        $em->flush();
    }
}
