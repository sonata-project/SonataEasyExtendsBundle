Installation
============

To begin, add the dependent bundles to the ``vendor/bundles`` directory. Add
the following lines to the file ``deps``::

  [SonataEasyExtendsBundle]
      git=git://github.com/sonata-project/SonataEasyExtendsBundle.git
      target=/bundles/Sonata/EasyExtendsBundle

and run::

  bin/vendors install

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