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

function block_iomad_microlearning_menu() {

    return array(
        'MicroLearnings' => array(
            'category' => 'MicroLearningAdmin',
            'tab' => 7,
            'name' => get_string('microlearning', 'block_iomad_microlearning'),
            'url' => '/blocks/iomad_microlearning/microlearning.php',
            'cap' => 'block/iomad_microlearning:microlearning_view',
            'icondefault' => 'courses',
            'style' => 'lthread',
            'icon' => 'fa-file-text',
            'iconsmall' => 'fa-money'
        ),
        'MicroLearningNuggets' => array(
            'category' => 'MicroLearningAdmin',
            'tab' => 7,
            'name' => get_string('learningnuggets', 'block_iomad_microlearning'),
            'url' => '/blocks/iomad_microlearning/nuggets.php',
            'cap' => 'block/iomad_microlearning:nuggets_view',
            'icondefault' => 'orders',
            'style' => 'ecomm',
            'icon' => 'fa-truck',
            'iconsmall' => 'fa-eye'
        ),
        'MicroLearningSchedules' => array(
            'category' => 'MicroLearningAdmin',
            'tab' => 7,
            'name' => get_string('learningschedules', 'block_iomad_microlearning'),
            'url' => '/blocks/iomad_microlearning/schedules.php',
            'cap' => 'block/iomad_microlearning:schedules_view',
            'icondefault' => 'orders',
            'style' => 'ecomm',
            'icon' => 'fa-truck',
            'iconsmall' => 'fa-eye'
        ),
        'MicroLearningUsers' => array(
            'category' => 'MicroLearningAdmin',
            'tab' => 7,
            'name' => get_string('learningusers', 'block_iomad_microlearning'),
            'url' => '/blocks/iomad_microlearning/users.php',
            'cap' => 'block/iomad_microlearning:users_view',
            'icondefault' => 'orders',
            'style' => 'ecomm',
            'icon' => 'fa-truck',
            'iconsmall' => 'fa-eye'
        ),
    );
}
