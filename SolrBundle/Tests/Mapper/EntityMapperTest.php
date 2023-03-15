<?php

declare(strict_types=1);

namespace Nines\SolrBundle\Tests\Mapper;

use DateTimeImmutable;
use DateTimeInterface;
use Nines\SolrBundle\Mapper\EntityMapper;
use Nines\SolrBundle\Metadata\EntityMetadata;
use Nines\SolrBundle\Metadata\FieldMetadata;
use Nines\SolrBundle\Metadata\IdMetadata;
use Nines\UtilBundle\Entity\AbstractEntity;
use Nines\UtilBundle\TestCase\ServiceTestCase;

class EntityMapperTest extends ServiceTestCase {
    private ?object $entity = null;

    public function testId() : void {
        $meta = new EntityMetadata();
        $meta->setClass(get_class($this->entity));
        $id = new IdMetadata();
        $id->setName('id');
        $id->setGetter('getId');
        $meta->setId($id);

        $em = new EntityMapper();
        $em->addEntity($meta);

        $document = $em->toDocument($this->entity);
        $fields = $document->getFields();
        $this->assertNotNull($fields['id']);
        $this->assertStringEndsWith(':123', $fields['id']);
    }

    public function testFixed() : void {
        $meta = new EntityMetadata();
        $meta->setClass(get_class($this->entity));
        $id = new IdMetadata();
        $id->setName('id');
        $id->setGetter('getId');
        $meta->setId($id);
        $meta->addFixed('fixed', 'fixed data');

        $em = new EntityMapper();
        $em->addEntity($meta);

        $document = $em->toDocument($this->entity);
        $fields = $document->getFields();
        $this->assertNotNull($fields['fixed']);
        $this->assertSame('fixed data', $fields['fixed']);
    }

    public function testField() : void {
        $meta = new EntityMetadata();
        $meta->setClass(get_class($this->entity));
        $id = new IdMetadata();
        $id->setName('id');
        $id->setGetter('getId');
        $meta->setId($id);

        $field = new FieldMetadata();
        $field->setSolrName('solr_field');
        $field->setFieldName('field');
        $field->setGetter('exec');
        $meta->addFieldMetadata($field);

        $em = new EntityMapper();
        $em->addEntity($meta);

        $document = $em->toDocument($this->entity);
        $fields = $document->getFields();
        $this->assertNotNull($fields['solr_field']);
        $this->assertSame('called', $fields['solr_field']);
    }

    public function testIdentify() : void {
        $meta = new EntityMetadata();
        $meta->setClass(get_class($this->entity));
        $id = new IdMetadata();
        $id->setName('id');
        $id->setGetter('getId');
        $meta->setId($id);

        $em = new EntityMapper();
        $em->addEntity($meta);

        $id = $em->identify($this->entity);
        $this->assertNotNull($id);
        $this->assertStringEndsWith(':123', $id);
    }

    protected function setUp() : void {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->entity = new class() extends AbstractEntity {
            public function __construct() {
                parent::__construct();
                $this->id = 123;
            }

            public function __toString() : string {
                return 'string';
            }

            public function exec() : string {
                return 'called';
            }

            public function execArgs(string $a, string $b) : string {
                return 'called ' . $a . ' ' . $b;
            }

            public function dt() : DateTimeInterface {
                return new DateTimeImmutable('2021-02-01 12:34:56');
            }

            public function data() : string {
                return '<a>abc</a>';
            }

            public function execNull() : ?string {
                return null;
            }
        };
    }
}
