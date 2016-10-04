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

    /** @var array Fixtures features which are available. */
    private $featurepaths = array(
        'default' => array(
            'test_1.feature',
            'test_2.feature',
        ),
        'withfeatures' => array(
            'theme_test_1.feature',
            'theme_test_2.feature',
            'theme_test_3.feature',
            'theme_test_4.feature',
            'theme_test_5.feature',
        ),
        'nofeatures' => array()
    );

    /** @var array Fixture contexts which are available */
    private $contextspath = array(
        'default' => array(
            'behat_test_context_1',
            'behat_test_context_2'
        ),
        'withfeatures' => array(
            'behat_test_context_2',
            'behat_theme_withfeatures_test_context_2',
            'behat_theme_withfeatures_behat_test_context_1'
        ),
        'nofeatures' => array(
            'behat_test_context_1',
            'behat_theme_nofeatures_test_context_1',
            'behat_theme_nofeatures_behat_test_context_2'
        ),
    );

    private $corefatures = array('test_1' => __DIR__.'/fixtures/core/test_1.feature',
                                 'test_2' => __DIR__.'/fixtures/core/test_2.feature');

    private $corecontexts = array('behat_test_context_1' => __DIR__.'/fixtures/core/behat_test_context_1.php',
                                  'behat_test_context_2' => __DIR__.'/fixtures/core/behat_test_context_2.php');

    /**
     * Setup test.
     */
    public function setup() {
        global $CFG;

        $this->resetAfterTest();
        $CFG->behat_wwwroot = 'http://example.com/behat';
    }

    /**
     * Utility function to build mock object.
     *
     * @param  behat_config_util $behatconfigutil
     * @param bool $notheme
     * @return mixed
     */
    private function get_behat_config_util($behatconfigutil, $notheme = false) {
        // Create a map of arguments to return values.
        $map = array(
            array('withfeatures', __DIR__.'/fixtures/theme/withfeatures'),
            array('nofeatures', __DIR__.'/fixtures/theme/nofeatures')
        );

        // List of themes is const for test.
        if ($notheme) {
            $themelist = array();
        } else {
            $themelist = array('withfeatures', 'nofeatures');
        }

        $behatconfigutil->expects($this->any())
            ->method('get_list_of_themes')
            ->will($this->returnValue($themelist));

        // Theme directory for testing.
        $behatconfigutil->expects($this->any())
            ->method('get_theme_test_directory')
            ->will($this->returnValueMap($map));

        return $behatconfigutil;
    }

    /**
     * Behat config for single run.
     */
    public function test_get_config_file_contents_with_single_run() {

        $mockbuilder = $this->getMockBuilder('behat_config_util');
        $mockbuilder->setMethods(array('get_theme_test_directory', 'get_list_of_themes'));

        $behatconfigutil = $mockbuilder->getMock();

        $behatconfigutil = $this->get_behat_config_util($behatconfigutil);
        $config = $behatconfigutil->get_config_file_contents($this->corefatures, $this->corecontexts);

        // Two suites should be present.
        $suites = $config['default']['suites'];
        $this->assertCount(3, $suites);

        // Check features.
        foreach ($this->featurepaths as $themename => $paths) {
            $this->assertCount(count($paths), $suites[$themename]['paths']);

            foreach ($paths as $key => $feature) {
                $this->assertContains($feature, $suites[$themename]['paths'][$key]);
            }
        }

        // Check contexts.
        foreach ($this->contextspath as $themename => $paths) {
            $this->assertCount(count($paths), $suites[$themename]['contexts']);

            foreach ($paths as $key => $context) {
                $this->assertTrue(in_array($context, $suites[$themename]['contexts']));
            }
        }

        // There are 6 step definitions.
        $this->assertCount(6, $config['default']['extensions']['Moodle\BehatExtension']['steps_definitions']);
    }

    /**
     * Behat config for single run with no theme installed.
     */
    public function test_get_config_file_contents_with_single_run_no_theme() {

        $mockbuilder = $this->getMockBuilder('behat_config_util');
        $mockbuilder->setMethods(array('get_theme_test_directory', 'get_list_of_themes'));

        $behatconfigutil = $mockbuilder->getMock();

        $behatconfigutil = $this->get_behat_config_util($behatconfigutil, true);
        $config = $behatconfigutil->get_config_file_contents($this->corefatures, $this->corecontexts);

        // Two suites should be present.
        $suites = $config['default']['suites'];
        $this->assertCount(1, $suites);

        $featurepaths = array(
            'default' => array(
                'test_1.feature',
                'test_2.feature',
            )
        );

        $contextspath = array(
            'default' => array(
                'behat_test_context_1',
                'behat_test_context_2'
            )
        );

        // Check features.
        foreach ($featurepaths as $themename => $paths) {
            $this->assertCount(count($paths), $suites[$themename]['paths']);

            foreach ($paths as $key => $feature) {
                $this->assertContains($feature, $suites[$themename]['paths'][$key]);
            }
        }

        // Check contexts.
        foreach ($contextspath as $themename => $paths) {
            $this->assertCount(count($paths), $suites[$themename]['contexts']);

            foreach ($paths as $key => $context) {
                $this->assertTrue(in_array($context, $suites[$themename]['contexts']));
            }
        }

        // There are 6 step definitions.
        $this->assertCount(2, $config['default']['extensions']['Moodle\BehatExtension']['steps_definitions']);
    }

    /**
     * Behat config for parallel run.
     */
    public function test_get_config_file_contents_with_parallel_run() {

        $mockbuilder = $this->getMockBuilder('behat_config_util');
        $mockbuilder->setMethods(array('get_theme_test_directory', 'get_list_of_themes'));

        $behatconfigutil = $mockbuilder->getMock();

        $behatconfigutil = $this->get_behat_config_util($behatconfigutil);

        // Test first run out of 3.
        $config = $behatconfigutil->get_config_file_contents($this->corefatures, $this->corecontexts, '', 3, 1);
        // Three suites should be present.
        $suites = $config['default']['suites'];
        $this->assertCount(3, $suites);
        // There is first feature file in first run.
        $featurepaths = array(
            'default' => array('test_1.feature'),
            'withfeatures' => array('theme_test_1.feature', 'theme_test_2.feature'),
            'nofeatures' => array()
        );
        // Check features.
        foreach ($featurepaths as $themename => $paths) {
            $this->assertCount(count($paths), $suites[$themename]['paths']);

            foreach ($paths as $key => $feature) {
                $this->assertContains($feature, $suites[$themename]['paths'][$key]);
            }
        }
        // Check contexts.
        foreach ($this->contextspath as $themename => $paths) {
            $this->assertCount(count($paths), $suites[$themename]['contexts']);

            foreach ($paths as $key => $context) {
                $this->assertTrue(in_array($context, $suites[$themename]['contexts']));
            }
        }
        // There are 6 step definitions.
        $this->assertCount(6, $config['default']['extensions']['Moodle\BehatExtension']['steps_definitions']);

        // Test second run out of 3.
        $config = $behatconfigutil->get_config_file_contents('', '', '', 3, 2);
        // Three suites should be present.
        $suites = $config['default']['suites'];
        $this->assertCount(3, $suites);
        // There is second feature file in first run.
        $featurepaths = array(
            'default' => array('test_2.feature'),
            'withfeatures' => array('theme_test_3.feature', 'theme_test_4.feature'),
            'nofeatures' => array()
        );
        // Check features.
        foreach ($featurepaths as $themename => $paths) {
            $this->assertCount(count($paths), $suites[$themename]['paths']);

            foreach ($paths as $key => $feature) {
                $this->assertContains($feature, $suites[$themename]['paths'][$key]);
            }
        }
        // Check contexts.
        foreach ($this->contextspath as $themename => $paths) {
            $this->assertCount(count($paths), $suites[$themename]['contexts']);

            foreach ($paths as $key => $context) {
                $this->assertTrue(in_array($context, $suites[$themename]['contexts']));
            }
        }
        // There are 6 step definitions.
        $this->assertCount(6, $config['default']['extensions']['Moodle\BehatExtension']['steps_definitions']);

        // Test third run out of 3.
        $config = $behatconfigutil->get_config_file_contents('', '', '', 3, 3);
        $suites = $config['default']['suites'];
        $this->assertCount(3, $suites);
        // There is second feature file in first run.
        $featurepaths = array(
            'default' => array(),
            'withfeatures' => array('theme_test_5.feature'),
            'nofeatures' => array()
        );
        // Check features.
        foreach ($featurepaths as $themename => $paths) {
            $this->assertCount(count($paths), $suites[$themename]['paths']);

            foreach ($paths as $key => $feature) {
                $this->assertContains($feature, $suites[$themename]['paths'][$key]);
            }
        }
        // Check contexts.
        foreach ($this->contextspath as $themename => $paths) {
            $this->assertCount(count($paths), $suites[$themename]['contexts']);

            foreach ($paths as $key => $context) {
                $this->assertTrue(in_array($context, $suites[$themename]['contexts']));
            }
        }
        // There are 6 step definitions.
        $this->assertCount(6, $config['default']['extensions']['Moodle\BehatExtension']['steps_definitions']);
    }

    /**
     * Behat config for parallel run.
     */
    public function test_get_config_file_contents_with_parallel_run_optimize_tags() {

        $mockbuilder = $this->getMockBuilder('behat_config_util');
        $mockbuilder->setMethods(array('get_theme_test_directory', 'get_list_of_themes'));

        $behatconfigutil = $mockbuilder->getMock();

        $behatconfigutil = $this->get_behat_config_util($behatconfigutil);

        // Test first run out of 3.
        $config = $behatconfigutil->get_config_file_contents($this->corefatures, $this->corecontexts, '@commontag', 3, 1);

        // Three suites should be present.
        $suites = $config['default']['suites'];
        $this->assertCount(3, $suites);
        // There is first feature file in first run.
        $featurepaths = array(
            'default' => array('test_1.feature'),
            'withfeatures' => array('theme_test_1.feature', 'theme_test_3.feature'),
            'nofeatures' => array()
        );
        // Check features.
        foreach ($featurepaths as $themename => $paths) {
            $this->assertCount(count($paths), $suites[$themename]['paths']);

            foreach ($paths as $key => $feature) {
                $this->assertContains($feature, $suites[$themename]['paths'][$key]);
            }
        }
        // Check contexts.
        foreach ($this->contextspath as $themename => $paths) {
            $this->assertCount(count($paths), $suites[$themename]['contexts']);

            foreach ($paths as $key => $context) {
                $this->assertTrue(in_array($context, $suites[$themename]['contexts']));
            }
        }
        // There are 6 step definitions.
        $this->assertCount(6, $config['default']['extensions']['Moodle\BehatExtension']['steps_definitions']);

        // Test second run out of 3.
        $config = $behatconfigutil->get_config_file_contents('', '', '@commontag', 3, 2);

        // Three suites should be present.
        $suites = $config['default']['suites'];
        $this->assertCount(3, $suites);
        // There is second feature file in first run.
        $featurepaths = array(
            'default' => array('test_2.feature'),
            'withfeatures' => array('theme_test_2.feature', 'theme_test_4.feature'),
            'nofeatures' => array()
        );
        // Check features.
        foreach ($featurepaths as $themename => $paths) {
            $this->assertCount(count($paths), $suites[$themename]['paths']);

            foreach ($paths as $key => $feature) {
                $this->assertContains($feature, $suites[$themename]['paths'][$key]);
            }
        }
        // Check contexts.
        foreach ($this->contextspath as $themename => $paths) {
            $this->assertCount(count($paths), $suites[$themename]['contexts']);

            foreach ($paths as $key => $context) {
                $this->assertTrue(in_array($context, $suites[$themename]['contexts']));
            }
        }
        // There are 6 step definitions.
        $this->assertCount(6, $config['default']['extensions']['Moodle\BehatExtension']['steps_definitions']);

        // Test third run out of 3.
        $config = $behatconfigutil->get_config_file_contents('', '', '', 3, 3);
        $suites = $config['default']['suites'];
        $this->assertCount(3, $suites);
        // There is second feature file in first run.
        $featurepaths = array(
            'default' => array(),
            'withfeatures' => array('theme_test_5.feature'),
            'nofeatures' => array()
        );
        // Check features.
        foreach ($featurepaths as $themename => $paths) {
            $this->assertCount(count($paths), $suites[$themename]['paths']);

            foreach ($paths as $key => $feature) {
                $this->assertContains($feature, $suites[$themename]['paths'][$key]);
            }
        }
        // Check contexts.
        foreach ($this->contextspath as $themename => $paths) {
            $this->assertCount(count($paths), $suites[$themename]['contexts']);

            foreach ($paths as $key => $context) {
                $this->assertTrue(in_array($context, $suites[$themename]['contexts']));
            }
        }
        // There are 6 step definitions.
        $this->assertCount(6, $config['default']['extensions']['Moodle\BehatExtension']['steps_definitions']);
    }

    /**
     * Test if clean features key and path is returned.
     * @dataProvider clean_features_path_list
     */
    public function test_get_clean_feature_key_and_path($featurepath, $key, $cleanfeaturepath) {
        global $CFG;

        // This is a hack so directory name is correctly detected in tests.
        //FIXME: MDL-55722 work out why this is necessary..
        $oldroot = $CFG->dirroot;
        $CFG->dirroot = 'C:';

        $behatconfigutil = new behat_config_util();

        // Fix expected directory path for OS.
        $cleanfeaturepath = testing_cli_fix_directory_separator($cleanfeaturepath);

        list($retkey, $retcleanfeaturepath) = $behatconfigutil->get_clean_feature_key_and_path($featurepath);

        $this->assertEquals($key, $retkey);
        $this->assertEquals($cleanfeaturepath, $retcleanfeaturepath);
        //FIXME: MDL-55722 work out why this is necessary..
        $CFG->dirroot = $oldroot;
    }

    public function clean_features_path_list() {
        return array(
            ['/home/test/this/that/test/behat/mod_assign.feature', 'mod_assign_behat_test_that_this_test', '/home/test/this/that/test/behat/mod_assign.feature'],
            ['/home/this/that/test/behat/mod_assign.feature', 'mod_assign_behat_test_that_this_home', '/home/this/that/test/behat/mod_assign.feature'],
            ['/home/that/test/behat/mod_assign.feature', 'mod_assign_behat_test_that_home', '/home/that/test/behat/mod_assign.feature'],
            ['/home/test/behat/mod_assign.feature', 'mod_assign_behat_test_home', '/home/test/behat/mod_assign.feature'],
            ['mod_assign.feature', 'mod_assign', 'mod_assign.feature'],
            ['C:\test\this\that\test\behat\mod_assign.feature', 'mod_assign_behat_test_that_this_test', 'C:\test\this\that\test\behat\mod_assign.feature'],
            ['C:\this\that\test\behat\mod_assign.feature', 'mod_assign_behat_test_that_this', 'C:\this\that\test\behat\mod_assign.feature'],
            ['C:\that\test\behat\mod_assign.feature', 'mod_assign_behat_test_that', 'C:\that\test\behat\mod_assign.feature'],
            ['C:\test\behat\mod_assign.feature', 'mod_assign_behat_test', 'C:\test\behat\mod_assign.feature'],
            ['C:\mod_assign.feature', 'mod_assign', 'C:\mod_assign.feature'],
        );
    }
}
// @codeCoverageIgnoreEnd
