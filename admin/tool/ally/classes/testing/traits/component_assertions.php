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
 * Interface for testing components.
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally\testing\traits;

trait component_assertions {

    /**
     * @param array $items
     * @param int $id
     * @param string $component
     * @param string $table
     * @param string $field
     */
    protected function assert_content_items_contain_item(array $items, $id, $component, $table, $field) {
        if (empty($items)) {
            $this->fail('Content items list is empty!');
        }
        foreach ($items as $item) {
            if (intval($item->id) === intval($id) && $item->component === $component &&
                $item->table === $table && $item->field === $field) {
                return;
            }
        }
        $compref = 'id "'.$id.'" component "'.$component. '" table "'.$table.'" and field "'.$field.'"';
        $this->fail('Content items list does not contain item with '.$compref);
    }

    /**
     * @param array $items
     * @param int $id
     * @param string $component
     * @param string $table
     * @param string $field
     */
    protected function assert_content_items_not_contain_item(array $items, $id, $component, $table, $field) {
        foreach ($items as $item) {
            if (intval($item->id) === intval($id) && $item->component === $component &&
                $item->table === $table && $item->field === $field) {
                $compref = 'id "'.$id.'" component "'.$component. '" table "'.$table.'" and field "'.$field.'"';
                $this->fail('Content items list should not contain item with '.$compref);
            }
        }
    }
}
