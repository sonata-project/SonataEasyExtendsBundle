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

namespace Sonata\EasyExtendsBundle\Tests\Command\Functional;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Sonata\EasyExtendsBundle\SonataEasyExtendsBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

final class AppKernel extends Kernel
{
    use MicroKernelTrait;

    public function __construct()
    {
        parent::__construct('test', false);
    }

    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new DoctrineBundle(),
            new SonataEasyExtendsBundle(),
        ];
    }

    public function getCacheDir(): string
    {
        return $this->getBaseDir().'cache';
    }

    public function getLogDir(): string
    {
        return $this->getBaseDir().'log';
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }

    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
    }

    protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader): void
    {
        $containerBuilder->loadFromExtension('framework', [
            'secret' => 'MySecret',
        ]);

        $containerBuilder->loadFromExtension('doctrine', [
            'orm' => [
                'entity_managers' => [
                    'default' => [
                        'auto_mapping' => true,
                        'mappings' => [
                            'App' => [
                                'type' => 'annotation',
                                'dir' => '%kernel.project_dir%/Entity',
                                'is_bundle' => false,
                                'prefix' => 'Sonata\EasyExtendsBundle\Tests\Command\Functional\Entity',
                                'alias' => 'SonataFunctional',
                            ],
                        ],
                    ],
                ],
            ],
            'dbal' => [
                'driver' => 'pdo_sqlite',
            ],
        ]);
    }

    private function getBaseDir(): string
    {
        return sys_get_temp_dir().'/sonata-easy-extends-bundle/var/';
    }
}
