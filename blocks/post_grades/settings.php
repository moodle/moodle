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

if ($ADMIN->fulltree) {
    require_once($CFG->dirroot . '/enrol/ues/publiclib.php');
    ues::require_daos();

    $s = ues::gen_str('block_post_grades');

    $periodurl = new moodle_url('/blocks/post_grades/posting_periods.php');
    $reseturl = new moodle_url('/blocks/post_grades/reset.php');

    $a = new stdClass;
    $a->period_url = $periodurl->out();
    $a->reset_url = $reseturl->out();

    $settings->add(new admin_setting_heading('block_post_grades_header',
        '', $s('header_help', $a)));

    $settings->add(new admin_setting_configtext('block_post_grades/domino_application_url',
        $s('domino_application_url'), '', ''));

    $settings->add(new admin_setting_configtext('block_post_grades/mylsu_gradesheet_url',
        $s('mylsu_gradesheet_url'), '', ''));

    $settings->add(new admin_setting_configcheckbox('block_post_grades/https_protocol',
        $s('https_protocol'), $s('https_protocol_desc'), 0));
}
