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
 * @package   block_iomad_microlearning
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Define the Iomad menu items that are defined by this plugin

function block_iomad_microlearning_menu() {

    return array(
        'MicroLearningSchedules' => array(
            'category' => 'MicroLearningAdmin',
            'tab' => 7,
            'name' => get_string('threads', 'block_iomad_microlearning'),
            'url' => '/blocks/iomad_microlearning/threads.php',
            'cap' => 'block/iomad_microlearning:edit_threads',
            'icondefault' => 'learningpath',
            'style' => 'micro',
            'icon' => 'fa-file-text',
            'iconsmall' => 'fa-microchip'
        ),
        'MicroLearningUsers' => array(
            'category' => 'MicroLearningAdmin',
            'tab' => 7,
            'name' => get_string('learningusers', 'block_iomad_microlearning'),
            'url' => '/blocks/iomad_microlearning/users.php',
            'cap' => 'block/iomad_microlearning:assign_threads',
            'icondefault' => 'users',
            'style' => 'micro',
            'icon' => 'fa-group',
            'iconsmall' => 'fa-microchip'
        ),
    );
}
