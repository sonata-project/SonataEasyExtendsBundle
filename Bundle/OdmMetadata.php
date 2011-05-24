<?php
/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\EasyExtendsBundle\Bundle;

use Symfony\Component\Finder\Finder;

class OdmMetadata
{
    protected $mappingDocumentDirectory;
    protected $extendedMappingDocumentDirectory;
    protected $documentDirectory;
    protected $extendedDocumentDirectory;

    public function __construct(BundleMetadata $bundleMetadata)
    {
        $this->mappingDocumentDirectory           = sprintf('%s/Resources/config/doctrine/', $bundleMetadata->getBundle()->getPath());
        $this->extendedMappingDocumentDirectory   = sprintf('%s/Resources/config/doctrine/', $bundleMetadata->getExtendedDirectory());
        $this->documentDirectory                  = sprintf('%s/Document', $bundleMetadata->getBundle()->getPath());
        $this->extendedDocumentDirectory          = sprintf('%s/Document', $bundleMetadata->getExtendedDirectory());
    }

    public function getMappingDocumentDirectory()
    {
        return $this->mappingDocumentDirectory;
    }

    public function getExtendedMappingDocumentDirectory()
    {
        return $this->extendedMappingDocumentDirectory;
    }

    public function getDocumentDirectory()
    {
        return $this->documentDirectory;
    }

    public function getExtendedDocumentDirectory()
    {
        return $this->extendedDocumentDirectory;
    }

    public function getDocumentMappingFiles()
    {
        try {
            $f = new Finder;
            $f->name('*.mongodb.xml');
            $f->notName('Base*.mongodb.xml');
            $f->in($this->getMappingDocumentDirectory());

            return $f->getIterator();
        } catch(\Exception $e) {

            return array();
        }
    }

    public function getDocumentNames()
    {
        $names = array();

        try {
            $f = new Finder;
            $f->name('*.mongodb.xml');
            $f->notName('Base*.mongodb.xml');
            $f->in($this->getMappingDocumentDirectory());

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
            $f->name('*.mongodb.xml');
            $f->notName('Base*.mongodb.xml');
            $f->in($this->getDocumentDirectory());

            return $f->getIterator();
        } catch(\Exception $e) {

            return array();
        }
    }
}