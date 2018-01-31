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

namespace Sonata\EasyExtendsBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use Sonata\AcmeBundle\SonataAcmeBundle;
use Sonata\EasyExtendsBundle\Command\GenerateCommand;
use Sonata\EasyExtendsBundle\Generator\GeneratorInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @author Marko Kunic <kunicmarko20@gmail.com>
 */
final class GenerateCommandTest extends TestCase
{
    /**
     * @dataProvider executeData
     */
    public function testExecute($args): void
    {
        $commandTester = $this->buildCommand($this->mockContainer());
        $commandTester->execute($args);

        $this->assertContains(
            'done!',
            $commandTester->getDisplay()
        );
    }

    public function executeData()
    {
        return [
            [
                [
                    '--dest' => 'src',
                    'bundle' => ['SonataAcmeBundle'],
                ],
            ],
            [
                [
                    '--dest' => 'src',
                    'bundle' => ['SonataAcmeBundle'],
                    '--namespace' => 'Application\\Sonata',
                ],
            ],
            [
                [
                    '--dest' => 'src',
                    'bundle' => ['SonataAcmeBundle'],
                    '--namespace_prefix' => 'App',
                ],
            ],
            [
                [
                    '--dest' => 'src',
                    'bundle' => ['SonataAcmeBundle'],
                    '--namespace' => 'Application\\Sonata',
                    '--namespace_prefix' => 'App',
                ],
            ],
        ];
    }

    public function testExecuteWrongDest(): void
    {
        $commandTester = $this->buildCommand($this->createMock(ContainerInterface::class));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("The provided destination folder 'fakedest' does not exist!");

        $commandTester->execute([
            '--dest' => 'fakedest',
        ]);
    }

    public function testNoArgument(): void
    {
        $commandTester = $this->buildCommand($this->mockContainerWithKernel());

        $commandTester->execute([
            '--dest' => 'src',
        ]);

        $this->assertContains(
            'You must provide a bundle name!',
            $commandTester->getDisplay()
        );
    }

    public function testFakeBundleName(): void
    {
        $commandTester = $this->buildCommand($this->mockContainerWithKernel());

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("The bundle 'FakeBundle' does not exist or is not registered in the kernel!");

        $commandTester->execute([
            '--dest' => 'src',
            'bundle' => ['FakeBundle'],
        ]);

        $this->assertContains(
            'You must provide a bundle name!',
            $commandTester->getDisplay()
        );
    }

    public function testNotExtendableBundle(): void
    {
        $commandTester = $this->buildCommand($this->mockContainerWithKernel(new \Symfony\Bundle\NotExtendableBundle()));

        $commandTester->execute([
            '--dest' => 'src',
            'bundle' => ['NotExtendableBundle'],
        ]);

        $this->assertContains(
            sprintf('Ignoring bundle : "Symfony\Bundle\NotExtendableBundle"'),
            $commandTester->getDisplay()
        );
    }

    public function testInvalidFolderStructure(): void
    {
        $commandTester = $this->buildCommand(
            $this->mockContainerWithKernel(new \Application\Sonata\NotExtendableBundle())
        );

        $commandTester->execute([
            '--dest' => 'src',
            'bundle' => ['NotExtendableBundle'],
        ]);

        $this->assertContains(
            sprintf('Application\Sonata\NotExtendableBundle : wrong directory structure'),
            $commandTester->getDisplay()
        );
    }

    private function buildCommand($container)
    {
        $command = new GenerateCommand();
        $command->setContainer($container);

        return new CommandTester($command);
    }

    private function mockContainer()
    {
        $containerMock = $this->mockContainerWithKernel();

        $containerMock->expects($this->exactly(6))
            ->method('get')
            ->willReturn($this->mockGenerator());

        return $containerMock;
    }

    private function mockContainerWithKernel($kernelReturnValue = null)
    {
        $containerMock = $this->createMock(ContainerInterface::class);

        $containerMock->expects($this->at(0))
            ->method('get')
            ->with('kernel')
            ->willReturn($this->mockKernel($kernelReturnValue));

        return $containerMock;
    }

    private function mockKernel($returnValue)
    {
        $kernelMock = $this->createMock(KernelInterface::class);

        $kernelMock->expects($this->once())
            ->method('getBundles')
            ->willReturn([
                $returnValue ?: new SonataAcmeBundle(),
            ]);

        return $kernelMock;
    }

    private function mockGenerator()
    {
        $generatorMock = $this->createMock(GeneratorInterface::class);

        $generatorMock->expects($this->exactly(5))
            ->method('generate');

        return $generatorMock;
    }
}
