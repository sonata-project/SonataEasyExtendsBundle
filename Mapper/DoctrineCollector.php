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

class DoctrineCollector
{
    protected $associations;

    private static $instance;

    public function __construct()
    {
        $this->associations = array();
    }

    /**
     * @return \Sonata\EasyExtendsBundle\Mapper\DoctrineCollector
     */
    static public function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
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
     * @return
     */
    public function getAssociations()
    {
        return $this->associations;
    }
}
