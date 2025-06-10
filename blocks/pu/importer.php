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
 * CSV import of ProctorU coupon codes and GUILD mappings.
 *
 * @package   block_pu
 * @copyright 2021 onwards LSUOnline & Continuing Education
 * @copyright 2021 onwards Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Make sure this can only run via CLI.
define('CLI_SCRIPT', true);

// Reuire config and CLIlib.
require_once('../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->dirroot . '/blocks/pu/classes/importhelpers.php');

// Make sure we are not in maintenance mode.
if (CLI_MAINTENANCE) {
    echo "CLI maintenance mode active, import execution suspended.\n";
    exit(1);
}

// Import the coupon codes.
pu_import_helper::block_pu_codeimport();

// Import the GUILD mappings.
pu_import_helper::block_pu_guildimporter();

// Fix any orphaned coupon mappings.
pu_import_helper::block_pu_code_unmap();

// Email out admins when coupon codes run low.
pu_import_helper::block_pu_codeslow();
