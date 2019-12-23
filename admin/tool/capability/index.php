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
 * For a given capability, show what permission it has for every role, and everywhere that it is overridden.
 *
 * @package    tool_capability
 * @copyright  2008 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot.'/'.$CFG->admin.'/tool/capability/locallib.php');
require_once($CFG->libdir.'/adminlib.php');

// Get URL parameters.
$systemcontext = context_system::instance();
$contextid = optional_param('context', $systemcontext->id, PARAM_INT);

// Check permissions.
list($context, $course, $cm) = get_context_info_array($contextid);
require_login($course, false, $cm);
require_capability('moodle/role:manage', $context);

// Print the header.
admin_externalpage_setup('toolcapability');

// Prepare the list of capabilities to choose from.
$capabilitychoices = array();
foreach ($context->get_capabilities() as $cap) {
    $capabilitychoices[$cap->name] = $cap->name . ': ' . get_capability_string($cap->name);
}

$allroles = role_fix_names(get_all_roles($context));
// Prepare the list of roles to choose from.
$rolechoices = array('0' => get_string('all'));
foreach ($allroles as $role) {
    $rolechoices[$role->id] = $role->localname;
}

$form = new tool_capability_settings_form(null, array(
    'capabilities' => $capabilitychoices,
    'roles' => $rolechoices
));
$PAGE->requires->yui_module(
    'moodle-tool_capability-search',
    'M.tool_capability.init_capability_search',
    array(array('strsearch' => get_string('search')))
);

// Log.
$capabilities = array();
$rolestoshow = array();
$roleids = array('0');
$cleanedroleids = array();
$onlydiff = false;
if ($data = $form->get_data()) {

    $roleids = array();
    if (!empty($data->roles)) {
        $roleids = $data->roles;
    }

    $capabilities = array();
    if (!empty($data->capability)) {
        $capabilities = $data->capability;
    }

    if (in_array('0', $roleids)) {
        $rolestoshow = $allroles;
    } else {
        $cleanedroleids = array_intersect(array_keys($allroles), $roleids);
        if (count($cleanedroleids) === 0) {
            $rolestoshow = $allroles;
        } else {
            foreach ($cleanedroleids as $id) {
                $rolestoshow[$id] = $allroles[$id];
            }
        }
    }

    if (isset($data->onlydiff)) {
        $onlydiff = $data->onlydiff;
    }
}

\tool_capability\event\report_viewed::create()->trigger();

$renderer = $PAGE->get_renderer('tool_capability');

echo $OUTPUT->header();

$form->display();

// If we have a capability, generate the report.
if (count($capabilities) && count($rolestoshow)) {
    /* @var tool_capability_renderer $renderer */
    echo $renderer->capability_comparison_table($capabilities, $context->id, $rolestoshow, $onlydiff);
}

// Footer.
echo $OUTPUT->footer();

function print_report_tree($contextid, $contexts, $allroles) {
    global $CFG;

    // Array for holding lang strings.
    static $strpermissions = null;
    if (is_null($strpermissions)) {
        $strpermissions = array(
            CAP_INHERIT => get_string('notset','role'),
            CAP_ALLOW => get_string('allow','role'),
            CAP_PREVENT => get_string('prevent','role'),
            CAP_PROHIBIT => get_string('prohibit','role')
        );
    }

    // Start the list item, and print the context name as a link to the place to
    // make changes.
    if ($contextid == context_system::instance()->id) {
        $url = "$CFG->wwwroot/$CFG->admin/roles/manage.php";
        $title = get_string('changeroles', 'tool_capability');
    } else {
        $url = "$CFG->wwwroot/$CFG->admin/roles/override.php?contextid=$contextid";
        $title = get_string('changeoverrides', 'tool_capability');
    }
    $context = context::instance_by_id($contextid);
    echo '<h3><a href="' . $url . '" title="' . $title . '">', $context->get_context_name(), '</a></h3>';

    // If there are any role overrides here, print them.
    if (!empty($contexts[$contextid]->rolecapabilities)) {
        $rowcounter = 0;
        echo '<table class="generaltable table-striped"><tbody>';
        foreach ($allroles as $role) {
            if (isset($contexts[$contextid]->rolecapabilities[$role->id])) {
                $permission = $contexts[$contextid]->rolecapabilities[$role->id];
                echo '<tr class="r' . ($rowcounter % 2) . '"><th class="cell">', $role->localname,
                        '</th><td class="cell">' . $strpermissions[$permission] . '</td></tr>';
                $rowcounter++;
            }
        }
        echo '</tbody></table>';
    }

    // After we have done the site context, change the string for CAP_INHERIT
    // from 'notset' to 'inherit'.
    $strpermissions[CAP_INHERIT] = get_string('inherit','role');

    // If there are any child contexts, print them recursively.
    if (!empty($contexts[$contextid]->children)) {
        echo '<ul>';
        foreach ($contexts[$contextid]->children as $childcontextid) {
            echo '<li>';
            print_report_tree($childcontextid, $contexts, $allroles);
            echo '</li>';
        }
        echo '</ul>';
    }
}
