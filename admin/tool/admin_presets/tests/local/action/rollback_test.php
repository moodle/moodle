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
 * Tests for the rollback class.
 *
 * @package    tool_admin_presets
 * @category   test
 * @copyright  2021 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \tool_admin_presets\local\action\rollback
 */
final class rollback_test extends \advanced_testcase {

    /**
     * Test the behaviour of execute() method.
     *
     * @covers ::execute
     */
    public function test_rollback_execute(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Set the config values (to confirm they change after applying the preset).
        set_config('enablebadges', 1);
        set_config('allowemojipicker', 1);
        set_config('mediawidth', '640', 'mod_lesson');
        set_config('maxanswers', '5', 'mod_lesson');
        set_config('maxanswers_adv', '1', 'mod_lesson');
        set_config('enablecompletion', 1);
        set_config('usecomments', 0);

        // Create a preset and apply it.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_adminpresets');
        $presetid = $generator->create_preset(['applypreset' => true]);
        $presetappid = $DB->get_field('adminpresets_app', 'id', ['adminpresetid' => $presetid]);

        $currentpresets = $DB->count_records('adminpresets');
        $currentitems = $DB->count_records('adminpresets_it');
        $currentadvitems = $DB->count_records('adminpresets_it_a');
        $currentplugins = $DB->count_records('adminpresets_plug');
        $this->assertCount(1, $DB->get_records('adminpresets_app'));
        $this->assertCount(3, $DB->get_records('adminpresets_app_it'));
        $this->assertCount(1, $DB->get_records('adminpresets_app_it_a'));
        $this->assertCount(2, $DB->get_records('adminpresets_app_plug'));

        // Check the setttings have changed accordingly after applying the preset.
        $this->assertEquals(0, get_config('core', 'enablebadges'));
        $this->assertEquals(900, get_config('mod_lesson', 'mediawidth'));
        $this->assertEquals(2, get_config('mod_lesson', 'maxanswers'));
        $this->assertEquals(1, get_config('core', 'allowemojipicker'));
        $this->assertEquals(1, get_config('core', 'enablecompletion'));
        $this->assertEquals(0, get_config('core', 'usecomments'));

        // Check the plugins visibility have changed accordingly with the ones defined in the preset.
        $enabledplugins = \core\plugininfo\enrol::get_enabled_plugins();
        $this->assertArrayNotHasKey('guest', $enabledplugins);
        $enabledplugins = \core\plugininfo\mod::get_enabled_plugins();
        $this->assertArrayNotHasKey('glossary', $enabledplugins);
        $enabledplugins = \core\plugininfo\qtype::get_enabled_plugins();
        $this->assertArrayHasKey('truefalse', $enabledplugins);

        // Initialise the parameters.
        $_POST['action'] = 'rollback';
        $_POST['mode'] = 'execute';
        $_POST['id'] = $presetappid;
        $_POST['sesskey'] = sesskey();

        // Create the rollback class and execute it.
        $action = new rollback();
        $action->execute();

        // Check the preset applied has been reverted (so the records in _appXX tables have been removed).
        $this->assertCount(0, $DB->get_records('adminpresets_app'));
        $this->assertCount(0, $DB->get_records('adminpresets_app_it'));
        $this->assertCount(0, $DB->get_records('adminpresets_app_it_a'));
        $this->assertCount(0, $DB->get_records('adminpresets_app_plug'));
        // Check the preset data hasn't changed.
        $this->assertCount($currentpresets, $DB->get_records('adminpresets'));
        $this->assertCount($currentitems, $DB->get_records('adminpresets_it'));
        $this->assertCount($currentadvitems, $DB->get_records('adminpresets_it_a'));
        $this->assertCount($currentplugins, $DB->get_records('adminpresets_plug'));

        // Check the setting values have been reverted accordingly.
        $this->assertEquals(1, get_config('core', 'enablebadges'));
        $this->assertEquals(640, get_config('mod_lesson', 'mediawidth'));
        $this->assertEquals(5, get_config('mod_lesson', 'maxanswers'));
        $this->assertEquals(1, get_config('mod_lesson', 'maxanswers_adv'));
        // These settings won't change, regardless if they are posted to the form.
        $this->assertEquals(1, get_config('core', 'allowemojipicker'));
        $this->assertEquals(1, get_config('core', 'enablecompletion'));
        $this->assertEquals(0, get_config('core', 'usecomments'));

        // Check the plugins visibility have been reverted accordingly.
        $enabledplugins = \core\plugininfo\enrol::get_enabled_plugins();
        $this->assertArrayHasKey('guest', $enabledplugins);
        $enabledplugins = \core\plugininfo\mod::get_enabled_plugins();
        $this->assertArrayHasKey('glossary', $enabledplugins);
        // This plugin won't change (because it had the same value than before the preset was applied).
        $enabledplugins = \core\plugininfo\qtype::get_enabled_plugins();
        $this->assertArrayHasKey('truefalse', $enabledplugins);
    }

    /**
     * Test the behaviour of execute() method when the preset applied id doesn't exist.
     *
     * @covers ::execute
     */
    public function test_rollback_execute_unexisting_presetapp(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a preset and apply it.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_adminpresets');
        $presetid = $generator->create_preset(['applypreset' => true]);
        $presetappid = $DB->get_field('adminpresets_app', 'id', ['adminpresetid' => $presetid]);

        // Initialise the parameters.
        $_POST['action'] = 'rollback';
        $_POST['mode'] = 'execute';
        $_POST['id'] = $presetappid * 2;  // Unexisting presetapp identifier.
        $_POST['sesskey'] = sesskey();

        // Create the rollback class and execute it.
        $action = new rollback();
        $this->expectException(\moodle_exception::class);
        $action->execute();
    }
}
