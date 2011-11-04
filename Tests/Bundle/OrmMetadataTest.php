<?php

namespace Sonata\EasyExtendsBundle\Tests\Bundle;

use Sonata\EasyExtendsBundle\Bundle\OrmMetadata;

class OrmMetadataTest extends \PHPUnit_Framework_TestCase
{
    public function testEntityNames()
    {
        $ormMetadata = new OrmMetadata($this->getBundleMetadataMock(__DIR__.'/Fixtures/bundle1'));

        $entityNames = $ormMetadata->getEntityNames();

        $this->assertEquals(array('Block', 'Page'), $entityNames);
    }

    public function testDirectoryWithDotInPath()
    {
        $ormMetadata = new OrmMetadata($this->getBundleMetadataMock(__DIR__.'/Fixtures/bundle2/dot.dot'));

        $entityNames = $ormMetadata->getEntityNames();

        $this->assertEquals(array('Block', 'Page'), $entityNames);
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

