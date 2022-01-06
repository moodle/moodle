<?php
// This file is part of Moodle - https://moodle.org/
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
 * The interface library between the core and the subsystem.
 *
 * @package     core_analytics
 * @copyright   2019 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Implements the inplace editable feature.
 *
 * @param string $itemtype Type if the inplace editable element
 * @param int $itemid Identifier of the element
 * @param string $newvalue New value for the element
 * @return \core\output\inplace_editable
 */
function core_analytics_inplace_editable($itemtype, $itemid, $newvalue) {

    if ($itemtype === 'modelname') {
        \external_api::validate_context(context_system::instance());
        require_capability('moodle/analytics:managemodels', \context_system::instance());

        $model = new \core_analytics\model($itemid);
        $model->rename(clean_param($newvalue, PARAM_NOTAGS));

        return $model->inplace_editable_name();
    }
}
