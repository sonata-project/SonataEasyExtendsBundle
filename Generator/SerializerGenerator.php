<?php
/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\EasyExtendsBundle\Generator;

use Symfony\Component\Console\Output\OutputInterface;
use Sonata\EasyExtendsBundle\Bundle\BundleMetadata;

class SerializerGenerator implements GeneratorInterface
{
    protected $serializerTemplate;

    public function __construct()
    {
        $this->serializerTemplate = file_get_contents(__DIR__.'/../Resources/skeleton/serializer/entity.mustache');
    }

    /**
     * @param OutputInterface $output
     * @param BundleMetadata  $bundleMetadata
     */
    public function generate(OutputInterface $output, BundleMetadata $bundleMetadata)
    {
        $output->writeln(' - Generating serializer files');

        $names = $bundleMetadata->getOrmMetadata()->getEntityNames();

        foreach ($names as $name) {

            $dest_file  = sprintf('%s/Entity.%s.xml', $bundleMetadata->getOrmMetadata()->getExtendedSerializerDirectory(), $name);

            if (is_file($dest_file)) {
                $output->writeln(sprintf('   ~ <info>%s</info>', $name));
            } else {
                $output->writeln(sprintf('   + <info>%s</info>', $name));

                $string = Mustache::replace($this->serializerTemplate, array(
                    'name'      => $name,
                    'namespace' => $bundleMetadata->getExtendedNamespace(),
                    'root_name' => strtolower($name),
                ));

                file_put_contents($dest_file, $string);
            }

        }
    }
}
