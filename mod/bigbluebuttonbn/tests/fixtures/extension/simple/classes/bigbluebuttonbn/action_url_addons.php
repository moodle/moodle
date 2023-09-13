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
namespace bbbext_simple\bigbluebuttonbn;

/**
 * A single action class to mutate the action URL.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2023 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 */
class action_url_addons extends \mod_bigbluebuttonbn\local\extension\action_url_addons {
    /**
     * Sample mutate the action URL.
     *
     *
     * @param string $action
     * @param array $data
     * @param array $metadata
     * @param int|null $instanceid
     * @return array associative array with the additional data and metadata (indexed by 'data' and
     * 'metadata' keys)
     */
    public function execute(
        string $action = '',
        array $data = [],
        array $metadata = [],
        ?int $instanceid = null
    ): array {
        if ($action == 'create' || $action == 'join') {
            global $DB;
            $record = $DB->get_record('bbbext_simple', [
                'bigbluebuttonbnid' => $instanceid,
            ]);
            if ($record) {
                $metadata['newfield'] = $record->newfield ?? '';
            }
        }
        return [
            'data' => $data,
            'metadata' => $metadata,
        ];
    }
}
