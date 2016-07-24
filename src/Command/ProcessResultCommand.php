<?php

namespace Cawolf\PhpDependencyAnalysisSuite\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

/**
 * Class ProcessResultCommand
 * @package Command
 */
class ProcessResultCommand extends Command
{
    /** @inheritdoc */
    protected function configure()
    {
        $this->setName('process-result')
            ->setDescription('Analyzes a result file and takes appropriate actions.')
            ->addArgument('result', InputArgument::REQUIRED, 'path to JSON result file of analyze command')
            ->addOption(
                'exit-code-on-cycle',
                null,
                InputOption::VALUE_REQUIRED,
                'exit code of command if a cycle is detected',
                1
            )
            ->addOption(
                'exit-code-on-warning',
                null,
                InputOption::VALUE_REQUIRED,
                'exit code of command if a warning is detected',
                2
            )
            ->addOption(
                'message-on-cycle',
                null,
                InputOption::VALUE_REQUIRED,
                'message to print if a cycle is detected',
                'One or more cycles were detected!'
            )
            ->addOption(
                'message-on-warning',
                null,
                InputOption::VALUE_REQUIRED,
                'message to print if a warning is detected',
                'One or more warnings were detected!'
            )
            ->addOption(
                'show-cycles',
                'c',
                InputOption::VALUE_NONE,
                'show information about detected cycles'
            )
            ->addOption(
                'show-warnings',
                'w',
                InputOption::VALUE_NONE,
                'show information about detected warnings'
            )
            ->addOption(
                'success-message',
                null,
                InputOption::VALUE_REQUIRED,
                'message to print if everything is fine',
                'No cycles or warnings were detected!'
            );
    }

    /** @inheritdoc */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $result = $this->parseResult($input);

        if (!isset($result['cycles']) || !isset($result['log'])) {
            throw new InvalidArgumentException(
                sprintf('File "%s" does not contain an analyze result', $input->getArgument('result'))
            );
        }

        return $this->processResult($input, $output, $result);
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
        $resultFile = null;
        return $result;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param array $result
     * @return int
     */
    protected function processResult(InputInterface $input, OutputInterface $output, array $result)
    {
        $hasCycles = count($result['cycles']) > 0;
        $hasWarnings = count($result['log']) > 0 && isset($result['log']['warning'])
            && count($result['log']['warning']) > 0;

        $returnCode = 0;
        if ($hasCycles) {
            $output->writeln($input->getOption('message-on-cycle'));
            if ($input->getOption('show-cycles')) {
                $this->showCycles($output, $result['cycles']);
            }
            $returnCode = $returnCode | $input->getOption('exit-code-on-cycle');
        }
        if ($hasWarnings) {
            $output->writeln($input->getOption('message-on-warning'));
            if ($input->getOption('show-warnings')) {
                $this->showWarnings($output, $result['log']['warning']);
            }
            $returnCode = $returnCode | $input->getOption('exit-code-on-warning');
        }
        if (!$hasCycles && !$hasWarnings) {
            $output->writeln($input->getOption('success-message'));
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
}
