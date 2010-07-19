<?php
require_once($CFG->libdir.'/simpletest/testportfoliolib.php');
require_once($CFG->dirroot.'/portfolio/download/lib.php');

Mock::generate('boxclient', 'mock_boxclient');
Mock::generatePartial('portfolio_plugin_download', 'mock_downloadplugin', array('ensure_ticket', 'ensure_account_tree'));


class testPortfolioPluginDownload extends portfoliolib_test {
    public static $includecoverage = array('lib/portfoliolib.php', 'portfolio/download/lib.php');
    public function setUp() {
        parent::setUp();
        $this->plugin = new mock_boxnetplugin($this);
        $this->plugin->boxclient = new mock_boxclient();
    }

    public function tearDown() {
        parent::tearDown();
    }

}

