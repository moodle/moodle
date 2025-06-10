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
        <div class='value'>
            <?php if (isset($branchinfo) && !empty($branchinfo)) { ?>
                <div class='attribute'>
                    <?php
                    /**
                     * Displays the label for the target Panopto server attribute.
                     *
                     * @package block_panopto
                     * @copyright  Panopto 2009 - 2024
                     * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                     */
                    echo get_string('attribute_target_panopto_server', 'block_panopto');
                    ?>
                </div>
                <div class='value'>
                    <?php
                    /**
                     * Displays the target Panopto server name for the category branch.
                     *
                     * @package block_panopto
                     * @copyright  Panopto 2009 - 2024
                     * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                     *
                     * @var string $branchinfo['targetserver'] Target server name
                     */
                    echo $branchinfo['targetserver'];
                    ?>
                </div>
                <div class='attribute'>
                    <?php
                    /**
                     * Displays the label for the target branch or category leaf attribute.
                     *
                     * @package block_panopto
                     * @copyright  Panopto 2009 - 2024
                     * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                     */
                    echo get_string('attribute_target_branch_leaf', 'block_panopto');
                    ?>
                </div>
                <div class='value'>
                    <?php
                    /**
                     * Displays the category name (branch leaf) for the target Panopto branch.
                     *
                     * @package block_panopto
                     * @copyright  Panopto 2009 - 2024
                     * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                     *
                     * @var string $branchinfo['categoryname'] Category name
                     */
                    echo $branchinfo['categoryname'];
                    ?>
                </div>
                <?php
                /**
                 * Wrapping else condition.
                 *
                 * @package block_panopto
                 * @copyright  Panopto 2009 - 2024
                 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                 */
            } else {
            ?>
                <div class='errorMessage'>
                    <?php
                    /**
                     * Displays an error message for invalid or missing category information.
                     *
                     * @package block_panopto
                     * @copyright  Panopto 2009 - 2024
                     * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                     */
                    echo get_string('error_invalid_category_information', 'block_panopto');
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
            <br />
