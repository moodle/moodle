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

use core_adminpresets\manager;

/**
 * Tests for the export class.
 *
 * @package    tool_admin_presets
 * @category   test
 * @copyright  2021 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \tool_admin_presets\local\action\export
 */
final class export_test extends \advanced_testcase {

    /**
     * Test the behaviour of execute() method.
     * @covers ::execute
     * @dataProvider export_execute_provider
     *
     * @param bool $includesensible Whether the sensible settings should be exported too or not.
     * @param string $presetname Preset name.
     */
    public function test_export_execute(bool $includesensible = false, string $presetname = 'Export 1'): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Get current presets and items.
        $currentpresets = $DB->count_records('adminpresets');
        $currentadvitems = $DB->count_records('adminpresets_it_a');

        // Initialise some settings (to compare their values have been exported as expected).
        set_config('recaptchapublickey', 'abcde');
        set_config('enablebadges', '0');
        set_config('mediawidth', '900', 'mod_lesson');
        set_config('maxanswers', '2', 'mod_lesson');
        set_config('maxanswers_adv', '0', 'mod_lesson');
        set_config('defaultfeedback', '0', 'mod_lesson');
        set_config('defaultfeedback_adv', '1', 'mod_lesson');

        // Get the data we are submitting for the form and mock submitting it.
        $formdata = [
            'name' => $presetname,
            'comments' => ['text' => 'This is a presets for testing export'],
            'author' => 'Super-Girl',
            'includesensiblesettings' => $includesensible,
            'admin_presets_submit' => 'Save changes',
        ];
        \tool_admin_presets\form\export_form::mock_submit($formdata);

        // Initialise the parameters and create the export class.
        $_POST['action'] = 'export';
        $_POST['mode'] = 'execute';
        $_POST['sesskey'] = sesskey();

        $action = new export();
        $sink = $this->redirectEvents();
        try {
            $action->execute();
        } catch (\exception $e) {
            // If export action was successfull, redirect should be called so we will encounter an
            // 'unsupported redirect error' moodle_exception.
            $this->assertInstanceOf(\moodle_exception::class, $e);
        } finally {
            // Check the preset record has been created.
            $presets = $DB->get_records('adminpresets');
            $this->assertCount($currentpresets + 1, $presets);
            $generator = $this->getDataGenerator()->get_plugin_generator('core_adminpresets');
            $presetid = $generator->access_protected($action, 'id');
            $this->assertArrayHasKey($presetid, $presets);
            $preset = $presets[$presetid];
            $this->assertEquals($presetname, $preset->name);
            $this->assertEquals(manager::NONCORE_PRESET, $preset->iscore);

            // Check the items, advanced attributes and plugins have been created.
            $this->assertGreaterThan(0, $DB->count_records('adminpresets_it', ['adminpresetid' => $presetid]));
            $this->assertGreaterThan($currentadvitems, $DB->count_records('adminpresets_it_a'));
            $this->assertGreaterThan(0, $DB->count_records('adminpresets_plug', ['adminpresetid' => $presetid]));

            // Check settings have been created with the expected values.
            $params = ['adminpresetid' => $presetid, 'plugin' => 'none', 'name' => 'enablebadges'];
            $setting = $DB->get_record('adminpresets_it', $params);
            $this->assertEquals('0', $setting->value);

            $params = ['adminpresetid' => $presetid, 'plugin' => 'mod_lesson', 'name' => 'mediawidth'];
            $setting = $DB->get_record('adminpresets_it', $params);
            $this->assertEquals('900', $setting->value);

            $params = ['adminpresetid' => $presetid, 'plugin' => 'mod_lesson', 'name' => 'maxanswers'];
            $setting = $DB->get_record('adminpresets_it', $params);
            $this->assertEquals('2', $setting->value);
            $params = ['itemid' => $setting->id, 'name' => 'maxanswers_adv'];
            $setting = $DB->get_record('adminpresets_it_a', $params);
            $this->assertEquals('0', $setting->value);

            $params = ['adminpresetid' => $presetid, 'plugin' => 'mod_lesson', 'name' => 'defaultfeedback'];
            $setting = $DB->get_record('adminpresets_it', $params);
            $this->assertEquals('0', $setting->value);
            $params = ['itemid' => $setting->id, 'name' => 'defaultfeedback_adv'];
            $setting = $DB->get_record('adminpresets_it_a', $params);
            $this->assertEquals('1', $setting->value);

            // Check plugins have been created with the expected values.
            $manager = \core_plugin_manager::instance();
            $plugintype = 'enrol';
            $plugins = $manager->get_present_plugins($plugintype);
            $enabledplugins = $manager->get_enabled_plugins($plugintype);
            foreach ($plugins as $pluginname => $unused) {
                $params = ['adminpresetid' => $presetid, 'plugin' => $plugintype, 'name' => $pluginname];
                $plugin = $DB->get_record('adminpresets_plug', $params);
                $enabled = (!empty($enabledplugins) && array_key_exists($pluginname, $enabledplugins));
                $this->assertEquals($enabled, (bool) $plugin->enabled);
            }

            // Check whether sensible settings have been exported or not.
            $params = ['adminpresetid' => $presetid, 'plugin' => 'none', 'name' => 'recaptchapublickey'];
            $recaptchasetting = $DB->get_record('adminpresets_it', $params);
            $params = ['adminpresetid' => $presetid, 'plugin' => 'none', 'name' => 'cronremotepassword'];
            $cronsetting = $DB->get_record('adminpresets_it', $params);
            if ($includesensible) {
                $this->assertEquals('abcde', $recaptchasetting->value);
                $this->assertNotFalse($cronsetting);
            } else {
                $this->assertFalse($recaptchasetting);
                $this->assertFalse($cronsetting);
            }

            // Check the export event has been raised.
            $events = $sink->get_events();
            $sink->close();
            $event = reset($events);
            $this->assertInstanceOf('\\tool_admin_presets\\event\\preset_exported', $event);
        }
    }

    /**
     * Data provider for test_export_execute().
     *
     * @return array
     */
    public static function export_execute_provider(): array {
        return [
            'Export settings and plugins, excluding sensible' => [
                'includesensible' => false,
            ],
            'Export settings and plugins, including sensible' => [
                'includesensible' => true,
            ],
            'Export settings and plugins, with Starter name (it should not be marked as core)' => [
                'includesensible' => false,
                'presetname' => 'Starter',
            ],
            'Export settings and plugins, with Full name (it should not be marked as core)' => [
                'includesensible' => false,
                'presetname' => 'Full',
            ],
        ];
    }
}
