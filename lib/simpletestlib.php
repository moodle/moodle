<?php // $Id$
/**
 * Utility functions to make unit testing easier.
 *
 * These functions, particularly the the database ones, are quick and
 * dirty methods for getting things done in test cases. None of these
 * methods should be used outside test code.
 *
 * @copyright &copy; 2006 The Open University
 * @author T.J.Hunt@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @version $Id$
 * @package SimpleTestEx
 */

require_once(dirname(__FILE__) . '/../config.php');
require_once($CFG->libdir . '/simpletestlib/simpletest.php');
require_once($CFG->libdir . '/simpletestlib/unit_tester.php');
require_once($CFG->libdir . '/simpletestlib/expectation.php');
require_once($CFG->libdir . '/simpletestlib/reporter.php');
require_once($CFG->libdir . '/simpletestlib/web_tester.php');
require_once($CFG->libdir . '/simpletestlib/mock_objects.php');

/**
 * Recursively visit all the files in the source tree. Calls the callback
 * function with the pathname of each file found.
 *
 * @param $path the folder to start searching from.
 * @param $callback the function to call with the name of each file found.
 * @param $fileregexp a regexp used to filter the search (optional).
 * @param $exclude If true, pathnames that match the regexp will be ingored. If false,
 *     only files that match the regexp will be included. (default false).
 * @param array $ignorefolders will not go into any of these folders (optional).
 */
function recurseFolders($path, $callback, $fileregexp = '/.*/', $exclude = false, $ignorefolders = array()) {
    $files = scandir($path);

    foreach ($files as $file) {
        $filepath = $path .'/'. $file;
        if ($file == '.' || $file == '..') {
            continue;
        } else if (is_dir($filepath)) {
            if (!in_array($filepath, $ignorefolders)) {
                recurseFolders($filepath, $callback, $fileregexp, $exclude, $ignorefolders);
            }
        } else if ($exclude xor preg_match($fileregexp, $filepath)) {
            call_user_func($callback, $filepath);
        }
    }
}

/**
 * An expectation for comparing strings ignoring whitespace.
 */
class IgnoreWhitespaceExpectation extends SimpleExpectation {
    var $expect;

    function IgnoreWhitespaceExpectation($content, $message = '%s') {
        $this->SimpleExpectation($message);
        $this->expect=$this->normalise($content);
    }

    function test($ip) {
        return $this->normalise($ip)==$this->expect;
    }

    function normalise($text) {
        return preg_replace('/\s+/m',' ',trim($text));
    }

    function testMessage($ip) {
        return "Input string [$ip] doesn't match the required value.";
    }
}

/**
 * An Expectation that two arrays contain the same list of values.
 */
class ArraysHaveSameValuesExpectation extends SimpleExpectation {
    var $expect;

    function ArraysHaveSameValuesExpectation($expected, $message = '%s') {
        $this->SimpleExpectation($message);
        if (!is_array($expected)) {
            trigger_error('Attempt to create an ArraysHaveSameValuesExpectation ' .
                    'with an expected value that is not an array.');
        }
        $this->expect = $this->normalise($expected);
    }

    function test($actual) {
        return $this->normalise($actual) == $this->expect;
    }

    function normalise($array) {
        sort($array);
        return $array;
    }

    function testMessage($actual) {
        return 'Array [' . implode(', ', $actual) .
                '] does not contain the expected list of values [' . implode(', ', $this->expect) . '].';
    }
}

/**
 * An Expectation that compares to objects, and ensures that for every field in the
 * expected object, there is a key of the same name in the actual object, with
 * the same value. (The actual object may have other fields to, but we ignore them.)
 */
class CheckSpecifiedFieldsExpectation extends SimpleExpectation {
    var $expect;

    function CheckSpecifiedFieldsExpectation($expected, $message = '%s') {
        $this->SimpleExpectation($message);
        if (!is_object($expected)) {
            trigger_error('Attempt to create a CheckSpecifiedFieldsExpectation ' .
                    'with an expected value that is not an object.');
        }
        $this->expect = $expected;
    }

    function test($actual) {
        foreach ($this->expect as $key => $value) {
            if (isset($value) && isset($actual->$key) && $actual->$key == $value) {
                // OK
            } else if (is_null($value) && is_null($actual->$key)) {
                // OK
            } else {
                return false;
            }
        }
        return true;
    }

    function testMessage($actual) {
        $mismatches = array();
        foreach ($this->expect as $key => $value) {
            if (isset($value) && isset($actual->$key) && $actual->$key == $value) {
                // OK
            } else if (is_null($value) && is_null($actual->$key)) {
                // OK
            } else {
                $mismatches[] = $key;
            }
        }
        return 'Actual object does not have all the same fields with the same values as the expected object (' .
                implode(', ', $mismatches) . ').';
    }
}

class MoodleUnitTestCase extends UnitTestCase {
    public $real_db;
    public $tables = array();
    public $pkfile;
    public $cfg;

    /**
     * In the constructor, record the max(id) of each test table into a csv file.
     * If this file already exists, it means that a previous run of unit tests
     * did not complete, and has left data undeleted in the DB. This data is then
     * deleted and the file is retained. Otherwise it is created.
     * @throws moodle_exception if CSV file cannot be created
     */
    public function __construct($label = false) {
        parent::UnitTestCase($label);

        // MDL-16483 Get PKs and save data to text file
        global $DB, $CFG;
        $this->pkfile = $CFG->dataroot.'/testtablespks.csv';
        $this->cfg = $CFG;
        $this->setup();

        $tables = $DB->get_tables();

        // The file exists, so use it to truncate tables (tests aborted before test data could be removed)
        if (file_exists($this->pkfile)) {
            $this->truncate_test_tables($this->get_table_data($this->pkfile));

        } else { // Create the file
            $tabledata = '';

            foreach ($tables as $table) {
                if ($table != 'sessions2') {
                    if (!$max_id = $DB->get_field_sql("SELECT MAX(id) FROM {$CFG->unittestprefix}{$table}")) {
                        $max_id = 0;
                    }
                    $tabledata .= "$table, $max_id\n";
                }
            }
            if (!file_put_contents($this->pkfile, $tabledata)) {
                throw new moodle_exception('testtablescsvfileunwritable', 'error');
            }
        }
    }

    /**
     * Given an array of tables and their max id, truncates all test table records whose id is higher than the ones in the $tabledata array.
     * @param array $tabledata
     */
    private function truncate_test_tables($tabledata) {
        global $CFG, $DB;

        $tables = $DB->get_tables();

        foreach ($tables as $table) {
            if ($table != 'sessions2' && isset($tabledata[$table])) {
                $DB->delete_records_select($table, "id > ?", array($tabledata[$table]));
            }
        }
    }

    /**
     * Given a filename, opens it and parses the csv contained therein. It expects two fields per line:
     * 1. Table name
     * 2. Max id
     * @param string $filename
     * @throws moodle_exception if file doesn't exist
     */
    public function get_table_data($filename) {
        if (file_exists($this->pkfile)) {
            $handle = fopen($this->pkfile, 'r');
            $tabledata = array();

            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $tabledata[$data[0]] = $data[1];
            }
            return $tabledata;
        } else {
            throw new moodle_exception('testtablescsvfilemissing', 'error');
            return false;
        }
    }

    /**
     * Method called before each test method. Replaces the real $DB with the one configured for unit tests (different prefix, $CFG->unittestprefix).
     * Also detects if this config setting is properly set, and if the user table exists.
     * TODO Improve detection of incorrectly built DB test tables (e.g. detect version discrepancy and offer to upgrade/rebuild)
     */
    public function setUp() {
        global $CFG, $DB;
        parent::setUp();

        $this->real_db = $DB;

        if (empty($CFG->unittestprefix)) {
            print_error("prefixnotset", 'simpletest');
        }

        $DB = moodle_database::get_driver_instance($CFG->dbtype, $CFG->dblibrary);
        $DB->connect($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname, $CFG->dbpersist, $CFG->unittestprefix);
        $manager = $DB->get_manager();

        if (!$manager->table_exists('user')) {
            print_error('tablesnotsetup', 'simpletest');
        }
    }

    /**
     * Method called after each test method. Doesn't do anything extraordinary except restore the global $DB to the real one.
     */
    public function tearDown() {
        global $DB;
        parent::tearDown();

        $DB = $this->real_db;
    }

    /**
     * This will execute once all the tests have been run. It should delete the text file holding info about database contents prior to the tests
     * It should also detect if data is missing from the original tables.
     */
    public function __destruct() {
        global $CFG;
        $CFG = $this->cfg;
        $this->truncate_test_tables($this->get_table_data($this->pkfile));
        fulldelete($this->pkfile);
        $this->tearDown();
    }
}

?>
