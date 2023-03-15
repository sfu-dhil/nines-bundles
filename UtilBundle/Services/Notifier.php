<?php

declare(strict_types=1);

namespace Nines\UtilBundle\Services;

use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class Notifier {
    private ?string $sender = null;

    private ?MailerInterface $mailer = null;

    private ?LoggerInterface $logger = null;

    private ?EntityLinker $linker = null;

    public function __construct(string $sender) {
        $this->sender = $sender;
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setMailer(MailerInterface $mailer) : void {
        $this->mailer = $mailer;
    }

    /**
     * @param array<string>|string $recipients
     * @param array<string,mixed> $context
     *
     * @throws TransportExceptionInterface
     *
     * @return array<TemplatedEmail>
     */
    public function notify($recipients, string $subject, string $template, array $context) : array {
        $this->logger->warning('Sending comment notifications');
        if ( ! is_array($recipients)) {
            $recipients = [$recipients];
        }
        $sent = [];
        $ctx = array_merge(['linker' => $this->linker], $context);
        foreach ($recipients as $recipient) {
            $this->logger->warning('Notification to ' . $recipient);
            $email = new TemplatedEmail();
            $email->from($this->sender);
            $email->to($recipient);
            $email->subject($subject);
            $email->htmlTemplate($template);
            $email->context($ctx);
            $this->mailer->send($email);
            $sent[] = $email;
        }

        return $sent;
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setLogger(LoggerInterface $logger) : void {
        $this->logger = $logger;
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setLinker(EntityLinker $linker) : void {
        $this->linker = $linker;
    }
}
