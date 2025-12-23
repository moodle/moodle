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
 * Callbacks for qbank_manageacategories
 *
 * @package   qbank_managecategories
 * @copyright 2024 Catalyst IT Europe Ltd
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Allow update of user preferences via AJAX.
 *
 * @return array[]
 */
function qbank_managecategories_user_preferences(): array {
    return [
        'qbank_managecategories_showdescriptions' => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => false,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
        'qbank_managecategories_includesubcategories_filter_default' => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => false,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
    ];
}

/**
 * In place editing callback for categories.
 *
 * @param string $itemtype type of the item.
 * @param int $itemid id of the category being edited.
 * @param string $newvalue the new value for the edited field.
 * @return \core\output\inplace_editable
 */
function qbank_managecategories_inplace_editable(string $itemtype, int $itemid, string $newvalue): \core\output\inplace_editable {
    if ($itemtype === 'categoryname') {
        return \qbank_managecategories\output\editable_name::callback($itemid, $newvalue);
    }
}
