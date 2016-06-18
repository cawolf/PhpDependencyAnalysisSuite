<?php

namespace Cawolf\PhpDependencyAnalysisSuite\Tests\Command;

use Cawolf\PhpDependencyAnalysisSuite\Command\GenerateConfigCommand;
use Prophecy\Argument;

class GenerateConfigCommandTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $input = $this->prophesize('Symfony\Component\Console\Input\InputInterface');
        $input->bind(Argument::cetera())->shouldBeCalled();
        $input->hasArgument(Argument::cetera())->shouldBeCalled();
        $input->validate()->shouldBeCalled();
        $input->isInteractive()->shouldBeCalled();
        $output = $this->prophesize('Symfony\Component\Console\Output\OutputInterface');
        $output->writeln('whoop')->shouldBeCalled();

        $command = new GenerateConfigCommand();
        $command->run($input->reveal(), $output->reveal());
    }
}
