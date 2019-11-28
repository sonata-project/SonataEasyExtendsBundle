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

namespace Sonata\EasyExtendsBundle\Tests\Command\Functional;

use Sonata\EasyExtendsBundle\Tests\Command\Functional\Entity\Block;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class DumpMappingCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);
        $command = $application->find('sonata:easy-extends:dump-mapping');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'manager' => 'default',
            'model' => Block::class,
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('<?php', $output);
        $this->assertStringContainsString('Block', $output);
    }

    protected static function getKernelClass()
    {
        return AppKernel::class;
    }
}
