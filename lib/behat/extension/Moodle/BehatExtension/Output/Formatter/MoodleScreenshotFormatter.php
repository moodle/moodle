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

use Behat\Behat\EventDispatcher\Event\AfterStepTested;
use Behat\Behat\EventDispatcher\Event\BeforeScenarioTested;
use Behat\Behat\EventDispatcher\Event\BeforeStepTested;
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
class MoodleScreenshotFormatter implements Formatter {

    /** @var OutputPrinter */
    private $printer;

    /** @var array */
    private $parameters;

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var int The scenario count */
    protected static $currentscenariocount = 0;

    /** @var int The step count within the current scenario */
    protected static $currentscenariostepcount = 0;

    /**
     * If we are saving any kind of dump on failure we should use the same parent dir during a run.
     *
     * @var The parent dir name
     */
    protected static $faildumpdirname = false;

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
            'tester.scenario_tested.before'    => 'beforeScenario',
            'tester.step_tested.before'        => 'beforeStep',
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
     * Reset currentscenariostepcount
     *
     * @param BeforeScenarioTested $event
     */
    public function beforeScenario(BeforeScenarioTested $event) {

        self::$currentscenariostepcount = 0;
        self::$currentscenariocount++;
    }

    /**
     * Increment currentscenariostepcount
     *
     * @param BeforeStepTested $event
     */
    public function beforeStep(BeforeStepTested $event) {
        self::$currentscenariostepcount++;
    }

    /**
     * Take screenshot after step is executed.    Behat\Behat\Event\html
     *
     * @param AfterStepTested $event
     */
    public function afterStep(AfterStepTested $event) {
        $behathookcontext = $event->getEnvironment()->getContext('behat_hooks');

        $formats = $this->getParameter('formats');
        $formats = explode(',', $formats);

        // Take screenshot.
        if (in_array('image', $formats)) {
            $this->take_screenshot($event, $behathookcontext);
        }

        // Save html content.
        if (in_array('html', $formats)) {
            $this->take_contentdump($event, $behathookcontext);
        }
    }

    /**
     * Return screenshot directory where all screenshots will be saved.
     *
     * @return string
     */
    protected function get_run_screenshot_dir() {
        global $CFG;

        if (self::$faildumpdirname) {
            return self::$faildumpdirname;
        }

        // If output_path is set then use output_path else use faildump_path.
        if ($this->getOutputPrinter()->getOutputPath()) {
            $screenshotpath = $this->getOutputPrinter()->getOutputPath();
        } else if ($CFG->behat_faildump_path) {
            $screenshotpath = $CFG->behat_faildump_path;
        } else {
            // It should never reach here.
            throw new FormatterException('You should specify --out "SOME/PATH" for moodle_screenshot format');
        }

        if ($this->getParameter('dir_permissions')) {
            $dirpermissions = $this->getParameter('dir_permissions');
        } else {
            $dirpermissions = 0777;
        }

        // All the screenshot dumps should be in the same parent dir.
        self::$faildumpdirname = $screenshotpath . DIRECTORY_SEPARATOR . date('Ymd_His');

        if (!is_dir(self::$faildumpdirname) && !mkdir(self::$faildumpdirname, $dirpermissions, true)) {
            // It shouldn't, we already checked that the directory is writable.
            throw new FormatterException(sprintf(
                'No directories can be created inside %s, check the directory permissions.', $screenshotpath
            ));
        }

        return self::$faildumpdirname;
    }

    /**
     * Take screenshot when a step fails.
     *
     * @throws Exception
     * @param AfterStepTested $event
     * @param Context $context
     */
    protected function take_screenshot(AfterStepTested $event, $context) {
        // BrowserKit can't save screenshots.
        if ($context->getMink()->isSessionStarted($context->getMink()->getDefaultSessionName())) {
            if (get_class($context->getMink()->getSession()->getDriver()) === 'Behat\Mink\Driver\BrowserKitDriver') {
                return false;
            }
            list ($dir, $filename) = $this->get_faildump_filename($event, 'png');
            $context->saveScreenshot($filename, $dir);
        }
    }

    /**
     * Take a dump of the page content when a step fails.
     *
     * @throws Exception
     * @param AfterStepTested $event
     * @param \Behat\Context\Context\Context $context
     */
    protected function take_contentdump(AfterStepTested $event, $context) {
        list ($dir, $filename) = $this->get_faildump_filename($event, 'html');
        $fh = fopen($dir . DIRECTORY_SEPARATOR . $filename, 'w');
        fwrite($fh, $context->getMink()->getSession()->getPage()->getContent());
        fclose($fh);
    }

    /**
     * Determine the full pathname to store a failure-related dump.
     *
     * This is used for content such as the DOM, and screenshots.
     *
     * @param AfterStepTested $event
     * @param String $filetype The file suffix to use. Limited to 4 chars.
     */
    protected function get_faildump_filename(AfterStepTested $event, $filetype) {
        // Make a directory for the scenario.
        $featurename = $event->getFeature()->getTitle();
        $featurename = preg_replace('/([^a-zA-Z0-9\_]+)/', '-', $featurename);
        if ($this->getParameter('dir_permissions')) {
            $dirpermissions = $this->getParameter('dir_permissions');
        } else {
            $dirpermissions = 0777;
        }

        $dir = $this->get_run_screenshot_dir();

        // We want a i-am-the-scenario-title format.
        $dir = $dir . DIRECTORY_SEPARATOR . self::$currentscenariocount . '-' . $featurename;
        if (!is_dir($dir) && !mkdir($dir, $dirpermissions, true)) {
            // We already checked that the directory is writable. This should not fail.
            throw new FormatterException(sprintf(
                'No directories can be created inside %s, check the directory permissions.', $dir
            ));
        }

        // The failed step text.
        // We want a stepno-i-am-the-failed-step.$filetype format.
        $filename = $event->getStep()->getText();
        $filename = preg_replace('/([^a-zA-Z0-9\_]+)/', '-', $filename);
        $filename = self::$currentscenariostepcount . '-' . $filename;

        // File name limited to 255 characters. Leaving 4 chars for the file
        // extension as we allow .png for images and .html for DOM contents.
        $filename = substr($filename, 0, 250) . '.' . $filetype;
        return [$dir, $filename];
    }
}
