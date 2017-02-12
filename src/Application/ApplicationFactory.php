<?php

namespace Cawolf\PhpDependencyAnalysisSuite\Application;

use Cawolf\PhpDependencyAnalysisSuite\Command\AnalyzeMultipleCommand;
use Cawolf\PhpDependencyAnalysisSuite\Command\GenerateConfigCommand;
use Cawolf\PhpDependencyAnalysisSuite\Command\ProcessResultCommand;
use PhpDA\Command\MessageInterface;
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
        $app = new Application('phpdasuite', '1.4');
        $app->add(new GenerateConfigCommand());
        $app->add(new AnalyzeMultipleCommand(new ConfigurationReader()));
        $app->add(new ProcessResultCommand(new ConfigurationReader()));

        $phpdaFactory = new \PhpDA\Command\ApplicationFactory();
        $phpdaApplication = $phpdaFactory->create();
        $app->add($phpdaApplication->get(MessageInterface::COMMAND));

        return $app;
    }
}
