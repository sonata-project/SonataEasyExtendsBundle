Installation
============

  - Add SonataEasyExtendsBundle to your src/Bundle dir

        git submodule add git://github.com/sonata-project/SonataEasyExtendsBundle.git vendor/bundles/Sonata/EasyExtendsBundle

  - Add SonataEasyExtendsBundle to your application kernel

        // app/AppKernel.php
        public function registerBundles()
        {
            return array(
                // ...
                new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
                // ...
            );
        }