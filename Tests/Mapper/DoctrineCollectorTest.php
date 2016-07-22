<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\EasyExtendsBundle\Tests\Mapper;

use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;

class DoctrineCollectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Sonata\EasyExtendsBundle\Mapper\DoctrineCollector::getIndexes
     * @covers \Sonata\EasyExtendsBundle\Mapper\DoctrineCollector::getUniques
     * @covers \Sonata\EasyExtendsBundle\Mapper\DoctrineCollector::getInheritanceTypes
     * @covers \Sonata\EasyExtendsBundle\Mapper\DoctrineCollector::getDiscriminatorColumns
     * @covers \Sonata\EasyExtendsBundle\Mapper\DoctrineCollector::getAssociations
     * @covers \Sonata\EasyExtendsBundle\Mapper\DoctrineCollector::getDiscriminators
     */
    public function testDefaultValues()
    {
        $collector = DoctrineCollector::getInstance();
        $this->assertEquals(array(), $collector->getIndexes());
        $this->assertEquals(array(), $collector->getUniques());
        $this->assertEquals(array(), $collector->getInheritanceTypes());
        $this->assertEquals(array(), $collector->getDiscriminatorColumns());
        $this->assertEquals(array(), $collector->getAssociations());
        $this->assertEquals(array(), $collector->getDiscriminators());
        $this->assertEquals(array(), $collector->getOverrides());
    }
}
