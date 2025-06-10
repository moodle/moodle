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

defined('MOODLE_INTERNAL') || die;


class report_editdates_mod_chat_date_extractor
                extends report_editdates_mod_date_extractor {

    public function __construct($course) {
        parent::__construct($course, 'chat');
        parent::load_data();
    }

    public function get_settings(cm_info $cm) {
        $chat = $this->mods[$cm->instance];
        return array('chattime' => new report_editdates_date_setting(
                                    get_string('chattime', 'chat'),
                                    $chat->chattime,
                                    self::DATETIME, false)
        );
    }

    public function validate_dates(cm_info $cm, array $dates) {
        $errors = array();
        return $errors;
    }

    public function save_dates(cm_info $cm, array $dates) {

        // Fetch module instance from $mods array.
        $chat = $this->mods[$cm->instance];

        $chat->instance = $cm->instance;
        $chat->coursemodule = $cm->id;

        // Updating date values.
        foreach ($dates as $datetype => $datevalue) {
            $chat->$datetype = $datevalue;
        }

        // Method name to update the instance and associated events.
        chat_update_instance($chat);
    }
}
