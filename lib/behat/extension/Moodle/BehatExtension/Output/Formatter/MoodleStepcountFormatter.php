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

use Behat\Behat\EventDispatcher\Event\AfterFeatureTested;
use Behat\Behat\EventDispatcher\Event\AfterStepTested;
use Behat\Behat\EventDispatcher\Event\BeforeFeatureTested;
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
 * @copyright  2016 onwards Rajesh Taneja
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleStepcountFormatter implements Formatter {

    /** @var int Number of steps executed in feature file. */
    private static $stepcount = 0;

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
            'tester.feature_tested.before'     => 'beforeFeature',
            'tester.feature_tested.after'      => 'afterFeature',
            'tester.step_tested.after'         => 'afterStep',
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
     * @return mixed
     */
    public function getParameter($name) {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
    }

    /**
     * Listens to "feature.before" event.
     *
     * @param BeforeFeatureTested $event
     */
    public function beforeFeature(BeforeFeatureTested $event) {
        self::$stepcount = 0;
    }

    /**
     * Listens to "feature.after" event.
     *
     * @param AfterFeatureTested $event
     */
    public function afterFeature(AfterFeatureTested $event) {
        $this->printer->writeln($event->getFeature()->getFile() . '::' . self::$stepcount);
    }

    /**
     * Listens to "step.after" event.
     *
     * @param AfterStepTested $event
     */
    public function afterStep(AfterStepTested $event) {
        self::$stepcount++;
    }
}
