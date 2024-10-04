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

namespace core_backup\fixtures;

use core_backup\hook\after_copy_form_definition;

/**
 * Callback for testing after_copy_form_definition hook.
 *
 * @package core_backup
 * @copyright 2024 Monash University (https://www.monash.edu)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class copy_form_hook_callbacks {
    /**
     * Callback for testing after_copy_form_definition hook.
     *
     * @param after_copy_form_definition $hook
     */
    public static function after_copy_form_definition(after_copy_form_definition $hook) {
        $mform = $hook->mform;
        $mform->addElement('checkbox', 'wierdtestname', 'Wierd test');
    }
}
