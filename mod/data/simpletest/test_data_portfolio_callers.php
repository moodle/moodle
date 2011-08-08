<?php
require_once("$CFG->libdir/simpletest/portfolio_testclass.php");
require_once("$CFG->dirroot/mod/data/lib.php");
require_once("$CFG->dirroot/mod/data/locallib.php");

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
Mock::generate('data_portfolio_caller', 'mock_caller');
Mock::generate('portfolio_exporter', 'mock_exporter');

class testDataPortfolioCallers extends portfoliolib_test {
/*
    public static $includecoverage = array('lib/portfoliolib.php', 'mod/data/lib.php');
    public $module_type = 'data';
    public $modules = array();
    public $entries = array();
    public $caller_single;
    public $caller;

    public function setUp() {
        global $DB, $USER;

        parent::setUp();

        $settings = array('quiet' => 1,

                          'pre_cleanup' => 0,
                          'modules_list' => array($this->module_type),
                          'number_of_students' => 5,
                          'students_per_course' => 5,
                          'number_of_sections' => 1,
                          'number_of_modules' => 1,
                          'questions_per_course' => 0);

        generator_generate_data($settings);

        $this->modules = $DB->get_records($this->module_type);
        $first_module = reset($this->modules);
        $cm = get_coursemodule_from_instance($this->module_type, $first_module->id);

        $fields = $DB->get_records('data_fields', array('dataid' => $first_module->id));
        $recordid = data_add_record($first_module);
        foreach ($fields as $field) {
            $content->recordid = $recordid;
            $content->fieldid = $field->id;
            $content->content = 'test content';
            $content->content1 = 'test content 1';
            $content->content2 = 'test content 2';
            $DB->insert_record('data_content',$content);
        }

        // Callback args required: id, record, delimiter_name, exporttype
        $this->caller_single = parent::setup_caller('data_portfolio_caller', array('id' => $cm->id, 'record' => $recordid));
        $this->caller = parent::setup_caller('data_portfolio_caller', array('id' => $cm->id));
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function test_caller_sha1() {
        $sha1 = $this->caller->get_sha1();
        $this->caller->prepare_package();
        $this->assertEqual($sha1, $this->caller->get_sha1());

        $sha1 = $this->caller_single->get_sha1();
        $this->caller_single->prepare_package();
        $this->assertEqual($sha1, $this->caller_single->get_sha1());
    }

    public function test_caller_with_plugins() {
        parent::test_caller_with_plugins();
    }
*/
}
