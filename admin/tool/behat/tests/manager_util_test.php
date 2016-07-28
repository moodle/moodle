<?php
// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Unit tests for behat manager.
 *
 * @package   tool_behat
 * @copyright  2016 Rajesh Taneja
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/' . $CFG->admin .'/tool/behat/locallib.php');
require_once($CFG->libdir . '/behat/classes/util.php');
require_once($CFG->libdir . '/behat/classes/behat_config_manager.php');

/**
 * Behat manager tests.
 *
 * @package    tool_behat
 * @copyright  2016 Rajesh Taneja
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_behat_manager_util_testcase extends advanced_testcase {

    /**
     * @var array core features.
     */
    private $corefeatures = array(
        'feedback_editpdf_behat_test1' => '/test/moodle/mod/assign/feedback/editpdf/tests/behat/behat_test1.feature',
        'feedback_file_behat_test2' => "C:\\test\\moodle\\mod\\assign\\feedback\\file\\tests\\behat\\behat_test2.feature",
        'moodle_login_behat_test3' => "C:\\test\\moodle/login/tests/behat/behat_test3.feature",
        );


    /**
     * @var array theme features.
     */
    private $themefeatures = array(
            'behat_themetest1_core_behat_tests_testtheme_theme' => '/test/moodle/theme/testtheme/tests/behat/core/behat_themetest1.feature',
            'behat_themetest2_mod_assign_behat_tests_testtheme_theme' => "C:\\test\\moodle\\theme\\testtheme\\tests\\behat\\mod_assign\\behat_themetest2.feature",
            'behat_themetest3_behat_tests_testtheme_theme_moodle' => "C:\\test\\moodle/theme/testtheme/tests/behat/behat_themetest3.feature",
        );

    /**
     * @var array core contexts.
     */
    private $corecontexts = array(
            'behat_context1' => '/test/moodle/mod/assign/feedback/editpdf/tests/behat/behat_context1.php',
            'behat_context2' => "C:\\test\\moodle\\blocks\\comments\\tests\\behat\\behat_context2.php",
            'behat_context3' => "C:\\test\\moodle/lib/editor/atto/tests/behat/behat_context3.php",
        );

    /**
     * @var array Theme contexts for test.
     */
    private $themecontexts = array(
            'behat_theme_testtheme_behat_context1' =>
                '/test/moodle/theme/testtheme/tests/behat/mod_assign/behat_theme_testtheme_behat_context1.php',
            'behat_theme_testtheme_behat_context2' =>
                "C:\\test\\moodle\\theme\\testtheme\\tests\\behat\\block_comments\\behat_theme_testtheme_behat_context2.php",
            'behat_theme_testtheme_behat_context3' =>
                "C:\\test\\moodle/theme/testtheme/tests/behat/editor_atto/behat_theme_testtheme_behat_context3.php"
        );

    /**
     * Keep instance of behat_config_util mock object.
     *
     * @var null
     */
    private $behatconfigutil = null;

    /**
     * Test setup.
     */
    public function setUp() {
        $this->resetAfterTest(true);
        $mockbuilder = $this->getMockBuilder('behat_config_util');
        $mockbuilder->setMethods(array('get_behat_features_for_theme', 'get_behat_contexts_for_theme',
            'get_list_of_themes', 'get_overridden_theme_contexts'));

        $this->behatconfigutil = $mockbuilder->getMock();

        // List of themes is const for test.
        $this->behatconfigutil->expects($this->any())
            ->method('get_list_of_themes')
            ->will($this->returnValue(array('testtheme')));

        $this->behatconfigutil->expects($this->any())
            ->method('get_behat_contexts_for_theme')
            ->with($this->equalTo('testtheme'))
            ->will($this->returnValue(array_keys($this->themecontexts)));

    }

    /**
     * Behat config for single run.
     *
     */
    public function test_get_config_file_contents_with_single_run() {
        global $CFG;

        $CFG->behat_wwwroot = 'http://example.com/behat';

        $behatconfigutil = $this->behatconfigutil;

        // No theme feature exists.
        $behatconfigutil->expects($this->once())
            ->method('get_behat_features_for_theme')
            ->with($this->anything())
            ->will($this->returnValue(array(array(), array())));

        $config = $behatconfigutil->get_config_file_contents($this->corefeatures, $this->corecontexts);

        $expectedconfigwithfeatures = "default:
  formatters:
    moodle_progress:
      output_styles:
        comment:
          - magenta
  suites:
    default:
      paths:
        - /test/moodle/mod/assign/feedback/editpdf/tests/behat/behat_test1.feature
        - 'C:\\test\\moodle\\mod\\assign\\feedback\\file\\tests\\behat\\behat_test2.feature'
        - 'C:\\test\\moodle/login/tests/behat/behat_test3.feature'
      contexts:
        - behat_context1
        - behat_context2
        - behat_context3
    testtheme:
      paths: {  }
      contexts:
        - behat_theme_testtheme_behat_context1
        - behat_theme_testtheme_behat_context2
        - behat_theme_testtheme_behat_context3
  extensions:
    Behat\\MinkExtension:
      base_url: 'http://example.com/behat'
      goutte: null
      selenium2:
        wd_host: 'http://localhost:4444/wd/hub'
";

        $this->assertContains($expectedconfigwithfeatures, $config);

        $expectedstepdefinitions = "steps_definitions:
        behat_context1: /test/moodle/mod/assign/feedback/editpdf/tests/behat/behat_context1.php
        behat_context2: 'C:\\test\\moodle\\blocks\\comments\\tests\\behat\\behat_context2.php'
        behat_context3: 'C:\\test\\moodle/lib/editor/atto/tests/behat/behat_context3.php'
";
        $this->assertContains($expectedstepdefinitions, $config);
    }

    /**
     * Behat config for parallel run.
     */
    public function test_get_config_file_contents_with_parallel_run() {
        global $CFG;

        $CFG->behat_wwwroot = 'http://example.com/behat';
        $behatconfigutil = $this->behatconfigutil;

        // No theme feature exists.
        $behatconfigutil->expects($this->any())
            ->method('get_behat_features_for_theme')
            ->with($this->anything())
            ->will($this->returnValue(array(array(), array())));

        $config = $behatconfigutil->get_config_file_contents($this->corefeatures, $this->corecontexts, '', 3, 1);

        // First run.
        $this->assertContains('/test/moodle/mod/assign/feedback/editpdf/tests/behat/behat_test1.feature',
            $config);
        $this->assertNotContains('C:\\test\\moodle\\mod\\assign\\feedback\\file\\tests\\behat\\behat_test2.feature',
            $config);
        $this->assertNotContains('C:\\test\\moodle/login/tests/behat/behat_test3.feature',
            $config);

        // Second run.
        $config = $behatconfigutil->get_config_file_contents($this->corefeatures, $this->corecontexts, '', 3, 2);

        $this->assertNotContains('/test/moodle/mod/assign/feedback/editpdf/tests/behat/behat_test1.feature',
            $config);
        $this->assertContains('C:\\test\\moodle\\mod\\assign\\feedback\\file\\tests\\behat\\behat_test2.feature',
            $config);
        $this->assertNotContains('C:\\test\\moodle/login/tests/behat/behat_test3.feature',
            $config);

        $config = $behatconfigutil->get_config_file_contents($this->corefeatures, $this->corecontexts, '', 3, 3);

        $this->assertNotContains('/test/moodle/mod/assign/feedback/editpdf/tests/behat/behat_test1.feature',
            $config);
        $this->assertNotContains('C:\\test\\moodle\\mod\\assign\\feedback\\file\\tests\\behat\\behat_test2.feature',
            $config);
        $this->assertContains('C:\\test\\moodle/login/tests/behat/behat_test3.feature',
            $config);
    }

    /**
     * Behat config with theme features.
     */
    public function test_get_config_file_contents_with_theme_features() {
        global $CFG;

        $behatconfigutil = $this->behatconfigutil;

        $suitefeatures = array_merge($this->corefeatures, $this->themefeatures);
        $themefeatures = $this->themefeatures;
        $behatconfigutil->expects($this->once())
            ->method('get_behat_features_for_theme')
            ->with($this->equalTo('testtheme'))
            ->will($this->returnValue(array(array(), $themefeatures)));

        $behatconfigutil->expects($this->once())
            ->method('get_behat_contexts_for_theme')
            ->with($this->equalTo('testtheme'))
            ->will($this->returnValue(array_keys($this->themecontexts)));

        $behatconfigutil->expects($this->once())
            ->method('get_overridden_theme_contexts')
            ->will($this->returnValue($this->themecontexts));
        $behatconfigutil->set_theme_suite_to_include_core_features(true);

        $CFG->behat_wwwroot = 'http://example.com/behat';
        $config = $behatconfigutil->get_config_file_contents($suitefeatures, $this->corecontexts);

        $expectedconfigwithfeatures = "default:
  formatters:
    moodle_progress:
      output_styles:
        comment:
          - magenta
  suites:
    default:
      paths:
        - /test/moodle/mod/assign/feedback/editpdf/tests/behat/behat_test1.feature
        - 'C:\\test\\moodle\\mod\\assign\\feedback\\file\\tests\\behat\\behat_test2.feature'
        - 'C:\\test\\moodle/login/tests/behat/behat_test3.feature'
      contexts:
        - behat_context1
        - behat_context2
        - behat_context3
    testtheme:
      paths:
        - /test/moodle/mod/assign/feedback/editpdf/tests/behat/behat_test1.feature
        - 'C:\\test\\moodle\\mod\\assign\\feedback\\file\\tests\\behat\\behat_test2.feature'
        - 'C:\\test\\moodle/login/tests/behat/behat_test3.feature'
        - /test/moodle/theme/testtheme/tests/behat/core/behat_themetest1.feature
        - 'C:\\test\\moodle\\theme\\testtheme\\tests\\behat\\mod_assign\\behat_themetest2.feature'
        - 'C:\\test\\moodle/theme/testtheme/tests/behat/behat_themetest3.feature'
      contexts:
        - behat_theme_testtheme_behat_context1
        - behat_theme_testtheme_behat_context2
        - behat_theme_testtheme_behat_context3
  extensions:
    Behat\\MinkExtension:
      base_url: 'http://example.com/behat'
      goutte: null
      selenium2:
        wd_host: 'http://localhost:4444/wd/hub'
";
        $this->assertContains($expectedconfigwithfeatures, $config);

        $expectedstepdefinitions = "steps_definitions:
        behat_context1: /test/moodle/mod/assign/feedback/editpdf/tests/behat/behat_context1.php
        behat_context2: 'C:\\test\\moodle\\blocks\\comments\\tests\\behat\\behat_context2.php'
        behat_context3: 'C:\\test\\moodle/lib/editor/atto/tests/behat/behat_context3.php'
        behat_theme_testtheme_behat_context1: /test/moodle/theme/testtheme/tests/behat/mod_assign/behat_theme_testtheme_behat_context1.php
        behat_theme_testtheme_behat_context2: 'C:\\test\\moodle\\theme\\testtheme\\tests\\behat\\block_comments\\behat_theme_testtheme_behat_context2.php'
        behat_theme_testtheme_behat_context3: 'C:\\test\\moodle/theme/testtheme/tests/behat/editor_atto/behat_theme_testtheme_behat_context3.php'";

        $this->assertContains($expectedstepdefinitions, $config);
    }

    /**
     * Behat config for parallel run.
     */
    public function test_get_config_file_contents_with_theme_and_parallel_run() {
        global $CFG;

        $CFG->behat_wwwroot = 'http://example.com/behat';

        $behatconfigutil = $this->behatconfigutil;

        $features = array_merge($this->corefeatures, $this->themefeatures);
        $themefeatures = $this->themefeatures;
        $behatconfigutil->expects($this->atLeastOnce())
            ->method('get_behat_features_for_theme')
            ->with($this->equalTo('testtheme'))
            ->will($this->returnValue(array(array(), $themefeatures)));

        $behatconfigutil->expects($this->atLeastOnce())
            ->method('get_behat_contexts_for_theme')
            ->with($this->equalTo('testtheme'))
            ->will($this->returnValue(array_keys($this->themecontexts)));

        $CFG->behat_wwwroot = 'http://example.com/behat';

        $behatconfigutil->set_theme_suite_to_include_core_features(false);

        $config = $behatconfigutil->get_config_file_contents($features, $this->themecontexts, '', 3, 1);

        // First run.
        $this->assertContains('/test/moodle/mod/assign/feedback/editpdf/tests/behat/behat_test1.feature',
            $config);
        $this->assertNotContains('C:\\test\\moodle\\mod\\assign\\feedback\\file\\tests\\behat\\behat_test2.feature',
            $config);
        $this->assertNotContains('C:\\test\\moodle/login/tests/behat/behat_test3.feature',
            $config);
        // Theme suite features.
        $this->assertContains('/test/moodle/theme/testtheme/tests/behat/core/behat_themetest1.feature',
            $config);
        $this->assertNotContains('C:\\test\\moodle\\theme\\testtheme\\tests\\behat\\mod_assign\\behat_themetest2.feature',
            $config);
        $this->assertNotContains('C:\\test\\moodle/theme/testtheme/tests/behat/behat_themetest3.feature',
            $config);

        // Second run.
        $config = $behatconfigutil->get_config_file_contents($features, $this->themecontexts, '', 3, 2);
        $this->assertNotContains('/test/moodle/mod/assign/feedback/editpdf/tests/behat/behat_test1.feature',
            $config);
        $this->assertContains('C:\\test\\moodle\\mod\\assign\\feedback\\file\\tests\\behat\\behat_test2.feature',
            $config);
        $this->assertNotContains('C:\\test\\moodle/login/tests/behat/behat_test3.feature',
            $config);
        // Theme suite features.
        $this->assertNotContains('/test/moodle/theme/testtheme/tests/behat/core/behat_themetest1.feature',
            $config);
        $this->assertContains('C:\\test\\moodle\\theme\\testtheme\\tests\\behat\\mod_assign\\behat_themetest2.feature',
            $config);
        $this->assertNotContains('C:\\test\\moodle/theme/testtheme/tests/behat/behat_themetest3.feature',
            $config);
    }

    /**
     * Test if clean features key and path is returned.
     * @dataProvider clean_features_path_list
     */
    public function test_get_clean_feature_key_and_path($featurepath, $key, $cleanfeaturepath) {

        $behatconfigutil = $this->behatconfigutil;
        // Fix expected directory path for OS.
        $cleanfeaturepath = str_replace('\\', DIRECTORY_SEPARATOR, $cleanfeaturepath);
        $cleanfeaturepath = str_replace('/', DIRECTORY_SEPARATOR, $cleanfeaturepath);

        if (testing_is_cygwin()) {
            $featurepath = str_replace('\\', '/', $cleanfeaturepath);
        }

        list($retkey, $retcleanfeaturepath) = $behatconfigutil->get_clean_feature_key_and_path($featurepath);

        $this->assertEquals($key, $retkey);
        $this->assertEquals($cleanfeaturepath, $retcleanfeaturepath);
    }

    public function clean_features_path_list() {
        return array(
            ['/home/test/this/that/test/behat/mod_assign.feature', 'mod_assign_behat_test_that_this_test', '/home/test/this/that/test/behat/mod_assign.feature'],
            ['/home/this/that/test/behat/mod_assign.feature', 'mod_assign_behat_test_that_this_home', '/home/this/that/test/behat/mod_assign.feature'],
            ['/home/that/test/behat/mod_assign.feature', 'mod_assign_behat_test_that_home', '/home/that/test/behat/mod_assign.feature'],
            ['/home/test/behat/mod_assign.feature', 'mod_assign_behat_test_home', '/home/test/behat/mod_assign.feature'],
            ['mod_assign.feature', 'mod_assign', 'mod_assign.feature'],
            ['C:\test\this\that\test\behat\mod_assign.feature', 'mod_assign_behat_test_that_this_test', 'C:\test\this\that\test\behat\mod_assign.feature'],
            ['C:\this\that\test\behat\mod_assign.feature', 'mod_assign_behat_test_that_this_C:', 'C:\this\that\test\behat\mod_assign.feature'],
            ['C:\that\test\behat\mod_assign.feature', 'mod_assign_behat_test_that_C:', 'C:\that\test\behat\mod_assign.feature'],
            ['C:\test\behat\mod_assign.feature', 'mod_assign_behat_test_C:', 'C:\test\behat\mod_assign.feature'],
            ['C:\mod_assign.feature', 'mod_assign_C:', 'C:\mod_assign.feature'],
        );
    }
}
// @codeCoverageIgnoreEnd
