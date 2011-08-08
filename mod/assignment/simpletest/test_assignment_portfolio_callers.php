<?php
require_once("$CFG->libdir/simpletest/portfolio_testclass.php");
require_once("$CFG->dirroot/mod/assignment/lib.php");
require_once("$CFG->dirroot/mod/assignment/locallib.php");

Mock::generate('assignment_portfolio_caller', 'mock_caller');
Mock::generate('portfolio_exporter', 'mock_exporter');


class testAssignmentPortfolioCallers extends portfoliolib_test {
    public static $includecoverage = array('lib/portfoliolib.php', 'mod/assignment/lib.php');
    public $module_type = 'assignment';
    public $modules = array();
    public $entries = array();
    public $caller;

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
     * 
    public function setUp() {
        global $DB, $USER;

        parent::setUp();
        $assignment_types = new stdClass();
        $assignment_types->type = GENERATOR_SEQUENCE;
        $assignment_types->options = array('online');

        $settings = array('quiet' => 1,
                          'modules_list' => array($this->module_type), 'assignment_grades' => true,
                          'assignment_type' => $assignment_types,
                          'number_of_students' => 5, 'students_per_course' => 5, 'number_of_sections' => 1,
                          'number_of_modules' => 3, 'questions_per_course' => 0);

        generator_generate_data($settings);

        $this->modules = $DB->get_records($this->module_type);
        $first_module = reset($this->modules);
        $cm = get_coursemodule_from_instance($this->module_type, $first_module->id);
        $submissions = $DB->get_records('assignment_submissions', array('assignment' => $first_module->id));
        $first_submission = reset($submissions);

        $this->caller = parent::setup_caller('assignment_portfolio_caller', array('id' => $cm->id), $first_submission->userid);
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function test_caller_sha1() {
        $sha1 = $this->caller->get_sha1();
        $this->caller->prepare_package();
        $this->assertEqual($sha1, $this->caller->get_sha1());
    }

    public function test_caller_with_plugins() {
        parent::test_caller_with_plugins();
    }
    */
}

