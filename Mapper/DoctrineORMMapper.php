<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\EasyExtendsBundle\Mapper;

use Symfony\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class DoctrineORMMapper implements EventSubscriber
{
    protected $associations;

    protected $doctrine;

    /**
     * @param \Symfony\Bundle\DoctrineBundle\Registry $doctrine
     * @param array $associations
     */
    public function __construct(Registry $doctrine, $associations = array())
    {
        $this->doctrine = $doctrine;
        $this->associations = $associations;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            'loadClassMetadata'
        );
    }

    /**
     * @param $class
     * @param $field
     * @param $mapping
     * @return void
     */
    public function addAssociation($class, $field, array $options)
    {
        if (!isset($this->associations[$class])) {
            $this->associations[$class] = array();
        }

        $this->associations[$class][$field] = $options;
    }

    /**
     * @param $eventArgs
     * @return void
     */
    public function loadClassMetadata($eventArgs)
    {
        $metadata = $eventArgs->getClassMetadata();

        if (!array_key_exists($metadata->name, $this->associations)) {
            return;
        }

        try {
            foreach ($this->associations[$metadata->name] as $type => $mappings) {
                foreach ($mappings as $mapping) {
                    call_user_func(array($metadata, $type), $mapping);
                }
            }
        } catch (\ReflectionException $e) {
            throw new \RuntimeException(sprintf('Error with class %s : %s', $metadata->name, $e->getMessage()), 404,  $e);
        }
    }
}
