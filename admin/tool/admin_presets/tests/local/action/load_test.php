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
 * Tests for the load class.
 *
 * @package    tool_admin_presets
 * @category   test
 * @copyright  2021 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \tool_admin_presets\local\action\load
 */
class load_test extends \advanced_testcase {

    /**
     * Test the behaviour of show() method when the preset id doesn't exist.
     *
     * @covers ::show
     */
    public function test_load_show_unexisting_preset(): void {

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create some presets.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_adminpresets');
        $presetid = $generator->create_preset();

        // Initialise the parameters and create the load class.
        $_POST['action'] = 'load';
        $_POST['mode'] = 'view';
        $_POST['id'] = $presetid * 2; // Unexisting preset identifier.

        $action = new load();
        $this->expectException(\moodle_exception::class);
        $action->show();
    }


    /**
     * Test the behaviour of preview() method when the preset id doesn't exist.
     *
     * @covers ::preview
     */
    public function test_load_preview_unexisting_preset(): void {

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create some presets.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_adminpresets');
        $presetid = $generator->create_preset();

        // Initialise the parameters and create the load class.
        $_POST['action'] = 'load';
        $_POST['mode'] = 'preview';
        $_POST['id'] = $presetid * 2; // Unexisting preset identifier.

        $action = new load();
        $action->preview();
        $outputs = $generator->access_protected($action, 'outputs');
        // In that case, no exception should be raised and the text of no preset found should be displayed.
        $this->assertEquals(get_string('errornopreset', 'core_adminpresets'), $outputs);
    }

    /**
     * Test the behaviour of execute() method.
     *
     * @covers ::execute
     */
    public function test_load_execute(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a preset.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_adminpresets');
        $presetid = $generator->create_preset();

        $currentpresets = $DB->count_records('adminpresets');
        $currentitems = $DB->count_records('adminpresets_it');
        $currentadvitems = $DB->count_records('adminpresets_it_a');
        $currentplugins = $DB->count_records('adminpresets_plug');
        $currentapppresets = $DB->count_records('adminpresets_app');
        $currentappitems = $DB->count_records('adminpresets_app_it');
        $currentappadvitems = $DB->count_records('adminpresets_app_it_a');
        $currentappplugins = $DB->count_records('adminpresets_app_plug');

        // Set the config values (to confirm they change after applying the preset).
        set_config('enablebadges', 1);
        set_config('allowemojipicker', 1);
        set_config('mediawidth', '640', 'mod_lesson');
        set_config('maxanswers', '5', 'mod_lesson');
        set_config('maxanswers_adv', '1', 'mod_lesson');
        set_config('enablecompletion', 1);
        set_config('usecomments', 0);

        // Get the data we are submitting for the form and mock submitting it.
        $formdata = [
            'id' => $presetid,
            'admin_presets_submit' => 'Load selected settings',
        ];
        \tool_admin_presets\form\load_form::mock_submit($formdata);

        // Initialise the parameters.
        $_POST['action'] = 'load';
        $_POST['mode'] = 'execute';
        $_POST['id'] = $presetid;
        $_POST['sesskey'] = sesskey();

        // Create the load class and execute it.
        $action = new load();
        $action->execute();

        // Check the preset applied has been added to database.
        $this->assertCount($currentapppresets + 1, $DB->get_records('adminpresets_app'));
        // Applied items: enablebadges@none, mediawitdh@mod_lesson and maxanswers@@mod_lesson.
        $this->assertCount($currentappitems + 3, $DB->get_records('adminpresets_app_it'));
        // Applied advanced items: maxanswers_adv@mod_lesson.
        $this->assertCount($currentappadvitems + 1, $DB->get_records('adminpresets_app_it_a'));
        // Applied plugins: enrol_guest and mod_glossary.
        $this->assertCount($currentappplugins + 2, $DB->get_records('adminpresets_app_plug'));
        // Check no new preset has been created.
        $this->assertCount($currentpresets, $DB->get_records('adminpresets'));
        $this->assertCount($currentitems, $DB->get_records('adminpresets_it'));
        $this->assertCount($currentadvitems, $DB->get_records('adminpresets_it_a'));
        $this->assertCount($currentplugins, $DB->get_records('adminpresets_plug'));

        // Check the setting values have changed accordingly with the ones defined in the preset.
        $this->assertEquals(0, get_config('core', 'enablebadges'));
        $this->assertEquals(900, get_config('mod_lesson', 'mediawidth'));
        $this->assertEquals(2, get_config('mod_lesson', 'maxanswers'));
        $this->assertEquals(0, get_config('mod_lesson', 'maxanswers_adv'));

        // These settings will never change.
        $this->assertEquals(1, get_config('core', 'allowemojipicker'));
        $this->assertEquals(1, get_config('core', 'enablecompletion'));
        $this->assertEquals(0, get_config('core', 'usecomments'));

        // Check the plugins visibility have changed accordingly with the ones defined in the preset.
        $enabledplugins = \core\plugininfo\enrol::get_enabled_plugins();
        $this->assertArrayNotHasKey('guest', $enabledplugins);
        $this->assertArrayHasKey('manual', $enabledplugins);
        $enabledplugins = \core\plugininfo\mod::get_enabled_plugins();
        $this->assertArrayNotHasKey('glossary', $enabledplugins);
        $this->assertArrayHasKey('assign', $enabledplugins);
        $enabledplugins = \core\plugininfo\qtype::get_enabled_plugins();
        $this->assertArrayHasKey('truefalse', $enabledplugins);
    }
}
