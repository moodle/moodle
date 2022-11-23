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
 * Admin settings for the multichoice question type.
 *
 * @package   qtype_multichoice
 * @copyright  2015 onwards Nadav Kavalerchik
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $menu = [
        new lang_string('answersingleno', 'qtype_multichoice'),
        new lang_string('answersingleyes', 'qtype_multichoice')
    ];
    $settings->add(new admin_setting_configselect('qtype_multichoice/answerhowmany',
    new lang_string('answerhowmany', 'qtype_multichoice'),
    new lang_string('answerhowmany_desc', 'qtype_multichoice'), '1', $menu));

    $settings->add(new admin_setting_configcheckbox('qtype_multichoice/shuffleanswers',
    new lang_string('shuffleanswers', 'qtype_multichoice'),
    new lang_string('shuffleanswers_desc', 'qtype_multichoice'), '1'));

    $settings->add(new qtype_multichoice_admin_setting_answernumbering('qtype_multichoice/answernumbering',
    new lang_string('answernumbering', 'qtype_multichoice'),
    new lang_string('answernumbering_desc', 'qtype_multichoice'), 'abc', null));

    $settings->add(new admin_setting_configcheckbox('qtype_multichoice/showstandardinstruction',
            new lang_string('showstandardinstruction', 'qtype_multichoice'),
            new lang_string('showstandardinstruction_desc', 'qtype_multichoice'), 0));

}
