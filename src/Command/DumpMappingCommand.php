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

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\Tools\Export\ClassMetadataExporter;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generate Application entities from bundle entities.
 *
 * NEXT_MAJOR: stop extending ContainerAwareCommand.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class DumpMappingCommand extends ContainerAwareCommand
{
    /**
     * @var Registry|null
     */
    private $registry;

    public function __construct(string $name = null, Registry $registry = null)
    {
        parent::__construct($name);

        if (null === $registry) {
            @trigger_error(sprintf(
                'Not providing a registry to "%s" is deprecated since 2.x and will no longer be possible in 3.0',
                \get_class($this)
            ), E_USER_DEPRECATED);
        }

        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('sonata:easy-extends:dump-mapping');
        $this->setDescription('Dump some mapping information (debug only)');

        $this->addArgument('manager', InputArgument::OPTIONAL, 'The manager name to use', false);
        $this->addArgument('model', InputArgument::OPTIONAL, 'The class to dump', false);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $factory = $this->getDoctrineRegistry()->getManager($input->getArgument('manager'))->getMetadataFactory();

        $metadata = $factory->getMetadataFor($input->getArgument('model'));

        $cme = new ClassMetadataExporter();
        $exporter = $cme->getExporter('php');

        $output->writeln($exporter->exportClassMetadata($metadata));
        $output->writeln('Done!');

        return 0;
    }

    private function getDoctrineRegistry(): Registry
    {
        if (null === $this->registry) {
            return $this->getContainer()->get('doctrine');
        }

        return $this->registry;
    }
}
