<?php
require_once("$CFG->libdir/simpletest/portfolio_testclass.php");
require_once("$CFG->dirroot/mod/glossary/lib.php");
require_once("$CFG->dirroot/mod/glossary/locallib.php");

/*
 * TODO: The portfolio unit tests were obselete and did not work.
 * They have been commented out so that they do not break the
 * unit tests in Moodle 2.
 *
 * At some point:
 * 1. These tests should be audited to see which ones were valuable.
 * 2. The useful ones should be rewritten using the current standards
 *    for writing test cases.
 *
 * This might be left until Moodle 2.1 when the test case framework
 * is due to change.
 */
Mock::generate('glossary_entry_portfolio_caller', 'mock_entry_caller');
Mock::generate('glossary_csv_portfolio_caller', 'mock_csv_caller');
Mock::generate('portfolio_exporter', 'mock_exporter');

class testGlossaryPortfolioCallers extends portfoliolib_test {
/*
    public static $includecoverage = array('lib/portfoliolib.php', 'mod/glossary/lib.php');
    public $glossaries = array();
    public $entries = array();
    public $entry_caller;
    public $csv_caller;

    public function setUp() {
        global $DB;

        parent::setUp();
        $settings = array('tiny' => 1, 'quiet' => 1, 'pre_cleanup' => 1,
                          'modules_list' => array('glossary'), 'entries_per_glossary' => 20,
                          'number_of_students' => 5, 'students_per_course' => 5, 'number_of_sections' => 1,
                          'number_of_modules' => 1, 'questions_per_course' => 0);
        generator_generate_data($settings);

        $this->glossaries = $DB->get_records('glossary');
        $first_glossary = reset($this->glossaries);
        $cm = get_coursemodule_from_instance('glossary', $first_glossary->id);

        $this->entries = $DB->get_records('glossary_entries', array('glossaryid' => $first_glossary->id));
        $first_entry = reset($this->entries);

        $callbackargs = array('id' => $cm->id, 'entryid' => $first_entry->id);
        $this->entry_caller = parent::setup_caller('glossary_entry_portfolio_caller', $callbackargs);

        $this->csv_caller = parent::setup_caller('glossary_csv_portfolio_caller', $callbackargs);
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function test_entry_caller_sha1() {
        $entry_sha1 = $this->entry_caller->get_sha1();
        $this->entry_caller->prepare_package();
        $this->assertEqual($entry_sha1, $this->entry_caller->get_sha1());
    }

    public function test_csv_caller_sha1() {
        $csv_sha1 = $this->csv_caller->get_sha1();
        $this->csv_caller->prepare_package();
        $this->assertEqual($csv_sha1, $this->csv_caller->get_sha1());
    }

    public function test_caller_with_plugins() {
        parent::test_caller_with_plugins();
    }
*/
}
