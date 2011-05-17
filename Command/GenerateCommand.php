<?php
/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\EasyExtendsBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Bundle\DoctrineBundle\Command\DoctrineCommand;

use Symfony\Bundle\FrameworkBundle\Util\Mustache;

use Sonata\EasyExtendsBundle\Bundle\BundleMetadata;

/**
 * Generate Application entities from bundle entities
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class GenerateCommand extends DoctrineCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('sonata:easy-extends:generate')
            ->setHelp(<<<EOT
The <info>easy-extends:generate:entities</info> command generates a set of Entities
in your Application Entity folder from the Entities set in bundles. This command
will allow to generate create custom code from the model.

  <info>./symfony easy-extends:generate SonataUserBundle</info>
EOT
        );

        $this->addArgument('bundle', InputArgument::REQUIRED, 'The bundle name to "easy-extends"');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // find a better way to detect the Application folder
        $application_dir = sprintf(
            "%s/Application",
            $this->container->get('kernel')->getRootDir()
        );

        $configuration = array(
            'application_dir' => $application_dir
        );

        foreach ($this->container->get('kernel')->getBundles() as $bundle) {

            if ($bundle->getName() != $input->getArgument('bundle')) {
                continue;
            }

            $bundleMetadata = new BundleMetadata($bundle, $configuration);

            // generate the bundle file
            if(!$bundleMetadata->isExtendable()) {
                $output->writeln(sprintf('Ignoring bundle : "<comment>%s</comment>"', $bundleMetadata->getClass()));
                continue;
            }

            // generate the bundle file
            if(!$bundleMetadata->isValid()) {
                $output->writeln(sprintf('%s : <comment>wrong folder structure</comment>', $bundleMetadata->getClass()));
                continue;
            }

            $output->writeln(sprintf('Processing bundle : "<info>%s</info>"', $bundleMetadata->getName()));

            $this->container->get('sonata.easy_extends.generator.bundle')
                ->generate($output, $bundleMetadata);

            $output->writeln(sprintf('Processing orm : "<info>%s</info>"', $bundleMetadata->getName()));
            $this->container->get('sonata.easy_extends.generator.orm')
                ->generate($output, $bundleMetadata);

            $output->writeln(sprintf('Processing odm : "<info>%s</info>"', $bundleMetadata->getName()));
            $this->container->get('sonata.easy_extends.generator.odm')
                ->generate($output, $bundleMetadata);

            $output->writeln('');
        }

        $output->writeln('done!');

    }
}