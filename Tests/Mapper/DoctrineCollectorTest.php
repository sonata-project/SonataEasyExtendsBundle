<?php
namespace Sonata\EasyExtendsBundle\Tests\Mapper;

use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class DoctrineCollectorTest extends \PHPUnit_Framework_TestCase
{
     /**
     * @covers DoctrineCollector::getIndexes
     * @covers DoctrineCollector::getInheritanceTypes
     * @covers DoctrineCollector::getDiscriminatorColumns
     * @covers DoctrineCollector::getAssociations
     * @covers DoctrineCollector::getDiscriminators
     */
    public function testDefaultValues()
    {
       $collector = DoctrineCollector::getInstance();
       $this->assertEquals(array(), $collector->getIndexes());
       $this->assertEquals(array(), $collector->getInheritanceTypes());
       $this->assertEquals(array(), $collector->getDiscriminatorColumns());
       $this->assertEquals(array(), $collector->getAssociations());
       $this->assertEquals(array(), $collector->getDiscriminators());
    }
}