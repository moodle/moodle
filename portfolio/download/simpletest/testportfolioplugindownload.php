<?php
require_once($CFG->libdir.'/simpletest/testportfoliolib.php');
require_once($CFG->dirroot.'/portfolio/download/lib.php');

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

Mock::generate('boxclient', 'mock_boxclient');
Mock::generatePartial('portfolio_plugin_download', 'mock_downloadplugin', array('ensure_ticket', 'ensure_account_tree'));
*/

class testPortfolioPluginDownload extends portfoliolib_test {
    public static $includecoverage = array('lib/portfoliolib.php', 'portfolio/download/lib.php');
    public function setUp() {
        parent::setUp();
//        $this->plugin = new mock_boxnetplugin($this);
//        $this->plugin->boxclient = new mock_boxclient();
    }

    public function tearDown() {
        parent::tearDown();
    }

}

