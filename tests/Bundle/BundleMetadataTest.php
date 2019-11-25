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

// Unfortunately phpunit cannot mock a class in chosen namespace.
// Therefore mocks are stored in Fixtures/bundle1 directory and required here.
require_once __DIR__.'/Fixtures/bundle1/SonataAcmeBundle.php';
require_once __DIR__.'/Fixtures/bundle1/SonataNotExtendableBundle.php';
require_once __DIR__.'/Fixtures/bundle1/SymfonyNotExtendableBundle.php';
require_once __DIR__.'/Fixtures/bundle1/LongNamespaceBundle.php';
require_once __DIR__.'/Fixtures/bundle1/AcmeBundle.php';

use PHPUnit\Framework\TestCase;
use Sonata\EasyExtendsBundle\Bundle\BundleMetadata;
use Sonata\EasyExtendsBundle\Bundle\OdmMetadata;
use Sonata\EasyExtendsBundle\Bundle\OrmMetadata;

class BundleMetadataTest extends TestCase
{
    public function testBundleMetadata()
    {
        $bundle = new \Sonata\AcmeBundle\SonataAcmeBundle();

        $bundleMetadata = new BundleMetadata($bundle, [
            'application_dir' => 'app/Application/:vendor',
            'namespace' => 'Application\\:vendor',
            'namespace_prefix' => '',
        ]);

        $this->assertTrue($bundleMetadata->isExtendable());
        $this->assertTrue($bundleMetadata->isValid());
        $this->assertSame('SonataAcmeBundle', $bundleMetadata->getName());
        $this->assertSame('Sonata', $bundleMetadata->getVendor());
        $this->assertSame('Sonata\AcmeBundle', $bundleMetadata->getNamespace());
        $this->assertSame('app/Application/Sonata/AcmeBundle', $bundleMetadata->getExtendedDirectory());
        $this->assertSame('Application\Sonata\AcmeBundle', $bundleMetadata->getExtendedNamespace());
        $this->assertInstanceOf(OrmMetadata::class, $bundleMetadata->getOrmMetadata());
        $this->assertInstanceOf(OdmMetadata::class, $bundleMetadata->getOdmMetadata());
        $this->assertSame($bundle, $bundleMetadata->getBundle());
    }

    public function testCustomNamespace()
    {
        $bundle = new \Sonata\AcmeBundle\SonataAcmeBundle();

        $bundleMetadata = new BundleMetadata($bundle, [
            'application_dir' => 'app/Custom/:vendor',
            'namespace' => 'Custom\\:vendor',
            'namespace_prefix' => '',
        ]);

        $this->assertSame('app/Custom/Sonata/AcmeBundle', $bundleMetadata->getExtendedDirectory());
        $this->assertSame('Custom\Sonata\AcmeBundle', $bundleMetadata->getExtendedNamespace());
    }

    public function testApplicationNotExtendableBundle()
    {
        $bundle = new \Application\Sonata\NotExtendableBundle();

        $bundleMetadata = new BundleMetadata($bundle, [
            'application_dir' => 'Application',
            'namespace' => 'Application',
            'namespace_prefix' => '',
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
            'namespace_prefix' => '',
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
            'namespace_prefix' => '',
        ]);

        $this->assertFalse($bundleMetadata->isValid());
    }

    public function testBundleName()
    {
        $bundle = new \Sonata\AcmeBundle\AcmeBundle();

        $bundleMetadata = new BundleMetadata($bundle, [
            'application_dir' => 'Application',
            'namespace' => 'Application',
            'namespace_prefix' => '',
        ]);

        $this->assertFalse($bundleMetadata->isValid());
    }

    public function testWithNamespacePrefix()
    {
        $bundle = new \Sonata\AcmeBundle\SonataAcmeBundle();

        $bundleMetadata = new BundleMetadata($bundle, [
            'application_dir' => 'src/Application/:vendor',
            'namespace' => 'Application\\:vendor',
            'namespace_prefix' => 'App\\',
        ]);

        $this->assertSame('SonataAcmeBundle', $bundleMetadata->getName());
        $this->assertSame('Sonata', $bundleMetadata->getVendor());
        $this->assertSame('Application', $bundleMetadata->getApplication());
        $this->assertSame('Sonata\AcmeBundle', $bundleMetadata->getNamespace());
        $this->assertSame('src/Application/Sonata/AcmeBundle', $bundleMetadata->getExtendedDirectory());
        $this->assertSame('App\Application\Sonata\AcmeBundle', $bundleMetadata->getExtendedNamespace());
        $this->assertSame($bundle, $bundleMetadata->getBundle());
    }
}
