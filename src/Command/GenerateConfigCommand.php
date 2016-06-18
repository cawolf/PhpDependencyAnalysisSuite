<?php

namespace Cawolf\PhpDependencyAnalysisSuite\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GenerateConfigCommand
 * @package Cawolf\PhpDependencyAnalysisSuite\Command
 */
class GenerateConfigCommand extends Command
{
    /** @inheritdoc */
    protected function configure()
    {
        $this->setName('cawolf:phpda:generate-config')
            ->setDescription('Whoop'); // TODO
    }

    /** @inheritdoc */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $input->isInteractive();
        $output->writeln('whoop'); // TODO
    }
}
