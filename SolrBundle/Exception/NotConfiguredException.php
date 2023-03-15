<?php

declare(strict_types=1);

namespace Nines\SolrBundle\Exception;

use Throwable;

class NotConfiguredException extends SolrException {
    public const CODE = 1;

    public const MESSAGE = 'No configured client for this environment.';

    public function __construct(?string $message = self::MESSAGE, ?int $code = self::CODE, ?Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
