<?php

namespace Cawolf\PhpDependencyAnalysisSuite\Application;

use Cawolf\PhpDependencyAnalysisSuite\Command\GenerateConfigCommand;
use Symfony\Component\Console\Application;

/**
 * Class ApplicationFactory
 * @package Cawolf\PhpDependencyAnalysisSuite\Application
 */
class ApplicationFactory
{
    /**
     * @return Application
     */
    public function create()
    {
        $app = new Application('phpdasuite', '0.1');
        $app->add(new GenerateConfigCommand());
        return $app;
    }
}
