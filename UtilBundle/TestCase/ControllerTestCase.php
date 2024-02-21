<?php

declare(strict_types=1);

namespace Nines\UtilBundle\TestCase;

use DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver;
use Doctrine\ORM\EntityManagerInterface;
use Nines\UserBundle\Entity\User;
use Nines\UserBundle\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Field\FormField;
use Symfony\Component\DomCrawler\Form;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

abstract class ControllerTestCase extends WebTestCase {
    protected ?KernelBrowser $client = null;

    protected ?EntityManagerInterface $em = null;

    /**
     * @param ?array<string,string> $credentials
     */
    protected function login(?array $credentials = null) : void {
        $this->client->restart();
        if ($credentials) {
            $session = static::getContainer()->get('session.factory')->createSession();

            /** @var UserRepository $repository */
            $repository = $this->em->getRepository(User::class);
            $user = $repository->findOneByEmail($credentials['username']);
            $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
            $session->set('_security_main', serialize($token));
            $session->save();
            $cookie = new Cookie($session->getName(), $session->getId());
            $this->client->getCookieJar()->set($cookie);
            static::getContainer()->get('security.token_storage')->setToken($token);
        }
    }

    protected function addField(Crawler $crawler, string $formName, string $name, string $value = '', string $type = 'text') : void {
        $doc = $crawler->getNode(0)->ownerDocument;
        $input = $doc->createElement('input');
        $input->setAttribute('name', $name);
        $input->setAttribute('type', $type);
        $input->setAttribute('value', $value);
        $formNode = $crawler->filter("form[name='{$formName}']")->getNode(0);
        $formNode->appendChild($input);
    }

    /**
     * @param Form|FormField $form
     */
    protected function overrideField($form, string $fieldName, null|array|bool|string $value) : void {
        $form[$fieldName]->disableValidation()->setValue($value);
    }

    protected function reset() : void {
        StaticDriver::rollBack();
        StaticDriver::beginTransaction();
    }

    protected function commit() : void {
        StaticDriver::commit();
    }

    protected function setUp() : void {
        $this->client = static::createClient();
        $this->em = static::getContainer()->get(EntityManagerInterface::class);
    }
}
