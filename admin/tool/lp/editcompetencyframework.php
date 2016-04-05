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
$returntype = optional_param('return', null, PARAM_TEXT);
$pagecontextid = required_param('pagecontextid', PARAM_INT);  // Reference to where we can from.

$framework = null;
if (!empty($id)) {
    // Always use the context from the framework when it exists.
    $framework = new \core_competency\competency_framework($id);
    $context = $framework->get_context();
} else {
    $context = context::instance_by_id($pagecontextid);
}

// We check that we have the permission to edit this framework, in its own context.
require_login();
\core_competency\api::require_enabled();
require_capability('moodle/competency:competencymanage', $context);

// Set up the framework page.
list($pagetitle, $pagesubtitle, $url, $frameworksurl) = tool_lp\page_helper::setup_for_framework($id,
        $pagecontextid, $framework, $returntype);
$output = $PAGE->get_renderer('tool_lp');
$form = new \tool_lp\form\competency_framework($url->out(false), array('context' => $context, 'persistent' => $framework));

if ($form->is_cancelled()) {
    redirect($frameworksurl);
} else if ($data = $form->get_data()) {
    if (empty($data->id)) {
        // Create new framework.
        $data->contextid = $context->id;
        $framework = \core_competency\api::create_framework($data);
        $frameworkmanageurl = new moodle_url('/admin/tool/lp/competencies.php', array(
            'pagecontextid' => $pagecontextid,
            'competencyframeworkid' => $framework->get_id()
        ));
        $messagesuccess = get_string('competencyframeworkcreated', 'tool_lp');
        redirect($frameworkmanageurl, $messagesuccess, 0, \core\output\notification::NOTIFY_SUCCESS);
    } else {
        \core_competency\api::update_framework($data);
        $messagesuccess = get_string('competencyframeworkupdated', 'tool_lp');
        redirect($frameworksurl, $messagesuccess, 0, \core\output\notification::NOTIFY_SUCCESS);
    }
}

echo $output->header();
echo $output->heading($pagetitle, 2);
echo $output->heading($pagesubtitle, 3);
$form->display();
echo $output->footer();
