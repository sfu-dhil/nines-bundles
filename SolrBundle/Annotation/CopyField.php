<?php

declare(strict_types=1);

namespace Nines\SolrBundle\Annotation;

use Doctrine\ORM\Mapping\Annotation;

/**
 * Copy data from one or more fields into a destination field.
 * Eg. @Solr\CopyField(from={"one", "two", "three"}, to="destination", type="texts").
 *
 * @Annotation
 *
 * @Target("ANNOTATION")
 */
class CopyField {
    /**
     * Source fields.
     *
     * @var array<string>
     */
    public array $from = [];

    /**
     * Destination fields.
     */
    public ?string $to = null;

    /**
     * The tpe of the destination field.
     */
    public ?string $type = null;
}
