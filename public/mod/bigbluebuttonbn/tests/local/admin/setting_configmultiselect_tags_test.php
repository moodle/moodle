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

namespace mod_bigbluebuttonbn\local\admin;

use advanced_testcase;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../../../../lib/adminlib.php');

/**
 * Tests for the setting_configmultiselect_tags class.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2025 Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 * @covers    \mod_bigbluebuttonbn\local\admin\setting_configmultiselect_tags
 */
final class setting_configmultiselect_tags_test extends advanced_testcase {
    /**
     * Test the constructor and basic properties.
     *
     * @covers \mod_bigbluebuttonbn\local\admin\setting_configmultiselect_tags::__construct
     */
    public function test_constructor(): void {
        $this->resetAfterTest();

        $choices = [
            'video' => 'Video',
            'presentation' => 'Presentation',
        ];

        $setting = new setting_configmultiselect_tags(
            'test_setting',
            'Test Setting',
            'Test Description',
            ['video'],
            $choices,
            'Select formats...',
            'No formats selected',
            true
        );

        $this->assertInstanceOf(setting_configmultiselect_tags::class, $setting);
    }

    /**
     * Test load_choices method with stored custom values.
     *
     * @covers \mod_bigbluebuttonbn\local\admin\setting_configmultiselect_tags::load_choices
     */
    public function test_load_choices_with_custom_values(): void {
        $this->resetAfterTest();

        $choices = [
            'video' => 'Video',
            'presentation' => 'Presentation',
        ];

        $setting = new setting_configmultiselect_tags(
            'bigbluebuttonbn_recording_safe_formats',
            'Recording formats',
            'Test Description',
            ['video'],
            $choices,
            'Select formats...',
            'No formats selected'
        );

        // Set a config value with a custom tag.
        set_config('bigbluebuttonbn_recording_safe_formats', 'video,presentation,customformat');

        $setting->load_choices();
        $loadedchoices = $setting->get_choices();

        // Verify that custom tag is now included in choices.
        $this->assertArrayHasKey('video', $loadedchoices);
        $this->assertArrayHasKey('presentation', $loadedchoices);
        $this->assertArrayHasKey('customformat', $loadedchoices);
        $this->assertEquals('Customformat', $loadedchoices['customformat']);
    }

    /**
     * Test load_choices removes empty keys.
     *
     * @covers \mod_bigbluebuttonbn\local\admin\setting_configmultiselect_tags::load_choices
     */
    public function test_load_choices_removes_empty_keys(): void {
        $this->resetAfterTest();

        $choices = [
            'video' => 'Video',
            '' => 'Empty Key',
            'presentation' => 'Presentation',
        ];

        $setting = new setting_configmultiselect_tags(
            'bigbluebuttonbn_test_formats',
            'Test formats',
            'Test Description',
            [],
            $choices,
            'Select formats...',
            'No formats selected'
        );

        $setting->load_choices();
        $loadedchoices = $setting->get_choices();

        $this->assertArrayNotHasKey('', $loadedchoices);
        $this->assertArrayHasKey('video', $loadedchoices);
        $this->assertArrayHasKey('presentation', $loadedchoices);
    }

    /**
     * Test write_setting with valid data.
     *
     * @covers \mod_bigbluebuttonbn\local\admin\setting_configmultiselect_tags::write_setting
     */
    public function test_write_setting_with_valid_data(): void {
        $this->resetAfterTest();

        $choices = [
            'video' => 'Video',
            'presentation' => 'Presentation',
        ];

        $setting = new setting_configmultiselect_tags(
            'bigbluebuttonbn_recording_safe_formats',
            'Recording formats',
            'Test Description',
            ['video'],
            $choices,
            'Select formats...',
            'No formats selected'
        );

        $data = ['video', 'presentation', 'podcast'];
        $result = $setting->write_setting($data);

        $this->assertEquals('', $result); // Empty string means success.

        $stored = get_config('core', 'bigbluebuttonbn_recording_safe_formats');
        $this->assertEquals('video,presentation,podcast', $stored);
    }

    /**
     * Test write_setting normalizes values to lowercase.
     *
     * @covers \mod_bigbluebuttonbn\local\admin\setting_configmultiselect_tags::write_setting
     */
    public function test_write_setting_normalizes_to_lowercase(): void {
        $this->resetAfterTest();

        $choices = ['video' => 'Video'];

        $setting = new setting_configmultiselect_tags(
            'bigbluebuttonbn_recording_safe_formats',
            'Recording formats',
            'Test Description',
            [],
            $choices,
            'Select formats...',
            'No formats selected'
        );

        $data = ['Video', 'PRESENTATION', 'PodCast'];
        $result = $setting->write_setting($data);

        $this->assertEquals('', $result);

        $stored = get_config('core', 'bigbluebuttonbn_recording_safe_formats');
        $this->assertEquals('video,presentation,podcast', $stored);
    }

    /**
     * Test write_setting removes duplicates.
     *
     * @covers \mod_bigbluebuttonbn\local\admin\setting_configmultiselect_tags::write_setting
     */
    public function test_write_setting_removes_duplicates(): void {
        $this->resetAfterTest();

        $choices = ['video' => 'Video'];

        $setting = new setting_configmultiselect_tags(
            'bigbluebuttonbn_recording_safe_formats',
            'Recording formats',
            'Test Description',
            [],
            $choices,
            'Select formats...',
            'No formats selected'
        );

        $data = ['video', 'Video', 'VIDEO', 'presentation', 'Presentation'];
        $result = $setting->write_setting($data);

        $this->assertEquals('', $result);

        $stored = get_config('core', 'bigbluebuttonbn_recording_safe_formats');
        $this->assertEquals('video,presentation', $stored);
    }

    /**
     * Test write_setting preserves order of existing values.
     *
     * @covers \mod_bigbluebuttonbn\local\admin\setting_configmultiselect_tags::write_setting
     */
    public function test_write_setting_preserves_order(): void {
        $this->resetAfterTest();

        $choices = ['video' => 'Video'];

        $setting = new setting_configmultiselect_tags(
            'bigbluebuttonbn_recording_safe_formats',
            'Recording formats',
            'Test Description',
            [],
            $choices,
            'Select formats...',
            'No formats selected'
        );

        // Set initial config with specific order.
        set_config('bigbluebuttonbn_recording_safe_formats', 'video,presentation,podcast');

        // Update with different order but same values.
        $data = ['podcast', 'video', 'presentation'];
        $setting->write_setting($data);

        // Should maintain original order.
        $stored = get_config('core', 'bigbluebuttonbn_recording_safe_formats');
        $this->assertEquals('video,presentation,podcast', $stored);
    }

    /**
     * Test write_setting appends new values.
     *
     * @covers \mod_bigbluebuttonbn\local\admin\setting_configmultiselect_tags::write_setting
     */
    public function test_write_setting_appends_new_values(): void {
        $this->resetAfterTest();

        $choices = ['video' => 'Video'];

        $setting = new setting_configmultiselect_tags(
            'bigbluebuttonbn_recording_safe_formats',
            'Recording formats',
            'Test Description',
            [],
            $choices,
            'Select formats...',
            'No formats selected'
        );

        // Set initial config.
        set_config('bigbluebuttonbn_recording_safe_formats', 'video,presentation');

        // Add new values.
        $data = ['video', 'presentation', 'podcast', 'notes'];
        $setting->write_setting($data);

        // New values should be appended.
        $stored = get_config('core', 'bigbluebuttonbn_recording_safe_formats');
        $this->assertEquals('video,presentation,podcast,notes', $stored);
    }

    /**
     * Test write_setting filters empty values.
     *
     * @covers \mod_bigbluebuttonbn\local\admin\setting_configmultiselect_tags::write_setting
     */
    public function test_write_setting_filters_empty_values(): void {
        $this->resetAfterTest();

        $choices = ['video' => 'Video'];

        $setting = new setting_configmultiselect_tags(
            'bigbluebuttonbn_recording_safe_formats',
            'Recording formats',
            'Test Description',
            [],
            $choices,
            'Select formats...',
            'No formats selected'
        );

        $data = ['video', '', '  ', 'presentation'];
        $result = $setting->write_setting($data);

        $this->assertEquals('', $result);

        $stored = get_config('core', 'bigbluebuttonbn_recording_safe_formats');
        $this->assertEquals('video,presentation', $stored);
    }

    /**
     * Test write_setting with non-array data.
     *
     * @covers \mod_bigbluebuttonbn\local\admin\setting_configmultiselect_tags::write_setting
     */
    public function test_write_setting_with_non_array(): void {
        $this->resetAfterTest();

        $choices = ['video' => 'Video'];

        $setting = new setting_configmultiselect_tags(
            'bigbluebuttonbn_recording_safe_formats',
            'Recording formats',
            'Test Description',
            [],
            $choices,
            'Select formats...',
            'No formats selected'
        );

        $result = $setting->write_setting('not_an_array');

        $this->assertEquals('', $result);
    }

    /**
     * Test resolve_label with existing language string.
     *
     * @covers \mod_bigbluebuttonbn\local\admin\setting_configmultiselect_tags::resolve_label
     */
    public function test_resolve_label_with_existing_string(): void {
        $this->resetAfterTest();

        $choices = [];

        $setting = new setting_configmultiselect_tags(
            'bigbluebuttonbn_recording_safe_formats',
            'Recording formats',
            'Test Description',
            [],
            $choices,
            'Select formats...',
            'No formats selected'
        );

        // Use reflection to test protected method.
        $reflection = new \ReflectionClass($setting);
        $method = $reflection->getMethod('resolve_label');
        $method->setAccessible(true);

        // Test with known format that has a language string.
        $label = $method->invoke($setting, 'video');
        $this->assertNotEmpty($label);
        $this->assertIsString($label);
    }

    /**
     * Test resolve_label with custom format.
     *
     * @covers \mod_bigbluebuttonbn\local\admin\setting_configmultiselect_tags::resolve_label
     */
    public function test_resolve_label_with_custom_format(): void {
        $this->resetAfterTest();

        $choices = [];

        $setting = new setting_configmultiselect_tags(
            'bigbluebuttonbn_recording_safe_formats',
            'Recording formats',
            'Test Description',
            [],
            $choices,
            'Select formats...',
            'No formats selected'
        );

        // Use reflection to test protected method.
        $reflection = new \ReflectionClass($setting);
        $method = $reflection->getMethod('resolve_label');
        $method->setAccessible(true);

        // Test with custom format names.
        $label = $method->invoke($setting, 'my_custom_format');
        $this->assertEquals('My custom format', $label);

        $label = $method->invoke($setting, 'another-custom-format');
        $this->assertEquals('Another custom format', $label);

        $label = $method->invoke($setting, 'my___multiple___underscores');
        $this->assertEquals('My multiple underscores', $label);
    }

    /**
     * Test resolve_label with empty value.
     *
     * @covers \mod_bigbluebuttonbn\local\admin\setting_configmultiselect_tags::resolve_label
     */
    public function test_resolve_label_with_empty_value(): void {
        $this->resetAfterTest();

        $choices = [];

        $setting = new setting_configmultiselect_tags(
            'bigbluebuttonbn_recording_safe_formats',
            'Recording formats',
            'Test Description',
            [],
            $choices,
            'Select formats...',
            'No formats selected'
        );

        // Use reflection to test protected method.
        $reflection = new \ReflectionClass($setting);
        $method = $reflection->getMethod('resolve_label');
        $method->setAccessible(true);

        $label = $method->invoke($setting, '');
        $this->assertEquals('', $label);

        $label = $method->invoke($setting, '   ');
        $this->assertEquals('', $label);
    }

    /**
     * Test integration: setting and retrieving custom formats.
     *
     * @covers \mod_bigbluebuttonbn\local\admin\setting_configmultiselect_tags::write_setting
     * @covers \mod_bigbluebuttonbn\local\admin\setting_configmultiselect_tags::load_choices
     */
    public function test_integration_custom_formats(): void {
        $this->resetAfterTest();

        $choices = [
            'video' => 'Video',
            'presentation' => 'Presentation',
        ];

        $setting = new setting_configmultiselect_tags(
            'bigbluebuttonbn_recording_safe_formats',
            'Recording formats',
            'Test Description',
            ['video', 'presentation'],
            $choices,
            'Select formats...',
            'No formats selected'
        );

        // Admin adds custom formats from different BBB deployments.
        $data = ['video', 'presentation', 'custom_webinar', 'mobile_recording'];
        $result = $setting->write_setting($data);

        $this->assertEquals('', $result);

        // Verify stored value.
        $stored = get_config('core', 'bigbluebuttonbn_recording_safe_formats');
        $this->assertEquals('video,presentation,custom_webinar,mobile_recording', $stored);

        // Load choices again to verify custom formats are included.
        $setting->load_choices();
        $loadedchoices = $setting->get_choices();

        $this->assertArrayHasKey('custom_webinar', $loadedchoices);
        $this->assertArrayHasKey('mobile_recording', $loadedchoices);
        $this->assertEquals('Custom webinar', $loadedchoices['custom_webinar']);
        $this->assertEquals('Mobile recording', $loadedchoices['mobile_recording']);
    }

    /**
     * Test that the setting works correctly with the actual config name used in production.
     *
     * @covers \mod_bigbluebuttonbn\local\admin\setting_configmultiselect_tags::write_setting
     */
    public function test_production_config_name(): void {
        $this->resetAfterTest();

        $choices = [
            'video' => 'Video',
            'presentation' => 'Presentation',
            'notes' => 'Notes',
            'podcast' => 'Podcast',
            'screenshare' => 'Screenshare',
        ];

        $setting = new setting_configmultiselect_tags(
            'bigbluebuttonbn_recording_safe_formats',
            'Recording formats',
            'Test Description',
            ['video', 'presentation'],
            $choices,
            'Select formats...',
            'No formats selected'
        );

        // Test default value.
        $data = ['video', 'presentation'];
        $setting->write_setting($data);

        $stored = get_config('core', 'bigbluebuttonbn_recording_safe_formats');
        $this->assertEquals('video,presentation', $stored);

        // Test adding all standard formats plus a custom one.
        $data = ['video', 'presentation', 'notes', 'podcast', 'screenshare', 'custom_hd'];
        $setting->write_setting($data);

        $stored = get_config('core', 'bigbluebuttonbn_recording_safe_formats');
        $this->assertEquals('video,presentation,notes,podcast,screenshare,custom_hd', $stored);
    }
}
