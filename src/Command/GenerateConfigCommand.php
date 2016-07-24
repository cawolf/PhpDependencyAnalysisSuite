<?php

namespace Cawolf\PhpDependencyAnalysisSuite\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class GenerateConfigCommand
 * @package Cawolf\PhpDependencyAnalysisSuite\Command
 */
class GenerateConfigCommand extends Command
{
    private static $formats = ['json', 'svg', 'html', 'script'];
    private static $modes = ['usage', 'call', 'inheritance'];

    /** @inheritdoc */
    protected function configure()
    {
        $this->setName('generate-config')
            ->setDescription('Generates a default configuration file to run the analyze command.')
            ->addArgument('source', InputArgument::REQUIRED, 'path to source files to be analyzed')
            ->addArgument('target', InputArgument::REQUIRED, 'target file to store the result')
            ->addArgument('configuration', InputArgument::REQUIRED, 'path to store configuration file')
            ->addOption(
                'format',
                null,
                InputOption::VALUE_REQUIRED,
                sprintf('output file format, possible values: <info>%s</info>', implode(', ', self::$formats)),
                'json'
            )
            ->addOption(
                'mode',
                null,
                InputOption::VALUE_REQUIRED,
                sprintf('mode of analyze command, possible values: <info>%s</info>', implode(', ', self::$modes)),
                'usage'
            )
            ->addOption(
                'ignore',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'multiple paths to be ignored by analyze command',
                ['tests']
            )
            ->addOption(
                'namespace-depth',
                null,
                InputOption::VALUE_REQUIRED,
                'depth of namespace used for grouping results',
                1
            );
    }

    /** @inheritdoc */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!in_array($input->getOption('mode'), self::$modes)) {
            throw new InvalidArgumentException(
                sprintf('The "mode" must be one of: %s', implode(', ', self::$modes))
            );
        }
        if (!in_array($input->getOption('format'), self::$formats)) {
            throw new InvalidArgumentException(
                sprintf('The "format" must be one of: %s', implode(', ', self::$formats))
            );
        }

        $configuration = Yaml::dump([
            'mode' => $input->getOption('mode'),
            'source' => $input->getArgument('source'),
            'filePattern' => '*.php',
            'ignore' => $input->getOption('ignore'),
            'formatter' => sprintf('PhpDA\Writer\Strategy\%s', ucfirst($input->getOption('format'))),
            'target' => $input->getArgument('target'),
            'groupLength' => $input->getOption('namespace-depth'),
            'visitor' => [
                'PhpDA\Parser\Visitor\TagCollector',
                'PhpDA\Parser\Visitor\SuperglobalCollector'
            ],
            'visitorOptions' => [
                'PhpDA\Parser\Visitor\Required\DeclaredNamespaceCollector' => [
                    'minDepth' => 2,
                    'sliceLength' => $input->getOption('namespace-depth') + 1
                ],
                'PhpDA\Parser\Visitor\Required\MetaNamespaceCollector' => [
                    'minDepth' => 2,
                    'sliceLength' => $input->getOption('namespace-depth') + 1
                ],
                'PhpDA\Parser\Visitor\Required\UsedNamespaceCollector' => [
                    'minDepth' => 2,
                    'sliceLength' => $input->getOption('namespace-depth') + 1
                ],
                'PhpDA\Parser\Visitor\TagCollector' => [
                    'minDepth' => 2,
                    'sliceLength' => $input->getOption('namespace-depth') + 1
                ]
            ]
        ]);

        $fileInfo = new \SplFileInfo($input->getArgument('configuration'));
        $configurationFile = $fileInfo->openFile('w');
        $configurationFile->fwrite($configuration);
        $output->writeln(sprintf(
            '<info>Configuration generated and written to "%s".</info>',
            $input->getArgument('configuration')
        ));
    }
}
