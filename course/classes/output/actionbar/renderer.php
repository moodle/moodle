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

namespace core_course\output\actionbar;

/**
 * Renderer class for the action bar.
 *
 * @package    core_course
 * @copyright  2024 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \plugin_renderer_base {

    /**
     * Renders the user selector trigger element in the action bar.
     *
     * @param user_selector $userselector The user selector object.
     * @return string The HTML output.
     */
    public function render_user_selector(user_selector $userselector): string {
        $data = $userselector->export_for_template($this);
        return parent::render_from_template($userselector->get_template(), $data);
    }

    /**
     * Renders the group selector trigger element in the action bar.
     *
     * @param group_selector $groupselector The group selector object.
     * @return string The HTML output.
     */
    protected function render_group_selector(group_selector $groupselector) {
        $data = $groupselector->export_for_template($this);
        return parent::render_from_template($groupselector->get_template(), $data);
    }
}
