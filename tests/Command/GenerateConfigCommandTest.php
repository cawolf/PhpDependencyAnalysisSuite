<?php

namespace Cawolf\PhpDependencyAnalysisSuite\Tests\Command;

use Cawolf\PhpDependencyAnalysisSuite\Command\GenerateConfigCommand;
use Prophecy\Argument;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Yaml;

class GenerateConfigCommandTest extends \PHPUnit_Framework_TestCase
{
    /** @var GenerateConfigCommand */
    private $command;

    /** @var CommandTester */
    private $commandTester;

    /** @inheritdoc */
    public function setUp()
    {
        $this->command = new GenerateConfigCommand();
        $this->commandTester = new CommandTester($this->command);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Not enough arguments (missing: "source, target, configuration").
     */
    public function testMissingArguments()
    {
        $this->commandTester->execute([], []);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Not enough arguments (missing: "source").
     */
    public function testMissingSource()
    {
        $this->commandTester->execute(['target' => 't', 'configuration' => 'c'], []);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Not enough arguments (missing: "target").
     */
    public function testMissingTarget()
    {
        $this->commandTester->execute(['source' => 's', 'configuration' => 'c'], []);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Not enough arguments (missing: "configuration").
     */
    public function testMissingConfiguration()
    {
        $this->commandTester->execute(['source' => 's', 'target' => 't'], []);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The "mode" must be one of: usage, call, inheritance
     */
    public function testInvalidMode()
    {
        $this->commandTester->execute(
            ['source' => 's', 'target' => 't', 'configuration' => 'c', '--mode' => 'invalid']
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The "format" must be one of: json, svg, html, script
     */
    public function testInvalidFormat()
    {
        $this->commandTester->execute(
            ['source' => 's', 'target' => 't', 'configuration' => 'c', '--format' => 'invalid']
        );
    }

    public function testFileNotWritable()
    {
        $lockedFilePath = sys_get_temp_dir() . '/phpda.yml';
        new \SplFileObject($lockedFilePath, 'w');
        chmod($lockedFilePath, 0000);
        try {
            $this->commandTester->execute(
                ['source' => 's', 'target' => 't', 'configuration' => $lockedFilePath]
            );
        } catch (\Exception $e) {
            $this->assertEquals(
                sprintf('SplFileObject::__construct(%s): failed to open stream: Permission denied', $lockedFilePath),
                $e->getMessage()
            );
        }
        chmod($lockedFilePath, 0777);
        unlink($lockedFilePath);
    }

    public function test()
    {
        $filename = sys_get_temp_dir() . '/phpda.yml';
        $this->commandTester->execute([
            'source' => 's',
            'target' => 't',
            'configuration' => $filename,
            '--format' => 'svg',
            '--mode' => 'call',
            '--ignore' => ['ignore1', 'ignore2'],
            '--group-length' => 4
        ]);
        $this->assertEquals(
            sprintf("Configuration generated and written to \"%s\".\n", $filename),
            $this->commandTester->getDisplay()
        );
        $this->assertFileExists($filename);
        $parsed = Yaml::parse(file_get_contents($filename));
        unlink($filename);
        $this->assertEquals([
            'mode' => 'call',
            'source' => 's',
            'filePattern' => '*.php',
            'ignore' => ['ignore1', 'ignore2'],
            'formatter' => 'PhpDA\Writer\Strategy\Svg',
            'target' => 't',
            'groupLength' => 4,
            'visitor' => [
                'PhpDA\Parser\Visitor\TagCollector',
                'PhpDA\Parser\Visitor\SuperglobalCollector'
            ],
            'visitorOptions' => [
                'PhpDA\Parser\Visitor\Required\DeclaredNamespaceCollector' => [
                    'minDepth' => 2,
                    'sliceLength' => 5
                ],
                'PhpDA\Parser\Visitor\Required\MetaNamespaceCollector' => [
                    'minDepth' => 2,
                    'sliceLength' => 5
                ],
                'PhpDA\Parser\Visitor\Required\UsedNamespaceCollector' => [
                    'minDepth' => 2,
                    'sliceLength' => 5
                ],
                'PhpDA\Parser\Visitor\TagCollector' => [
                    'minDepth' => 2,
                    'sliceLength' => 5
                ]
            ]
        ], $parsed);
    }
}
