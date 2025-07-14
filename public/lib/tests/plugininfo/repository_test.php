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
 * Unit tests for repository plugin manager class.
 *
 * @package   core
 * @copyright 2021 Amaia Anabitarte <amaia@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types = 1);
namespace core\plugininfo;

/**
 * Tests of the repository plugin manager.
 */
final class repository_test extends \advanced_testcase {

    /**
     * Test the enable_plugin function to check that it enables and disables repository plugins properly.
     *
     * @dataProvider enable_plugin_provider
     * @param string $pluginname Repository to enable.
     * @param int|null $initialvisibility Initialvalue for visibility field.
     * @param int $newstatus New enabled status for the plugin.
     * @param bool $result Wether the repository is part of enabled plugin list or not.
     */
    public function test_enable_plugin(string $pluginname, ?int $initialvisibility, int $newstatus, bool $result): void {
        global $DB;

        $this->resetAfterTest();

        $DB->set_field('repository', 'visible', $initialvisibility, ['type' => $pluginname]);
        repository::enable_plugin($pluginname, $newstatus);

        $enableplugins = repository::get_enabled_plugins();
        $this->assertSame($result, in_array($pluginname, $enableplugins));
    }

    /**
     * Data provider for the load_disk_version tests for testing with invalid supported fields.
     *
     * @return array
     */
    public static function enable_plugin_provider(): array {
        return [
            'Disable an enable and visible repository' => [
                'pluginname' => 'contentbank',
                'initialvisibility' => repository::REPOSITORY_ON,
                'newstatus' => repository::REPOSITORY_DISABLED,
                'result' => false,
            ],
            'Disable an enable and hidden repository' => [
                'pluginname' => 'contentbank',
                'initialvisibility' => repository::REPOSITORY_OFF,
                'newstatus' => repository::REPOSITORY_DISABLED,
                'result' => false,
            ],
            'Disable a disabled repository' => [
                'pluginname' => 'coursefiles',
                'initialvisibility' => null,
                'newstatus' => repository::REPOSITORY_DISABLED,
                'result' => false
            ],
            'Enable an enable and visible repository' => [
                'pluginname' => 'contentbank',
                'initialvisibility' => repository::REPOSITORY_ON,
                'newstatus' => repository::REPOSITORY_ON,
                'result' => true,
            ],
            'Enable an enable and hidden repository' => [
                'pluginname' => 'contentbank',
                'initialvisibility' => repository::REPOSITORY_OFF,
                'newstatus' => repository::REPOSITORY_ON,
                'result' => true,
            ],
            'Enable a disabled repository' => [
                'pluginname' => 'coursefiles',
                'initialvisibility' => null,
                'newstatus' => repository::REPOSITORY_ON,
                'result' => true,
            ],
            'Enable but hide an enable and visible repository' => [
                'pluginname' => 'contentbank',
                'initialvisibility' => repository::REPOSITORY_ON,
                'newstatus' => repository::REPOSITORY_OFF,
                'result' => true,
            ],
            'Enable but hide an enable and hidden repository' => [
                'pluginname' => 'contentbank',
                'initialvisibility' => repository::REPOSITORY_OFF,
                'newstatus' => repository::REPOSITORY_OFF,
                'result' => true,
            ],
            'Enable but hide a disabled repository' => [
                'pluginname' => 'coursefiles',
                'initialvisibility' => null,
                'newstatus' => repository::REPOSITORY_OFF,
                'result' => true,
            ],
        ];
    }
}
