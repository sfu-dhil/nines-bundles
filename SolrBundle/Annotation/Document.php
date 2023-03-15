<?php

declare(strict_types=1);

namespace Nines\SolrBundle\Annotation;

/**
 * Apply this annotation to doctrine entities that should be indexed.
 *
 * @Annotation
 *
 * @Target({"CLASS"})
 */
class Document {
    /**
     * Copy data between fields.
     *
     * @var array<\Nines\SolrBundle\Annotation\CopyField>
     */
    public array $copyField = [];

    /**
     * Indexed (or virtual) fields may be computed from other fields during
     * indexing.
     *
     * @var array<\Nines\SolrBundle\Annotation\ComputedField>
     */
    public array $computedFields = [];
}
