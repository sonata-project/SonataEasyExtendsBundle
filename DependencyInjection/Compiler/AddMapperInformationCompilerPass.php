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

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerInterface;
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
        $mapper = $container->getDefinition('sonata.easy_extends.doctrine.mapper');

        foreach (DoctrineCollector::getInstance()->getAssociations() as $class => $associations) {
            foreach ($associations as $field => $options) {
                $mapper->addMethodCall('addAssociation', array($class, $field, $options));
            }
        }
    }
}