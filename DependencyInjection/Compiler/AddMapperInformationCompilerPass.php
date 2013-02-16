<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\EasyExtendsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;

/*
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class AddMapperInformationCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('doctrine')) {
            $container->removeDefinition('sonata.easy_extends.doctrine.mapper');

            return;
        }

        $mapper = $container->getDefinition('sonata.easy_extends.doctrine.mapper');

        foreach (DoctrineCollector::getInstance()->getAssociations() as $class => $associations) {
            foreach ($associations as $field => $options) {
                $mapper->addMethodCall('addAssociation', array($class, $field, $options));
            }
        }

        foreach (DoctrineCollector::getInstance()->getIndexes() as $class => $indexes) {
            foreach ($indexes as $field => $options) {
                $mapper->addMethodCall('addIndex', array($class, $field, $options));
            }
        }
    }
}
