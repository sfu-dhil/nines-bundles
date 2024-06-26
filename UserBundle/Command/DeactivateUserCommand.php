<?php

declare(strict_types=1);

namespace Nines\UserBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Nines\UserBundle\Entity\User;
use Nines\UserBundle\Services\UserManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(name: 'nines:user:deactivate')]
class DeactivateUserCommand extends AbstractUserCommand {
    private ?UserManager $manager = null;

    public function __construct(UserManager $manager, ValidatorInterface $validator, EntityManagerInterface $em) {
        parent::__construct($validator, $em);
        $this->manager = $manager;
    }

    /**
     * @return array<int,mixed>
     */
    protected function getArgs() : array {
        return [
            ['name' => 'email', 'desc' => 'Email address for the new user account', 'question' => 'Email address: ', 'valid' => [new NotBlank(), new Email()]],
        ];
    }

    protected function configure() : void {
        $this->setDescription('Disable a user account');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int {
        $email = $input->getArgument('email');

        /** @var ?User $user */
        $user = $this->manager->find($email);
        if ( ! $user) {
            $output->writeln("Cannot find user {$email}.");

            return 1;
        }

        $user->setActive(false);
        $this->em->flush();

        $output->writeln("Account {$user->getEmail()} is not active.");

        return 0;
    }
}
