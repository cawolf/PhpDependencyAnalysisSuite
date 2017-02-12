<?php

namespace Cawolf\PhpDependencyAnalysisSuite\Tests\Application;

use Cawolf\PhpDependencyAnalysisSuite\Application\ConfigurationReader;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Yaml\Yaml;

class ConfigurationReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage File "a/path/that/does/not/exist" does not exist or is not readable
     */
    public function testInaccessibleFile()
    {
        $reader = new ConfigurationReader();
        $reader->readFromFile('a/path/that/does/not/exist');
    }

    public function test()
    {
        $filePath = sys_get_temp_dir() . '/test-' . md5(time()) . '.yml';
        $file = new \SplFileObject($filePath, 'w');
        $file->fwrite(Yaml::dump(['success-message' => 'success!']));
        $file = null;

        $reader = new ConfigurationReader();
        self::assertEquals(['success-message' => 'success!'], $reader->readFromFile($filePath));
    }
}
