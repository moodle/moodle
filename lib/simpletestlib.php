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
 * Utility functions to make unit testing easier.
 *
 * These functions, particularly the the database ones, are quick and
 * dirty methods for getting things done in test cases. None of these
 * methods should be used outside test code.
 *
 * Major Contirbutors
 *     - T.J.Hunt@open.ac.uk
 *
 * @package    core
 * @subpackage simpletestex
 * @copyright  &copy; 2006 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Includes
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
        if (strpos($file, '.') === 0) {
            /// Don't check hidden files.
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
 *
 * @package moodlecore
 * @subpackage simpletestex
 * @copyright &copy; 2006 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
 *
 * @package moodlecore
 * @subpackage simpletestex
 * @copyright &copy; 2006 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
 *
 * @package moodlecore
 * @subpackage simpletestex
 * @copyright &copy; 2006 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
            } else if (!isset($actual->$key)) {
                $mismatches[] = $key . ' (expected [' . $value . '] but was missing.';
            } else {
                $mismatches[] = $key . ' (expected [' . $value . '] got [' . $actual->$key . '].';
            }
        }
        return 'Actual object does not have all the same fields with the same values as the expected object (' .
                implode(', ', $mismatches) . ').';
    }
}

abstract class XMLStructureExpectation extends SimpleExpectation {
    /**
     * Parse a string as XML and return a DOMDocument;
     * @param $html
     * @return unknown_type
     */
    protected function load_xml($html) {
        $prevsetting = libxml_use_internal_errors(true);
        $parser = new DOMDocument();
        if (!$parser->loadXML('<html>' . $html . '</html>')) {
            $parser = new DOMDocument();
        }
        libxml_clear_errors();
        libxml_use_internal_errors($prevsetting);
        return $parser;
    }

    function testMessage($html) {
        $parsererrors = $this->load_xml($html);
        if (is_array($parsererrors)) {
            foreach ($parsererrors as $key => $message) {
                $parsererrors[$key] = $message->message;
            }
            return 'Could not parse XML [' . $html . '] errors were [' . 
                    implode('], [', $parsererrors) . ']';
        }
        return $this->customMessage($html);
    }
}
/**
 * An Expectation that looks to see whether some HMTL contains a tag with a certain attribute.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ContainsTagWithAttribute extends XMLStructureExpectation {
    protected $tag;
    protected $attribute;
    protected $value;

    function __construct($tag, $attribute, $value, $message = '%s') {
        parent::__construct($message);
        $this->tag = $tag;
        $this->attribute = $attribute;
        $this->value = $value;
    }

    function test($html) {
        $parser = $this->load_xml($html);
        if (is_array($parser)) {
            return false;
        }
        $list = $parser->getElementsByTagName($this->tag);

        foreach ($list as $node) {
            if ($node->attributes->getNamedItem($this->attribute)->nodeValue === (string) $this->value) {
                return true;
            }
        }
        return false;
    }

    function customMessage($html) {
        return 'Content [' . $html . '] does not contain the tag [' .
                $this->tag . '] with attribute [' . $this->attribute . '="' . $this->value . '"].';
    }
}

/**
 * An Expectation that looks to see whether some HMTL contains a tag with an array of attributes.
 * All attributes must be present and their values must match the expected values.
 * A third parameter can be used to specify attribute=>value pairs which must not be present in a positive match.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ContainsTagWithAttributes extends XMLStructureExpectation {
    /**
     * @var string $tag The name of the Tag to search
     */
    protected $tag;
    /**
     * @var array $expectedvalues An associative array of parameters, all of which must be matched
     */
    protected $expectedvalues = array();
    /**
     * @var array $forbiddenvalues An associative array of parameters, none of which must be matched
     */
    protected $forbiddenvalues = array();
    /**
     * @var string $failurereason The reason why the test failed: nomatch or forbiddenmatch
     */
    protected $failurereason = 'nomatch';

    function __construct($tag, $expectedvalues, $forbiddenvalues=array(), $message = '%s') {
        parent::__construct($message);
        $this->tag = $tag;
        $this->expectedvalues = $expectedvalues;
        $this->forbiddenvalues = $forbiddenvalues;
    }

    function test($html) {
        $parser = $this->load_xml($html);
        if (is_array($parser)) {
            return false;
        }

        $list = $parser->getElementsByTagName($this->tag);
        $foundamatch = false;

        // Iterating through inputs
        foreach ($list as $node) {
            if (empty($node->attributes) || !is_a($node->attributes, 'DOMNamedNodeMap')) {
                continue;
            }

            // For the current expected attribute under consideration, check that values match
            $allattributesmatch = true;

            foreach ($this->expectedvalues as $expectedattribute => $expectedvalue) {
                if ($node->getAttribute($expectedattribute) === '' && $expectedvalue !== '') {
                    $this->failurereason = 'nomatch';
                    continue 2; // Skip this tag, it doesn't have all the expected attributes
                }
                if ($node->getAttribute($expectedattribute) !== (string) $expectedvalue) {
                    $allattributesmatch = false;
                    $this->failurereason = 'nomatch';
                }
            }

            if ($allattributesmatch) {
                $foundamatch = true;

                // Now make sure this node doesn't have any of the forbidden attributes either
                $nodeattrlist = $node->attributes;

                foreach ($nodeattrlist as $domattrname => $domattr) {
                    if (array_key_exists($domattrname, $this->forbiddenvalues) && $node->getAttribute($domattrname) === (string) $this->forbiddenvalues[$domattrname]) {
                        $this->failurereason = "forbiddenmatch:$domattrname:" . $node->getAttribute($domattrname);
                        $foundamatch = false;
                    }
                }
            }
        }

        return $foundamatch;
    }

    function customMessage($html) {
        $output = 'Content [' . $html . '] ';

        if (preg_match('/forbiddenmatch:(.*):(.*)/', $this->failurereason, $matches)) {
            $output .= "contains the tag $this->tag with the forbidden attribute=>value pair: [$matches[1]=>$matches[2]]";
        } else if ($this->failurereason == 'nomatch') {
            $output .= 'does not contain the tag [' . $this->tag . '] with attributes [';
            foreach ($this->expectedvalues as $var => $val) {
                $output .= "$var=\"$val\" ";
            }
            $output = rtrim($output);
            $output .= '].';
        }

        return $output;
    }
}

/**
 * An Expectation that looks to see whether some HMTL contains a tag with an array of attributes.
 * All attributes must be present and their values must match the expected values.
 * A third parameter can be used to specify attribute=>value pairs which must not be present in a positive match.
 *
 * @copyright 2010 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ContainsSelectExpectation extends XMLStructureExpectation {
    /**
     * @var string $tag The name of the Tag to search
     */
    protected $name;
    /**
     * @var array $expectedvalues An associative array of parameters, all of which must be matched
     */
    protected $choices;
    /**
     * @var array $forbiddenvalues An associative array of parameters, none of which must be matched
     */
    protected $selected;
    /**
     * @var string $failurereason The reason why the test failed: nomatch or forbiddenmatch
     */
    protected $enabled;

    function __construct($name, $choices, $selected = null, $enabled = null, $message = '%s') {
        parent::__construct($message);
        $this->name = $name;
        $this->choices = $choices;
        $this->selected = $selected;
        $this->enabled = $enabled;
    }

    function test($html) {
        $parser = $this->load_xml($html);
        if (is_array($parser)) {
            return false;
        }

        $list = $parser->getElementsByTagName('select');

        // Iterating through inputs
        foreach ($list as $node) {
            if (empty($node->attributes) || !is_a($node->attributes, 'DOMNamedNodeMap')) {
                continue;
            }

            if ($node->getAttribute('name') != $this->name) {
                continue;
            }

            if ($this->enabled === true && $node->getAttribute('disabled')) {
                continue;
            } else if ($this->enabled === false && $node->getAttribute('disabled') != 'disabled') {
                continue;
            }

            $options = $node->getElementsByTagName('option');
            reset($this->choices);
            foreach ($options as $option) {
                if ($option->getAttribute('value') != key($this->choices)) {
                    continue 2;
                }
                if ($option->firstChild->wholeText != current($this->choices)) {
                    continue 2;
                }
                if ($option->getAttribute('value') === $this->selected &&
                        !$option->hasAttribute('selected')) {
                    continue 2;
                }
                next($this->choices);
            }
            if (current($this->choices) !== false) {
                // The HTML did not contain all the choices.
                return false;
            }
            return true;
        }
        return false;
    }

    function customMessage($html) {
        if ($this->enabled === true) {
            $state = 'an enabled';
        } else if ($this->enabled === false) {
            $state = 'a disabled';
        } else {
            $state = 'a';
        }
        $output = 'Content [' . $html . '] does not contain ' . $state .
                ' <select> with name ' . $this->name . ' and choices ' .
                implode(', ', $this->choices);
        if ($this->selected) {
            $output .= ' with ' . $this->selected . ' selected).';
        }

        return $output;
    }
}

/**
 * The opposite of {@link ContainsTagWithAttributes}. The test passes only if
 * the HTML does not contain a tag with the given attributes.
 *
 * @copyright 2010 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class DoesNotContainTagWithAttributes extends ContainsTagWithAttributes {
    function __construct($tag, $expectedvalues, $message = '%s') {
        parent::__construct($tag, $expectedvalues, array(), $message);
    }
    function test($html) {
        return !parent::test($html);
    }
    function customMessage($html) {
        $output = 'Content [' . $html . '] ';

        $output .= 'contains the tag [' . $this->tag . '] with attributes [';
        foreach ($this->expectedvalues as $var => $val) {
            $output .= "$var=\"$val\" ";
        }
        $output = rtrim($output);
        $output .= '].';

        return $output;
    }
}

/**
 * An Expectation that looks to see whether some HMTL contains a tag with a certain text inside it.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ContainsTagWithContents extends XMLStructureExpectation {
    protected $tag;
    protected $content;

    function __construct($tag, $content, $message = '%s') {
        parent::__construct($message);
        $this->tag = $tag;
        $this->content = $content;
    }

    function test($html) {
        $parser = $this->load_xml($html);
        $list = $parser->getElementsByTagName($this->tag);

        foreach ($list as $node) {
            if ($node->textContent == $this->content) {
                return true;
            }
        }

        return false;
    }

    function testMessage($html) {
        return 'Content [' . $html . '] does not contain the tag [' .
                $this->tag . '] with contents [' . $this->content . '].';
    }
}

/**
 * An Expectation that looks to see whether some HMTL contains an empty tag of a specific type.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ContainsEmptyTag extends XMLStructureExpectation {
    protected $tag;

    function __construct($tag, $message = '%s') {
        parent::__construct($message);
        $this->tag = $tag;
    }

    function test($html) {
        $parser = $this->load_xml($html);
        $list = $parser->getElementsByTagName($this->tag);

        foreach ($list as $node) {
            if (!$node->hasAttributes() && !$node->hasChildNodes()) {
                return true;
            }
        }

        return false;
    }

    function testMessage($html) {
        return 'Content ['.$html.'] does not contain the empty tag ['.$this->tag.'].';
    }
}


/**
 * This class lets you write unit tests that access a separate set of test
 * tables with a different prefix. Only those tables you explicitly ask to
 * be created will be.
 *
 * This class has failities for flipping $USER->id.
 *
 * The tear-down method for this class should automatically revert any changes
 * you make during test set-up using the metods defined here. That is, it will
 * drop tables for you automatically and revert to the real $DB and $USER->id.
 *
 * @package moodlecore
 * @subpackage simpletestex
 * @copyright &copy; 2006 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class UnitTestCaseUsingDatabase extends UnitTestCase {
    private $realdb;
    protected $testdb;
    private $realuserid = null;
    private $tables = array();

    private $realcfg;
    protected $testcfg;

    public function __construct($label = false) {
        global $DB, $CFG;

        // Complain if we get this far and $CFG->unittestprefix is not set.
        if (empty($CFG->unittestprefix)) {
            throw new coding_exception('You cannot use UnitTestCaseUsingDatabase unless you set $CFG->unittestprefix.');
        }

        // Only do this after the above text.
        parent::UnitTestCase($label);

        // Create the test DB instance.
        $this->realdb = $DB;
        $this->testdb = moodle_database::get_driver_instance($CFG->dbtype, $CFG->dblibrary);
        $this->testdb->connect($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname, $CFG->unittestprefix);

        // Set up test config
        $this->testcfg = (object)array(
                'testcfg' => true, // Marker that this is a test config
                'libdir' => $CFG->libdir, // Must use real one so require_once works
                'dirroot' => $CFG->dirroot, // Must use real one
                'dataroot' => $CFG->dataroot, // Use real one for now (maybe this should change?)
                'ostype' => $CFG->ostype, // Real one
                'wwwroot' => 'http://www.example.org', // Use fixed url
                'siteadmins' => '0', // No admins
                'siteguest' => '0' // No guest
        );
        $this->realcfg = $CFG;
    }

    /**
     * Switch to using the test database for all queries until further notice.
     */
    protected function switch_to_test_db() {
        global $DB;
        if ($DB === $this->testdb) {
            debugging('switch_to_test_db called when the test DB was already selected. This suggest you are doing something wrong and dangerous. Please review your code immediately.', DEBUG_DEVELOPER);
        }
        $DB = $this->testdb;
    }

    /**
     * Revert to using the test database for all future queries.
     */
    protected function revert_to_real_db() {
        global $DB;
        if ($DB !== $this->testdb) {
            debugging('revert_to_real_db called when the test DB was not already selected. This suggest you are doing something wrong and dangerous. Please review your code immediately.', DEBUG_DEVELOPER);
        }
        $DB = $this->realdb;
    }

    /**
     * Switch to using the test $CFG for all queries until further notice.
     */
    protected function switch_to_test_cfg() {
        global $CFG;
        if (isset($CFG->testcfg)) {
            debugging('switch_to_test_cfg called when the test CFG was already selected. This suggest you are doing something wrong and dangerous. Please review your code immediately.', DEBUG_DEVELOPER);
        }
        $CFG = $this->testcfg;
    }

    /**
     * Revert to using the real $CFG for all future queries.
     */
    protected function revert_to_real_cfg() {
        global $CFG;
        if (!isset($CFG->testcfg)) {
            debugging('revert_to_real_cfg called when the test CFG was not already selected. This suggest you are doing something wrong and dangerous. Please review your code immediately.', DEBUG_DEVELOPER);
        }
        $CFG = $this->realcfg;
    }

    /**
     * Switch $USER->id to a test value.
     *
     * It might be worth making this method do more robuse $USER switching in future,
     * however, this is sufficient for my needs at present.
     */
    protected function switch_global_user_id($userid) {
        global $USER;
        if (!is_null($this->realuserid)) {
            debugging('switch_global_user_id called when $USER->id was already switched to a different value. This suggest you are doing something wrong and dangerous. Please review your code immediately.', DEBUG_DEVELOPER);
        } else {
            $this->realuserid = $USER->id;
        }
        $USER->id = $userid;
    }

    /**
     * Revert $USER->id to the real value.
     */
    protected function revert_global_user_id() {
        global $USER;
        if (is_null($this->realuserid)) {
            debugging('revert_global_user_id called without switch_global_user_id having been called first. This suggest you are doing something wrong and dangerous. Please review your code immediately.', DEBUG_DEVELOPER);
        } else {
            $USER->id = $this->realuserid;
            $this->realuserid = null;
        }
    }

    /**
     * Check that the user has not forgotten to clean anything up, and if they
     * have, display a rude message and clean it up for them.
     */
    private function automatic_clean_up() {
        global $DB, $CFG;
        $cleanmore = false;

        // Drop any test tables that were created.
        foreach ($this->tables as $tablename => $notused) {
            $this->drop_test_table($tablename);
        }

        // Switch back to the real DB if necessary.
        if ($DB !== $this->realdb) {
            $this->revert_to_real_db();
            $cleanmore = true;
        }

        // Switch back to the real CFG if necessary.
        if (isset($CFG->testcfg)) {
            $this->revert_to_real_cfg();
            $cleanmore = true;
        }

        // revert_global_user_id if necessary.
        if (!is_null($this->realuserid)) {
            $this->revert_global_user_id();
            $cleanmore = true;
        }

        if ($cleanmore) {
            accesslib_clear_all_caches_for_unit_testing();
            $course = 'reset';
            get_fast_modinfo($course);
        }
    }

    public function tearDown() {
        $this->automatic_clean_up();
        parent::tearDown();
    }

    public function __destruct() {
        // Should not be necessary thanks to tearDown, but no harm in belt and braces.
        $this->automatic_clean_up();
    }

    /**
     * Create a test table just like a real one, getting getting the definition from
     * the specified install.xml file.
     * @param string $tablename the name of the test table.
     * @param string $installxmlfile the install.xml file in which this table is defined.
     *      $CFG->dirroot . '/' will be prepended, and '/db/install.xml' appended,
     *      so you need only specify, for example, 'mod/quiz'.
     */
    protected function create_test_table($tablename, $installxmlfile) {
        global $CFG;
        $dbman = $this->testdb->get_manager();
        if (isset($this->tables[$tablename])) {
            debugging('You are attempting to create test table ' . $tablename . ' again. It already exists. Please review your code immediately.', DEBUG_DEVELOPER);
            return;
        }
        if ($dbman->table_exists($tablename)) {
            debugging('This table ' . $tablename . ' already exists from a previous execution. If the error persists you will need to review your code to ensure it is being created only once.', DEBUG_DEVELOPER);
            $dbman->drop_table(new xmldb_table($tablename));
        }
        $dbman->install_one_table_from_xmldb_file($CFG->dirroot . '/' . $installxmlfile . '/db/install.xml', $tablename, true); // with structure cache enabled!
        $this->tables[$tablename] = 1;
    }

    /**
     * Convenience method for calling create_test_table repeatedly.
     * @param array $tablenames an array of table names.
     * @param string $installxmlfile the install.xml file in which this table is defined.
     *      $CFG->dirroot . '/' will be prepended, and '/db/install.xml' appended,
     *      so you need only specify, for example, 'mod/quiz'.
     */
    protected function create_test_tables($tablenames, $installxmlfile) {
        foreach ($tablenames as $tablename) {
            $this->create_test_table($tablename, $installxmlfile);
        }
    }

    /**
     * Drop a test table.
     * @param $tablename the name of the test table.
     */
    protected function drop_test_table($tablename) {
        if (!isset($this->tables[$tablename])) {
            debugging('You are attempting to drop test table ' . $tablename . ' but it does not exist. Please review your code immediately.', DEBUG_DEVELOPER);
            return;
        }
        $dbman = $this->testdb->get_manager();
        $table = new xmldb_table($tablename);
        $dbman->drop_table($table);
        unset($this->tables[$tablename]);
    }

    /**
     * Convenience method for calling drop_test_table repeatedly.
     * @param array $tablenames an array of table names.
     */
    protected function drop_test_tables($tablenames) {
        foreach ($tablenames as $tablename) {
            $this->drop_test_table($tablename);
        }
    }

    /**
     * Load a table with some rows of data. A typical call would look like:
     *
     * $config = $this->load_test_data('config_plugins',
     *         array('plugin', 'name', 'value'), array(
     *         array('frog', 'numlegs', 2),
     *         array('frog', 'sound', 'croak'),
     *         array('frog', 'action', 'jump'),
     * ));
     *
     * @param string $table the table name.
     * @param array $cols the columns to fill.
     * @param array $data the data to load.
     * @return array $objects corresponding to $data.
     */
    protected function load_test_data($table, array $cols, array $data) {
        $results = array();
        foreach ($data as $rowid => $row) {
            $obj = new stdClass;
            foreach ($cols as $key => $colname) {
                $obj->$colname = $row[$key];
            }
            $obj->id = $this->testdb->insert_record($table, $obj);
            $results[$rowid] = $obj;
        }
        return $results;
    }

    /**
     * Clean up data loaded with load_test_data. The call corresponding to the
     * example load above would be:
     *
     * $this->delete_test_data('config_plugins', $config);
     *
     * @param string $table the table name.
     * @param array $rows the rows to delete. Actually, only $rows[$key]->id is used.
     */
    protected function delete_test_data($table, array $rows) {
        $ids = array();
        foreach ($rows as $row) {
            $ids[] = $row->id;
        }
        $this->testdb->delete_records_list($table, 'id', $ids);
    }
}


/**
 * @package moodlecore
 * @subpackage simpletestex
 * @copyright &copy; 2006 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class FakeDBUnitTestCase extends UnitTestCase {
    public $tables = array();
    public $pkfile;
    public $cfg;
    public $DB;

    /**
     * In the constructor, record the max(id) of each test table into a csv file.
     * If this file already exists, it means that a previous run of unit tests
     * did not complete, and has left data undeleted in the DB. This data is then
     * deleted and the file is retained. Otherwise it is created.
     *
     * throws moodle_exception if CSV file cannot be created
     */
    public function __construct($label = false) {
        global $DB, $CFG;

        if (empty($CFG->unittestprefix)) {
            return;
        }

        parent::UnitTestCase($label);
        // MDL-16483 Get PKs and save data to text file

        $this->pkfile = $CFG->dataroot.'/testtablespks.csv';
        $this->cfg = $CFG;

        UnitTestDB::instantiate();

        $tables = $DB->get_tables();

        // The file exists, so use it to truncate tables (tests aborted before test data could be removed)
        if (file_exists($this->pkfile)) {
            $this->truncate_test_tables($this->get_table_data($this->pkfile));

        } else { // Create the file
            $tabledata = '';

            foreach ($tables as $table) {
                if ($table != 'sessions') {
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

        if (empty($CFG->unittestprefix)) {
            return;
        }

        $tables = $DB->get_tables();

        foreach ($tables as $table) {
            if ($table != 'sessions' && isset($tabledata[$table])) {
                // $DB->delete_records_select($table, "id > ?", array($tabledata[$table]));
            }
        }
    }

    /**
     * Given a filename, opens it and parses the csv contained therein. It expects two fields per line:
     * 1. Table name
     * 2. Max id
     *
     * throws moodle_exception if file doesn't exist
     *
     * @param string $filename
     */
    public function get_table_data($filename) {
        global $CFG;

        if (empty($CFG->unittestprefix)) {
            return;
        }

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
            throw new moodle_exception('testtablescsvfilemissing', 'simpletest', '', $a);
            return false;
        }
    }

    /**
     * Method called before each test method. Replaces the real $DB with the one configured for unit tests (different prefix, $CFG->unittestprefix).
     * Also detects if this config setting is properly set, and if the user table exists.
     * @todo Improve detection of incorrectly built DB test tables (e.g. detect version discrepancy and offer to upgrade/rebuild)
     */
    public function setUp() {
        global $DB, $CFG;

        if (empty($CFG->unittestprefix)) {
            return;
        }

        parent::setUp();
        $this->DB =& $DB;
        ob_start();
    }

    /**
     * Method called after each test method. Doesn't do anything extraordinary except restore the global $DB to the real one.
     */
    public function tearDown() {
        global $DB, $CFG;

        if (empty($CFG->unittestprefix)) {
            return;
        }

        if (empty($DB)) {
            $DB = $this->DB;
        }
        $DB->cleanup();
        parent::tearDown();

        // Output buffering
        if (ob_get_length() > 0) {
            ob_end_flush();
        }
    }

    /**
     * This will execute once all the tests have been run. It should delete the text file holding info about database contents prior to the tests
     * It should also detect if data is missing from the original tables.
     */
    public function __destruct() {
        global $CFG, $DB;

        if (empty($CFG->unittestprefix)) {
            return;
        }

        $CFG = $this->cfg;
        $this->tearDown();
        UnitTestDB::restore();
        fulldelete($this->pkfile);
    }

    /**
     * Load a table with some rows of data. A typical call would look like:
     *
     * $config = $this->load_test_data('config_plugins',
     *         array('plugin', 'name', 'value'), array(
     *         array('frog', 'numlegs', 2),
     *         array('frog', 'sound', 'croak'),
     *         array('frog', 'action', 'jump'),
     * ));
     *
     * @param string $table the table name.
     * @param array $cols the columns to fill.
     * @param array $data the data to load.
     * @return array $objects corresponding to $data.
     */
    public function load_test_data($table, array $cols, array $data) {
        global $CFG, $DB;

        if (empty($CFG->unittestprefix)) {
            return;
        }

        $results = array();
        foreach ($data as $rowid => $row) {
            $obj = new stdClass;
            foreach ($cols as $key => $colname) {
                $obj->$colname = $row[$key];
            }
            $obj->id = $DB->insert_record($table, $obj);
            $results[$rowid] = $obj;
        }
        return $results;
    }

    /**
     * Clean up data loaded with load_test_data. The call corresponding to the
     * example load above would be:
     *
     * $this->delete_test_data('config_plugins', $config);
     *
     * @param string $table the table name.
     * @param array $rows the rows to delete. Actually, only $rows[$key]->id is used.
     */
    public function delete_test_data($table, array $rows) {
        global $CFG, $DB;

        if (empty($CFG->unittestprefix)) {
            return;
        }

        $ids = array();
        foreach ($rows as $row) {
            $ids[] = $row->id;
        }
        $DB->delete_records_list($table, 'id', $ids);
    }
}

/**
 * This is a Database Engine proxy class: It replaces the global object $DB with itself through a call to the
 * static instantiate() method, and restores the original global $DB through restore().
 * Internally, it routes all calls to $DB to a real instance of the database engine (aggregated as a member variable),
 * except those that are defined in this proxy class. This makes it possible to add extra code to the database engine
 * without subclassing it.
 *
 * @package moodlecore
 * @subpackage simpletestex
 * @copyright &copy; 2006 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class UnitTestDB {
    public static $DB;
    private static $real_db;

    public $table_data = array();

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

        if (empty(UnitTestDB::$DB)) {
            UnitTestDB::$DB = moodle_database::get_driver_instance($CFG->dbtype, $CFG->dblibrary);
            UnitTestDB::$DB->connect($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname, $CFG->unittestprefix);
        }

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
     *
     * throws moodle_exception If trying to update a record not inserted by unit tests.
     */
    public function update_record($table, $dataobject, $bulk=false) {
        global $DB;
        if ((empty($this->table_data[$table]) || !in_array($dataobject->id, $this->table_data[$table])) && !($table == 'course_categories' && $dataobject->id == 1)) {
            // return UnitTestDB::$DB->update_record($table, $dataobject, $bulk);
            $a = new stdClass();
            $a->id = $dataobject->id;
            $a->table = $table;
            throw new moodle_exception('updatingnoninsertedrecord', 'simpletest', '', $a);
        } else {
            return UnitTestDB::$DB->update_record($table, $dataobject, $bulk);
        }
    }

    /**
     * Overriding delete_record: If we are deleting a record that was NOT inserted by unit tests,
     * throw an exception and cancel delete.
     *
     * throws moodle_exception If trying to delete a record not inserted by unit tests.
     */
    public function delete_records($table, array $conditions=array()) {
        global $DB;
        $tables_to_ignore = array('context_temp');

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
            if (!in_array($table, $tables_to_ignore) && (empty($this->table_data[$table]) || !in_array($id, $this->table_data[$table]))) {
                $proceed_with_delete = false;
                $a->id = $id;
                break;
            }
        }

        if ($proceed_with_delete) {
            return UnitTestDB::$DB->delete_records($table, $conditions);
        } else {
            throw new moodle_exception('deletingnoninsertedrecord', 'simpletest', '', $a);
        }
    }

    /**
     * Overriding delete_records_select: If we are deleting a record that was NOT inserted by unit tests,
     * throw an exception and cancel delete.
     *
     * throws moodle_exception If trying to delete a record not inserted by unit tests.
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
            throw new coding_exception('$conditions is not an array.');
        }
        return UnitTestDB::$DB->get_field($table, $return, $conditions);
    }
}
