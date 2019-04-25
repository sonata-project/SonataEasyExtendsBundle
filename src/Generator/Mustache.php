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

namespace Sonata\EasyExtendsBundle\Generator;

class Mustache
{
    /**
     * @param string $string
     * @param array  $parameters
     *
     * @return string
     */
    public static function replace(string $string, array $parameters): string
    {
        $replacer = static function ($match) use ($parameters) {
            return $parameters[$match[1]] ?? $match[0];
        };

        return preg_replace_callback('/{{\s*(.+?)\s*}}/', $replacer, $string);
    }

    /**
     * @param string $file
     * @param array  $parameters
     *
     * @return string
     */
    public static function replaceFromFile(string $file, array $parameters): string
    {
        return self::replace(file_get_contents($file), $parameters);
    }
}
