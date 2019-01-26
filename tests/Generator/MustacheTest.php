<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\EasyExtendsBundle\Tests\Generator;

use PHPUnit\Framework\TestCase;
use Sonata\EasyExtendsBundle\Generator\Mustache;

class MustacheTest extends TestCase
{
    public function testMustache(): void
    {
        $this->assertSame(Mustache::replace(' Hello {{ world }}', [
          'world' => 'world',
        ]), ' Hello world');

        $this->assertSame(Mustache::replace(' Hello {{world}}', [
          'world' => 'world',
        ]), ' Hello world');

        $this->assertSame(Mustache::replace(' Hello {{ world }}', [
          'no-world' => 'world',
        ]), ' Hello {{ world }}');

        $file = sprintf('%s/../fixtures/test.mustache', __DIR__);
        $this->assertSame(Mustache::replaceFromFile($file, [
          'world' => 'world',
        ]), 'Hello world');
    }
}
