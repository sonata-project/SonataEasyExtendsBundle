Installation
============

To begin, add the dependent bundles::

    php composer.phar require sonata-project/easy-extends-bundle

Next, be sure to enable the new bundles in your application kernel:

.. code-block:: php

  <?php
  // app/appkernel.php
  public function registerBundles()
  {
      return array(
          // ...
          new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
          // ...
      );
  }