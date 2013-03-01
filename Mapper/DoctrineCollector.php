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

    protected $indexes;

    protected $discriminators;

    protected $discriminatorColumns;

    protected $inheritanceTypes;

    private static $instance;

    public function __construct()
    {
        $this->associations = array();
        $this->indexes = array();
        $this->discriminatorColumns = array();
        $this->inheritanceTypes = array();
        $this->discriminators = array();
    }

    /**
     * @return \Sonata\EasyExtendsBundle\Mapper\DoctrineCollector
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Add a discriminator to a class.
     *
     * @param  string  $class               The Class
     * @param  string  $key                 Key is the database value and values are the classes
     * @param  string  $discriminatorClass  The mapped class
     *
     * @return void
     */
    public function addDiscriminator($class, $key, $discriminatorClass)
    {
        if (!isset($this->discriminators[$class])) {
            $this->discriminators[$class] = array();
        }

        if (!isset($this->discriminators[$class][$key])) {
            $this->discriminators[$class][$key] = $discriminatorClass;
        }
    }

    /**
     * Add the Discriminator Column.
     *
     * @param string $class
     * @param array $columnDef
     *
     * @return void
     */
    public function addDiscriminatorColumn($class, array $columnDef)
    {
        if (!isset($this->discriminatorColumns[$class])) {
            $this->discriminatorColumns[$class] = $columnDef;
        }
    }

    /**
     * @param string $class
     * @param string $type
     *
     * @return void
     */
    public function addInheritanceType($class, $type)
    {
        if (!isset($this->inheritanceTypes[$class])) {
            $this->inheritanceTypes[$class] = $type;
        }
    }

    /**
     * @param $class
     * @param $type
     * @param  array $options
     * @return void
     */
    public function addAssociation($class, $type, array $options)
    {
        if (!isset($this->associations[$class])) {
            $this->associations[$class] = array();
        }

        if (!isset($this->associations[$class][$type])) {
            $this->associations[$class][$type] = array();
        }

        $this->associations[$class][$type][] = $options;
    }

    /**
     * @param $class
     * @param $name
     * @param  array $columns
     * @return void
     */
    public function addIndex($class, $name, array $columns)
    {
        if (!isset($this->indexes[$class])) {
            $this->indexes[$class] = array();
        }

        if (isset($this->indexes[$class][$name])) {
            return;
        }

        $this->indexes[$class][$name] = $columns;
    }

    /**
     * @return array
     */
    public function getAssociations()
    {
        return $this->associations;
    }
    /**
     * @return array
     */
    public function getDiscriminators()
    {
        return $this->discriminators;
    }

    /**
     * @return array
     */
    public function getDiscriminatorColumns()
    {
        return $this->discriminatorColumns;
    }
    /**
     * @return array
     */
    public function getInheritanceTypes()
    {
        return $this->inheritanceTypes;
    }
    /**
     * @return array
     */
    public function getIndexes()
    {
        return $this->indexes;
    }
}
