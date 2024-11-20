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

function tool_lp_menu() {

    return array(
        'editframeworks' => array(
            'category' => 'CompetencyAdmin',
            'tab' => 5,
            'name' => get_string('competencyframeworks', 'tool_lp'),
            'url' => '/admin/tool/lp/competencyframeworks.php?pagecontextid=1',
            'cap' => 'block/iomad_company_admin:competencyview',
            'icondefault' => 'courses',
            'style' => 'competency',
            'icon' => 'fa-list',
            'iconsmall' => 'fa-eye'
        ),
        'edittemplates' => array(
            'category' => 'CompetencyAdmin',
            'tab' => 5,
            'name' => get_string('templates', 'tool_lp'),
            'url' => '/admin/tool/lp/learningplans.php?pagecontextid=1',
            'cap' => 'block/iomad_company_admin:templateview',
            'icondefault' => 'userenrolements',
            'style' => 'competency',
            'icon' => 'fa-cubes',
            'iconsmall' => 'fa-eye'
        ),
    );
}
