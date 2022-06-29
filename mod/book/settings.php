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
 * Book plugin settings
 *
 * @package    mod_book
 * @copyright  2004-2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once(__DIR__.'/lib.php');

    // General settings

    $options = book_get_numbering_types();

    $settings->add(new admin_setting_configmultiselect('book/numberingoptions',
        get_string('numberingoptions', 'mod_book'), get_string('numberingoptions_desc', 'mod_book'),
        array_keys($options), $options));

    // Modedit defaults.

    $settings->add(new admin_setting_heading('bookmodeditdefaults',
        get_string('modeditdefaults', 'admin'), get_string('condifmodeditdefaults', 'admin')));

    $settings->add(new admin_setting_configselect('book/numbering',
        get_string('numbering', 'mod_book'), '', BOOK_NUM_NUMBERS, $options));

}
