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

namespace factor_token\event;

use stdClass;

/**
 * Event for a token being created for a user.
 *
 * @package     factor_token
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class token_created extends \core\event\base {

    /**
     * Create instance of event.
     *
     * @param stdClass $user the User object of the User who had the token creeated.
     * @param array $state an array of the state of the token.
     *
     * @return \core\event\base the token_created_event event
     *
     * @throws \coding_exception
     */
    public static function token_created_event(stdClass $user, array $state): \core\event\base {
        $data = [
            'relateduserid' => $user->id,
            'context' => \context_user::instance($user->id),
            'other' => [
                'userid' => $user->id,
                'state' => json_encode($state),
            ],
        ];

        return self::create($data);
    }

    /**
     * Init method.
     *
     * @return void
     */
    protected function init(): void {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description(): string {
        $info = json_decode($this->other['state']);
        $string = '<br>';
        foreach ($info as $name => $value) {
            if ($name === 'expiry') {
                $value = userdate($value);
            }

            $string .= ucwords($name) . ': ' . $value . '<br>';
        }

        $data = new stdClass();
        $data->string = $string;
        $data->userid = $this->other['userid'];
        return get_string('tokenstoredindevice', 'factor_token', $data);
    }

    /**
     * Return localised event name.
     *
     * @return string
     * @throws \coding_exception
     */
    public static function get_name(): string {
        return get_string('event:token_created', 'factor_token');
    }
}
