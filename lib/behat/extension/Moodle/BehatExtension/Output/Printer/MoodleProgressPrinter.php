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

namespace Moodle\BehatExtension\Output\Printer;

use Behat\Behat\Output\Node\Printer\SetupPrinter;
use Behat\Testwork\Call\CallResult;
use Behat\Testwork\Hook\Tester\Setup\HookedTeardown;
use Behat\Testwork\Output\Formatter;
use Behat\Testwork\Output\Printer\OutputPrinter;
use Behat\Testwork\Tester\Setup\Setup;
use Behat\Testwork\Tester\Setup\Teardown;
use Moodle\BehatExtension\Driver\WebDriver;

// phpcs:disable moodle.NamingConventions.ValidFunctionName.LowercaseMethod

/**
 * Prints hooks in a pretty fashion.
 *
 * @package    core
 * @copyright  2016 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class MoodleProgressPrinter implements SetupPrinter {

    /**
     * @var string Moodle directory root.
     */
    private $moodledirroot;

    /**
     * @var bool true if output is displayed.
     */
    private static $outputdisplayed;

    /**
     * Constructor.
     *
     * @param string $moodledirroot Moodle dir root.
     */
    public function __construct($moodledirroot) {
        $this->moodledirroot = $moodledirroot;
    }

    /**
     * Prints setup state.
     *
     * @param Formatter $formatter
     * @param Setup     $setup
     */
    public function printSetup(Formatter $formatter, Setup $setup) {
        if (empty(self::$outputdisplayed)) {
            $this->printMoodleInfo($formatter->getOutputPrinter());
            self::$outputdisplayed = true;
        }
    }

    /**
     * Prints teardown state.
     *
     * @param Formatter $formatter
     * @param Teardown  $teardown
     */
    public function printTeardown(Formatter $formatter, Teardown $teardown) {
        if (!$teardown instanceof HookedTeardown) {
            return;
        }

        foreach ($teardown->getHookCallResults() as $callresult) {
            $this->printTeardownHookCallResult($formatter->getOutputPrinter(), $callresult);
        }
    }

    /**
     * We print the site info + driver used and OS.
     *
     * @param Printer $printer
     * @return void
     */
    public function printMoodleInfo($printer) {
        require_once($this->moodledirroot . '/lib/behat/classes/util.php');

        $browser = WebDriver::getBrowserName();

        // Calling all directly from here as we avoid more behat framework extensions.
        $runinfo = \behat_util::get_site_info();
        $runinfo .= 'Server OS "' . PHP_OS . '"' . ', Browser: "' . $browser . '"' . PHP_EOL;
        $runinfo .= 'Started at ' . date('d-m-Y, H:i', time());

        $printer->writeln($runinfo);
    }

    /**
     * Prints teardown hook call result.
     *
     * @param OutputPrinter $printer
     * @param CallResult    $callresult
     */
    private function printTeardownHookCallResult(OutputPrinter $printer, CallResult $callresult) {
        // Notify dev that chained step is being used.
        if (\Moodle\BehatExtension\EventDispatcher\Tester\ChainedStepTester::is_chained_step_used()) {
            $printer->writeln();
            $printer->write(
                "{+failed}Chained steps are deprecated. " .
                "See https://docs.moodle.org/dev/Acceptance_testing/" .
                "Migrating_from_Behat_2.5_to_3.x_in_Moodle#Changes_required_in_context_file{-failed}"
            );
        }

        if (!$callresult->hasStdOut() && !$callresult->hasException()) {
            return;
        }

        $hook = $callresult->getCall()->getCallee();
        $path = $hook->getPath();

        $printer->writeln($hook);
        $printer->writeln($path);
    }
}
