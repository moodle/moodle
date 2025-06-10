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
             * Displays an error message regarding version compatibility.
             * The message is retrieved from the Panopto block language strings.
             *
             * @package block_panopto
             * @copyright  Panopto 2009 - 2024
             * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
             */
            echo get_string('bulk_task_version_error', 'block_panopto');
            ?>
        </div>
        <div class="alert alert-error alert-block">
            <div class="attribute">
                <?php
                /**
                 * Displays the label for the impacted server attribute.
                 *
                 * @package block_panopto
                 * @copyright  Panopto 2009 - 2024
                 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                 */
                echo get_string('impacted_server_attr', 'block_panopto');
                ?>
            </div>
            <div class='value'>
                <?php
                /**
                 * Displays the server name of the impacted Panopto course.
                 *
                 * @package block_panopto
                 * @copyright  Panopto 2009 - 2024
                 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                 *
                 * @var string $oldpanoptocourse->panopto->servername Server name
                 */
                echo $oldpanoptocourse->panopto->servername;
                ?>
            </div>
            <div class="attribute">
                <?php
                /**
                 * Displays the label for the minimum required Panopto version attribute.
                 *
                 * @package block_panopto
                 * @copyright  Panopto 2009 - 2024
                 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                 */
                echo get_string('minimum_required_version_attr', 'block_panopto');
                ?>
            </div>
            <div class='value'>
                <?php
                /**
                 * Displays the minimum required Panopto version for compatibility.
                 *
                 * @package block_panopto
                 * @copyright  Panopto 2009 - 2024
                 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                 *
                 * @var string \panopto_data::$requiredpanoptoversion Required version
                 */
                echo \panopto_data::$requiredpanoptoversion;
                ?>
            </div>
            <div class="attribute">
                <?php
                /**
                 * Displays the label for the current Panopto version attribute.
                 *
                 * @package block_panopto
                 * @copyright  Panopto 2009 - 2024
                 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                 */
                echo get_string('current_version_attr', 'block_panopto');
                ?>
            </div>
            <div class='value'>
                <?php
                /**
                 * Displays the active Panopto server version.
                 *
                 * @package block_panopto
                 * @copyright  Panopto 2009 - 2024
                 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
                 *
                 * @var string $activepanoptoserverversion Active server version
                 */
                echo $activepanoptoserverversion;
                ?>
            </div>
        </div>
    </div>
</div>
