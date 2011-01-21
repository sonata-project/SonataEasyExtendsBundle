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

  <info>./symfony easy-extends:generate</info>
EOT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // find a better way to detect the Application folder
        $application_dir = sprintf(
            "%s/../src/Application",
            $this->container->get('kernel')->getRootDir()
        );

        $configuration = array(
            'application_dir' => $application_dir
        );

        foreach ($this->container->get('kernel')->getBundles() as $bundle) {

            $bundle_metadata = new BundleMetadata($bundle, $configuration);

            // generate the bundle file
            if(!$bundle_metadata->isExtendable()) {
                $output->writeln(sprintf('Ignoring bundle : "<comment>%s</comment>"', $bundle_metadata->getClass()));
                continue;
            }

            // generate the bundle file
            if(!$bundle_metadata->isValid()) {
                $output->writeln(sprintf('%s : <comment>wrong folder structure</comment>', $bundle_metadata->getClass()));
                
                continue;
            }

            $output->writeln(sprintf('Processing bundle : "<info>%s</info>"', $bundle_metadata->getName()));

            $this->generateBundleDirectory($output, $bundle_metadata);

            $this->generateBundleFile($output, $bundle_metadata);

            $this->generateMappingEntityFiles($output, $bundle_metadata);

            $this->generateEntityFiles($output, $bundle_metadata);

            $this->generateEntityRepositoryFiles($output, $bundle_metadata);
            $output->writeln('');
        }

        $output->writeln('done!');
        
    }

    public function generateBundleDirectory(OutputInterface $output, BundleMetadata $bundle_metadata)
    {

        $directories = array(
            '',
            'Resources/config/doctrine/metadata/orm',
            'Resources/config/routing',
            'Resources/views',
            'Entity',
            'Controller'
        );

        foreach($directories as $directory) {
            $dir = sprintf('%s/%s', $bundle_metadata->getExtendedDirectory(), $directory);
            if(!is_dir($dir)) {
                $output->writeln(sprintf('  > generating bundle directory <comment>%s</comment>', $dir));
                mkdir($dir, 0755, true);
            }
        }
    }

    public function generateBundleFile(OutputInterface $output, BundleMetadata $bundle_metadata)
    {
        $file = sprintf('%s/Application%s.php', $bundle_metadata->getExtendedDirectory(), $bundle_metadata->getName());

        if(is_file($file)) {
            return;
        }

        $output->writeln(sprintf('  > generating bundle file <comment>%s</comment>', $file));

        $string = Mustache::renderString($this->getBundleTemplate(), array(
            'bundle'    => $bundle_metadata->getName(),
            'namespace' => $bundle_metadata->getExtendedNamespace(),
        ));

        file_put_contents($file, $string);
    }

    public function generateMappingEntityFiles($output, BundleMetadata $bundle_metadata)
    {

        $output->writeln(' - Copy entity files');

        $files = $bundle_metadata->getEntityMappingFiles();

        foreach ($files as $file) {

            // copy mapping definition
            $dest_file  = sprintf('%s/%s', $bundle_metadata->getExtendedMappingEntityDirectory(), $file->getFileName());
            $src_file   = sprintf('%s/%s', $bundle_metadata->getMappingEntityDirectory(), $file->getFileName());

            if(is_file($dest_file)) {
                $output->writeln(sprintf('   ~ <info>%s</info>', $file->getFileName()));
            } else {
                $output->writeln(sprintf('   + <info>%s</info>', $file->getFileName()));
                copy($src_file, $dest_file);
            }
        }
    }

    public function generateEntityFiles(OutputInterface $output, BundleMetadata $bundle_metadata)
    {
        $output->writeln(' - Generating entity files');

        $names = $bundle_metadata->getEntityNames();

        foreach ($names as $name) {

            $extended_name = $name;

            $dest_file  = sprintf('%s/%s.php', $bundle_metadata->getExtendedEntityDirectory(), $name);
            $src_file = sprintf('%s/%s.php', $bundle_metadata->getEntityDirectory(), $extended_name);

            if(!is_file($src_file)) {
                $extended_name = 'Base'.$name;
                $src_file = sprintf('%s/%s.php', $bundle_metadata->getEntityDirectory(), $extended_name);

                if(!is_file($src_file)) {
                    $output->writeln(sprintf('   ! <info>%s</info>', $extended_name));

                    continue;
                }
            }

            if(is_file($dest_file)) {
                $output->writeln(sprintf('   ~ <info>%s</info>', $name));
            } else {
                $output->writeln(sprintf('   + <info>%s</info>', $name));

                $string = Mustache::renderString($this->getEntityTemplate(), array(
                    'extended_namespace'    => $bundle_metadata->getExtendedNamespace(),
                    'name'                  => $name != $extended_name ? $extended_name : $name,
                    'class'                 => $name,
                    'extended_name'         => $name == $extended_name ? 'Base'.$name : $extended_name,
                    'namespace'             => $bundle_metadata->getNamespace()
                ));

                file_put_contents($dest_file, $string);
            }

        }
    }

    public function generateEntityRepositoryFiles(OutputInterface $output, BundleMetadata $bundle_metadata)
    {
        $output->writeln(' - Generating entity repository files');

        $names = $bundle_metadata->getEntityNames();

        foreach ($names as $name) {

            $dest_file  = sprintf('%s/%sRepository.php', $bundle_metadata->getExtendedEntityDirectory(), $name);
            $src_file   = sprintf('%s/Base%sRepository.php', $bundle_metadata->getEntityDirectory(), $name);

            if(!is_file($src_file)) {
                $output->writeln(sprintf('   ! <info>%sRepository</info>', $name));
                continue;
            }
            
            if(is_file($dest_file)) {
                $output->writeln(sprintf('   ~ <info>%sRepository</info>', $name));
            } else {
                $output->writeln(sprintf('   + <info>%sRepository</info>', $name));

                $string = Mustache::renderString($this->getEntityRepositoryTemplate(), array(
                    'extended_namespace'    => $bundle_metadata->getExtendedNamespace(),
                    'name'                  => $name,
                    'namespace'             => $bundle_metadata->getNamespace()
                ));

                file_put_contents($dest_file, $string);
            }
        }
    }

    
    public function getEntityTemplate()
    {
        return <<<MUSTACHE
<?php
/**
 * This file is part of the <name> project.
 *
 * (c) <yourname> <youremail>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace {{ extended_namespace }}\Entity;

use {{ namespace }}\Entity\{{ name }} as {{ extended_name }};

/**
 * This file has been generated by the EasyExtends bundle ( http://sonata-project.org/easy-extends )
 *
 * References :
 *   working with object : http://www.doctrine-project.org/projects/orm/2.0/docs/reference/working-with-objects/en
 *
 * @author <yourname> <youremail>
 */
class {{ class }} extends {{ extended_name }}
{

    /**
     * @var integer \$id
     */
    protected \$id;

    /**
     * Get id
     *
     * @return integer \$id
     */
    public function getId()
    {
        return \$this->id;
    }

}
MUSTACHE;
        
    }

    public function getEntityRepositoryTemplate()
    {
        return <<<MUSTACHE
<?php
/**
 * This file is part of the <name> project.
 *
 * (c) <yourname> <youremail>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace {{ extended_namespace }}\Entity;

use {{ namespace}}\Entity\Base{{ name }}Repository;

/**
 * This file has been generated by the EasyExtends bundle ( http://sonata-project.org/easy-extends )
 *
 * References :
 *   custom repository : http://www.doctrine-project.org/projects/orm/2.0/docs/reference/working-with-objects/en#querying:custom-repositories
 *   query builder     : http://www.doctrine-project.org/projects/orm/2.0/docs/reference/query-builder/en
 *   dql               : http://www.doctrine-project.org/projects/orm/2.0/docs/reference/dql-doctrine-query-language/en
 *
 * @author <yourname> <youremail>
 */
class {{ name }}Repository extends Base{{ name }}Repository
{

}
MUSTACHE;
    }

    
    public function getBundleTemplate()
    {

        return <<<MUSTACHE
<?php
/**
 * This file is part of the <name> project.
 *
 * (c) <yourname> <youremail>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace {{ namespace }};

use Symfony\Component\HttpKernel\Bundle\Bundle;


/**
 * This file has been generated by the EasyExtends bundle ( http://sonata-project.org/easy-extends )
 *
 * References :
 *   custom repository : http://www.doctrine-project.org/projects/orm/2.0/docs/reference/working-with-objects/en#querying:custom-repositories
 *   query builder     : http://www.doctrine-project.org/projects/orm/2.0/docs/reference/query-builder/en
 *   dql               : http://www.doctrine-project.org/projects/orm/2.0/docs/reference/dql-doctrine-query-language/en
 *
 * @author <yourname> <youremail>
 */
class Application{{ bundle }} extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return '{{ bundle }}';
    }

    /**
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        return __NAMESPACE__;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return strtr(__DIR__, '\\\', '/');
    }
}
MUSTACHE;
        
    }
}