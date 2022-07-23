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
use Behat\Behat\Definition\Printer\ConsoleDefinitionListPrinter;
use Behat\Testwork\Cli\Controller;
use Behat\Testwork\Suite\SuiteRepository;
use Moodle\BehatExtension\Definition\Printer\ConsoleDefinitionInformationPrinter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Available definition controller, for calling moodle information printer.
 *
 * @package    core
 * @copyright  2016 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class AvailableDefinitionsController implements Controller {
    /** @var SuiteRepository */
    private $suiterepository;

    /** @var DefinitionWriter */
    private $writer;

    /** @var ConsoleDefinitionListPrinter */
    private $listprinter;

    /** @var ConsoleDefinitionInformationPrinter */
    private $infoprinter;

    /**
     * Initializes controller.
     *
     * @param SuiteRepository                     $suiterepository
     * @param DefinitionWriter                    $writer
     * @param ConsoleDefinitionListPrinter        $listprinter
     * @param ConsoleDefinitionInformationPrinter $infoprinter
     */
    public function __construct(
        SuiteRepository $suiterepository,
        DefinitionWriter $writer,
        ConsoleDefinitionListPrinter $listprinter,
        ConsoleDefinitionInformationPrinter $infoprinter
    ) {
        $this->suiterepository = $suiterepository;
        $this->writer = $writer;
        $this->listprinter = $listprinter;
        $this->infoprinter = $infoprinter;
    }

    /**
     * Configures command to be executable by the controller.
     *
     * @param Command $command
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
     * Executes controller.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return null|integer
     */
    public function execute(InputInterface $input, OutputInterface $output) {
        if (null === $argument = $input->getOption('definitions')) {
            return null;
        }

        $printer = $this->getdefinitionPrinter($argument);
        foreach ($this->suiterepository->getSuites() as $suite) {
            $this->writer->printSuiteDefinitions($printer, $suite);
        }

        return 0;
    }

    /**
     * Returns definition printer for provided option argument.
     *
     * @param string $argument
     *
     * @return \Behat\Behat\Definition\Printer\DefinitionPrinter
     */
    private function getdefinitionprinter($argument) {
        if ('l' === $argument) {
            return $this->listprinter;
        }

        if ('i' !== $argument) {
            $this->infoprinter->setSearchCriterion($argument);
        }

        return $this->infoprinter;
    }
}
