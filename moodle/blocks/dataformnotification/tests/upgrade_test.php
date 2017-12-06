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

defined('MOODLE_INTERNAL') || die();

/**
 * PHPUnit block dataformnotification upgrade testcase.
 *
 * @package    block_dataformnotification
 * @category   phpunit
 * @group      block_dataformnotification
 * @group      mod_dataform
 * @copyright  2015 Itamar Tzadok {@link http://substantialmethods.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_dataformnotification_upgrade_testcase extends advanced_testcase {

    public function test_upgrade() {
        global $DB, $CFG;

        $blockname = 'dataformnotification';
        $component = "block_$blockname";

        require_once("$CFG->dirroot/blocks/$blockname/db/upgrade.php");

        $this->resetAfterTest(true);

        // Get the generator.
        $generator = $this->getDataGenerator()->get_plugin_generator($component);

        // Create instance with pre-2014111000 config data.
        $recipient = array(
            'admin' => 1,
            'support' => 1,
            'author' => 1,
            'role' => 1,
            'username' => 'admin',
            'email' => 'recp@example.com',
        );

        $config = new stdClass;
        $config->message = 'Hello world';

        foreach ($recipient as $var => $value) {
            $config->{"recipient$var"} = $value;
        }

        $record = new stdClass;
        $record->configdata = base64_encode(serialize($config));
        $bi = $generator->create_instance($record);

        // Verigy old style config.
        $block = block_instance($blockname, $bi);
        $this->assertEquals('Hello world', $block->config->message);
        foreach ($recipient as $var => $value) {
            $this->assertEquals($value, $block->config->{"recipient$var"});
        }

        // Upgrade instances.
        block_dataformnotification_config_adjustments_2014111006();

        // Get the updated block.
        $ubi = $DB->get_record('block_instances', array('id' => $bi->id));

        // Verigy new style config.
        $block = block_instance($blockname, $ubi);
        $this->assertEquals(false, isset($block->config->message));
        $this->assertEquals('Hello world', $block->config->contenttext);
        foreach ($recipient as $var => $value) {
            $this->assertEquals(false, isset($block->config->{"recipient$var"}));
            $this->assertEquals($value, $block->config->recipient[$var]);
        }
    }
}
