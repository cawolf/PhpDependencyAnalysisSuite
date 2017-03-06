<?php

namespace Cawolf\PhpDependencyAnalysisSuite\Command;

use Cawolf\PhpDependencyAnalysisSuite\Application\ConfigurationReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

/**
 * Class ProcessResultCommand
 * @package Cawolf\PhpDependencyAnalysisSuite\Command
 */
class ProcessResultCommand extends Command
{
    /** @var ConfigurationReader */
    private $configurationReader;

    /** @var InputDefinition */
    private $inputDefinition;

    /**
     * ProcessResultCommand constructor.
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
        $this->inputDefinition = new InputDefinition([
            new InputArgument('result', InputArgument::REQUIRED, 'path to JSON result file of analyze command'),
            new InputOption(
                'configuration-file',
                'f',
                InputOption::VALUE_REQUIRED,
                'path to default configuration file, values are overwritten by command options'
            ),
            new InputOption(
                'exit-code-on-cycle',
                null,
                InputOption::VALUE_REQUIRED,
                'exit code of command if a cycle is detected',
                1
            ),
            new InputOption(
                'exit-code-on-warning',
                null,
                InputOption::VALUE_REQUIRED,
                'exit code of command if a warning is detected',
                2
            ),
            new InputOption(
                'message-on-cycle',
                null,
                InputOption::VALUE_REQUIRED,
                'message to print if a cycle is detected',
                'One or more cycles were detected!'
            ),
            new InputOption(
                'message-on-warning',
                null,
                InputOption::VALUE_REQUIRED,
                'message to print if a warning is detected',
                'One or more warnings were detected!'
            ),
            new InputOption(
                'show-cycles',
                'c',
                InputOption::VALUE_NONE,
                'show information about detected cycles'
            ),
            new InputOption(
                'show-warnings',
                'w',
                InputOption::VALUE_NONE,
                'show information about detected warnings'
            ),
            new InputOption(
                'success-message',
                null,
                InputOption::VALUE_REQUIRED,
                'message to print if everything is fine',
                'No cycles or warnings were detected!'
            )
        ]);

        $this->setName('process-result')
            ->setDescription('Analyzes a result file and takes appropriate actions.')
            ->setDefinition($this->inputDefinition);
    }

    /** @inheritdoc */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $options = $this->resolveOptions($input);

        $result = $this->parseResult($input);

        return $this->processResult($options, $output, $result);
    }

    /**
     * @param InputInterface $input
     * @return array
     */
    private function parseResult(InputInterface $input)
    {
        $resultFileInfo = new \SplFileInfo($input->getArgument('result'));
        if (!$resultFileInfo->isFile() || !$resultFileInfo->isReadable()) {
            throw new InvalidArgumentException(
                sprintf('File "%s" does not exist or is not readable', $input->getArgument('result'))
            );
        }

        $resultFile = $resultFileInfo->openFile();
        $decoder = new JsonDecode(true);
        $result = $decoder->decode($resultFile->fread($resultFile->getSize()), JsonEncoder::FORMAT);

        if (!isset($result['cycles']) || !isset($result['log'])) {
            throw new InvalidArgumentException(
                sprintf('File "%s" does not contain an analyze result', $input->getArgument('result'))
            );
        }
        return $result;
    }

    /**
     * @param array $options
     * @param OutputInterface $output
     * @param array $result
     * @return int
     */
    protected function processResult(array $options, OutputInterface $output, array $result)
    {
        $hasCycles = count($result['cycles']) > 0;
        $hasWarnings = count($result['log']) > 0 && isset($result['log']['warning'])
            && count($result['log']['warning']) > 0;

        $returnCode = 0;
        if ($hasCycles) {
            $output->writeln($options['message-on-cycle']);
            if ($options['show-cycles']) {
                $this->showCycles($output, $result['cycles']);
            }
            $returnCode = $returnCode | $options['exit-code-on-cycle'];
        }
        if ($hasWarnings) {
            $output->writeln($options['message-on-warning']);
            if ($options['show-warnings']) {
                $this->showWarnings($output, $result['log']['warning']);
            }
            $returnCode = $returnCode | $options['exit-code-on-warning'];
        }
        if (!$hasCycles && !$hasWarnings) {
            $output->writeln($options['success-message']);
            return $returnCode;
        }
        return $returnCode;
    }

    /**
     * @param OutputInterface $output
     * @param array $cycles
     */
    private function showCycles(OutputInterface $output, array $cycles)
    {
        foreach ($cycles as $cycle) {
            $output->writeln(
                sprintf(
                    'Detected cycle: %s',
                    implode(' => ', $cycle)
                )
            );
        }
    }

    /**
     * @param OutputInterface $output
     * @param array $warnings
     */
    private function showWarnings(OutputInterface $output, array $warnings)
    {
        foreach ($warnings as $warning) {
            $file = '<unknown>';
            if ($warning['context'][0]) {
                $file = $warning['context'][0];
            }
            $output->writeln(
                sprintf(
                    'Detected warning: %s in file "%s"',
                    $warning['message'],
                    $file
                )
            );
        }
    }

    /**
     * @param InputInterface $input
     * @return array
     */
    private function resolveOptions(InputInterface $input)
    {
        $options = $input->getOptions();
        if ($options['configuration-file']) {
            $configOptions = array_diff_assoc(
                $this->configurationReader->readFromFile($options['configuration-file']),
                $this->inputDefinition->getOptionDefaults()
            );
            $commandOptions = array_diff_assoc($options, $this->inputDefinition->getOptionDefaults());
            $options = array_merge($this->inputDefinition->getOptionDefaults(), $configOptions, $commandOptions);
        }
        return $options;
    }
}
