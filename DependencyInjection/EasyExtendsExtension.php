<?php
/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Bundle\EasyExtendsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;

use Symfony\Component\Finder\Finder;

/**
 * EasyExtendsExtension
 *
 *
 * @author     Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class EasyExtendsExtension extends Extension {

    /**
     * Loads the url shortener configuration.
     *
     * @param array            $config    An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function configLoad($config, ContainerBuilder $container) {

        $definition = new Definition('Bundle\\EasyExtends\\Service\\Service');
        $definition->addMethodCall('setMapping', array($this->retrieveEntitiesList($container)));

        $container->setDefinition('easy_extends', $definition);
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     */
    public function getXsdValidationBasePath() {

        return __DIR__.'/../Resources/config/schema';
    }

    public function getNamespace() {

        return 'http://www.sonata-project.org/schema/dic/sonata-basket';
    }

    public function getAlias() {

        return "easy_extends";
    }

    public function retrieveEntitiesList($container) {
        $bundleDirs = $container->getParameter('kernel.bundle_dirs');

        unset($bundleDirs['Application']);

        $mapping = array();

        $finder = new Finder;
        $finder = $finder
            ->files()
            ->name('*.php');


        foreach ($container->getParameter('kernel.bundles') as $className) {
            $tmp = dirname(str_replace('\\', '/', $className));
            $namespace = str_replace('/', '\\', dirname($tmp));
            $class = basename($tmp);

            if (!isset($bundleDirs[$namespace])) {
                continue;
            }

            $directory = false;
            foreach($bundleDirs as $dir) {

                if(!is_dir($dir.'/'.$class.'/Entity')) {
                    continue;
                }

                $directory = $dir.'/'.$class.'/Entity';
            }

            if(!$directory) {
                continue;
            }

            foreach($finder->in($directory) as $file) {
                $mapped_class       = sprintf('%s\%s\Entity\%s', $namespace, $class, $file->getbaseName('.php'));
                $application_class  = sprintf('Application\%s\Entity\%s', $class, $file->getbaseName('.php'));
                $mapping[$mapped_class] = $application_class;
            }
        }

        return $mapping;
    }
}