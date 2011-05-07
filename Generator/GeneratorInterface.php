<?php
/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\EasyExtendsBundle\Generator;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Util\Mustache;

use Sonata\EasyExtendsBundle\Bundle\BundleMetadata;

interface GeneratorInterface
{

    /**
     * @abstract
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Sonata\EasyExtendsBundle\Bundle\BundleMetadata $bundleMetadata
     * @return void
     */
    function generate(OutputInterface $output, BundleMetadata $bundleMetadata);
}