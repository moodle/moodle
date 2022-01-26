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

namespace tool_admin_presets\local\action;

/**
 * Tests for the delete class.
 *
 * @package    tool_admin_presets
 * @category   test
 * @copyright  2021 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \tool_admin_presets\local\action\delete
 */
class delete_test extends \advanced_testcase {

    /**
     * Test the behaviour of execute() method.
     *
     * @covers ::execute
     */
    public function test_delete_execute(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create some presets.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_adminpresets');
        $presetid1 = $generator->create_preset(['name' => 'Preset 1', 'applypreset' => true]);
        $presetid2 = $generator->create_preset(['name' => 'Preset 2']);

        $currentpresets = $DB->count_records('adminpresets');
        $currentitems = $DB->count_records('adminpresets_it');
        $currentadvitems = $DB->count_records('adminpresets_it_a');
        $currentplugins = $DB->count_records('adminpresets_plug');

        // Only preset1 has been applied.
        $this->assertCount(1, $DB->get_records('adminpresets_app'));
        // Only the preset1 settings that have changed: enablebadges, mediawidth and maxanswers.
        $this->assertCount(3, $DB->get_records('adminpresets_app_it'));
        // Only the preset1 advanced settings that have changed: maxanswers_adv.
        $this->assertCount(1, $DB->get_records('adminpresets_app_it_a'));
        // Only the preset1 plugins that have changed: enrol_guest and mod_glossary.
        $this->assertCount(2, $DB->get_records('adminpresets_app_plug'));

        // Initialise the parameters and create the delete class.
        $_POST['action'] = 'delete';
        $_POST['mode'] = 'execute';
        $_POST['id'] = $presetid1;
        $_POST['sesskey'] = sesskey();

        $action = new delete();
        $sink = $this->redirectEvents();
        try {
            $action->execute();
        } catch (\exception $e) {
            // If delete action was successfull, redirect should be called so we will encounter an
            // 'unsupported redirect error' moodle_exception.
            $this->assertInstanceOf(\moodle_exception::class, $e);
        } finally {
            // Check the preset data has been removed.
            $presets = $DB->get_records('adminpresets');
            $this->assertCount($currentpresets - 1, $presets);
            $preset = reset($presets);
            $this->assertArrayHasKey($presetid2, $presets);
            // Check preset items.
            $this->assertCount($currentitems - 4, $DB->get_records('adminpresets_it'));
            $this->assertCount(0, $DB->get_records('adminpresets_it', ['adminpresetid' => $presetid1]));
            // Check preset advanced items.
            $this->assertCount($currentadvitems - 1, $DB->get_records('adminpresets_it_a'));
            // Check preset plugins.
            $this->assertCount($currentplugins - 3, $DB->get_records('adminpresets_plug'));
            $this->assertCount(0, $DB->get_records('adminpresets_plug', ['adminpresetid' => $presetid1]));
            // Check preset applied tables are empty now.
            $this->assertCount(0, $DB->get_records('adminpresets_app'));
            $this->assertCount(0, $DB->get_records('adminpresets_app_it'));
            $this->assertCount(0, $DB->get_records('adminpresets_app_it_a'));
            $this->assertCount(0, $DB->get_records('adminpresets_app_plug'));

            // Check the delete event has been raised.
            $events = $sink->get_events();
            $sink->close();
            $event = reset($events);
            $this->assertInstanceOf('\\tool_admin_presets\\event\\preset_deleted', $event);
        }
    }

    /**
     * Test the behaviour of execute() method when the preset id doesn't exist.
     *
     * @covers ::execute
     */
    public function test_delete_execute_unexisting_preset(): void {

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create some presets.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_adminpresets');
        $presetid = $generator->create_preset(['name' => 'Preset 1']);

        // Initialise the parameters and create the delete class.
        $_POST['action'] = 'delete';
        $_POST['mode'] = 'execute';
        $_POST['id'] = $presetid * 2; // Unexisting preset identifier.
        $_POST['sesskey'] = sesskey();

        $action = new delete();
        $this->expectException(\moodle_exception::class);
        $action->execute();
    }

    /**
     * Test the behaviour of show() method when the preset id doesn't exist.
     *
     * @covers ::show
     */
    public function test_delete_show_unexisting_preset(): void {

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create some presets.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_adminpresets');
        $presetid = $generator->create_preset(['name' => 'Preset 1']);

        // Initialise the parameters and create the delete class.
        $_POST['action'] = 'delete';
        $_POST['mode'] = 'show';
        $_POST['id'] = $presetid * 2; // Unexisting preset identifier.

        $action = new delete();
        $this->expectException(\moodle_exception::class);
        $action->show();
    }
}
