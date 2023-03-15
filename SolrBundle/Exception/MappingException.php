<?php

declare(strict_types=1);

namespace Nines\SolrBundle\Exception;

use Throwable;

class MappingException extends SolrException {
    public const CODE = 2;

    public const MESSAGE = 'The index mapping is misconfigured.';

    public function __construct(?string $message = self::MESSAGE, ?int $code = self::CODE, ?Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
