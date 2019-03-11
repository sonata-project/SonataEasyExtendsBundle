UPGRADE 2.x
===========

## Deprecated commands that get the dependencies from the container

Any command that extends `Sonata\CacheBundle\Command\DumpMappingCommand` or
`Sonata\CacheBundle\Command\DumpMappingCommand` should all dependencies as a 
constructor parameter.
This should be done automatically in applications that have
autowiring enabled for such commands.

### Deprecated

Generated Bundles no longer use Bundle inheritance, because Symfony dropped the support for this in 3.4+ [symfony blog](https://symfony.com/blog/new-in-symfony-3-4-deprecated-bundle-inheritance)
If You really need it, you can add it yourself.

UPGRADE FROM 2.2 to 2.3
=======================

### Tests

All files under the ``Tests`` directory are now correctly handled as internal test classes. 
You can't extend them anymore, because they are only loaded when running internal tests. 
More information can be found in the [composer docs](https://getcomposer.org/doc/04-schema.md#autoload-dev).
