<?php

declare(strict_types=1);

namespace Nines\UtilBundle\Logger;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestProcessor implements ProcessorInterface {
    public function __construct(
        protected RequestStack $requestStack,
    ) {
    }

    public function __invoke(LogRecord $record) : LogRecord {
        $request = $this->requestStack->getCurrentRequest();
        if ( ! $request) {
            return $record;
        }
        $record->extra['ip'] = $request->getClientIp();
        $record->extra['url'] = $request->getUri();

        return $record;
    }
}
