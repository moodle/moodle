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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// General test helpers.
use block_quickmail\messenger\message\substitution_code;

trait has_general_helpers {

    public function dg() {
        return $this->getDataGenerator();
    }

    public function dd($thing) {
        var_dump($thing);
        die;
    }

    public function get_user_ids_from_user_array(array $users, $asstring = false) {
        $userids = array_map(function($user) {
            return $user->id;
        }, $users);

        return ! $asstring
            ? $userids
            : implode( ',', $userids);
    }

    public function get_course_config_params(array $overrideparams = []) {
        $defaultmessagetype = get_config('moodle', 'block_quickmail_message_types_available');

        $defaultdefaultmessagetype = $defaultmessagetype == 'all' ? 'email' : $defaultmessagetype;

        $supporteduserfieldsstring = implode(',', substitution_code::get('user'));

        $params = [];

        $params['allowstudents'] = array_key_exists('allowstudents',
            $overrideparams) ? $overrideparams['allowstudents'] : (int) get_config('moodle', 'block_quickmail_allowstudents');
        $params['roleselection'] = array_key_exists('roleselection',
            $overrideparams) ? $overrideparams['roleselection'] : get_config('moodle', 'block_quickmail_roleselection');
        $params['receipt'] = array_key_exists('receipt',
            $overrideparams) ? $overrideparams['receipt'] : (int) get_config('moodle', 'block_quickmail_receipt');
        $params['prepend_class'] = array_key_exists('prepend_class',
            $overrideparams) ? $overrideparams['prepend_class'] : get_config('moodle', 'block_quickmail_prepend_class');
        $params['ferpa'] = array_key_exists('ferpa',
            $overrideparams) ? $overrideparams['ferpa'] : get_config('moodle', 'block_quickmail_ferpa');
        $params['downloads'] = array_key_exists('downloads',
            $overrideparams) ? $overrideparams['downloads'] : (int) get_config('moodle', 'block_quickmail_downloads');
        $params['allow_mentor_copy'] = array_key_exists('allow_mentor_copy',
            $overrideparams) ? $overrideparams['allow_mentor_copy'] : (int) get_config('moodle',
                                                                                       'block_quickmail_allow_mentor_copy');
        $params['additionalemail'] = array_key_exists('additionalemail',
            $overrideparams) ? $overrideparams['additionalemail'] : (int) get_config('moodle', 'block_quickmail_additionalemail');
        $params['message_types_available'] = array_key_exists('message_types_available',
            $overrideparams) ? $overrideparams['message_types_available'] : $defaultmessagetype;
        $params['default_message_type'] = array_key_exists('default_message_type',
            $overrideparams) ? $overrideparams['default_message_type'] : $defaultdefaultmessagetype;
        $params['send_now_threshold'] = array_key_exists('send_now_threshold',
            $overrideparams) ? $overrideparams['send_now_threshold'] : (int) get_config('moodle',
                                                                                        'block_quickmail_send_now_threshold');

        return $params;
    }

    public function update_system_config_value($configname, $newvalue) {
        global $DB;

        if ($record = $DB->get_record('config', ['name' => $configname])) {
            $record->value = $newvalue;

            $DB->update_record('config', $record);
        } else {
            $DB->insert_record('config', (object)[
                'name' => $configname,
                'value' => $newvalue,
            ]);
        }
    }

    public function override_params($values, $overrides) {
        foreach (array_keys($values) as $key) {
            if (array_key_exists($key, $overrides)) {
                $values[$key] = $overrides[$key];
            }
        }

        return $values;
    }

    public function get_timestamp_for_date($string) {
        $datetime = new \DateTime($string);

        return $datetime->getTimestamp();
    }

    public function get_past_time() {
        return $this->get_timestamp_for_date('mar 1 2017');
    }

    public function get_recent_time() {
        return $this->get_timestamp_for_date('may 12 2018');
    }

    public function get_now_time() {
        return $this->get_timestamp_for_date('now');
    }

    public function get_soon_time() {
        return $this->get_timestamp_for_date('july 4 2018');
    }

    public function get_future_time() {

        // 2020-10-30, Segun Babalola.
        // The hardcoded, date above is now in the past. Modifying this method to always return a future date.
        return strtotime("+10 years");
    }

}
