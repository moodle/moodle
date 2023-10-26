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
 * Unit tests helper for xAPI library.
 *
 * This file contains unit test helpers related to xAPI library.
 *
 * @package    core_xapi
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_xapi;

use stdClass;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/fixtures/handler.php');
require_once(__DIR__ . '/fixtures/xapi_test_statement_post.php');

/**
 * Contains helper functions for xAPI PHPUnit Tests.
 *
 * @package    core_xapi
 * @since      Moodle 3.9
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_helper {

    /** @var \core\log\reader contains a valid logstore reader. */
    private $store;

    /**
     * Constructor for a xAPI test helper.
     *
     */
    public function init_log() {
        // Enable logs.
        set_config('jsonformat', 1, 'logstore_standard');
        set_config('enabled_stores', 'logstore_standard', 'tool_log');
        set_config('buffersize', 0, 'logstore_standard');
        set_config('logguests', 1, 'logstore_standard');
        $manager = get_log_manager(true);
        $stores = $manager->get_readers();
        $this->store = $stores['logstore_standard'];
    }

    /**
     * Return the last log entry from standardlog.
     *
     * @return \core\event\base|null The last log event or null if none found.
     */
    public function get_last_log_entry(): ?\core\event\base {

        $select = "component = :component";
        $params = ['component' => 'core_xapi'];
        $records = $this->store->get_events_select($select, $params, 'timecreated DESC', 0, 1);

        if (empty($records)) {
            return null;
        }
        return array_pop($records);
    }
}
