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

namespace Moodle\BehatExtension\Output\Formatter;

use Behat\Behat\EventDispatcher\Event\AfterOutlineTested;
use Behat\Behat\EventDispatcher\Event\AfterScenarioTested;
use Behat\Testwork\Output\Formatter;
use Behat\Testwork\Output\Printer\OutputPrinter;

// phpcs:disable moodle.NamingConventions.ValidFunctionName.LowercaseMethod

/**
 * Feature step counter for distributing features between parallel runs.
 *
 * Use it with --dry-run (and any other selectors combination) to
 * get the results quickly.
 *
 * @package core
 * @copyright  2015 onwards Rajesh Taneja
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleListFormatter implements Formatter {

    /** @var OutputPrinter */
    private $printer;

    /** @var array */
    private $parameters;

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /**
     * Initializes formatter.
     *
     * @param string        $name
     * @param string        $description
     * @param array         $parameters
     * @param OutputPrinter $printer
     */
    public function __construct($name, $description, array $parameters, OutputPrinter $printer) {
        $this->name = $name;
        $this->description = $description;
        $this->parameters = $parameters;
        $this->printer = $printer;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents() {
        return [

            'tester.scenario_tested.after'     => 'afterScenario',
            'tester.outline_tested.after'      => 'afterOutlineExample',
        ];
    }

    /**
     * Returns formatter name.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Returns formatter description.
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Returns formatter output printer.
     *
     * @return OutputPrinter
     */
    public function getOutputPrinter() {
        return $this->printer;
    }

    /**
     * Sets formatter parameter.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function setParameter($name, $value) {
        $this->parameters[$name] = $value;
    }

    /**
     * Returns parameter name.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getParameter($name) {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
    }

    /**
     * Listens to "scenario.after" event.
     *
     * @param AfterScenarioTested $event
     */
    public function afterScenario(AfterScenarioTested $event) {
        $scenario = $event->getScenario();
        $this->printer->writeln($event->getFeature()->getFile() . ':' . $scenario->getLine());
    }


    /**
     * Listens to "outline.example.after" event.
     *
     * @param AfterOutlineTested $event
     */
    public function afterOutlineExample(AfterOutlineTested $event) {
        $outline = $event->getOutline();
        $line = $outline->getLine();
        $this->printer->writeln($event->getFeature()->getFile() . ':' . $line);
    }
}
