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
use Sonata\EasyExtendsBundle\Bundle\PhpcrMetadata;

class PhpcrMetadataTest extends TestCase
{
    public function testDocumentNames(): void
    {
        $odmMetadata = new PhpcrMetadata($this->getBundleMetadataMock(__DIR__.'/Fixtures/bundle1'));

        $documentNames = $odmMetadata->getDocumentNames();

        $this->assertContains('Block', $documentNames);
        $this->assertContains('Page', $documentNames);
    }

    public function testDirectoryWithDotInPath(): void
    {
        $odmMetadata = new PhpcrMetadata($this->getBundleMetadataMock(__DIR__.'/Fixtures/bundle2/dot.dot'));

        $documentNames = $odmMetadata->getDocumentNames();

        $this->assertContains('Block', $documentNames);
        $this->assertContains('Page', $documentNames);
    }

    public function testGetMappingDocumentDirectory(): void
    {
        $bundlePath = __DIR__.'/Fixtures/bundle1';
        $expectedDirectory = $bundlePath.'/Resources/config/doctrine/';

        $odmMetadata = new PhpcrMetadata($this->getBundleMetadataMock($bundlePath));

        $directory = $odmMetadata->getMappingDocumentDirectory();

        $this->assertSame($expectedDirectory, $directory);
    }

    public function testGetExtendedMappingDocumentDirectory(): void
    {
        $bundlePath = __DIR__.'/Fixtures/bundle1';
        $expectedDirectory = 'Application/Sonata/AcmeBundle/Resources/config/doctrine/';

        $odmMetadata = new PhpcrMetadata($this->getBundleMetadataMock($bundlePath));

        $directory = $odmMetadata->getExtendedMappingDocumentDirectory();

        $this->assertSame($expectedDirectory, $directory);
    }

    public function testGetDocumentDirectory(): void
    {
        $bundlePath = __DIR__.'/Fixtures/bundle1';
        $expectedDirectory = $bundlePath.'/PHPCR';

        $odmMetadata = new PhpcrMetadata($this->getBundleMetadataMock($bundlePath));

        $directory = $odmMetadata->getDocumentDirectory();

        $this->assertSame($expectedDirectory, $directory);
    }

    public function testGetExtendedDocumentDirectory(): void
    {
        $bundlePath = __DIR__.'/Fixtures/bundle1';
        $expectedDirectory = 'Application/Sonata/AcmeBundle/PHPCR';

        $odmMetadata = new PhpcrMetadata($this->getBundleMetadataMock($bundlePath));

        $directory = $odmMetadata->getExtendedDocumentDirectory();

        $this->assertSame($expectedDirectory, $directory);
    }

    public function testGetExtendedSerializerDirectory(): void
    {
        $bundlePath = __DIR__.'/Fixtures/bundle1';
        $expectedDirectory = 'Application/Sonata/AcmeBundle/Resources/config/serializer';

        $odmMetadata = new PhpcrMetadata($this->getBundleMetadataMock($bundlePath));

        $directory = $odmMetadata->getExtendedSerializerDirectory();

        $this->assertSame($expectedDirectory, $directory);
    }

    public function testGetDocumentMappingFiles(): void
    {
        $odmMetadata = new PhpcrMetadata($this->getBundleMetadataMock(__DIR__.'/Fixtures/bundle1'));

        $filterIterator = $odmMetadata->getDocumentMappingFiles();

        $files = [];
        foreach ($filterIterator as $file) {
            $files[] = $file->getFilename();
        }

        $this->assertInstanceOf('Iterator', $filterIterator);
        $this->assertContainsOnly('Symfony\Component\Finder\SplFileInfo', $filterIterator);
        $this->assertContains('Block.phpcr.xml.skeleton', $files);
        $this->assertContains('Page.phpcr.xml.skeleton', $files);
        $this->assertNotContains('Block.odm.xml.skeleton', $files);
        $this->assertNotContains('Page.odm.xml.skeleton', $files);
    }

    public function testGetDocumentMappingFilesWithFilesNotFound(): void
    {
        $odmMetadata = new PhpcrMetadata($this->getBundleMetadataMock(__DIR__.'/Fixtures'));

        $result = $odmMetadata->getDocumentMappingFiles();

        $this->assertInternalType('array', $result);
        $this->assertEmpty($result);
    }

    public function testGetRepositoryFiles(): void
    {
        $odmMetadata = new PhpcrMetadata($this->getBundleMetadataMock(__DIR__.'/Fixtures/bundle1'));

        $filterIterator = $odmMetadata->getRepositoryFiles();

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

    public function testGetRepositoryFilesWithFilesNotFound(): void
    {
        $odmMetadata = new PhpcrMetadata($this->getBundleMetadataMock(__DIR__.'/Fixtures'));

        $result = $odmMetadata->getRepositoryFiles();

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
