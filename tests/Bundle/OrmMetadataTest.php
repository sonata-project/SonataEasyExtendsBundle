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
use Sonata\AcmeBundle\SonataAcmeBundle;
use Sonata\EasyExtendsBundle\Bundle\BundleMetadata;
use Sonata\EasyExtendsBundle\Bundle\OrmMetadata;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class OrmMetadataTest extends TestCase
{
    public function testEntityNames(): void
    {
        $ormMetadata = new OrmMetadata($this->getBundleMetadataMock(__DIR__.'/Fixtures/bundle1'));

        $entityNames = $ormMetadata->getEntityNames();

        $this->assertCount(4, $entityNames);
        $this->assertContains('Block', $entityNames);
        $this->assertContains('Page', $entityNames);
    }

    public function testDirectoryWithDotInPath(): void
    {
        $ormMetadata = new OrmMetadata($this->getBundleMetadataMock(__DIR__.'/Fixtures/bundle2/dot.dot'));

        $entityNames = $ormMetadata->getEntityNames();

        $this->assertCount(4, $entityNames);
        $this->assertContains('Block', $entityNames);
        $this->assertContains('Page', $entityNames);
    }

    public function testGetMappingEntityDirectory(): void
    {
        $bundlePath = __DIR__.'/Fixtures/bundle1';
        $expectedDirectory = $bundlePath.'/Resources/config/doctrine/';

        $ormMetadata = new OrmMetadata($this->getBundleMetadataMock($bundlePath));

        $directory = $ormMetadata->getMappingEntityDirectory();

        $this->assertSame($expectedDirectory, $directory);
    }

    public function testGetExtendedMappingEntityDirectory(): void
    {
        $bundlePath = __DIR__.'/Fixtures/bundle1';
        $expectedDirectory = 'Application/Sonata/AcmeBundle/Resources/config/doctrine/';

        $ormMetadata = new OrmMetadata($this->getBundleMetadataMock($bundlePath));

        $directory = $ormMetadata->getExtendedMappingEntityDirectory();

        $this->assertSame($expectedDirectory, $directory);
    }

    public function testGetEntityDirectory(): void
    {
        $bundlePath = __DIR__.'/Fixtures/bundle1';
        $expectedDirectory = $bundlePath.'/Entity';

        $ormMetadata = new OrmMetadata($this->getBundleMetadataMock($bundlePath));

        $directory = $ormMetadata->getEntityDirectory();

        $this->assertSame($expectedDirectory, $directory);
    }

    public function testGetExtendedEntityDirectory(): void
    {
        $bundlePath = __DIR__.'/Fixtures/bundle1';
        $expectedDirectory = 'Application/Sonata/AcmeBundle/Entity';

        $ormMetadata = new OrmMetadata($this->getBundleMetadataMock($bundlePath));

        $directory = $ormMetadata->getExtendedEntityDirectory();

        $this->assertSame($expectedDirectory, $directory);
    }

    public function testGetExtendedSerializerDirectory(): void
    {
        $bundlePath = __DIR__.'/Fixtures/bundle1';
        $expectedDirectory = 'Application/Sonata/AcmeBundle/Resources/config/serializer';

        $ormMetadata = new OrmMetadata($this->getBundleMetadataMock($bundlePath));

        $directory = $ormMetadata->getExtendedSerializerDirectory();

        $this->assertSame($expectedDirectory, $directory);
    }

    public function testGetEntityMappingFiles(): void
    {
        $ormMetadata = new OrmMetadata($this->getBundleMetadataMock(__DIR__.'/Fixtures/bundle1'));

        $filterIterator = $ormMetadata->getEntityMappingFiles();

        $files = [];
        foreach ($filterIterator as $file) {
            $files[] = $file->getFilename();
        }

        $this->assertInstanceOf('Iterator', $filterIterator);
        $this->assertContainsOnly(SplFileInfo::class, $filterIterator);
        $this->assertContains('Block.orm.xml.skeleton', $files);
        $this->assertContains('Page.orm.xml.skeleton', $files);
        $this->assertNotContains('Block.mongodb.xml.skeleton', $files);
        $this->assertNotContains('Page.mongodb.xml.skeleton', $files);
    }

    public function testGetEntityMappingFilesWithFilesNotFound(): void
    {
        $ormMetadata = new OrmMetadata($this->getBundleMetadataMock(__DIR__.'/Fixtures'));

        $result = $ormMetadata->getEntityMappingFiles();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testGetRepositoryFiles(): void
    {
        $ormMetadata = new OrmMetadata($this->getBundleMetadataMock(__DIR__.'/Fixtures/bundle1'));

        $filterIterator = $ormMetadata->getRepositoryFiles();

        $files = [];
        foreach ($filterIterator as $file) {
            $files[] = $file->getFilename();
        }

        $this->assertInstanceOf('Iterator', $filterIterator);
        $this->assertContainsOnly(SplFileInfo::class, $filterIterator);
        $this->assertContains('BlockRepository.php', $files);
        $this->assertContains('PageRepository.php', $files);
        $this->assertNotContains('Block.php', $files);
        $this->assertNotContains('Page.php', $files);
    }

    public function testGetRepositoryFilesWithFilesNotFound(): void
    {
        $ormMetadata = new OrmMetadata($this->getBundleMetadataMock(__DIR__.'/Fixtures'));

        $result = $ormMetadata->getRepositoryFiles();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * @param string $bundlePath
     *
     * @return BundleMetadata
     */
    private function getBundleMetadataMock($bundlePath)
    {
        $bundle = $this->createMock(Bundle::class);
        $bundle
            ->method('getPath')
            ->willReturn($bundlePath);

        $bundleMetadata = $this->createMock(BundleMetadata::class);
        $bundleMetadata
            ->method('getBundle')
            ->willReturn($bundle);
        $bundleMetadata
            ->method('getClass')
            ->willReturn(SonataAcmeBundle::class);
        $bundleMetadata
            ->method('getExtendedDirectory')
            ->willReturn('Application/Sonata/AcmeBundle');

        return $bundleMetadata;
    }
}
