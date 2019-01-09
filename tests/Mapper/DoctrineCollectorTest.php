<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\EasyExtendsBundle\Tests\Mapper;

use PHPUnit\Framework\TestCase;
use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;

class DoctrineCollectorTest extends TestCase
{
    /**
     * @covers \Sonata\EasyExtendsBundle\Mapper\DoctrineCollector::getIndexes
     * @covers \Sonata\EasyExtendsBundle\Mapper\DoctrineCollector::getUniques
     * @covers \Sonata\EasyExtendsBundle\Mapper\DoctrineCollector::getInheritanceTypes
     * @covers \Sonata\EasyExtendsBundle\Mapper\DoctrineCollector::getDiscriminatorColumns
     * @covers \Sonata\EasyExtendsBundle\Mapper\DoctrineCollector::getAssociations
     * @covers \Sonata\EasyExtendsBundle\Mapper\DoctrineCollector::getDiscriminators
     */
    public function testDefaultValues(): void
    {
        $collector = DoctrineCollector::getInstance();
        $this->assertEquals([], $collector->getIndexes());
        $this->assertEquals([], $collector->getUniques());
        $this->assertEquals([], $collector->getInheritanceTypes());
        $this->assertEquals([], $collector->getDiscriminatorColumns());
        $this->assertEquals([], $collector->getAssociations());
        $this->assertEquals([], $collector->getDiscriminators());
        $this->assertEquals([], $collector->getOverrides());
    }

    public function testClear(): void
    {
        $collector = DoctrineCollector::getInstance();
        $collector->addIndex(\stdClass::class, 'name', ['column']);
        $collector->addUnique(\stdClass::class, 'name', ['column']);
        $collector->addInheritanceType(\stdClass::class, 'type');
        $collector->addDiscriminatorColumn(\stdClass::class, ['columnDef']);
        $collector->addAssociation(\stdClass::class, 'type', ['options']);
        $collector->addDiscriminator(\stdClass::class, 'key', 'discriminatorClass');
        $collector->addOverride(\stdClass::class, 'type', ['options']);

        $collector->clear();

        $this->assertEquals([], $collector->getIndexes());
        $this->assertEquals([], $collector->getUniques());
        $this->assertEquals([], $collector->getInheritanceTypes());
        $this->assertEquals([], $collector->getDiscriminatorColumns());
        $this->assertEquals([], $collector->getAssociations());
        $this->assertEquals([], $collector->getDiscriminators());
        $this->assertEquals([], $collector->getOverrides());
    }
}
