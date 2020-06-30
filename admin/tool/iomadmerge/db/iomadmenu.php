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

// Define the Iomad menu items that are defined by this plugin

function tool_iomadmerge_menu() {
    return array(
        'ToolIomadMerge' => array(
            'category' => 'UserAdmin',
            'tab' => 2,
            'name' => get_string('pluginname', 'tool_iomadmerge'),
            'url' => '/admin/tool/iomadmerge/index.php',
            'cap' => 'tool/iomadmerge:iomadmerge',
            'icondefault' => 'groupsassign',
            'style' => 'user',
            'icon' => 'fa-group',
            'iconsmall' => 'fa-plus-square'
        ),
    );
}
