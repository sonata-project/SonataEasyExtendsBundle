<?php

namespace Sonata\EasyExtendsBundle\Tests\Bundle;

use Sonata\EasyExtendsBundle\Bundle\OdmMetadata;

class OdmMetadataTest extends \PHPUnit_Framework_TestCase
{
    public function testEntityNames()
    {
        $ormMetadata = new OdmMetadata($this->getBundleMetadataMock(__DIR__.'/Fixtures/bundle1'));

        $documentNames = $ormMetadata->getDocumentNames();

        $this->assertEquals(array('Block', 'Page'), $documentNames);
    }

    public function testDirectoryWithDotInPath()
    {
        $ormMetadata = new OdmMetadata($this->getBundleMetadataMock(__DIR__.'/Fixtures/bundle2/dot.dot'));

        $documentNames = $ormMetadata->getDocumentNames();

        $this->assertEquals(array('Block', 'Page'), $documentNames);
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
            null,
            array($bundle),
            '',
            true
        );
        $bundleMetadata->expects($this->any())
            ->method('getBundle')
            ->will($this->returnValue($bundle));
        $bundleMetadata->expects($this->any())
            ->method('getClass')
            ->will($this->returnValue('Sonata\\PageBundle\\SonataPageBundle'));

        return $bundleMetadata;
    }
}

