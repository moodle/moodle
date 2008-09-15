<?php // $Id$
require_once($CFG->libdir.'/simpletest/testportfoliolib.php');
require_once($CFG->dirroot.'/mod/resource/lib.php');
require_once($CFG->dirroot.'/admin/generator.php');

Mock::generate('resource_portfolio_caller', 'mock_caller');
Mock::generate('portfolio_exporter', 'mock_exporter');

class testResourcePortfolioCallers extends portfoliolib_test {
    public $module_type = 'resource';
    public $modules = array();
    public $entries = array();
    public $caller;

    public function setUp() {
        global $DB, $USER;

        parent::setUp();

        $settings = array('quiet' => 1, 'pre_cleanup' => 1,
                          'modules_list' => array($this->module_type),
                          'number_of_students' => 15, 'students_per_course' => 15, 'number_of_sections' => 1,
                          'number_of_modules' => 1, 'messages_per_resource' => 15);

        generator_generate_data($settings);

        $this->modules = $DB->get_records($this->module_type);
        $first_module = reset($this->modules);
        $cm = get_coursemodule_from_instance($this->module_type, $first_module->id);

        $this->caller = parent::setup_caller('resource_portfolio_caller', array('id' => $cm->id));
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
