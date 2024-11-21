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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intelliboard
 * @copyright  2019 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

namespace Containers;

use DataExtractor;

class GroupsContainer extends BaseContainer{

    public static function get($selected, DataExtractor $extractor, $params = array()) {
        $mode = $extractor->getMode();

        return array_map(function($group) use ($extractor, $mode) {

            if (empty($group['name'])) {
                $column = ColumnsContainer::get($group, $extractor)['sql'];
                $group['name'] = $column;
            } else {
                $group['name'] = is_numeric($group['name'])? ColumnsContainer::getById($group['name'], $extractor)['name'] : $group['name'];
            }

            return $group['name'];

        }, $selected);
    }

    public static function construct($groups, DataExtractor $extractor, $params = array()) {
        return implode(',' . $extractor->getSeparator() . ' ', $groups) . $extractor->getSeparator();
    }

}