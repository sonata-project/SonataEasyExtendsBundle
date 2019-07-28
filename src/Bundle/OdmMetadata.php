<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
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
    /**
     * @var string
     */
    protected $mappingDocumentDirectory;

    /**
     * @var string
     */
    protected $extendedMappingDocumentDirectory;

    /**
     * @var string
     */
    protected $documentDirectory;

    /**
     * @var string
     */
    protected $extendedDocumentDirectory;

    /**
     * @var string
     */
    protected $extendedSerializerDirectory;

    public function __construct(BundleMetadata $bundleMetadata)
    {
        $this->mappingDocumentDirectory = sprintf('%s/Resources/config/doctrine/', $bundleMetadata->getBundle()->getPath());
        $this->extendedMappingDocumentDirectory = sprintf('%s/Resources/config/doctrine/', $bundleMetadata->getExtendedDirectory());
        $this->documentDirectory = sprintf('%s/Document', $bundleMetadata->getBundle()->getPath());
        $this->extendedDocumentDirectory = sprintf('%s/Document', $bundleMetadata->getExtendedDirectory());
        $this->extendedSerializerDirectory = sprintf('%s/Resources/config/serializer', $bundleMetadata->getExtendedDirectory());
    }

    public function getMappingDocumentDirectory(): string
    {
        return $this->mappingDocumentDirectory;
    }

    public function getExtendedMappingDocumentDirectory(): string
    {
        return $this->extendedMappingDocumentDirectory;
    }

    public function getDocumentDirectory(): string
    {
        return $this->documentDirectory;
    }

    public function getExtendedDocumentDirectory(): string
    {
        return $this->extendedDocumentDirectory;
    }

    public function getExtendedSerializerDirectory(): string
    {
        return $this->extendedSerializerDirectory;
    }

    /**
     * @return array|\Iterator
     */
    public function getDocumentMappingFiles(): iterable
    {
        try {
            $f = new Finder();
            $f->name('*.mongodb.xml.skeleton');
            $f->in($this->getMappingDocumentDirectory());

            return $f->getIterator();
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getDocumentNames(): array
    {
        $names = [];

        try {
            $f = new Finder();
            $f->name('*.mongodb.xml.skeleton');
            $f->in($this->getMappingDocumentDirectory());

            foreach ($f->getIterator() as $file) {
                $name = explode('.', $file->getFilename());
                $names[] = $name[0];
            }
        } catch (\Exception $e) {
        }

        return $names;
    }

    /**
     * @return array|\Iterator
     */
    public function getRepositoryFiles(): iterable
    {
        try {
            $f = new Finder();
            $f->name('*Repository.php');
            $f->in($this->getDocumentDirectory());

            return $f->getIterator();
        } catch (\Exception $e) {
            return [];
        }
    }
}
