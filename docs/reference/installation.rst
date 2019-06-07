.. index::
    double: Reference; Installation

Installation
============

Download the Bundle
-------------------

.. code-block:: bash

    composer require sonata-project/easy-extends-bundle --dev

Enable the Bundle
-----------------

Then, enable the bundle by adding it to the list of registered bundles
in ``bundles.php`` file of your project::

    // config/bundles.php

    return [
        // ...
        Sonata\EasyExtendsBundle\SonataEasyExtendsBundle::class => ['all' => true],
    ];
