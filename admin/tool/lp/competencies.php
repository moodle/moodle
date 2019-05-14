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

$search = optional_param('search', '', PARAM_RAW);
$competencyid = optional_param('competencyid', 0, PARAM_INT);
$competency = null;
if ($competencyid) {
    $competency = \core_competency\api::read_competency($competencyid);
    $id = $competency->get('competencyframeworkid');
    $pagecontext = $competency->get_context();
    $pagecontextid = $pagecontext->id;  // Reference to the context we came from.
} else {
    $id = required_param('competencyframeworkid', PARAM_INT);
    $pagecontextid = required_param('pagecontextid', PARAM_INT);  // Reference to the context we came from.
    $pagecontext = context::instance_by_id($pagecontextid);
}

require_login();
\core_competency\api::require_enabled();

$framework = \core_competency\api::read_framework($id);
$context = $framework->get_context();

if (!\core_competency\competency_framework::can_read_context($context)) {
    throw new required_capability_exception($context, 'moodle/competency:competencyview', 'nopermissions', '');
}

$title = get_string('competencies', 'core_competency');
$pagetitle = get_string('competenciesforframework', 'tool_lp', $framework->get('shortname'));

// Set up the page.
$url = new moodle_url("/admin/tool/lp/competencies.php", array('competencyframeworkid' => $framework->get('id'),
    'pagecontextid' => $pagecontextid));
$frameworksurl = new moodle_url('/admin/tool/lp/competencyframeworks.php', array('pagecontextid' => $pagecontextid));

$PAGE->set_context($pagecontext);
$PAGE->navigation->override_active_url($frameworksurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_url($url);
$PAGE->navbar->add($framework->get('shortname'), $url);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$output = $PAGE->get_renderer('tool_lp');
echo $output->header();

$page = new \tool_lp\output\manage_competencies_page($framework, $search, $pagecontext, $competency);
echo $output->render($page);

// Log the framework viewed event after rendering the page.
\core_competency\api::competency_framework_viewed($framework);

echo $output->footer();
