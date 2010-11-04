<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace EasyExtendsBundle\Service;


class Service {

    protected $mapping = array();
    protected $cache = array();

    public function __construct($mapping) {
        $this->mapping = $mapping;
    }

    public function getInstance($class, $arguments = array()) {

        $class = $this->getClasse($class);

        if(!isset($this->cache[$class])) {
            $this->cache[$class] = new ReflectionClass($class);
        }

        return $this->cache[$class]->newInstanceArgs($arguments);
    }

    public function getClass($class) {

        if(!isset($this->mapping[$class])) {
            throw new RuntimeException(sprintf('The %s class is not mapped to a main class', $class));
        }

        return $this->mapping[$class];
    }
    
}