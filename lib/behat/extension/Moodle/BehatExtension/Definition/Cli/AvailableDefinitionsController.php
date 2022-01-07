<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace Moodle\BehatExtension\Definition\Cli;

use Behat\Behat\Definition\DefinitionWriter;
use Moodle\BehatExtension\Definition\Printer\ConsoleDefinitionInformationPrinter;
use Behat\Behat\Definition\Printer\ConsoleDefinitionListPrinter;
use Behat\Behat\Definition\Printer\DefinitionPrinter;
use Behat\Testwork\Cli\Controller;
use Behat\Testwork\Suite\SuiteRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Available definition controller, for calling moodle information printer.
 *
 * @package    behat
 * @copyright  2016 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class AvailableDefinitionsController implements Controller {
    /**
     * @var SuiteRepository
     */
    private $suiteRepository;
    /**
     * @var DefinitionWriter
     */
    private $writer;
    /**
     * @var ConsoleDefinitionListPrinter
     */
    private $listPrinter;
    /**
     * @var ConsoleDefinitionInformationPrinter
     */
    private $infoPrinter;

    /**
     * Initializes controller.
     *
     * @param SuiteRepository                     $suiteRepository
     * @param DefinitionWriter                    $writer
     * @param ConsoleDefinitionListPrinter        $listPrinter
     * @param ConsoleDefinitionInformationPrinter $infoPrinter
     */
    public function __construct(
        SuiteRepository $suiteRepository,
        DefinitionWriter $writer,
        ConsoleDefinitionListPrinter $listPrinter,
        ConsoleDefinitionInformationPrinter $infoPrinter
    ) {
        $this->suiteRepository = $suiteRepository;
        $this->writer = $writer;
        $this->listPrinter = $listPrinter;
        $this->infoPrinter = $infoPrinter;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(Command $command) {
        $command->addOption('--definitions', '-d', InputOption::VALUE_REQUIRED,
            "Print all available step definitions:" . PHP_EOL .
            "- use <info>--definitions l</info> to just list definition expressions." . PHP_EOL .
            "- use <info>--definitions i</info> to show definitions with extended info." . PHP_EOL .
            "- use <info>--definitions 'needle'</info> to find specific definitions." . PHP_EOL .
            "Use <info>--lang</info> to see definitions in specific language."
        );
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output) {
        if (null === $argument = $input->getOption('definitions')) {
            return null;
        }

        $printer = $this->getDefinitionPrinter($argument);
        foreach ($this->suiteRepository->getSuites() as $suite) {
            $this->writer->printSuiteDefinitions($printer, $suite);
        }

        return 0;
    }

    /**
     * Returns definition printer for provided option argument.
     *
     * @param string $argument
     *
     * @return DefinitionPrinter
     */
    private function getDefinitionPrinter($argument) {
        if ('l' === $argument) {
            return $this->listPrinter;
        }

        if ('i' !== $argument) {
            $this->infoPrinter->setSearchCriterion($argument);
        }

        return $this->infoPrinter;
    }
}
