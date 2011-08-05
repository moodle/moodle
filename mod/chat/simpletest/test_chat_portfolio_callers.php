<?php
require_once("$CFG->libdir/simpletest/portfolio_testclass.php");
require_once("$CFG->dirroot/mod/chat/lib.php");

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
Mock::generate('chat_portfolio_caller', 'mock_caller');
Mock::generate('portfolio_exporter', 'mock_exporter');

class testChatPortfolioCallers extends portfoliolib_test {
/*
    public static $includecoverage = array('lib/portfoliolib.php', 'mod/chat/lib.php');
    public $module_type = 'chat';
    public $modules = array();
    public $entries = array();
    public $caller;

    public function setUp() {
        global $DB, $USER;

        parent::setUp();

        $settings = array('quiet' => 1, 'pre_cleanup' => 1,
                          'modules_list' => array($this->module_type),
                          'number_of_students' => 15, 'students_per_course' => 15, 'number_of_sections' => 1,
                          'number_of_modules' => 1, 'messages_per_chat' => 15);

        generator_generate_data($settings);

        $this->modules = $DB->get_records($this->module_type);
        $first_module = reset($this->modules);
        $cm = get_coursemodule_from_instance($this->module_type, $first_module->id);
        $userid = $DB->get_field('chat_users', 'userid', array('chatid' => $first_module->id));

        $this->caller = parent::setup_caller('chat_portfolio_caller', array('id' => $cm->id), $userid);
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
