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
 * Extend simpletest to support code coverage analysis
 *
 * This package contains a collection of classes that, extending standard simpletest
 * ones, provide code coverage analysis to already existing tests. Also there are some
 * utility functions designed to make the coverage control easier.
 *
 * @package    core
 * @subpackage simpletestcoverage
 * @copyright  2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Includes
 */
require_once(dirname(__FILE__) . '/../config.php');
require_once($CFG->libdir.'/tablelib.php');

require_once($CFG->libdir . '/simpletestlib.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/report/unittest/ex_simple_test.php');

require_once($CFG->libdir . '/spikephpcoverage/src/CoverageRecorder.php');
require_once($CFG->libdir . '/spikephpcoverage/src/reporter/HtmlCoverageReporter.php');

/**
 * AutoGroupTest class extension supporting code coverage
 *
 * This class extends AutoGroupTest to add the funcitionalities
 * necessary to run code coverage, allowing its activation and
 * specifying included / excluded files to be analysed
 *
 * @package   moodlecore
 * @subpackage simpletestcoverage
 * @copyright 2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class autogroup_test_coverage extends AutoGroupTest {

    private $performcoverage; // boolean
    private $coveragename;    // title of the coverage report
    private $coveragedir;     // dir, relative to dataroot/coverage where the report will be saved
    private $includecoverage; // paths to be analysed by the coverage report
    private $excludecoverage; // paths to be excluded from the coverage report

    function __construct($showsearch, $test_name = null,
                         $performcoverage = false, $coveragename = 'Code Coverage Report',
                         $coveragedir = 'report') {
        parent::__construct($showsearch, $test_name);
        $this->performcoverage = $performcoverage;
        $this->coveragename    = $coveragename;
        $this->coveragedir     = $coveragedir;
        $this->includecoverage = array();
        $this->excludecoverage = array();
    }

    public function addTestFile($file, $internalcall = false) {
        global $CFG;

        if ($this->performcoverage) {
            $refinfo = moodle_reflect_file($file);
            require_once($file);
            if ($refinfo->classes) {
                foreach ($refinfo->classes as $class) {
                    $reflection = new ReflectionClass($class);
                    if ($staticprops = $reflection->getStaticProperties()) {
                        if (isset($staticprops['includecoverage']) && is_array($staticprops['includecoverage'])) {
                            foreach ($staticprops['includecoverage'] as $toinclude) {
                                $this->add_coverage_include_path($toinclude);
                            }
                        }
                        if (isset($staticprops['excludecoverage']) && is_array($staticprops['excludecoverage'])) {
                            foreach ($staticprops['excludecoverage'] as $toexclude) {
                                $this->add_coverage_exclude_path($toexclude);
                            }
                        }
                    }
                }
                // Automatically add the test dir itself, so nothing will be covered there
                $this->add_coverage_exclude_path(dirname($file));
            }
        }
        parent::addTestFile($file, $internalcall);
    }

    public function add_coverage_include_path($path) {
        global $CFG;

        $path = $CFG->dirroot . '/' . $path; // Convert to full path
        if (!in_array($path, $this->includecoverage)) {
            array_push($this->includecoverage, $path);
        }
    }

    public function add_coverage_exclude_path($path) {
        global $CFG;

        $path = $CFG->dirroot . '/' . $path; // Convert to full path
        if (!in_array($path, $this->excludecoverage)) {
            array_push($this->excludecoverage, $path);
        }
    }

    /**
     * Run the autogroup_test_coverage using one internally defined code coverage reporter
     * automatically generating the coverage report. Only supports one instrumentation
     * to be executed and reported.
     */
    public function run(&$simpletestreporter) {
        global $CFG;

        if (moodle_coverage_recorder::can_run_codecoverage() && $this->performcoverage) {
            // Testing with coverage
            $covreporter = new moodle_coverage_reporter($this->coveragename, $this->coveragedir);
            $covrecorder = new moodle_coverage_recorder($covreporter);
            $covrecorder->setIncludePaths($this->includecoverage);
            $covrecorder->setExcludePaths($this->excludecoverage);
            $covrecorder->start_instrumentation();
            parent::run($simpletestreporter);
            $covrecorder->stop_instrumentation();
            $covrecorder->generate_report();
            moodle_coverage_reporter::print_summary_info(basename($this->coveragedir));
        } else {
            // Testing without coverage
            parent::run($simpletestreporter);
        }
    }

    /**
     * Run the autogroup_test_coverage tests using one externally defined code coverage reporter
     * allowing further process of coverage data once tests are over. Supports multiple
     * instrumentations (code coverage gathering sessions) to be executed.
     */
    public function run_with_external_coverage(&$simpletestreporter, &$covrecorder) {

        if (moodle_coverage_recorder::can_run_codecoverage() && $this->performcoverage) {
            $covrecorder->setIncludePaths($this->includecoverage);
            $covrecorder->setExcludePaths($this->excludecoverage);
            $covrecorder->start_instrumentation();
            parent::run($simpletestreporter);
            $covrecorder->stop_instrumentation();
        } else {
            // Testing without coverage
            parent::run($simpletestreporter);
        }
    }
}

/**
 * CoverageRecorder class extension supporting multiple
 * coverage instrumentations to be accumulated
 *
 * This class extends CoverageRecorder class in order to
 * support multimple xdebug code coverage sessions to be
 * executed and get acummulated info about all them in order
 * to produce one unique report (default CoverageRecorder
 * resets info on each instrumentation (coverage session)
 *
 * @package   moodlecore
 * @subpackage simpletestcoverage
 * @copyright 2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_coverage_recorder extends CoverageRecorder {

    public function __construct($reporter='new moodle_coverage_reporter()') {
        parent::__construct(array(), array(), $reporter);
    }

    /**
     * Stop gathering coverage data, saving it for later reporting
     */
    public function stop_instrumentation() {
        if(extension_loaded("xdebug")) {
            $lastcoveragedata = xdebug_get_code_coverage(); // Get last instrumentation coverage data
            xdebug_stop_code_coverage(); // Stop code coverage
            $this->coverageData = self::merge_coverage_data($this->coverageData, $lastcoveragedata); // Append lastcoveragedata
            $this->logger->debug("[moodle_coverage_recorder::stopInstrumentation()] Code coverage: " . print_r($this->coverageData, true),
                __FILE__, __LINE__);
            return true;
        } else {
            $this->logger->critical("[moodle_coverage_recorder::stopInstrumentation()] Xdebug not loaded.", __FILE__, __LINE__);
        }
        return false;
    }

    /**
     * Start gathering coverage data
     */
    public function start_instrumentation() {
        $this->startInstrumentation(); /// Simple lowercase wrap over Spike function
    }

    /**
     * Generate the code coverage report
     */
    public function generate_report() {
        $this->generateReport(); /// Simple lowercase wrap over Spike function
    }

    /**
     * Determines if the server is able to run code coverage analysis
     *
     * @return bool
     */
    static public function can_run_codecoverage() {
        // Only req is xdebug loaded. PEAR XML is already in place and available
        if(!extension_loaded("xdebug")) {
            return false;
        }
        return true;
    }

    /**
     * Merge two collections of complete code coverage data
     */
    protected static function merge_coverage_data($cov1, $cov2) {

        $result = array();

        // protection against empty coverage collections
        if (!is_array($cov1)) {
            $cov1 = array();
        }
        if (!is_array($cov2)) {
            $cov2 = array();
        }

        // Get all the files used in both coverage datas
        $files = array_unique(array_merge(array_keys($cov1), array_keys($cov2)));

        // Iterate, getting results
        foreach($files as $file) {
            // If file exists in both coverages, let's merge their lines
            if (array_key_exists($file, $cov1) && array_key_exists($file, $cov2)) {
                $result[$file] = self::merge_lines_coverage_data($cov1[$file], $cov2[$file]);
            // Only one of the coverages has the file
            } else if (array_key_exists($file, $cov1)) {
                $result[$file] = $cov1[$file];
            } else {
                $result[$file] = $cov2[$file];
            }
        }
        return $result;
    }

    /**
     * Merge two collections of lines of code coverage data belonging to the same file
     *
     * Merge algorithm obtained from Phing: http://phing.info
     */
    protected static function merge_lines_coverage_data($lines1, $lines2) {

        $result = array();

        reset($lines1);
        reset($lines2);

        while (current($lines1) && current($lines2)) {
            $linenr1 = key($lines1);
            $linenr2 = key($lines2);

            if ($linenr1 < $linenr2) {
                $result[$linenr1] = current($lines1);
                next($lines1);
            } else if ($linenr2 < $linenr1) {
                $result[$linenr2] = current($lines2);
                next($lines2);
            } else {
                if (current($lines1) < 0) {
                    $result[$linenr2] = current($lines2);
                } else if (current($lines2) < 0) {
                    $result[$linenr2] = current($lines1);
                } else {
                    $result[$linenr2] = current($lines1) + current($lines2);
                }
                next($lines1);
                next($lines2);
            }
        }

        while (current($lines1)) {
            $result[key($lines1)] = current($lines1);
            next($lines1);
        }

        while (current($lines2)) {
            $result[key($lines2)] = current($lines2);
            next($lines2);
        }

        return $result;
    }
}

/**
 * HtmlCoverageReporter class extension supporting Moodle customizations
 *
 * This class extends the HtmlCoverageReporter class in order to
 * implement Moodle look and feel, inline reporting after executing
 * unit tests, proper linking and other tweaks here and there.
 *
 * @package   moodlecore
 * @subpackage simpletestcoverage
 * @copyright 2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_coverage_reporter extends HtmlCoverageReporter {

    public function __construct($heading='Coverage Report', $dir='report') {
        global $CFG;
        parent::__construct($heading, '', $CFG->dataroot . '/codecoverage/' . $dir);
    }

    /**
     * Writes one row in the index.html table to display filename
     * and coverage recording.
     *
     * Overrided to transform names and links to shorter format
     *
     * @param $fileLink link to html details file.
     * @param $realFile path to real PHP file.
     * @param $fileCoverage Coverage recording for that file.
     * @return string HTML code for a single row.
     * @access protected
     */
    protected function writeIndexFileTableRow($fileLink, $realFile, $fileCoverage) {

        global $CFG;

        $fileLink = str_replace($CFG->dirroot, '', $fileLink);
        $realFile = str_replace($CFG->dirroot, '', $realFile);

        return parent::writeIndexFileTableRow($fileLink, $realFile, $fileCoverage);;
    }

    /**
     * Mark a source code file based on the coverage data gathered
     *
     * Overrided to transform names and links to shorter format
     *
     * @param $phpFile Name of the actual source file
     * @param $fileLink Link to the html mark-up file for the $phpFile
     * @param &$coverageLines Coverage recording for $phpFile
     * @return boolean FALSE on failure
     * @access protected
     */
    protected function markFile($phpFile, $fileLink, &$coverageLines) {
        global $CFG;

        $fileLink = str_replace($CFG->dirroot, '', $fileLink);

        return parent::markFile($phpFile, $fileLink, $coverageLines);
    }


    /**
     * Update the grand totals
     *
     * Overrided to avoid the call to recordFileCoverageInfo()
     * because it has been already executed by writeIndexFile() and
     * cause files to be duplicated in the fileCoverage property
     */
    protected function updateGrandTotals(&$coverageCounts) {
        $this->grandTotalLines += $coverageCounts['total'];
        $this->grandTotalCoveredLines += $coverageCounts['covered'];
        $this->grandTotalUncoveredLines += $coverageCounts['uncovered'];
    }

    /**
     * Generate the static report
     *
     * Overrided to generate the serialised object to be displayed inline
     * with the test results.
     *
     * @param &$data  Reference to Coverage Data
     */
    public function generateReport(&$data) {
        parent::generateReport($data);

        // head data
        $data = new stdClass();
        $data->time   = time();
        $data->title  = $this->heading;
        $data->output = $this->outputDir;

        // summary data
        $data->totalfiles       = $this->grandTotalFiles;
        $data->totalln          = $this->grandTotalLines;
        $data->totalcoveredln   = $this->grandTotalCoveredLines;
        $data->totaluncoveredln = $this->grandTotalUncoveredLines;
        $data->totalpercentage  = $this->getGrandCodeCoveragePercentage();

        // file details data
        $data->coveragedetails = $this->fileCoverage;

        // save serialised object
        file_put_contents($data->output . '/codecoverage.ser', serialize($data));
    }

    /**
     * Return the html contents for the summary for the last execution of the
     * given test type
     *
     * @param string $type of the test to return last execution summary (dbtest|unittest)
     * @return string html contents of the summary
     */
    static public function get_summary_info($type) {
        global $CFG, $OUTPUT;

        $serfilepath = $CFG->dataroot . '/codecoverage/' . $type . '/codecoverage.ser';
        if (file_exists($serfilepath) && is_readable($serfilepath)) {
            if ($data = unserialize(file_get_contents($serfilepath))) {
                // return one table with all the totals (we avoid individual file results here)
                $result = '';
                $table = new html_table();
                $table->align = array('right', 'left');
                $table->tablealign = 'center';
                $table->attributes['class'] = 'codecoveragetable';
                $table->id = 'codecoveragetable_' . $type;
                $table->rowclasses = array('label', 'value');
                $table->data = array(
                        array(get_string('date')                           , userdate($data->time)),
                        array(get_string('files')                          , format_float($data->totalfiles, 0)),
                        array(get_string('totallines', 'simpletest')       , format_float($data->totalln, 0)),
                        array(get_string('executablelines', 'simpletest')  , format_float($data->totalcoveredln + $data->totaluncoveredln, 0)),
                        array(get_string('coveredlines', 'simpletest')     , format_float($data->totalcoveredln, 0)),
                        array(get_string('uncoveredlines', 'simpletest')   , format_float($data->totaluncoveredln, 0)),
                        array(get_string('coveredpercentage', 'simpletest'), format_float($data->totalpercentage, 2) . '%')
                );

                $url = $CFG->wwwroot . '/admin/report/unittest/coveragefile.php/' . $type . '/index.html';
                $result .= $OUTPUT->heading($data->title, 3, 'main codecoverageheading');
                $result .= $OUTPUT->heading('<a href="' . $url . '" onclick="javascript:window.open(' . "'" . $url . "'" . ');return false;"' .
                                   ' title="">' . get_string('codecoveragecompletereport', 'simpletest') . '</a>', 4, 'main codecoveragelink');
                $result .= html_writer::table($table);

                return $OUTPUT->box($result, 'generalbox boxwidthwide boxaligncenter codecoveragebox', '', true);
            }
        }
        return false;
    }

    /**
     * Print the html contents for the summary for the last execution of the
     * given test type
     *
     * @param string $type of the test to return last execution summary (dbtest|unittest)
     * @return string html contents of the summary
     */
    static public function print_summary_info($type) {
        echo self::get_summary_info($type);
    }

    /**
     * Return the html code needed to browse latest code coverage complete report of the
     * given test type
     *
     * @param string $type of the test to return last execution summary (dbtest|unittest)
     * @return string html contents of the summary
     */
    static public function get_link_to_latest($type) {
        global $CFG, $OUTPUT;

        $serfilepath = $CFG->dataroot . '/codecoverage/' . $type . '/codecoverage.ser';
        if (file_exists($serfilepath) && is_readable($serfilepath)) {
            if ($data = unserialize(file_get_contents($serfilepath))) {
                $info = new stdClass();
                $info->date       = userdate($data->time);
                $info->files      = format_float($data->totalfiles, 0);
                $info->percentage = format_float($data->totalpercentage, 2) . '%';

                $strlatestreport  = get_string('codecoveragelatestreport', 'simpletest');
                $strlatestdetails = get_string('codecoveragelatestdetails', 'simpletest', $info);

                // return one link to latest complete report
                $result = '';
                $url = $CFG->wwwroot . '/admin/report/unittest/coveragefile.php/' . $type . '/index.html';
                $result .= $OUTPUT->heading('<a href="' . $url . '" onclick="javascript:window.open(' . "'" . $url . "'" . ');return false;"' .
                    ' title="">' . $strlatestreport . '</a>', 3, 'main codecoveragelink');
                $result .= $OUTPUT->heading($strlatestdetails, 4, 'main codecoveragedetails');
                return $OUTPUT->box($result, 'generalbox boxwidthwide boxaligncenter codecoveragebox', '', true);
            }
        }
        return false;
    }

    /**
     * Print the html code needed to browse latest code coverage complete report of the
     * given test type
     *
     * @param string $type of the test to return last execution summary (dbtest|unittest)
     * @return string html contents of the summary
     */
    static public function print_link_to_latest($type) {
        echo self::get_link_to_latest($type);
    }
}


/**
 * Return information about classes and functions
 *
 * This function will parse any PHP file, extracting information about the
 * classes and functions defined within it, providing "File Reflection" as
 * PHP standard reflection classes don't support that.
 *
 * The idea and the code has been obtained from the Zend Framework Reflection API
 * http://framework.zend.com/manual/en/zend.reflection.reference.html
 *
 * Usage: $ref_file = moodle_reflect_file($file);
 *
 * @param string $file full path to the php file to introspect
 * @return object object with both 'classes' and 'functions' properties
 */
function moodle_reflect_file($file) {

    $contents = file_get_contents($file);
    $tokens   = token_get_all($contents);

    $functionTrapped = false;
    $classTrapped    = false;
    $openBraces      = 0;

    $classes   = array();
    $functions = array();

    foreach ($tokens as $token) {
        /*
         * Tokens are characters representing symbols or arrays
         * representing strings. The keys/values in the arrays are
         *
         * - 0 => token id,
         * - 1 => string,
         * - 2 => line number
         *
         * Token ID's are explained here:
         * http://www.php.net/manual/en/tokens.php.
         */

        if (is_array($token)) {
            $type    = $token[0];
            $value   = $token[1];
            $lineNum = $token[2];
        } else {
            // It's a symbol
            // Maintain the count of open braces
            if ($token == '{') {
                $openBraces++;
            } else if ($token == '}') {
                $openBraces--;
            }

            continue;
        }

        switch ($type) {
            // Name of something
            case T_STRING:
                if ($functionTrapped) {
                    $functions[] = $value;
                    $functionTrapped = false;
                } elseif ($classTrapped) {
                    $classes[] = $value;
                    $classTrapped = false;
                }
                continue;

            // Functions
            case T_FUNCTION:
                if ($openBraces == 0) {
                    $functionTrapped = true;
                }
                break;

            // Classes
            case T_CLASS:
                $classTrapped = true;
                break;

            // Default case: do nothing
            default:
                break;
        }
    }

    return (object)array('classes' => $classes, 'functions' => $functions);
}
