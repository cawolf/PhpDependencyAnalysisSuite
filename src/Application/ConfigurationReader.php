<?php

namespace Cawolf\PhpDependencyAnalysisSuite\Application;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Yaml\Yaml;

/**
 * Class ConfigurationReader
 * @package Cawolf\PhpDependencyAnalysisSuite\Application
 */
class ConfigurationReader
{
    /**
     * @param string $fileName
     * @return array
     * @throws InvalidArgumentException
     */
    public function readFromFile($fileName)
    {
        $fileInfo = new \SplFileInfo($fileName);
        if (!$fileInfo->isFile() || !$fileInfo->isReadable()) {
            throw new InvalidArgumentException(
                sprintf('File "%s" does not exist or is not readable', $fileName)
            );
        }
        $file = $fileInfo->openFile();
        return Yaml::parse($file->fread($file->getSize()));
    }
}
