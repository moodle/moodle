<?php // $Id$
require_once($CFG->libdir.'/simpletest/testportfoliolib.php');
require_once($CFG->dirroot.'/mod/assignment/lib.php');
require_once($CFG->dirroot.'/admin/generator.php');

Mock::generate('assignment_portfolio_caller', 'mock_caller');
Mock::generate('portfolio_exporter', 'mock_exporter');

class testAssignmentPortfolioCallers extends portfoliolib_test {
    public $module_type = 'assignment';
    public $modules = array();
    public $entries = array();
    public $caller;

    public function setUp() {
        global $DB, $USER;

        parent::setUp();

        $settings = array('quiet' => 1, 'database_prefix' => 'tst_', 'pre_cleanup' => 1,
                          'modules_list' => array($this->module_type), 'assignment_grades' => true,
                          'assignment_type' => 'online',
                          'number_of_students' => 5, 'students_per_course' => 5, 'number_of_sections' => 1,
                          'number_of_modules' => 1, 'questions_per_course' => 0);
        generator_generate_data($settings);

        $this->modules = $DB->get_records($this->module_type);
        $first_module = reset($this->modules);
        $cm = get_coursemodule_from_instance($this->module_type, $first_module->id);
        $submissions = $DB->get_records('assignment_submissions', array('assignment' => $first_module->id));
        $first_submission = reset($submissions);

        $callbackargs = array('id' => $cm->id);
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

}
?>
