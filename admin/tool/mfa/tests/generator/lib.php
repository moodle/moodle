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

global $CFG;
require_once(__DIR__ . '/../../lib.php');

/**
 * Data generator for tool_mfa plugin.
 *
 * @package    tool_mfa
 * @category   test
 * @copyright  2024 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_mfa_generator extends component_generator_base {
    /**
     * Create user factors.
     *
     * @param   array $record
     * @return  stdClass
     */
    public function create_user_factors(array $record): \stdClass {
        global $DB;

        $factorobject = \tool_mfa\plugininfo\factor::get_factor($record['factor']);
        if (!$factorobject) {
            throw new coding_exception('Unknown factor supplied.');
        }

        $user = $DB->get_record('user', ['username' => $record['username']]);
        if (!$user) {
            throw new coding_exception('No user found with that username.');
        }

        $record = (object) array_merge([
            'userid' => $user->id,
            'secret' => '555553',
            'timecreated' => time() - DAYSECS,
            'createdfromip' => '0:0:0:0:0:0:0:1',
            'timemodified' => time() - MINSECS,
            'lastverified' => time(),
            'revoked' => 0,
            'lockcounter' => 0,
        ], $record);
        $record->id = $DB->insert_record('tool_mfa', $record);

        return $record;
    }
}
