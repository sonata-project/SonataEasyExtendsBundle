<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\EasyExtendsBundle\Mapper;

class DoctrineCollector
{
    /**
     * @var array
     */
    protected $associations;

    /**
     * @var array
     */
    protected $indexes;

    /**
     * @var array
     */
    protected $uniques;

    /**
     * @var array
     */
    protected $discriminators;

    /**
     * @var array
     */
    protected $discriminatorColumns;

    /**
     * @var array
     */
    protected $inheritanceTypes;

    /**
     * @var array
     */
    protected $overrides;

    /**
     * @var DoctrineCollector
     */
    private static $instance;

    public function __construct()
    {
        $this->associations = [];
        $this->indexes = [];
        $this->uniques = [];
        $this->discriminatorColumns = [];
        $this->inheritanceTypes = [];
        $this->discriminators = [];
        $this->overrides = [];
    }

    /**
     * @return DoctrineCollector
     */
    public static function getInstance(): self
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Add a discriminator to a class.
     *
     * @param string $class              The Class
     * @param string $key                Key is the database value and values are the classes
     * @param string $discriminatorClass The mapped class
     */
    public function addDiscriminator(string $class, string $key, string $discriminatorClass): void
    {
        if (!isset($this->discriminators[$class])) {
            $this->discriminators[$class] = [];
        }

        if (!isset($this->discriminators[$class][$key])) {
            $this->discriminators[$class][$key] = $discriminatorClass;
        }
    }

    /**
     * Add the Discriminator Column.
     *
     * @param string $class
     * @param array  $columnDef
     */
    public function addDiscriminatorColumn(string $class, array $columnDef): void
    {
        if (!isset($this->discriminatorColumns[$class])) {
            $this->discriminatorColumns[$class] = $columnDef;
        }
    }

    /**
     * @param string $class
     * @param string $type
     */
    public function addInheritanceType(string $class, string $type): void
    {
        if (!isset($this->inheritanceTypes[$class])) {
            $this->inheritanceTypes[$class] = $type;
        }
    }

    /**
     * @param string $class
     * @param string $type
     * @param array  $options
     */
    public function addAssociation(string $class, string $type, array $options): void
    {
        if (!isset($this->associations[$class])) {
            $this->associations[$class] = [];
        }

        if (!isset($this->associations[$class][$type])) {
            $this->associations[$class][$type] = [];
        }

        $this->associations[$class][$type][] = $options;
    }

    /**
     * @param string $class
     * @param string $name
     * @param array  $columns
     */
    public function addIndex(string $class, string $name, array $columns): void
    {
        if (!isset($this->indexes[$class])) {
            $this->indexes[$class] = [];
        }

        if (isset($this->indexes[$class][$name])) {
            return;
        }

        $this->indexes[$class][$name] = $columns;
    }

    /**
     * @param string $class
     * @param string $name
     * @param array  $columns
     */
    public function addUnique(string $class, string $name, array $columns): void
    {
        if (!isset($this->indexes[$class])) {
            $this->uniques[$class] = [];
        }

        if (isset($this->uniques[$class][$name])) {
            return;
        }

        $this->uniques[$class][$name] = $columns;
    }

    /**
     * Adds new override.
     *
     * @param string $class
     * @param string $type
     * @param array  $options
     */
    final public function addOverride(string $class, string $type, array $options): void
    {
        if (!isset($this->overrides[$class])) {
            $this->overrides[$class] = [];
        }

        if (!isset($this->overrides[$class][$type])) {
            $this->overrides[$class][$type] = [];
        }

        $this->overrides[$class][$type][] = $options;
    }

    /**
     * @return array
     */
    public function getAssociations(): array
    {
        return $this->associations;
    }

    /**
     * @return array
     */
    public function getDiscriminators(): array
    {
        return $this->discriminators;
    }

    /**
     * @return array
     */
    public function getDiscriminatorColumns(): array
    {
        return $this->discriminatorColumns;
    }

    /**
     * @return array
     */
    public function getInheritanceTypes(): array
    {
        return $this->inheritanceTypes;
    }

    /**
     * @return array
     */
    public function getIndexes(): array
    {
        return $this->indexes;
    }

    /**
     * @return array
     */
    public function getUniques(): array
    {
        return $this->uniques;
    }

    /**
     * Get all overrides.
     *
     * @return array
     */
    final public function getOverrides(): array
    {
        return $this->overrides;
    }
}
