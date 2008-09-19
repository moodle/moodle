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
                $a = new stdClass();
                $a->filename = $this->pkfile;
                throw new moodle_exception('testtablescsvfileunwritable', 'simpletest', '', $a);
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
                // $DB->delete_records_select($table, "id > ?", array($tabledata[$table]));
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
            $a = new stdClass();
            $a->filename = $this->pkfile;
            debug_print_backtrace();
            throw new moodle_exception('testtablescsvfilemissing', 'simpletest', '', $a);
            return false;
        }
    }

    /**
     * Method called before each test method. Replaces the real $DB with the one configured for unit tests (different prefix, $CFG->unittestprefix).
     * Also detects if this config setting is properly set, and if the user table exists.
     * TODO Improve detection of incorrectly built DB test tables (e.g. detect version discrepancy and offer to upgrade/rebuild)
     */
    public function setUp() {
        parent::setUp();
        UnitTestDB::instantiate();
    }

    /**
     * Method called after each test method. Doesn't do anything extraordinary except restore the global $DB to the real one.
     */
    public function tearDown() {
        global $DB;
        $DB->cleanup();
        parent::tearDown();
    }

    /**
     * This will execute once all the tests have been run. It should delete the text file holding info about database contents prior to the tests
     * It should also detect if data is missing from the original tables.
     */
    public function __destruct() {
        global $CFG, $DB;

        $CFG = $this->cfg;
        $this->tearDown();
        UnitTestDB::restore();
        fulldelete($this->pkfile);
    }
}

/**
 * This is a Database Engine proxy class: It replaces the global object $DB with itself through a call to the
 * static instantiate() method, and restores the original global $DB through restore().
 * Internally, it routes all calls to $DB to a real instance of the database engine (aggregated as a member variable),
 * except those that are defined in this proxy class. This makes it possible to add extra code to the database engine
 * without subclassing it.
 */
class UnitTestDB {
    public static $DB;
    private static $real_db;

    public $table_data = array();

    public function __construct() {

    }

    /**
     * Call this statically to connect to the DB using the unittest prefix, instantiate
     * the unit test db, store it as a member variable, instantiate $this and use it as the new global $DB.
     */
    public static function instantiate() {
        global $CFG, $DB;
        UnitTestDB::$real_db = clone($DB);

        if (empty($CFG->unittestprefix)) {
            print_error("prefixnotset", 'simpletest');
        }

        UnitTestDB::$DB = moodle_database::get_driver_instance($CFG->dbtype, $CFG->dblibrary);
        UnitTestDB::$DB->connect($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname, $CFG->dbpersist, $CFG->unittestprefix);
        $manager = UnitTestDB::$DB->get_manager();

        if (!$manager->table_exists('user')) {
            print_error('tablesnotsetup', 'simpletest');
        }

        $DB = new UnitTestDB();
    }

    public function __call($method, $args) {
        // Set args to null if they don't exist (up to 10 args should do)
        if (!method_exists($this, $method)) {
            return call_user_func_array(array(UnitTestDB::$DB, $method), $args);
        } else {
            call_user_func_array(array($this, $method), $args);
        }
    }

    public function __get($variable) {
        return UnitTestDB::$DB->$variable;
    }

    public function __set($variable, $value) {
        UnitTestDB::$DB->$variable = $value;
    }

    public function __isset($variable) {
        return isset(UnitTestDB::$DB->$variable);
    }

    public function __unset($variable) {
        unset(UnitTestDB::$DB->$variable);
    }

    /**
     * Overriding insert_record to keep track of the ids inserted during unit tests, so that they can be deleted afterwards
     */
    public function insert_record($table, $dataobject, $returnid=true, $bulk=false) {
        global $DB;
        $id = UnitTestDB::$DB->insert_record($table, $dataobject, $returnid, $bulk);
        $this->table_data[$table][] = $id;
        return $id;
    }

    /**
     * Overriding update_record: If we are updating a record that was NOT inserted by unit tests,
     * throw an exception and cancel update.
     * @throws moodle_exception If trying to update a record not inserted by unit tests.
     */
    public function update_record($table, $dataobject, $bulk=false) {
        global $DB;
        if (empty($this->table_data[$table]) || !in_array($dataobject->id, $this->table_data[$table])) {
            return UnitTestDB::$DB->update_record($table, $dataobject, $bulk);
            // $a = new stdClass();
            // $a->id = $dataobject->id;
            // $a->table = $table;
            // debug_print_backtrace();
            // throw new moodle_exception('updatingnoninsertedrecord', 'simpletest', '', $a);
        } else {
            return UnitTestDB::$DB->update_record($table, $dataobject, $bulk);
        }
    }

    /**
     * Overriding delete_record: If we are deleting a record that was NOT inserted by unit tests,
     * throw an exception and cancel delete.
     * @throws moodle_exception If trying to delete a record not inserted by unit tests.
     */
    public function delete_records($table, array $conditions=null) {
        global $DB;
        $a = new stdClass();
        $a->table = $table;

        // Get ids matching conditions
        if (!$ids_to_delete = $DB->get_field($table, 'id', $conditions)) {
            return UnitTestDB::$DB->delete_records($table, $conditions);
        }

        $proceed_with_delete = true;

        if (!is_array($ids_to_delete)) {
            $ids_to_delete = array($ids_to_delete);
        }

        foreach ($ids_to_delete as $id) {
            if (!in_array($id, $this->table_data[$table])) {
                $proceed_with_delete = false;
                $a->id = $id;
                break;
            }
        }

        if ($proceed_with_delete) {
            return UnitTestDB::$DB->delete_records($table, $conditions);
        } else {
            debug_print_backtrace();
            throw new moodle_exception('deletingnoninsertedrecord', 'simpletest', '', $a);
        }
    }

    /**
     * Overriding delete_records_select: If we are deleting a record that was NOT inserted by unit tests,
     * throw an exception and cancel delete.
     * @throws moodle_exception If trying to delete a record not inserted by unit tests.
     */
    public function delete_records_select($table, $select, array $params=null) {
        global $DB;
        $a = new stdClass();
        $a->table = $table;

        // Get ids matching conditions
        if (!$ids_to_delete = $DB->get_field_select($table, 'id', $select, $params)) {
            return UnitTestDB::$DB->delete_records_select($table, $select, $params);
        }

        $proceed_with_delete = true;

        foreach ($ids_to_delete as $id) {
            if (!in_array($id, $this->table_data[$table])) {
                $proceed_with_delete = false;
                $a->id = $id;
                break;
            }
        }

        if ($proceed_with_delete) {
            return UnitTestDB::$DB->delete_records_select($table, $select, $params);
        } else {
            debug_print_backtrace();
            throw new moodle_exception('deletingnoninsertedrecord', 'simpletest', '', $a);
        }
    }

    /**
     * Removes from the test DB all the records that were inserted during unit tests,
     */
    public function cleanup() {
        global $DB;
        foreach ($this->table_data as $table => $ids) {
            foreach ($ids as $id) {
                $DB->delete_records($table, array('id' => $id));
            }
        }
    }

    /**
     * Restores the global $DB object.
     */
    public static function restore() {
        global $DB;
        $DB = UnitTestDB::$real_db;
    }

    public function get_field($table, $return, array $conditions) {
        if (!is_array($conditions)) {
            debug_print_backtrace();
        }
        return UnitTestDB::$DB->get_field($table, $return, $conditions);
    }
}
?>
