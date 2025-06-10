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
        <div class='error'>
            <?php
            /**
             * Displays an access error message for a bulk task in the Panopto block.             *
             * Retrieves the error message from language strings in the Panopto block plugin,
             * substituting the target user's information.
             *
             * @package block_panopto
             * @copyright  Panopto 2009 - 2024
             * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
             *
             * @param string $targetuser Username or user ID involved in the access error
             */
            echo get_string('bulk_task_access_error', 'block_panopto', $targetuser);
            ?>
        </div>
    </div>
</div>
