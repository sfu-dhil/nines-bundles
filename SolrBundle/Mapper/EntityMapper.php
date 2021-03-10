<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace Nines\SolrBundle\Mapper;

use Doctrine\Common\Util\ClassUtils;
use ReflectionClass;
use ReflectionMethod;

class EntityMapper {
    /**
     * Maps doctrine entity to solr fields.
     *
     * @var array
     */
    private $entityMap;

    /**
     * @var array
     */
    private $copyFields;

    public function __construct() {
        $this->copyFields = [];
    }

    public function getMap() {
        return $this->entityMap;
    }

    public function addClass($class) : void {
        $this->entityMap[$class] = [
            '_fixed' => [],
        ];
    }

    public function addId($class, $name, $options) : void {
        $this->entityMap[$class]['_id'] = [
            'field' => $name,
            'mutator' => null,
            'getter' => $options['getter'],
        ];
    }

    public function addFixed($class, $name, $value) : void {
        $this->entityMap[$class]['_fixed'][$name] = $value;
    }

    public function getMappedClasses() {
        return array_keys($this->entityMap);
    }

    public function getMethodDefinition($string) {
        if ( ! $string) {
            return null;
        }

        $definition = [
            'method' => $string,
            'args' => null,
        ];

        if (false !== ($n = mb_strpos($string, '('))) {
            $definition['method'] = mb_substr($string, 0, $n);
            $args = explode(',', mb_substr($string, $n + 1, -1));
            $definition['args'] = array_map(function ($s) {
                return trim($s, " \t\n\r\0\x0B'\"");
            }, $args);
        }

        return $definition;
    }

    public function addField($class, $name, $solr, $options) : void {
        $this->entityMap[$class][$name] = [
            'field' => $solr,
            'mutator' => $this->getMethodDefinition($options['mutator']),
            'getter' => $this->getMethodDefinition($options['getter']),
        ];
    }

    public function invoke($obj, $method, $args) {
        if ( ! $obj) {
            return null;
        }
        $call = function($object) use ($method, $args) {
            if ($args && count($args) > 0) {
                $ref = new ReflectionMethod($object, $method);

                return $ref->invokeArgs($object, $args);
            }

            return $object->{$method}();
        };

        if(is_array($obj)) {
            return array_map($call, $obj);
        }
        return $call($obj);
    }

    public function mapEntity($entity) {
        if ( ! $entity) {
            return null;
        }
        $class = ClassUtils::getClass($entity);
        if ( ! isset($this->entityMap[$class])) {
            return null;
        }

        $idGetter = $this->entityMap[$class]['_id']['getter'];
        $map = [
            'id' => $class . ':' . $entity->{$idGetter}(),
            'class_s' => $class,
        ];
        foreach($this->entityMap[$class]['_fixed'] as $k => $v) {
            $map[$k] = $v;
        }

        foreach ($this->entityMap[$class] as $k => $v) {
            if($k === '_fixed') {
                foreach($v as $name => $value) {
                    $map[$name] = $value;
                }
                continue;
            }
            if (in_array($v['field'], ['id', 'class_s'], true)) {
                continue;
            }
            $data = $this->invoke($entity, $v['getter']['method'], $v['getter']['args']);
            if ($v['mutator']) {
                $data = $this->invoke($data, $v['mutator']['method'], $v['mutator']['args']);
            }
            $map[$v['field']] = $data;
        }

        return $map;
    }

    public function getCopyFields() : array {
        return $this->copyFields;
    }

    /**
     * @return EntityMapper
     */
    public function setCopyFields(array $copyFields) : self {
        $this->copyFields = $copyFields;

        return $this;
    }
}
