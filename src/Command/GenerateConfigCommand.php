<?php

namespace Cawolf\PhpDependencyAnalysisSuite\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateConfigCommand extends Command
{
    protected function configure()
    {
        $this->setName('cawolf:phpda:generate-config')
            ->setDescription('Whoop'); // TODO
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('whoop'); // TODO
    }
}
