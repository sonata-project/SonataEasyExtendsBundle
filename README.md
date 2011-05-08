Prototype to easily share entities accross Bundle and Application
-----------------------------------------------------------------

EasyExtendsBundle is a prototype for generating a valid bundle structure from
a Vendor Bundle. The tools is started with simple command line: ``easy-extends:generate``.

The command will generates:
  - All required directories for one bundle (Controller, config, doctrine, views, ...)
  - The mapping and entity files from those defined in the CPB. The SuperClass must be prefixed by BaseXXXXXX.
  - The table name from the bundle name + entity name. (blog__post, where blog is the BlogBundle and Post the entity name)


Installation
============

  - Add EasyExtendsBundle to your src/Bundle dir

        git submodule add git@github.com:sonata-project/EasyExtendsBundle.git src/Sonata/EasyExtendsBundle

  - Add EasyExtendsBundle to your application kernel

        // app/AppKernel.php
        public function registerBundles()
        {
            return array(
                // ...
                new Sonata\EasyExtendsBundle\EasyExtendsBundle(),
                // ...
            );
        }
