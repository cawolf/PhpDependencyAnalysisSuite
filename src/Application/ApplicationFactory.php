<?php

namespace Cawolf\PhpDependencyAnalysisSuite\Application;

use Cawolf\PhpDependencyAnalysisSuite\Command\GenerateConfigCommand;
use Symfony\Component\Console\Application;

class ApplicationFactory
{
    public function create()
    {
        $app = new Application('phpdasuite', '0.1');
        $app->setDefaultCommand('cawolf:phpda:generate-config');
        $app->add(new GenerateConfigCommand());
        return $app;
    }
}
