<?php

declare(strict_types=1);

namespace Nines\UserBundle\Command;

use Nines\UserBundle\Entity\User;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

#[AsCommand(name: 'nines:user:activate')]
class ActivateUserCommand extends AbstractUserCommand {
    /**
     * @return array<int,mixed>
     */
    protected function getArgs() : array {
        return [
            ['name' => 'email', 'desc' => 'Email address for the account', 'question' => 'Email address: ', 'valid' => [new NotBlank(), new Email()]],
        ];
    }

    protected function configure() : void {
        $this->setDescription('Enable a user account');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int {
        $email = $input->getArgument('email');

        /** @var ?User $user */
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

        if ( ! $user) {
            $output->writeln("Cannot find user {$email}.");

            return 1;
        }

        $user->setActive(true);
        $this->em->flush();

        $output->writeln("Account {$user->getEmail()} is active.");

        return 0;
    }
}
