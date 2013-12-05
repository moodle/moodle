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
 * Unit tests for admin/tool/behat.
 *
 * @package   tool_behat
 * @copyright  2012 David Monllaó
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/' . $CFG->admin .'/tool/behat/locallib.php');
require_once($CFG->libdir . '/behat/classes/util.php');
require_once($CFG->libdir . '/behat/classes/behat_config_manager.php');

/**
 * Allows access to internal methods without exposing them.
 *
 * @package    tool_behat
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_behat_config_manager extends behat_config_manager {

    /**
     * Allow access to protected method
     * @see parent::merge_config()
     * @param mixed $config
     * @param mixed $localconfig
     * @return mixed
     */
    public static function merge_config($config, $localconfig) {
        return parent::merge_config($config, $localconfig);
    }

    /**
     * Allow access to protected method
     * @see parent::get_config_file_contents()
     * @param array $features
     * @param array $stepsdefinitions
     * @return string
     */
    public static function get_config_file_contents($features, $stepsdefinitions) {
        return parent::get_config_file_contents($features, $stepsdefinitions);
    }
}

/**
 * Tool behat tests.
 *
 * @package    tool_behat
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_behat_testcase extends advanced_testcase {

    /**
     * behat_config_manager tests.
     */
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

        $array = testable_behat_config_manager::merge_config($array1, $array2);

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

        $array = testable_behat_config_manager::merge_config($array1, $array2);

        // Overrides applied.
        $this->assertNotEmpty($array['simple']);
        $this->assertNotEmpty($array['array']);
        $this->assertTrue(is_array($array['simple']));
        $this->assertFalse(is_array($array['array']));

        // Other values are maintained.
        $this->assertEquals('same', $array['the']);
    }

    /**
     * behat_config_manager tests.
     */
    public function test_config_file_contents() {
        global $CFG;

        // Skip tests if behat is not installed.
        $vendorpath = $CFG->dirroot . '/vendor';
        if (!file_exists($vendorpath . '/autoload.php') || !is_dir($vendorpath . '/behat')) {
            $this->markTestSkipped('Behat not installed.');
        }

        // To avoid user value at config.php level.
        unset($CFG->behat_config);

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

        $contents = testable_behat_config_manager::get_config_file_contents($features, $stepsdefinitions);

        // YAML decides when is is necessary to wrap strings between single quotes, so not controlled
        // values like paths should not be asserted including the key name as they would depend on the
        // directories values.
        $this->assertContains($CFG->dirroot . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'behat' . DIRECTORY_SEPARATOR . 'features', $contents);

        // If it was empty should be filled by behat_config_manager::get_config_file_contents().
        $this->assertNotNull($CFG->behat_wwwroot);

        // Not quoted strings.
        $this->assertContains('micarro: /me/lo/robaron', $contents);
        $this->assertContains('class: behat_init_context', $contents);

        // YAML uses single quotes to wrap URL strings.
        $this->assertContains("base_url: '" . $CFG->behat_wwwroot . "'", $contents);

        // Lists.
        $this->assertContains('- feature1', $contents);
        $this->assertContains('- feature3', $contents);

        unset($CFG->behat_wwwroot);
    }

}

