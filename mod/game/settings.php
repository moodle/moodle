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
 * Form for creating and modifying a game
 *
 * @package    mod_game
 * @copyright  2007 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once(dirname(__FILE__).'/lib.php');

    // General settings.

    $settings->add(new admin_setting_configcheckbox('game/hidebookquiz',
        get_string('hidebookquiz', 'game'), get_string('confighidebookquiz', 'game'), 0));

    $settings->add(new admin_setting_configcheckbox('game/hidecross',
        get_string('hidecross', 'game'), get_string('confighidecross', 'game'), 0));

    $settings->add(new admin_setting_configcheckbox('game/hidehiddenpicture',
        get_string('hidehiddenpicture', 'game'), get_string('confighidehiddenpicture', 'game'), 0));

    $settings->add(new admin_setting_configcheckbox('game/hidecryptex',
        get_string('hidecryptex', 'game'), get_string('confighidecryptex', 'game'), 0));

    $settings->add(new admin_setting_configcheckbox('game/hidehangman',
        get_string('hidehangman', 'game'), get_string('confighidehangman', 'game'), 0));

    $settings->add(new admin_setting_configcheckbox('game/hidemillionaire',
        get_string('hidemillionaire', 'game'), get_string('confighidemillionaire', 'game'), 0));

    $settings->add(new admin_setting_configcheckbox('game/hidesnakes',
        get_string('hidesnakes', 'game'), get_string('confighidesnakes', 'game'), 0));

    $settings->add(new admin_setting_configcheckbox('game/hidesudoku',
        get_string('hidesudoku', 'game'), get_string('confighidesudoku', 'game'), 0));

    $settings->add(new admin_setting_configtext('game/hangmanimagesets', get_string('hangmanimagesets', 'game'),
            get_string('confighangmanimagesets', 'game'), 2, PARAM_INT));

}
