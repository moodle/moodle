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
 * Tests for the base class.
 *
 * @package    tool_admin_presets
 * @category   test
 * @copyright  2021 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \tool_admin_presets\local\action\base
 */
class base_test extends \advanced_testcase {

    /**
     * Test the behaviour of log() method.
     *
     * @covers ::log
     * @dataProvider log_provider
     *
     * @param string $action Action to log.
     * @param string $mode Mode to log.
     * @param string|null $expectedclassname The expected classname or null if no event is expected.
     */
    public function test_base_log(string $action, string $mode, ?string $expectedclassname): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Initialise the parameters and create the class.
        if (!empty($mode)) {
            $_POST['mode'] = $mode;
        }
        if (!empty($action)) {
            $_POST['action'] = $action;
        }
        $base = new base();

        // Redirect events (to capture them) and call to the log method.
        $sink = $this->redirectEvents();
        $base->log();
        $events = $sink->get_events();
        $sink->close();
        $event = reset($events);

        // Validate event data.
        if (is_null($expectedclassname)) {
            $this->assertFalse($event);
        } else {
            $this->assertInstanceOf('\\tool_admin_presets\\event\\' . $expectedclassname, $event);
        }
    }

    /**
     * Data provider for test_base_log().
     *
     * @return array
     */
    public static function log_provider(): array {
        return [
            // Action = base.
            'action=base and mode = show' => [
                'action' => 'base',
                'mode' => 'show',
                'expectedclassname' => 'presets_listed',
            ],
            'action=base and mode = execute' => [
                'action' => 'base',
                'mode' => 'execute',
                'expectedclassname' => 'presets_listed',
            ],

            // Action = delete.
            'action=delete and mode = show' => [
                'action' => 'delete',
                'mode' => 'show',
                'expectedclassname' => null,
            ],
            'action=delete and mode = execute' => [
                'action' => 'delete',
                'mode' => 'execute',
                'expectedclassname' => 'preset_deleted',
            ],
            'mode = delete and action = base' => [
                'action' => 'base',
                'mode' => 'delete',
                'expectedclassname' => 'preset_deleted',
            ],

            // Action = export.
            'action=export and mode = show' => [
                'action' => 'export',
                'mode' => 'show',
                'expectedclassname' => null,
            ],
            'action=export and mode = execute' => [
                'action' => 'export',
                'mode' => 'execute',
                'expectedclassname' => 'preset_exported',
            ],
            'mode = export and action = download_xml' => [
                'action' => 'export',
                'mode' => 'download_xml',
                'expectedclassname' => 'preset_downloaded',
            ],

            // Action = load.
            'action=load and mode = show' => [
                'action' => 'load',
                'mode' => 'show',
                'expectedclassname' => 'preset_previewed',
            ],
            'action=load and mode = execute' => [
                'action' => 'load',
                'mode' => 'execute',
                'expectedclassname' => 'preset_loaded',
            ],

            // Unexisting action/method.
            'Unexisting action' => [
                'action' => 'unexisting',
                'mode' => 'show',
                'expectedclassname' => null,
            ],
            'Unexisting mode' => [
                'action' => 'delete',
                'mode' => 'unexisting',
                'expectedclassname' => null,
            ],
        ];
    }
}
