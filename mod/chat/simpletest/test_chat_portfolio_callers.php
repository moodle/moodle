<?php // $Id$
require_once($CFG->libdir.'/simpletest/testportfoliolib.php');
require_once($CFG->dirroot.'/mod/chat/lib.php');
require_once($CFG->dirroot.'/admin/generator.php');

Mock::generate('chat_portfolio_caller', 'mock_caller');
Mock::generate('portfolio_exporter', 'mock_exporter');

class testChatPortfolioCallers extends portfoliolib_test {
    public $module_type = 'chat';
    public $modules = array();
    public $entries = array();
    public $caller;

    public function setUp() {
        global $DB, $USER;

        parent::setUp();

        $settings = array('quiet' => 1, 'database_prefix' => 'tst_', 'pre_cleanup' => 1,
                          'modules_list' => array($this->module_type),
                          'number_of_students' => 15, 'students_per_course' => 15, 'number_of_sections' => 1,
                          'number_of_modules' => 1, 'messages_per_chat' => 15);

        generator_generate_data($settings);

        $this->modules = $DB->get_records($this->module_type);
        $first_module = reset($this->modules);
        $cm = get_coursemodule_from_instance($this->module_type, $first_module->id);
        $userid = $DB->get_field('chat_users', 'userid', array('chatid' => $first_module->id));

        $callbackargs = array('id' => $cm->id);
        $this->caller = new chat_portfolio_caller($callbackargs);
        $this->caller->set('exporter', new mock_exporter());

        $user = $DB->get_record('user', array('id' => $userid));
        $this->caller->set('user', $user);
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
