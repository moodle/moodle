<?php
require_once("$CFG->libdir/simpletest/testportfoliolib.php");
require_once("$CFG->dirroot/portfolio/boxnet/lib.php");

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
Mock::generate('boxclient', 'mock_boxclient');
Mock::generatePartial('portfolio_plugin_boxnet', 'mock_boxnetplugin', array('ensure_ticket', 'ensure_account_tree'));

class testPortfolioPluginBoxnet extends portfoliolib_test {
/*
    public static $includecoverage = array('lib/portfoliolib.php', 'portfolio/boxnet/lib.php');
    public function setUp() {
        global $DB;

        parent::setUp();
        $this->plugin = new mock_boxnetplugin($this);
        $this->plugin->boxclient = new mock_boxclient();

        $settings = array('tiny' => 1, 'quiet' => 1, 'database_prefix' => 'tst_', 'pre_cleanup' => 1,
                          'modules_list' => array('glossary'), 'entries_per_glossary' => 20,
                          'number_of_students' => 5, 'students_per_course' => 5, 'number_of_sections' => 1,
                          'number_of_modules' => 1, 'questions_per_course' => 0);
        generator_generate_data($settings);
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function test_caller_glossary() {
        global $DB;
        $glossaries = $DB->get_records('glossary');
        print_object($glossaries);
    }

    public function test_something() {
        global $DB;

        $ticket = md5(rand(0,873907));
        $authtoken = 'ezfoeompplpug3ofii4nud0d8tvg96e0';

        $this->plugin->setReturnValue('ensure_account_tree', true);
        $this->plugin->setReturnValue('ensure_ticket', $ticket);

        $this->plugin->boxclient->setReturnValue('renameFile', true);
        $this->plugin->boxclient->setReturnValue('uploadFile', array('status' => 'upload_ok', 'id' => array(1)));
        $this->plugin->boxclient->setReturnValue('createFolder', array(1 => 'folder 1', 2 => 'folder 2'));
        $this->plugin->boxclient->setReturnValue('isError', false);
        $this->plugin->boxclient->authtoken = $authtoken;

        $this->assertTrue($this->plugin->set('exporter', $this->exporter));
        $this->assertTrue($this->plugin->set('ticket', $ticket));
        $this->assertTrue($this->plugin->set('authtoken', $authtoken));
        $this->plugin->set_export_config(array('folder' => 1));

        $this->assertTrue($this->plugin->prepare_package());
        $this->assertTrue($this->plugin->send_package());
    }
*/
}
