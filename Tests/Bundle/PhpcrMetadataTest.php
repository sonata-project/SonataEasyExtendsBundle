<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\EasyExtendsBundle\Tests\Bundle;

use Sonata\EasyExtendsBundle\Bundle\PhpcrMetadata;

class PhpcrMetadataTest extends \PHPUnit_Framework_TestCase
{
    public function testDocumentNames()
    {
        $odmMetadata = new PhpcrMetadata($this->getBundleMetadataMock(__DIR__.'/Fixtures/bundle1'));

        $documentNames = $odmMetadata->getDocumentNames();

        $this->assertContains('Block', $documentNames);
        $this->assertContains('Page', $documentNames);
    }

    public function testDirectoryWithDotInPath()
    {
        $odmMetadata = new PhpcrMetadata($this->getBundleMetadataMock(__DIR__.'/Fixtures/bundle2/dot.dot'));

        $documentNames = $odmMetadata->getDocumentNames();

        $this->assertContains('Block', $documentNames);
        $this->assertContains('Page', $documentNames);
    }

    public function testGetMappingDocumentDirectory()
    {
        $bundlePath = __DIR__.'/Fixtures/bundle1';
        $expectedDirectory = $bundlePath.'/Resources/config/doctrine/';

        $odmMetadata = new PhpcrMetadata($this->getBundleMetadataMock($bundlePath));

        $directory = $odmMetadata->getMappingDocumentDirectory();

        $this->assertEquals($expectedDirectory, $directory);
    }

    public function testGetExtendedMappingDocumentDirectory()
    {
        $bundlePath = __DIR__.'/Fixtures/bundle1';
        $expectedDirectory = 'Application/Sonata/AcmeBundle/Resources/config/doctrine/';

        $odmMetadata = new PhpcrMetadata($this->getBundleMetadataMock($bundlePath));

        $directory = $odmMetadata->getExtendedMappingDocumentDirectory();

        $this->assertEquals($expectedDirectory, $directory);
    }

    public function testGetDocumentDirectory()
    {
        $bundlePath = __DIR__.'/Fixtures/bundle1';
        $expectedDirectory = $bundlePath.'/PHPCR';

        $odmMetadata = new PhpcrMetadata($this->getBundleMetadataMock($bundlePath));

        $directory = $odmMetadata->getDocumentDirectory();

        $this->assertEquals($expectedDirectory, $directory);
    }

    public function testGetExtendedDocumentDirectory()
    {
        $bundlePath = __DIR__.'/Fixtures/bundle1';
        $expectedDirectory = 'Application/Sonata/AcmeBundle/PHPCR';

        $odmMetadata = new PhpcrMetadata($this->getBundleMetadataMock($bundlePath));

        $directory = $odmMetadata->getExtendedDocumentDirectory();

        $this->assertEquals($expectedDirectory, $directory);
    }

    public function testGetExtendedSerializerDirectory()
    {
        $bundlePath = __DIR__.'/Fixtures/bundle1';
        $expectedDirectory = 'Application/Sonata/AcmeBundle/Resources/config/serializer';

        $odmMetadata = new PhpcrMetadata($this->getBundleMetadataMock($bundlePath));

        $directory = $odmMetadata->getExtendedSerializerDirectory();

        $this->assertEquals($expectedDirectory, $directory);
    }

    public function testGetDocumentMappingFiles()
    {
        $odmMetadata = new PhpcrMetadata($this->getBundleMetadataMock(__DIR__.'/Fixtures/bundle1'));

        $filterIterator = $odmMetadata->getDocumentMappingFiles();

        $files = array();
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

    public function testGetDocumentMappingFilesWithFilesNotFound()
    {
        $odmMetadata = new PhpcrMetadata($this->getBundleMetadataMock(__DIR__.'/Fixtures'));

        $result = $odmMetadata->getDocumentMappingFiles();

        $this->assertInternalType('array', $result);
        $this->assertEmpty($result);
    }

    public function testGetRepositoryFiles()
    {
        $odmMetadata = new PhpcrMetadata($this->getBundleMetadataMock(__DIR__.'/Fixtures/bundle1'));

        $filterIterator = $odmMetadata->getRepositoryFiles();

        $files = array();
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
        $odmMetadata = new PhpcrMetadata($this->getBundleMetadataMock(__DIR__.'/Fixtures'));

        $result = $odmMetadata->getRepositoryFiles();

        $this->assertInternalType('array', $result);
        $this->assertEmpty($result);
    }

    /**
     * @param string $bundlePath
     *
     * @return Sonata\EasyExtendsBundle\Bundle\BundleMetadata
     */
    private function getBundleMetadataMock($bundlePath)
    {
        $bundle = $this->getMock('Symfony\Component\HttpKernel\Bundle\Bundle');
        $bundle->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue($bundlePath));

        $bundleMetadata = $this->getMock(
            'Sonata\EasyExtendsBundle\Bundle\BundleMetadata',
            array(),
            array($bundle),
            '',
            true
        );
        $bundleMetadata->expects($this->any())
            ->method('getBundle')
            ->will($this->returnValue($bundle));
        $bundleMetadata->expects($this->any())
            ->method('getClass')
            ->will($this->returnValue('Sonata\\AcmeBundle\\SonataAcmeBundle'));
        $bundleMetadata->expects($this->any())
            ->method('getExtendedDirectory')
            ->will($this->returnValue('Application/Sonata/AcmeBundle'));

        return $bundleMetadata;
    }
}
