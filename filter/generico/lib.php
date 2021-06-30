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
 * @package filter_generico
 * @copyright  2014 Justin Hunt (http://poodll.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

function filter_generico_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    $config = get_config('filter_generico');

    for ($i = 1; $i <= $config->templatecount; $i++) {
        if ($context->contextlevel == CONTEXT_SYSTEM) {
            if ($filearea === 'uploadjs' . $i || $filearea === 'uploadcss' . $i) {
                return \filter_generico\generico_utils::setting_file_serve($filearea, $args, $forcedownload, $options);
            }
        }
    }
    send_file_not_found();
}

/**
 * called back on customcss or custom js update, to bump the rev flag
 * this is appended to the customcss url (and sometimes js) so will force a cache refresh
 *
 */
function filter_generico_update_revision() {
    set_config('revision', time(), 'filter_generico');
}
