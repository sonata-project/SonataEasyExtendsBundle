Prototype to easily share entities accross Bundle and Application

## Installation

### Add EasyExtendsBundle to your src/Bundle dir

    git submodule add git@github.com:sonata-project/EasyExtendsBundle.git src/Bundle/Sonata/EasyExtendsBundle

### Add EasyExtendsBundle to your application kernel

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Bundle\Sonata\EasyExtendsBundle\EasyExtendsBundle(),
            // ...
        );
    }


### Add this line into your config.yml file 

    easy_extends.config: ~
