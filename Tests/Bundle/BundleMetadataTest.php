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

// Unfortunately phpunit cannot mock a class in chosen namespace.
// Therefore mocks are stored in Fixtures/bundle1 directory and required here.
require_once __DIR__.'/Fixtures/bundle1/SonataAcmeBundle.php';
require_once __DIR__.'/Fixtures/bundle1/SonataNotExtendableBundle.php';
require_once __DIR__.'/Fixtures/bundle1/SymfonyNotExtendableBundle.php';
require_once __DIR__.'/Fixtures/bundle1/LongNamespaceBundle.php';
require_once __DIR__.'/Fixtures/bundle1/AcmeBundle.php';

use PHPUnit\Framework\TestCase;
use Sonata\EasyExtendsBundle\Bundle\BundleMetadata;

class BundleMetadataTest extends TestCase
{
    public function testBundleMetadata()
    {
        $bundle = new \Sonata\AcmeBundle\SonataAcmeBundle();

        $bundleMetadata = new BundleMetadata($bundle, [
            'application_dir' => 'app/Application/:vendor',
            'namespace' => 'Application\\:vendor',
        ]);

        $this->assertTrue($bundleMetadata->isExtendable());
        $this->assertTrue($bundleMetadata->isValid());
        $this->assertEquals('SonataAcmeBundle', $bundleMetadata->getName());
        $this->assertEquals('Sonata', $bundleMetadata->getVendor());
        $this->assertEquals('Sonata\AcmeBundle', $bundleMetadata->getNamespace());
        $this->assertEquals('app/Application/Sonata/AcmeBundle', $bundleMetadata->getExtendedDirectory());
        $this->assertEquals('Application\Sonata\AcmeBundle', $bundleMetadata->getExtendedNamespace());
        $this->assertInstanceOf('Sonata\EasyExtendsBundle\Bundle\OrmMetadata', $bundleMetadata->getOrmMetadata());
        $this->assertInstanceOf('Sonata\EasyExtendsBundle\Bundle\OdmMetadata', $bundleMetadata->getOdmMetadata());
        $this->assertSame($bundle, $bundleMetadata->getBundle());
    }

    public function testCustomNamespace()
    {
        $bundle = new \Sonata\AcmeBundle\SonataAcmeBundle();

        $bundleMetadata = new BundleMetadata($bundle, [
            'application_dir' => 'app/Custom/:vendor',
            'namespace' => 'Custom\\:vendor',
        ]);

        $this->assertEquals('app/Custom/Sonata/AcmeBundle', $bundleMetadata->getExtendedDirectory());
        $this->assertEquals('Custom\Sonata\AcmeBundle', $bundleMetadata->getExtendedNamespace());
    }

    public function testApplicationNotExtendableBundle()
    {
        $bundle = new \Application\Sonata\NotExtendableBundle();

        $bundleMetadata = new BundleMetadata($bundle, [
            'application_dir' => 'Application',
            'namespace' => 'Application',
        ]);

        $this->assertFalse($bundleMetadata->isValid());
        $this->assertFalse($bundleMetadata->isExtendable());
    }

    public function testSymfonyNotExtendableBundle()
    {
        $bundle = new \Symfony\Bundle\NotExtendableBundle();

        $bundleMetadata = new BundleMetadata($bundle, [
            'application_dir' => 'Application',
            'namespace' => 'Application',
        ]);

        $this->assertFalse($bundleMetadata->isValid());
        $this->assertFalse($bundleMetadata->isExtendable());
    }

    public function testBundleNamespace()
    {
        $bundle = new \Sonata\Bundle\AcmeBundle\LongNamespaceBundle();

        $bundleMetadata = new BundleMetadata($bundle, [
            'application_dir' => 'Application',
            'namespace' => 'Application',
        ]);

        $this->assertFalse($bundleMetadata->isValid());
    }

    public function testBundleName()
    {
        $bundle = new \Sonata\AcmeBundle\AcmeBundle();

        $bundleMetadata = new BundleMetadata($bundle, [
            'application_dir' => 'Application',
            'namespace' => 'Application',
        ]);

        $this->assertFalse($bundleMetadata->isValid());
    }
}
