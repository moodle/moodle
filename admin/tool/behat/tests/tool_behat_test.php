<?php
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
 * Unit tests for admin/tool/behat
 *
 * @package   tool_behat
 * @copyright  2012 David Monllaó
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/' . $CFG->admin .'/tool/behat/locallib.php');

/**
 * Allows access to internal methods without exposing them
 */
class testable_tool_behat extends tool_behat {

    /**
     * @see parent::merge_config()
     * @param mixed $config
     * @param mixed $localconfig
     * @return mixed
     */
    public static function merge_config($config, $localconfig) {
        return parent::merge_config($config, $localconfig);
    }

    /**
     * @see parent::get_config_file_contents()
     * @param string $prefix
     * @param array $features
     * @param array $stepsdefinitions
     * @return string
     */
    public static function get_config_file_contents($prefix, $features, $stepsdefinitions) {
        return parent::get_config_file_contents($prefix, $features, $stepsdefinitions);
    }
}

/**
 * Tool behat tests
 */
class tool_behat_testcase extends advanced_testcase {


     public function test_switch_environment() {

         // Only run the tests if behat dependencies are installed.
         // We don't need to pre-check PHPUnit initialisation because we are running on it.
         if (version_compare(PHP_VERSION, '5.4.0', '>=') && tool_behat::are_behat_dependencies_installed()) {
             tool_behat::switchenvironment('enable');
             $this->assertTrue(tool_behat::is_test_mode_enabled());
             $this->assertFalse(tool_behat::is_test_environment_running());

             // We trigger a debugging() if it's already enabled.
             tool_behat::switchenvironment('enable');
             $this->assertDebuggingCalled();

             tool_behat::switchenvironment('disable');
             $this->assertFalse(tool_behat::is_test_mode_enabled());
             $this->assertFalse(tool_behat::is_test_environment_running());

             // We trigger a debugging() if it's already enabled.
             tool_behat::switchenvironment('disable');
             $this->assertDebuggingCalled();

             // Ensure all continues disabled.
             $this->assertFalse(tool_behat::is_test_mode_enabled());
             $this->assertFalse(tool_behat::is_test_environment_running());
         }
     }

     public function test_merge_configs() {

         // Simple default config.
         $array1 = array(
             'the' => 'same',
             'simple' => 'value',
             'array' => array(
                 'one' => 'arrayvalue1',
                 'two' => 'arrayvalue2'
             )
         );

         // Simple override.
         $array2 = array(
             'simple' => 'OVERRIDDEN1',
             'array' => array(
                 'one' => 'OVERRIDDEN2'
             ),
             'newprofile' => array(
                 'anotherlevel' => array(
                     'andanotherone' => array(
                         'list1',
                         'list2'
                     )
                 )
             )
         );

         $array = testable_tool_behat::merge_config($array1, $array2);

         // Overriddes are applied.
         $this->assertEquals('OVERRIDDEN1', $array['simple']);
         $this->assertEquals('OVERRIDDEN2', $array['array']['one']);

         // Other values are respected.
         $this->assertNotEmpty($array['array']['two']);

         // Completely new nodes are added.
         $this->assertNotEmpty($array['newprofile']);
         $this->assertNotEmpty($array['newprofile']['anotherlevel']['andanotherone']);
         $this->assertEquals('list1', $array['newprofile']['anotherlevel']['andanotherone'][0]);
         $this->assertEquals('list2', $array['newprofile']['anotherlevel']['andanotherone'][1]);

         // Complex override changing vectors to scalars and scalars to vectors.
         $array2 = array(
             'simple' => array(
                 'simple' => 'should',
                 'be' => 'overridden',
                 'by' => 'this-array'
             ),
             'array' => 'one'
         );

         $array = testable_tool_behat::merge_config($array1, $array2);

         // Overrides applied.
         $this->assertNotEmpty($array['simple']);
         $this->assertNotEmpty($array['array']);
         $this->assertTrue(is_array($array['simple']));
         $this->assertFalse(is_array($array['array']));

         // Other values are maintained.
         $this->assertEquals('same', $array['the']);
     }

     public function test_config_file_contents() {
         global $CFG;

         unset($CFG->behatconfig);

         // List.
         $features = array(
             'feature1',
             'feature2',
             'feature3'
         );

         // Associative array.
         $stepsdefinitions = array(
             'micarro' => '/me/lo/robaron',
             'anoche' => '/cuando/yo/dormia'
         );

         $contents = testable_tool_behat::get_config_file_contents('/i/am/a/prefix/', $features, $stepsdefinitions);

         $this->assertContains('features: /i/am/a/prefix/lib/behat/features', $contents);
         $this->assertContains('micarro: /me/lo/robaron', $contents);
         $this->assertContains('base_url: \'' . $CFG->test_wwwroot . '\'', $contents);
         $this->assertContains('class: behat_init_context', $contents);
         $this->assertContains('- feature1', $contents);
         $this->assertContains('- feature3', $contents);

     }
}

