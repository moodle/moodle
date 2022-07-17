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

/**
 * Custom PHP_CodeSniffer Runner for local_codechecker.
 *
 * @package    local_codechecker
 * @copyright  2020 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_codechecker;

/**
 * Custom PHP_CodeSniffer\Runner for local_codechecker.
 *
 * This custom runner just intercepts the init() method, to be able
 * to add all our configuration. The alternative to this is to play
 * with fake $_SERVER['argv' {@see PHP_CodeSniffer\Config}.
 *
 * @copyright  2020 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class runner extends \PHP_CodeSniffer\Runner {

    /**
     * Create an instance of the runner.
     */
    public function __construct() {
        // Needed constant.
        if (defined('PHP_CODESNIFFER_CBF') === false) {
            define('PHP_CODESNIFFER_CBF', true);
        }
        // Pass the parallel as CLI, disabled. Note
        // this is to avoid some nasty argv notices.
        $this->config = new \PHP_CodeSniffer\Config([
            '--parallel=1',
        ]);
    }

    /**
     * Set the file where the XML report is going to be written
     *
     * @param string $reportfile file path to XML report.
     */
    public function set_reportfile($reportfile) {
        $this->config->reports = [report::class => $reportfile];
    }

    /**
     * Set if the report should include warnings or no.
     *
     * @param bool $includewarnings
     */
    public function set_includewarnings($includewarnings) {
        if (empty($includewarnings)) {
            $this->config->warningSeverity = 0; // Disable warnings.
        }
    }

    /**
     * Set the files the runner is going to process.
     *
     * @param string[] $files array of full paths to files or directories.
     */
    public function set_files($files) {
        $this->config->files = $files;
    }

    /**
     * Set all the patterns in file paths to be ignored
     *
     * @param string[] $ignorepatterns array of paths to ignore (libs, fixtures...)
     */
    public function set_ignorepatterns($ignorepatterns) {
        $this->config->ignored = $ignorepatterns;
    }

    /**
     * Set the verbosity for the output.
     *
     * @param int $verbosity How verbose the output should be. Check expected values in {@see PHP_CodeSniffer\Config}.
     */
    public function set_verbosity(int $verbosity): void {
        $this->config->verbosity = $verbosity;
    }

    /**
     * Set if the interactive checking mode should be enabled or not.
     *
     * @param bool $interactive If true, will stop after each file with errors and wait for user input.
     */
    public function set_interactive(bool $interactive): void {
        $this->config->interactive = $interactive;
    }

    /**
     * Initialise the runner, invoked by run().
     */
    public function init() {

        $this->config->standards = ['moodle'];
        $this->config->extensions = ['php' => 'PHP'];

        // Added all our customizations, finally call parent.
        parent::init();
    }

    /**
     * Runs a codechecker execution.
     *
     * Instead of using the upstream runner, we just use this reduced version
     * that simplifies all the configuration, init and processing to suit
     * codechecker simpler needs from the UI.
     */
    public function run() {
        // Setup everything.
        $this->init();

        // Create the reporter to manage all the reports from the run.
        $this->reporter = new \PHP_CodeSniffer\Reporter($this->config);

        // And build the file list to iterate over.
        $todo = new \PHP_CodeSniffer\Files\FileList($this->config, $this->ruleset);

        foreach ($todo as $file) {
            if ($file->ignored === false) {
                try {
                    $this->processFile($file);
                } catch (\PHP_CodeSniffer\Exceptions\DeepExitException $e) {
                    echo $e->getMessage();
                    return $e->getCode();
                } catch (\Exception $e) {
                    $error = 'Problem during processing; checking has been aborted. The error message was: '.$e->getMessage();
                    $file->addErrorOnLine($error, 1, 'Internal.Exception');
                }
                $file->cleanUp();
            }
        }

        // Have finished, generate the final reports.
        $this->reporter->printReports();
    }
}
