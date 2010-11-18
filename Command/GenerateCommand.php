<?php
/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Bundle\EasyExtendsBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Bundle\DoctrineBundle\Command\DoctrineCommand;

use Symfony\Bundle\FrameworkBundle\Util\Mustache;

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
            ->setName('easy-extends:generate')
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
            $this->container->getKernelService()->getRootDir()
        );

        foreach ($this->container->getKernelService()->getBundles() as $bundle) {

            // retrieve the full bundle classname
            $class = $bundle->getReflection()->getName();


            // does not extends Application bundle ...
            if(strpos($class, 'Application') === 0 || strpos($class, 'Symfony') === 0) {
                $output->writeln(sprintf('Ignoring bundle : "<comment>%s</comment>"', $class));
                continue;
            }

            // generate the bundle file
            $this->generateBundleDirectory($output, $bundle, $application_dir);
            $this->generateBundleFile($output, $bundle, $application_dir);

            // transform classname to a path and substract it to get the destination
            $path = dirname(str_replace('\\', '/', $class));
            $destination = str_replace('/'.$path, "", $bundle->getPath());

            if ($metadatas = $this->getBundleMetadatas($bundle)) {

                $this->generateEntityFiles($output, $bundle, $metadatas, $application_dir);
                $this->generateEntityRepositoryFiles($output, $bundle, $metadatas, $application_dir);
                $this->generateMetadataFiles($output, $bundle, $metadatas, $application_dir);
            }
        }
        
    }

    public function generateBundleDirectory($output, $bundle, $application_dir)
    {
        $dir = sprintf('%s/%s', $application_dir, $bundle->getName());

        if(is_dir($dir))
        {
            return;
        }

        $output->writeln(sprintf('  > generating bundle directory <comment>%s</comment>', $dir));
        
        mkdir($dir, 0755, true);
        mkdir($dir.'/Resources/config/doctrine/metadata/orm', 0755, true);
        mkdir($dir.'/Resources/config/routing', 0755, true);
        mkdir($dir.'/Resources/view', 0755, true);
        mkdir($dir.'/Entity', 0755, true);
        mkdir($dir.'/Controller', 0755, true);
    }

    public function generateBundleFile($output, $bundle, $application_dir)
    {
        $file = sprintf('%s/%s/%s.php', $application_dir, $bundle->getName(), $bundle->getName());

        if(is_file($file))
        {
            return;
        }

        $output->writeln(sprintf('  > generating bundle file <comment>%s</comment>', $file));

        $string = Mustache::renderString($this->getBundleTemplate(), array(
            'bundle'    => $bundle->getName(),
        ));

        file_put_contents($file, $string);
    }

    public function generateEntityFiles($output, $bundle, $metadatas, $application_dir)
    {
        $output->writeln(sprintf('Generating entity files for "<info>%s</info>"', $bundle->getName()));

        foreach ($metadatas as $metadata) {
            $class = substr($metadata->name, strripos($metadata->name, '\\') + 1);

            $ns = $bundle->getNamespacePrefix().'\\'.$bundle->getName().'\\Entity';

            // metadata loader is broken, load all entities
            if(strpos($metadata->name, $ns) === false)
            {
                continue;
            }

            // only extends Base class
            if(strpos($class, "Base" ) !== 0 && substr($class, 0, -10) !== "Repository") {
                continue;
            }

            $class = substr($class, 4);
            $file = sprintf("%s/%s/Entity/%s.php",
                $application_dir,
                $bundle->getName(),
                $class
            );

            if(is_file($file)) {
                continue;
            }

            $output->writeln(sprintf('  > generating entity <comment>%s</comment>', $class));

            $string = Mustache::renderString($this->getEntityTemplate(), array(
                'bundle'    => $bundle->getName(),
                'class'     => $class,
                'extends'   => $metadata->name
            ));

            file_put_contents($file, $string);
        }
    }

    public function generateEntityRepositoryFiles($output, $bundle, $metadatas, $application_dir)
    {
        $output->writeln(sprintf('Generating entity repository files for "<info>%s</info>"', $bundle->getName()));

        foreach ($metadatas as $metadata) {
            $class = substr($metadata->name, strripos($metadata->name, '\\') + 1).'Repository';

            $ns = $bundle->getNamespacePrefix().'\\'.$bundle->getName().'\\Entity';

            $repository_file = sprintf('%s/Entity/%s.php', $bundle->getPath(), $class);

            // metadata loader is broken, load all entities
            if(strpos($metadata->name, $ns) === false)
            {
                continue;
            }

            if(!is_file($repository_file)) {
                $output->writeln(sprintf('  > file <comment>%s</comment> does not exist', $class));
                continue;    
            }

            
            // only extends Base class
            if(strpos($class, "Base") !== 0 && substr($class, 0, -10) === "Repository") {
                continue;
            }

            $class = substr($class, 4);

            $file = sprintf("%s/%s/Entity/%s.php",
                $application_dir,
                $bundle->getName(),
                $class
            );

            if(is_file($file)) {
                continue;
            }

            $output->writeln(sprintf('  > generating <comment>%s</comment>', $class));

            $string = Mustache::renderString($this->getEntityRepositoryTemplate(), array(
                'bundle'    => $bundle->getName(),
                'class'     => $class,
                'extends'   => $metadata->name
            ));

            file_put_contents($file, $string);
        }
    }

    public function hasRepositoryClass($bundle, $metadata)
    {

        $class = substr($metadata->name, strripos($metadata->name, '\\') + 1).'Repository';

        $repository_file = sprintf('%s/Entity/%s.php', $bundle->getPath(), $class);
        
        if(!is_file($repository_file)) {

            return false;
        }

        return true;
    }
    
    public function generateMetadataFiles($output, $bundle, $metadatas, $application_dir)
    {
        $output->writeln(sprintf('Generating metadata files for "<info>%s</info>"', $bundle->getName()));

        foreach ($metadatas as $metadata) {
            $class = substr($metadata->name, strripos($metadata->name, '\\') + 1);

            $ns = $bundle->getNamespacePrefix().'\\'.$bundle->getName().'\\Entity';

            // metadata loader is broken, load all entities
            if(strpos($metadata->name, $ns) === false)
            {
                continue;
            }
            
            // only extends Base class
            if(strpos($class, "Base" ) !== 0) {
                continue;
            }

            $class = substr($class, 4);
            $file = sprintf("%s/%s/Resources/config/doctrine/metadata/orm/Application.%s.Entity.%s.dcm.xml",
                $application_dir,
                $bundle->getName(),
                $bundle->getName(),
                $class
            );

            if(is_file($file)) {
                continue;
            }

            if($this->hasRepositoryClass($bundle, $metadata)) {
                $repository = sprintf('Application\\%s\\Entity\\%sRepository', $bundle->getName(), $class);
            } else {
                $repository = 'Doctrine\\ORM\\EntityRepository';
            }

            $output->writeln(sprintf('  > generating metadata <comment>Entity.%s.dcm.xml</comment>', $class));

            $string = Mustache::renderString($this->getMetadataTemplate(), array(
                'bundle'        => $bundle->getName(),
                'class'         => $class,
                'table'         => \Doctrine\Common\Util\Inflector::tableize(str_replace("Bundle", "", $bundle->getName()).'_'.$class),
                'repository'    => $repository
            ));

            file_put_contents($file, $string);
        }
    }
    
    public function getEntityTemplate()
    {
        return '<?php
/**
 * This file is part of the <name> project.
 *
 * (c) <yourname> <youremail>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\{{ bundle }}\Entity;

/**
 * This file has been generated by the EasyExtends bundle ( http://sonata-project.org/easy-extends )
 *
 * References :
 *   working with object : http://www.doctrine-project.org/projects/orm/2.0/docs/reference/working-with-objects/en
 *
 * @author <yourname> <youremail>
 */
class {{ class }} extends \{{ extends }} {

    /**
     * @var integer $id
     */
    private $id;

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

}';
    }

    public function getEntityRepositoryTemplate()
    {
        return '<?php
/**
 * This file is part of the <name> project.
 *
 * (c) <yourname> <youremail>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\{{ bundle }}\Entity;

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
class {{ class }} extends \{{ extends }}Repository {

}';
    }

    
    public function getBundleTemplate()
    {

        return '<?php
/**
 * This file is part of the <name> project.
 *
 * (c) <yourname> <youremail>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\{{ bundle }};

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
class {{ bundle }} extends Bundle {

}';
        
    }

    public function getMetadataTemplate()
    {
        return '<?xml version="1.0" encoding="utf-8"?>
<doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping" xsi="http://www.w3.org/2001/XMLSchema-instance" schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">
    <!--
         This file has been generated by the EasyExtends bundle ( http://sonata-project.org/easy-extends )

         References :
            xsd                  : https://github.com/doctrine/doctrine2/blob/master/doctrine-mapping.xsd
            xml mapping          : http://www.doctrine-project.org/projects/orm/2.0/docs/reference/xml-mapping/en
            association mapping  : http://www.doctrine-project.org/projects/orm/2.0/docs/reference/association-mapping/en
    -->
    <entity
        name="Application\{{ bundle }}\Entity\{{ class }}"
        table="{{ table }}"
        repository-class="{{ repository }}">

        <id name="id" type="integer" column="id">
            <generator strategy="AUTO"/>
        </id>

    </entity>
</doctrine-mapping>';

    }

}