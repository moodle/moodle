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
 * This page lets users to manage site wide competencies.
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

$id = optional_param('id', 0, PARAM_INT);
$competencyframeworkid = optional_param('competencyframeworkid', 0, PARAM_INT);
$pagecontextid = required_param('pagecontextid', PARAM_INT);  // Reference to the context we came from.
$parentid = optional_param('parentid', 0, PARAM_INT);

require_login();
\core_competency\api::require_enabled();

if (empty($competencyframeworkid) && empty($id)) {
    throw new coding_exception('Competencyframeworkid param is required');
}

// Get competency framework.
$competencyframework = null;
if (!empty($competencyframeworkid)) {
    $competencyframework = \core_competency\api::read_framework($competencyframeworkid);
}

// Get competency.
$competency = null;
if (!empty($id)) {
    $competency = \core_competency\api::read_competency($id);
    if (empty($competencyframework)) {
        $competencyframework = $competency->get_framework();
    }
}

// Get parent competency, if any.
$parent = null;
if ($competency) {
    $parent = $competency->get_parent();
} else if ($parentid) {
    $parent = \core_competency\api::read_competency($parentid);
}

// Get page URL.
$urloptions = [
    'id' => $id,
    'competencyframeworkid' => $competencyframework->get_id(),
    'parentid' => $parentid,
    'pagecontextid' => $pagecontextid
];
$url = new moodle_url("/admin/tool/lp/editcompetency.php", $urloptions);

// Set up the page.
list($title, $subtitle, $returnurl) = \tool_lp\page_helper::setup_for_competency($pagecontextid, $url, $competencyframework,
    $competency, $parent);

// Set up the form.
$formoptions = [
    'competencyframework' => $competencyframework,
    'parent' => $parent,
    'persistent' => $competency,
    'pagecontextid' => $pagecontextid
];
$form = new \tool_lp\form\competency($url->out(false), $formoptions);

// Form cancelled.
if ($form->is_cancelled()) {
    redirect($returnurl);
}

// Get form data.
$data = $form->get_data();
if ($data) {
    if (empty($competency)) {
        \core_competency\api::create_competency($data);
        $returnmsg = get_string('competencycreated', 'tool_lp');
    } else {
        \core_competency\api::update_competency($data);
        $returnmsg = get_string('competencyupdated', 'tool_lp');
    }
    redirect($returnurl, $returnmsg, null, \core\output\notification::NOTIFY_SUCCESS);
}

// Render the page.
$output = $PAGE->get_renderer('tool_lp');
echo $output->header();
echo $output->heading($title);
echo $output->heading($subtitle, 3);

$form->display();

echo $output->footer();
