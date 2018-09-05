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

function local_report_user_license_allocations_menu() {

        return array(
            'userlicenseallocations' => array(
                'category' => 'Reports',
                'tab' => 7,
                'name' => get_string('pluginname', 'local_report_user_license_allocations'),
                'url' => '/local/report_user_license_allocations/index.php',
                'cap' => 'local/report_user_license_allocations:view',
                'icondefault' => 'report',
                'style' => 'report',
                'icon' => 'fa-user-secret',
                'iconsmall' => 'fa-bar-chart-o',
            ),
        );
}
