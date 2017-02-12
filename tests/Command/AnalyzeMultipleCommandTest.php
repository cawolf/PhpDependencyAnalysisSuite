<?php

namespace Cawolf\PhpDependencyAnalysisSuite\Command;

use Cawolf\PhpDependencyAnalysisSuite\Application\ConfigurationReader;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Tester\CommandTester;

class AnalyzeMultipleCommandTest extends \PHPUnit_Framework_TestCase
{
    /** @var ObjectProphecy|ConfigurationReader */
    private $configReader;

    /** @var AnalyzeMultipleCommand */
    private $command;

    /** @var CommandTester */
    private $commandTester;

    /** @inheritdoc */
    public function setUp()
    {
        $this->configReader = $this->prophesize(ConfigurationReader::class);
        $this->command = new AnalyzeMultipleCommand($this->configReader->reveal());
        $this->commandTester = new CommandTester($this->command);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Not enough arguments (missing: "configurationFile").
     */
    public function testMissingArguments()
    {
        $this->commandTester->execute([]);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage namespaceDepthStart must be greater than 0
     */
    public function testNamespaceDepthStartBelowOne()
    {
        $this->commandTester->execute(['configurationFile' => '/some/path', 'namespaceDepthStart' => -1]);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage namespaceDepthEnd must be greater than namespaceDepthStart
     */
    public function testNamespaceDepthEndGreaterThanNamespaceDepthStart()
    {
        $this->commandTester->execute(['configurationFile' => '/some/path', 'namespaceDepthStart' => 2, 'namespaceDepthEnd' => 1]);
    }

    public function test()
    {
        self::assertTrue(true);
    }
}
