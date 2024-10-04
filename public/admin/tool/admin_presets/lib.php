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
 * Admin presets callbacks
 *
 * @package     tool_admin_presets
 * @copyright   2024 David Carrillo <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

use core\output\inplace_editable;

/**
 * Inplace editable functionality
 *
 * @param string $itemtype
 * @param int $itemid
 * @param string $newvalue
 * @return inplace_editable
 */
function tool_admin_presets_inplace_editable(string $itemtype, int $itemid, string $newvalue): inplace_editable {
    global $DB;
    $context = \context_system::instance();
    \core_external\external_api::validate_context($context);

    require_capability('moodle/site:config', $context);

    switch ($itemtype) {
        case 'presetname':
            $newvalue = clean_param($newvalue, PARAM_TEXT);
            $edithint = get_string('editadminpresetname', 'tool_admin_presets');
            $displayvalue = format_string($newvalue, true, ['context' => \context_system::instance(), 'escape' => false]);
            $editlabel = get_string('newvaluefor', 'form', $displayvalue);

            // Update value in database.
            $DB->set_field('adminpresets', 'name', $newvalue, [
                'id' => $itemid,
                'iscore' => \core_adminpresets\manager::NONCORE_PRESET,
            ]);

            break;
        default:
            throw new \coding_exception('Unexpected admin preset inplace editable item type');
    }

    return new inplace_editable('tool_admin_presets', $itemtype, $itemid, true, $displayvalue, $newvalue, $edithint, $editlabel);
}
