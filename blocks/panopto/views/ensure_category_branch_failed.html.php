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
        <?php if (isset($panoptoversioninfo) && !empty($panoptoversioninfo)) { ?>
            <div class='errorMessage'>
                <?php
                /**
                 * Displays an error message when a newer Panopto version is required.                 *
                 * Retrieves the error message from the Panopto block language strings,
                 * substituting version information for context.
                 *
                 * @package block_panopto
                 * @copyright  Panopto 2009 - 2024
                 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                 *
                 * @param string $panoptoversioninfo Required Panopto version details
                 */
                echo get_string('categories_need_newer_panopto', 'block_panopto', $panoptoversioninfo);
                ?>
            </div>
            <?php
            /**
             * Else condition.
             *
             * @package block_panopto
             * @copyright  Panopto 2009 - 2024
             * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
             */
        } else {
        ?>
            <div class='attribute'>
                <?php
                /**
                 * Displays label indicating a branch-creation failure in Panopto categories.
                 *
                 * @package block_panopto
                 * @copyright  Panopto 2009 - 2024
                 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                 */
                echo get_string('attribute_ensure_branch_failed', 'block_panopto');
                ?>
            </div>
            <div class='value'>
                <?php
                /**
                 * Displays a message to inform users of a failure in ensuring category branching.
                 *
                 * @package block_panopto
                 * @copyright  Panopto 2009 - 2024
                 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                 */
                echo get_string('ensure_category_branch_failed', 'block_panopto');
                ?>
            </div>
            <?php
            /**
             * Closing else condition.
             *
             * @package block_panopto
             * @copyright  Panopto 2009 - 2024
             * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
             */
        }
        ?>
        </div>
    </div>
</div>
