<?php

declare(strict_types=1);

namespace Nines\UserBundle\Exception;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

class AccountInactiveException extends AccountStatusException {
    public function getMessageKey() : string {
        return 'The user account is not active.';
    }
}
