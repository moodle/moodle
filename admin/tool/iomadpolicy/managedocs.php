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
 * Manage iomadpolicy documents used on the site.
 *
 * Script arguments:
 * - archived=<int> Show only archived versions of the given iomadpolicy document
 *
 * @package     tool_iomadpolicy
 * @copyright   2018 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

$archived = optional_param('archived', 0, PARAM_INT);
$import = optional_param('import', 0, PARAM_INT);

if ($import && has_capability('tool/iomadpolicy:managedocs', \context_system::instance())) {
    if (!$DB->get_records('tool_iomadpolicy')) {
        tool_iomadpolicy\api::import_policies();
    }
}
require_login();

admin_externalpage_setup('tool_iomadpolicy_managedocs', '', ['archived' => $archived]);

$output = $PAGE->get_renderer('tool_iomadpolicy');

$manpage = new \tool_iomadpolicy\output\page_managedocs_list($archived);

echo $output->header();
echo $output->render($manpage);
echo $output->footer();
