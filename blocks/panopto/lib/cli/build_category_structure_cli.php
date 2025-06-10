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
 * This logic will iterate through all leaf categories in Moodle and build a matching folder branch on Panopto.
 *
 * After all branches are ensured then Panopto should have a folder structure that matches the Moodle category structure.
 *
 * @package block_panopto
 * @copyright  Panopto 2009 - 2017 with contributions from Hittesh Ahuja
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Do not require MOODLE_INTERNAL definition since this is a CLI file.

define('CLI_SCRIPT', true);

// @codingStandardsIgnoreLine
global $CFG;
if (empty($CFG)) {
    // @codingStandardsIgnoreLine
    require_once(dirname(__FILE__) . '/../../../../config.php');
}
require_once(dirname(__FILE__) . '/../panopto_category_data.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->libdir . '/formslib.php');

$admin = get_admin();
if (!$admin) {
    mtrace(get_string('error_no_admin_account_found', 'block_panopto'));
    die;
}

\core\session\manager::set_user(get_admin());
cli_heading(get_string('cli_heading_build_category_structure', 'block_panopto'));

/**
 * This CLI script will build the Moodle category structure on the target Panopto site.
 * This will not sync existing folders to that new category structure.
 *
 * @param array $params
 * $params[1] - the panopto server the user is trying to use this script with. e.g. "example.hosted.panopto.com"
 * $params[2] - the application key associated with the Moodle IDP on th target panopto server.
 */
function build_panopto_category_structure($params) {
    if (!isset($params[1]) || !isset($params[2])) {
        mtrace(get_string('cli_category_invalid_arguments', 'block_panopto'));
    } else {
        panopto_category_data::build_category_structure(false, $params[1], $params[2]);
    }
}

build_panopto_category_structure($argv);
