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

namespace Sonata\EasyExtendsBundle\Tests\Bundle;

use PHPUnit\Framework\TestCase;
use Sonata\EasyExtendsBundle\Bundle\BundleMetadata;
use Sonata\EasyExtendsBundle\Bundle\OrmMetadata;

class OrmMetadataTest extends TestCase
{
    public function testEntityNames()
    {
        $ormMetadata = new OrmMetadata($this->getBundleMetadataMock(__DIR__.'/Fixtures/bundle1'));

        $entityNames = $ormMetadata->getEntityNames();

        $this->assertCount(4, $entityNames);
        $this->assertContains('Block', $entityNames);
        $this->assertContains('Page', $entityNames);
    }

    public function testDirectoryWithDotInPath()
    {
        $ormMetadata = new OrmMetadata($this->getBundleMetadataMock(__DIR__.'/Fixtures/bundle2/dot.dot'));

        $entityNames = $ormMetadata->getEntityNames();

        $this->assertCount(4, $entityNames);
        $this->assertContains('Block', $entityNames);
        $this->assertContains('Page', $entityNames);
    }

    public function testGetMappingEntityDirectory()
    {
        $bundlePath = __DIR__.'/Fixtures/bundle1';
        $expectedDirectory = $bundlePath.'/Resources/config/doctrine/';

        $ormMetadata = new OrmMetadata($this->getBundleMetadataMock($bundlePath));

        $directory = $ormMetadata->getMappingEntityDirectory();

        $this->assertSame($expectedDirectory, $directory);
    }

    public function testGetExtendedMappingEntityDirectory()
    {
        $bundlePath = __DIR__.'/Fixtures/bundle1';
        $expectedDirectory = 'Application/Sonata/AcmeBundle/Resources/config/doctrine/';

        $ormMetadata = new OrmMetadata($this->getBundleMetadataMock($bundlePath));

        $directory = $ormMetadata->getExtendedMappingEntityDirectory();

        $this->assertSame($expectedDirectory, $directory);
    }

    public function testGetEntityDirectory()
    {
        $bundlePath = __DIR__.'/Fixtures/bundle1';
        $expectedDirectory = $bundlePath.'/Entity';

        $ormMetadata = new OrmMetadata($this->getBundleMetadataMock($bundlePath));

        $directory = $ormMetadata->getEntityDirectory();

        $this->assertSame($expectedDirectory, $directory);
    }

    public function testGetExtendedEntityDirectory()
    {
        $bundlePath = __DIR__.'/Fixtures/bundle1';
        $expectedDirectory = 'Application/Sonata/AcmeBundle/Entity';

        $ormMetadata = new OrmMetadata($this->getBundleMetadataMock($bundlePath));

        $directory = $ormMetadata->getExtendedEntityDirectory();

        $this->assertSame($expectedDirectory, $directory);
    }

    public function testGetExtendedSerializerDirectory()
    {
        $bundlePath = __DIR__.'/Fixtures/bundle1';
        $expectedDirectory = 'Application/Sonata/AcmeBundle/Resources/config/serializer';

        $ormMetadata = new OrmMetadata($this->getBundleMetadataMock($bundlePath));

        $directory = $ormMetadata->getExtendedSerializerDirectory();

        $this->assertSame($expectedDirectory, $directory);
    }

    public function testGetEntityMappingFiles()
    {
        $ormMetadata = new OrmMetadata($this->getBundleMetadataMock(__DIR__.'/Fixtures/bundle1'));

        $filterIterator = $ormMetadata->getEntityMappingFiles();

        $files = [];
        foreach ($filterIterator as $file) {
            $files[] = $file->getFilename();
        }

        $this->assertInstanceOf('Iterator', $filterIterator);
        $this->assertContainsOnly('Symfony\Component\Finder\SplFileInfo', $filterIterator);
        $this->assertContains('Block.orm.xml.skeleton', $files);
        $this->assertContains('Page.orm.xml.skeleton', $files);
        $this->assertNotContains('Block.mongodb.xml.skeleton', $files);
        $this->assertNotContains('Page.mongodb.xml.skeleton', $files);
    }

    public function testGetEntityMappingFilesWithFilesNotFound()
    {
        $ormMetadata = new OrmMetadata($this->getBundleMetadataMock(__DIR__.'/Fixtures'));

        $result = $ormMetadata->getEntityMappingFiles();

        $this->assertInternalType('array', $result);
        $this->assertEmpty($result);
    }

    public function testGetRepositoryFiles()
    {
        $ormMetadata = new OrmMetadata($this->getBundleMetadataMock(__DIR__.'/Fixtures/bundle1'));

        $filterIterator = $ormMetadata->getRepositoryFiles();

        $files = [];
        foreach ($filterIterator as $file) {
            $files[] = $file->getFilename();
        }

        $this->assertInstanceOf('Iterator', $filterIterator);
        $this->assertContainsOnly('Symfony\Component\Finder\SplFileInfo', $filterIterator);
        $this->assertContains('BlockRepository.php', $files);
        $this->assertContains('PageRepository.php', $files);
        $this->assertNotContains('Block.php', $files);
        $this->assertNotContains('Page.php', $files);
    }

    public function testGetRepositoryFilesWithFilesNotFound()
    {
        $ormMetadata = new OrmMetadata($this->getBundleMetadataMock(__DIR__.'/Fixtures'));

        $result = $ormMetadata->getRepositoryFiles();

        $this->assertInternalType('array', $result);
        $this->assertEmpty($result);
    }

    /**
     * @param string $bundlePath
     *
     * @return BundleMetadata
     */
    private function getBundleMetadataMock($bundlePath)
    {
        $bundle = $this->createMock('Symfony\Component\HttpKernel\Bundle\Bundle');
        $bundle->expects($this->any())
            ->method('getPath')
            ->willReturn($bundlePath);

        $bundleMetadata = $this->createMock(
            'Sonata\EasyExtendsBundle\Bundle\BundleMetadata',
            [],
            [$bundle],
            '',
            true
        );
        $bundleMetadata->expects($this->any())
            ->method('getBundle')
            ->willReturn($bundle);
        $bundleMetadata->expects($this->any())
            ->method('getClass')
            ->willReturn('Sonata\\AcmeBundle\\SonataAcmeBundle');
        $bundleMetadata->expects($this->any())
            ->method('getExtendedDirectory')
            ->willReturn('Application/Sonata/AcmeBundle');

        return $bundleMetadata;
    }
}
