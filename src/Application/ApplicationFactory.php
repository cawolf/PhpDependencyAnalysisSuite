<?php

namespace Cawolf\PhpDependencyAnalysisSuite\Application;

use Cawolf\PhpDependencyAnalysisSuite\Command\GenerateConfigCommand;
use Cawolf\PhpDependencyAnalysisSuite\Command\ProcessResultCommand;
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
        $app = new Application('phpdasuite', '1.0');
        $app->add(new GenerateConfigCommand());
        $app->add(new ProcessResultCommand());
        return $app;
    }
}
