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

namespace Sonata\EasyExtendsBundle\Command;

use Sonata\EasyExtendsBundle\Bundle\BundleMetadata;
use Sonata\EasyExtendsBundle\Generator\BundleGenerator;
use Sonata\EasyExtendsBundle\Generator\OdmGenerator;
use Sonata\EasyExtendsBundle\Generator\OrmGenerator;
use Sonata\EasyExtendsBundle\Generator\PHPCRGenerator;
use Sonata\EasyExtendsBundle\Generator\SerializerGenerator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Generate Application entities from bundle entities.
 *
 * NEXT_MAJOR: stop extending ContainerAwareCommand.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class GenerateCommand extends ContainerAwareCommand
{
    /**
     * @var KernelInterface|null
     */
    private $kernel;

    /**
     * @var BundleGenerator|null
     */
    private $bundleGenerator;

    /**
     * @var OrmGenerator|null
     */
    private $ormGenerator;

    /**
     * @var OdmGenerator|null
     */
    private $odmGenerator;

    /**
     * @var PHPCRGenerator|null
     */
    private $phpcrGenerator;

    /**
     * @var SerializerGenerator|null
     */
    private $serializerGenerator;

    public function __construct(
        string $name = null,
        KernelInterface $kernel = null,
        BundleGenerator $bundleGenerator = null,
        OrmGenerator $ormGenerator = null,
        OdmGenerator $odmGenerator = null,
        PHPCRGenerator $phpcrGenerator = null,
        SerializerGenerator $serializerGenerator = null
    ) {
        parent::__construct($name);

        if (null === $kernel) {
            @trigger_error(sprintf(
                'Not providing a kernel to "%s" is deprecated since 2.x and will no longer be possible in 3.0',
                \get_class($this)
            ), E_USER_DEPRECATED);
        }

        if (null === $bundleGenerator) {
            @trigger_error(sprintf(
                'Not providing a bundleGenerator to "%s" is deprecated since 2.x and will no longer be possible in 3.0',
                \get_class($this)
            ), E_USER_DEPRECATED);
        }

        if (null === $ormGenerator) {
            @trigger_error(sprintf(
                'Not providing a ormGenerator to "%s" is deprecated since 2.x and will no longer be possible in 3.0',
                \get_class($this)
            ), E_USER_DEPRECATED);
        }

        if (null === $odmGenerator) {
            @trigger_error(sprintf(
                'Not providing a odmGenerator to "%s" is deprecated since 2.x and will no longer be possible in 3.0',
                \get_class($this)
            ), E_USER_DEPRECATED);
        }

        if (null === $phpcrGenerator) {
            @trigger_error(sprintf(
                'Not providing a phpcrGenerator to "%s" is deprecated since 2.x and will no longer be possible in 3.0',
                \get_class($this)
            ), E_USER_DEPRECATED);
        }

        if (null === $serializerGenerator) {
            @trigger_error(sprintf(
                'Not providing a serializerGenerator to "%s" is deprecated since 2.x and will no longer be possible in 3.0',
                \get_class($this)
            ), E_USER_DEPRECATED);
        }

        $this->kernel = $kernel;
        $this->bundleGenerator = $bundleGenerator;
        $this->ormGenerator = $ormGenerator;
        $this->odmGenerator = $odmGenerator;
        $this->phpcrGenerator = $phpcrGenerator;
        $this->serializerGenerator = $serializerGenerator;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sonata:easy-extends:generate')
            ->setHelp(<<<'EOT'
The <info>easy-extends:generate:entities</info> command generating a valid bundle structure from a Vendor Bundle.

  <info>ie: ./app/console sonata:easy-extends:generate SonataUserBundle</info>
EOT
            );

        $this->setDescription('Create entities used by Sonata\'s bundles');

        $this->addArgument('bundle', InputArgument::IS_ARRAY, 'The bundle name to "easy-extends"');
        $this->addOption('dest', 'd', InputOption::VALUE_OPTIONAL, 'The base folder where the Application will be created', false);
        $this->addOption('namespace', 'ns', InputOption::VALUE_OPTIONAL, 'The namespace for the classes', false);
        $this->addOption('namespace_prefix', 'nsp', InputOption::VALUE_OPTIONAL, 'The namespace prefix for the classes', false);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $destOption = $input->getOption('dest');
        if ($destOption) {
            $dest = realpath($destOption);
            if (false === $dest) {
                throw new \RuntimeException(sprintf('The provided destination folder \'%s\' does not exist!', $destOption));
            }
        } else {
            $dest = $this->getKernel()->getRootDir();
        }

        $namespace = $input->getOption('namespace');
        if ($namespace) {
            if (!preg_match('/^(?:(?:[[:alnum:]]+|:vendor)\\\\?)+$/', $namespace)) {
                throw new \InvalidArgumentException('The provided namespace \'%s\' is not a valid namespace!', $namespace);
            }
        } else {
            $namespace = 'Application\:vendor';
        }

        $configuration = [
            'application_dir' => sprintf('%s%s%s', $dest, \DIRECTORY_SEPARATOR, str_replace('\\', \DIRECTORY_SEPARATOR, $namespace)),
            'namespace' => $namespace,
            'namespace_prefix' => '',
        ];

        if ($namespacePrefix = $input->getOption('namespace_prefix')) {
            $configuration['namespace_prefix'] = rtrim($namespacePrefix, '\\').'\\';
        }

        $bundleNames = $input->getArgument('bundle');

        if (empty($bundleNames)) {
            $output->writeln('');
            $output->writeln('<error>You must provide a bundle name!</error>');
            $output->writeln('');
            $output->writeln('  Bundles availables :');
            /** @var BundleInterface $bundle */
            foreach ($this->getKernel()->getBundles() as $bundle) {
                $bundleMetadata = new BundleMetadata($bundle, $configuration);

                if (!$bundleMetadata->isExtendable()) {
                    continue;
                }

                $output->writeln(sprintf('     - %s', $bundle->getName()));
            }

            $output->writeln('');
        } else {
            foreach ($bundleNames as $bundleName) {
                $processed = $this->generate($bundleName, $configuration, $output);

                if (!$processed) {
                    throw new \RuntimeException(sprintf(
                        '<error>The bundle \'%s\' does not exist or is not registered in the kernel!</error>',
                        $bundleName
                    ));
                }
            }
        }

        $output->writeln('done!');

        return 0;
    }

    /**
     * Generates a bundle entities from a bundle name.
     *
     * @param string          $bundleName
     * @param array           $configuration
     * @param OutputInterface $output
     *
     * @return bool
     */
    protected function generate($bundleName, array $configuration, $output)
    {
        $processed = false;

        /** @var BundleInterface $bundle */
        foreach ($this->getKernel()->getBundles() as $bundle) {
            if ($bundle->getName() !== $bundleName) {
                continue;
            }

            $processed = true;
            $bundleMetadata = new BundleMetadata($bundle, $configuration);

            // generate the bundle file.
            if (!$bundleMetadata->isExtendable()) {
                $output->writeln(sprintf('Ignoring bundle : "<comment>%s</comment>"', $bundleMetadata->getClass()));

                continue;
            }

            // generate the bundle file
            if (!$bundleMetadata->isValid()) {
                $output->writeln(sprintf(
                    '%s : <comment>wrong directory structure</comment>',
                    $bundleMetadata->getClass()
                ));

                continue;
            }

            $output->writeln(sprintf('Processing bundle : "<info>%s</info>"', $bundleMetadata->getName()));

            $this->getBundleGenerator()
                ->generate($output, $bundleMetadata);

            $output->writeln(sprintf('Processing Doctrine ORM : "<info>%s</info>"', $bundleMetadata->getName()));
            $this->getOrmGenerator()
                ->generate($output, $bundleMetadata);

            $output->writeln(sprintf('Processing Doctrine ODM : "<info>%s</info>"', $bundleMetadata->getName()));
            $this->getOdmGenerator()
                ->generate($output, $bundleMetadata);

            $output->writeln(sprintf('Processing Doctrine PHPCR : "<info>%s</info>"', $bundleMetadata->getName()));
            $this->getPHPCRGenerator()
                ->generate($output, $bundleMetadata);

            $output->writeln(sprintf('Processing Serializer config : "<info>%s</info>"', $bundleMetadata->getName()));
            $this->getSerializerGenerator()
                ->generate($output, $bundleMetadata);

            $output->writeln('');
        }

        return $processed;
    }

    private function getKernel(): KernelInterface
    {
        if (null === $this->kernel) {
            return $this->getContainer()->get('kernel');
        }

        return $this->kernel;
    }

    private function getBundleGenerator(): BundleGenerator
    {
        if (null === $this->bundleGenerator) {
            return $this->getContainer()->get('sonata.easy_extends.generator.bundle');
        }

        return $this->bundleGenerator;
    }

    private function getOrmGenerator(): OrmGenerator
    {
        if (null === $this->ormGenerator) {
            return $this->getContainer()->get('sonata.easy_extends.generator.orm');
        }

        return $this->ormGenerator;
    }

    private function getOdmGenerator(): OdmGenerator
    {
        if (null === $this->odmGenerator) {
            return $this->getContainer()->get('sonata.easy_extends.generator.odm');
        }

        return $this->odmGenerator;
    }

    private function getPHPCRGenerator(): PHPCRGenerator
    {
        if (null === $this->phpcrGenerator) {
            return $this->getContainer()->get('sonata.easy_extends.generator.phpcr');
        }

        return $this->phpcrGenerator;
    }

    private function getSerializerGenerator(): SerializerGenerator
    {
        if (null === $this->serializerGenerator) {
            return $this->getContainer()->get('sonata.easy_extends.generator.serializer');
        }

        return $this->serializerGenerator;
    }
}
