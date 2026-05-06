<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core_admin\setting\setting;

use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Unit tests for the admin_setting_configexecutable class.
 *
 * @package    core_admin
 * @category   test
 * @copyright  2013 David Mudrak <david@moodle.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(configexecutable::class)]
#[CoversClass(configfile::class)]
#[CoversClass(configdirectory::class)]
final class configexecutable_test extends \advanced_testcase {
    #[\Override]
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->libdir . '/adminlib.php');
        parent::setUpBeforeClass();
    }

    /**
     * Testing whether a configexecutable setting is executable.
     */
    public function test_admin_setting_configexecutable(): void {
        global $CFG;
        $this->resetAfterTest();

        $CFG->theme = 'classic';
        $executable = new configexecutable('test1', 'Text 1', 'Help Path', '');

        // Check for an invalid path.
        $result = $executable->output_html($CFG->dirroot . '/lib/tests/other/file_does_not_exist');
        $this->assertMatchesRegularExpression('/class="text-danger"/', $result);

        // Check for a directory.
        $result = $executable->output_html($CFG->dirroot);
        $this->assertMatchesRegularExpression('/class="text-danger"/', $result);

        // Check for a file which is not executable.
        $result = $executable->output_html($CFG->dirroot . '/lib/upgrade.txt');
        $this->assertMatchesRegularExpression('/class="text-danger"/', $result);

        // Check for an executable file using PHP_BINARY (the current PHP executable).
        $result = $executable->output_html(PHP_BINARY);
        $this->assertMatchesRegularExpression('/class="text-success"/', $result);

        // Check for no file specified.
        $result = $executable->output_html('');
        $this->assertMatchesRegularExpression('/name="s__test1"/', $result);
        $this->assertMatchesRegularExpression('/value=""/', $result);
    }

    /**
     * Test preventexecpath setting for configexecutable, configfile, and configdirectory.
     */
    public function test_preventexecpath(): void {
        $this->resetAfterTest();

        set_config('preventexecpath', 0);
        set_config('execpath', null, 'abc_cde');
        $this->assertFalse(get_config('abc_cde', 'execpath'));
        $setting = new configexecutable('abc_cde/execpath', 'some desc', '', '/xx/yy');
        $setting->write_setting('/oo/pp');
        $this->assertSame('/oo/pp', get_config('abc_cde', 'execpath'));

        // Prevent changes.
        set_config('preventexecpath', 1);
        $setting->write_setting('/mm/nn');
        $this->assertSame('/oo/pp', get_config('abc_cde', 'execpath'));

        // Use default in install.
        set_config('execpath', null, 'abc_cde');
        $setting->write_setting('/mm/nn');
        $this->assertSame('/xx/yy', get_config('abc_cde', 'execpath'));

        // Use empty value if no default.
        $setting = new configexecutable('abc_cde/execpath', 'some desc', '', null);
        set_config('execpath', null, 'abc_cde');
        $setting->write_setting('/mm/nn');
        $this->assertSame('', get_config('abc_cde', 'execpath'));

        // This also affects admin_setting_configfile and admin_setting_configdirectory.

        set_config('preventexecpath', 0);
        set_config('execpath', null, 'abc_cde');
        $this->assertFalse(get_config('abc_cde', 'execpath'));
        $setting = new configfile('abc_cde/execpath', 'some desc', '', '/xx/yy');
        $setting->write_setting('/oo/pp');
        $this->assertSame('/oo/pp', get_config('abc_cde', 'execpath'));

        // Prevent changes.
        set_config('preventexecpath', 1);
        $setting->write_setting('/mm/nn');
        $this->assertSame('/oo/pp', get_config('abc_cde', 'execpath'));

        // Use default in install.
        set_config('execpath', null, 'abc_cde');
        $setting->write_setting('/mm/nn');
        $this->assertSame('/xx/yy', get_config('abc_cde', 'execpath'));

        // Use empty value if no default.
        $setting = new configfile('abc_cde/execpath', 'some desc', '', null);
        set_config('execpath', null, 'abc_cde');
        $setting->write_setting('/mm/nn');
        $this->assertSame('', get_config('abc_cde', 'execpath'));

        set_config('preventexecpath', 0);
        set_config('execpath', null, 'abc_cde');
        $this->assertFalse(get_config('abc_cde', 'execpath'));
        $setting = new configdirectory('abc_cde/execpath', 'some desc', '', '/xx/yy');
        $setting->write_setting('/oo/pp');
        $this->assertSame('/oo/pp', get_config('abc_cde', 'execpath'));

        // Prevent changes.
        set_config('preventexecpath', 1);
        $setting->write_setting('/mm/nn');
        $this->assertSame('/oo/pp', get_config('abc_cde', 'execpath'));

        // Use default in install.
        set_config('execpath', null, 'abc_cde');
        $setting->write_setting('/mm/nn');
        $this->assertSame('/xx/yy', get_config('abc_cde', 'execpath'));

        // Use empty value if no default.
        $setting = new configdirectory('abc_cde/execpath', 'some desc', '', null);
        set_config('execpath', null, 'abc_cde');
        $setting->write_setting('/mm/nn');
        $this->assertSame('', get_config('abc_cde', 'execpath'));
    }
}
