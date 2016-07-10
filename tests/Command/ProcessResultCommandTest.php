<?php

namespace Cawolf\PhpDependencyAnalysisSuite\Tests\Command;

use Cawolf\PhpDependencyAnalysisSuite\Command\ProcessResultCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class ProcessResultCommandTest extends \PHPUnit_Framework_TestCase
{
    /** @var ProcessResultCommand */
    private $command;

    /** @var CommandTester */
    private $commandTester;

    /** @var string */
    private $testFile;

    /** @inheritdoc */
    public function setUp()
    {
        $this->command = new ProcessResultCommand();
        $this->commandTester = new CommandTester($this->command);
        $this->testFile = null;
    }

    /** @inheritdoc */
    public function tearDown()
    {
        if ($this->testFile) {
            unlink($this->testFile);
        }
    }

    /**
     * @param array $content
     * @return string
     */
    private function createResultFile(array $content)
    {
        $encoder = new JsonEncode();
        $filePath = sys_get_temp_dir() . '/test-' . md5(time()) . '.json';
        $file = new \SplFileObject($filePath, 'w');
        $file->fwrite($encoder->encode($content, JsonEncoder::FORMAT));
        $file = null;
        $this->testFile = $filePath;
        return $filePath;
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Not enough arguments (missing: "result").
     */
    public function testMissingArguments()
    {
        $this->commandTester->execute([]);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage File "/really-not/existent/path" does not exist or is not readable
     */
    public function testInvalidFormat()
    {
        $this->commandTester->execute(['result' => '/really-not/existent/path']);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessageRegExp /File ".+" does not contain an analyze result/
     */
    public function testInvalidContent()
    {
        $file = $this->createResultFile([]);
        $this->commandTester->execute(['result' => $file]);
    }

    /**
     * @param array $fileContent
     * @param array $commandOptions
     * @param string $expectedOutput
     * @param int $expectedExit
     * @dataProvider dataProvider
     */
    public function test(array $fileContent, array $commandOptions, $expectedOutput, $expectedExit)
    {
        $file = $this->createResultFile($fileContent);
        $exit = $this->commandTester->execute(array_merge(['result' => $file], $commandOptions));
        $this->assertEquals($expectedExit, $exit);
        $this->assertEquals($expectedOutput, $this->commandTester->getDisplay());
    }

    public function dataProvider()
    {
        return [
            [
                ['cycles' => [], 'log' => []],
                [],
                "No cycles or warnings were detected!\n",
                0
            ],
            [
                ['cycles' => [], 'log' => []],
                ['--success-message' => 'Another success message'],
                "Another success message\n",
                0
            ],
            [
                ['cycles' => ['cycle info'], 'log' => []],
                [],
                "One or more cycles were detected!\n",
                1
            ],
            [
                ['cycles' => ['cycle info'], 'log' => []],
                ['--message-on-cycle' => 'Another cycle message', '--exit-code-on-cycle' => 5],
                "Another cycle message\n",
                5
            ],
            [
                ['cycles' => [], 'log' => ['warning' => ['a waring']]],
                [],
                "One or more warnings were detected!\n",
                2
            ],
            [
                ['cycles' => [], 'log' => ['warning' => ['a waring']]],
                ['--message-on-warning' => 'Another warning message', '--exit-code-on-warning' => 15],
                "Another warning message\n",
                15
            ],
            [
                ['cycles' => ['cycle info'], 'log' => ['warning' => ['a waring']]],
                [],
                "One or more cycles were detected!\nOne or more warnings were detected!\n",
                3
            ],
        ];
    }
}
