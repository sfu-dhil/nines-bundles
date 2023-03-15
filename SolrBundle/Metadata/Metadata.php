<?php

declare(strict_types=1);

namespace Nines\SolrBundle\Metadata;

/**
 * Parent class for metadata.
 */
abstract class Metadata {
    /**
     * Parse a function call to get the function's name and arguments. Returns
     * an array of [name, arguments[]].
     *
     * @return array<int, mixed>
     */
    public function parseFunctionCall(string $string) : array {
        $name = $string;
        $args = [];

        if (false !== ($n = mb_strpos($string, '('))) {
            $name = mb_substr($string, 0, $n);
            $args = explode(',', mb_substr($string, $n + 1, -1));
            $args = array_map(fn ($s) => preg_replace("/^(?:[[:space:]'\"]*)|(?:[[:space:]'\"]*)$/u", '', $s), $args);
        }

        return [$name, $args];
    }
}
