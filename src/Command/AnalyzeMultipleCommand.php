<?php

namespace Cawolf\PhpDependencyAnalysisSuite\Command;

use Cawolf\PhpDependencyAnalysisSuite\Application\ConfigurationReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AnalyzeMultipleCommand
 * @package Cawolf\PhpDependencyAnalysisSuite\Command
 */
class AnalyzeMultipleCommand extends Command
{
    /** @var ConfigurationReader */
    private $configurationReader;

    /**
     * AnalyzeMultipleCommand constructor.
     * @param ConfigurationReader $configurationReader
     */
    public function __construct(ConfigurationReader $configurationReader)
    {
        parent::__construct();
        $this->configurationReader = $configurationReader;
    }

    /** @inheritdoc */
    protected function configure()
    {
        $this->setName('analyze-multiple')
            ->setDescription('Runs the analysis command multiple times for different namespace depths.')
            ->addArgument('configurationFile', InputArgument::REQUIRED, 'path to store configuration file')
            ->addArgument('namespaceDepthStart', InputArgument::OPTIONAL, 'start of namespace depth to run the analysis', 1)
            ->addArgument('namespaceDepthEnd', InputArgument::OPTIONAL, 'end of namespace depth to run the analysis', 2);
    }

    /** @inheritdoc */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getArgument('namespaceDepthStart') <= 0) {
            throw new InvalidArgumentException('namespaceDepthStart must be greater than 0');
        }
        if ($input->getArgument('namespaceDepthStart') >= $input->getArgument('namespaceDepthEnd')) {
            throw new InvalidArgumentException('namespaceDepthEnd must be greater than namespaceDepthStart');
        }

        $configuration = $this->configurationReader->readFromFile($input->getArgument('configurationFile'));
        $analyze = $this->getApplication()->find('analyze');

        $analyzeInput = new ArrayInput(['phpda-svg.yml']); // TODO iterate over namespace depths

        $exit = $analyze->run($analyzeInput, $output);
    }
}
