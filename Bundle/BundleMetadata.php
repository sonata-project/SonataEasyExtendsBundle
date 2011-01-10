<?php
/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bundle\Sonata\EasyExtendsBundle\Bundle;

use Symfony\Component\Finder\Finder;

class BundleMetadata
{
    protected $bundle;

    protected $vendor = false;

    protected $valid = false;

    protected $extendedDirectory = false;

    protected $configuration = array();

    public function __construct($bundle, array $configuration = array())
    {
        $this->bundle = $bundle;
        $this->configuration = $configuration;

        $this->buildInformation();
    }

    protected function buildInformation()
    {

        $information = explode('\\', $this->getClass());
        
        if($information[0] == 'Bundle' && count($information) == 4)  { // with vendor name

            $this->extendedDirectory = sprintf('%s/%s/%s', $this->configuration['application_dir'], $information[1], $information[2]);
            $this->vendor = $information[1];
            $this->valid = true;
            
        } else if($information[0] == 'Bundle' && count($information) == 3) { // wo vendor name

            $this->extendedDirectory = sprintf('%s/%s', $this->configuration['application_dir'], $information[1]);
            $this->valid = true;
            
        } else {

            $this->valid = false;
            
        }

    }

    public function isExtendable()
    {
        // does not extends Application bundle ...
        return !(
            strpos($this->getClass(), 'Application') === 0
            || strpos($this->getClass(), 'Symfony') === 0
        );

    }
    public function getClass()
    {
        return $this->bundle->getReflection()->getName();
    }

    public function isValid()
    {
        return $this->valid;
    }

    public function getExtendedDirectory()
    {
        return $this->extendedDirectory;
    }

    public function getVendor()
    {
        return $this->vendor;
    }

    public function getMappingEntityDirectory()
    {

        return sprintf('%s/Resources/config/doctrine/metadata/orm', $this->bundle->getPath());
    }

    public function getExtendedMappingEntityDirectory()
    {

        return sprintf('%s/Resources/config/doctrine/metadata/orm', $this->getExtendedDirectory());
    }

    public function getEntityDirectory()
    {

        return sprintf('%s/Entity', $this->bundle->getPath());
    }

    public function getExtendedEntityDirectory()
    {

        return sprintf('%s/Entity', $this->getExtendedDirectory());
    }
    
    public function getEntityMappingFiles()
    {
        try {
            $f = new Finder;
            $f->name('Application.*.dcm.xml');
            $f->in($this->getMappingEntityDirectory());

            return $f->getIterator();
        } catch(\Exception $e) {
            
            return array();
        }
    }

    public function getEntityNames()
    {
        $names = array();
        
        try {
            $f = new Finder;
            $f->name('Application.*.dcm.xml');
            $f->in($this->getMappingEntityDirectory());

            foreach($f->getIterator() as $file) {
                $e = explode('.', $file);
                $names[] = $e[count($e) - 3];
            }

        } catch(\Exception $e) {

        }

        return $names;
    }

    public function getRepositoryFiles()
    {
        try {
            $f = new Finder;
            $f->name('Application.*.dcm.xml');
            $f->in($this->getEntityDirectory());

            return $f->getIterator();
        } catch(\Exception $e) {

            return array();
        }
    }

    public function getExtendedNamespace()
    {
        if($this->getVendor())
        {
            return sprintf('Application\%s\%s', $this->getVendor(), $this->getName());
        }

        return sprintf('Application\%s', $this->getName());
    }

    public function getNamespace()
    {
        if($this->getVendor())
        {
            return sprintf('Bundle\%s\%s', $this->getVendor(), $this->getName());
        }

        return sprintf('Bundle\%s', $this->getName());
    }
    
    public function getName($with_vendor = false)
    {
        if($with_vendor && $this->getVendor())
        {
            return sprintf("%s%s", $this->getVendor(), $this->bundle->getName());
        }

        return $this->bundle->getName();
    }


}