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
 * Settings for the local_ai_manager plugin.
 *
 * @package    block_ai_chat
 * @copyright  2024 ISB Bayern
 * @author     Tobias Garske
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {

    $ADMIN->add('blocksettings', new admin_category('block_ai_chat_settings',
            new lang_string('pluginname', 'block_ai_chat')));

    if ($ADMIN->fulltree) {

        $settings->add(new admin_setting_configtextarea('block_ai_chat/showonpagetypes',
                new lang_string('showonpagetypes', 'block_ai_chat'),
                new lang_string('showonpagetypesdesc', 'block_ai_chat'),
                '',
        ));
        $settings->add(new admin_setting_configcheckbox('block_ai_chat/replacehelp',
                new lang_string('replacehelp', 'block_ai_chat'),
                '',
                0,
        ));
    }
}
