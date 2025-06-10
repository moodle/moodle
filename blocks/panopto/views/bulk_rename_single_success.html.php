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
 * The template used to display when we begin processing
 *
 * @package block_panopto
 * @copyright  Panopto 2009 - 2024
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
?>

<div class='block_panopto'>
    <div class='panoptoProcessInformation'>
        <div class='attribute'>
            <?php
            /**
             * Displays label for the attempted target course ID.
             *
             * @package block_panopto
             * @copyright  Panopto 2009 - 2024
             * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
             */
            echo get_string('attempted_target_course_id', 'block_panopto');
            ?>
        </div>
        <div class='value'>
            <?php
            /**
             * Displays the Moodle ID of the target course for the attempted folder rename.
             *
             * @package block_panopto
             * @copyright  Panopto 2009 - 2024
             * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
             *
             * @var int $currentfoldernamecontainer->moodleid Target course Moodle ID
             */
            echo $currentfoldernamecontainer->moodleid;
            ?>
        </div>
        <div class='attribute'>
            <?php
            /**
             * Displays label for the original folder name attribute.
             *
             * @package block_panopto
             * @copyright  Panopto 2009 - 2024
             * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
             */
            echo get_string('attribute_original_name', 'block_panopto');
            ?>
        </div>
        <div class='value'>
            <?php
            /**
             * Displays the original name of the folder before the rename attempt.
             *
             * @package block_panopto
             * @copyright  Panopto 2009 - 2024
             * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
             *
             * @var string $currentfoldernamecontainer->oldname Original folder name
             */
            echo $currentfoldernamecontainer->oldname;
            ?>
        </div>
        <div class='attribute'>
            <?php
            /**
             * Displays label for the new folder name attribute.
             *
             * @package block_panopto
             * @copyright  Panopto 2009 - 2024
             * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
             */
            echo get_string('attribute_new_name', 'block_panopto');
            ?>
        </div>
        <div class='value'>
            <?php
            /**
             * Displays the new name of the folder after the rename attempt.
             *
             * @package block_panopto
             * @copyright  Panopto 2009 - 2024
             * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
             *
             * @var string $currentfoldernamecontainer->newname New folder name
             */
            echo $currentfoldernamecontainer->newname;
            ?>
        </div>
    </div>
</div>
