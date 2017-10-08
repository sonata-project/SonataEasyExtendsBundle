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

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Sonata\EasyExtendsBundle\Mapper\DoctrineORMMapper;

class DoctrineORMMapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * @var ClassMetadataInfo
     */
    private $metadata;

    public function setUp()
    {
        $this->doctrine = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry', [], [], '', false);
        $this->metadata = $this->getMock('Doctrine\ORM\Mapping\ClassMetadataInfo', [], [], '', false);
    }

    public function testLoadDiscriminators()
    {
        $this->metadata
            ->expects($this->atLeastOnce())
            ->method('setDiscriminatorMap')
            ->with(['key' => 'discriminator']);

        $this->metadata->name = 'class';
        $mapper = new DoctrineORMMapper($this->doctrine);
        $mapper->addDiscriminator('class', 'key', 'discriminator');

        $r = new \ReflectionObject($mapper);
        $m = $r->getMethod('loadDiscriminators');
        $m->setAccessible(true);
        $m->invoke($mapper, $this->metadata);
    }

    public function testLoadDiscriminatorColumns()
    {
        $this->metadata
            ->expects($this->atLeastOnce())
            ->method('setDiscriminatorColumn')
            ->with(['name' => 'disc']);

        $this->metadata->name = 'class';
        $mapper = new DoctrineORMMapper($this->doctrine);
        $mapper->addDiscriminatorColumn('class', ['name' => 'disc']);

        $r = new \ReflectionObject($mapper);
        $m = $r->getMethod('loadDiscriminatorColumns');
        $m->setAccessible(true);
        $m->invoke($mapper, $this->metadata);
    }

    public function testInheritanceTypes()
    {
        $this->metadata
            ->expects($this->atLeastOnce())
            ->method('setInheritanceType')
            ->with(1);

        $this->metadata->name = 'class';
        $mapper = new DoctrineORMMapper($this->doctrine);
        $mapper->addInheritanceType('class', 1);

        $r = new \ReflectionObject($mapper);
        $m = $r->getMethod('loadInheritanceTypes');
        $m->setAccessible(true);
        $m->invoke($mapper, $this->metadata);
    }
}
