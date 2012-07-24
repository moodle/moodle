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
 * For a given capability, show what permission it has for every role, and
 * everywhere that it is overridden.
 *
 * @package    tool
 * @subpackage capability
 * @copyright  2008 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

// Check permissions.
require_login();
$systemcontext = context_system::instance();
require_capability('moodle/role:manage', $systemcontext);

// Get URL parameters.
$capability = optional_param('capability', '', PARAM_CAPABILITY);
$roleids = optional_param_array('roles', array('0'), PARAM_INT);

// Clean the passed in list of role ids. If 'All' selected as an option, or
// if none were selected, do all roles.
$allroles = role_fix_names(get_all_roles());
$cleanedroleids = array();
foreach ($roleids as $roleid) {
    if ($roleid == 0) {
        $cleanedroleids = array_keys($allroles);
        break;
    }
    if (array_key_exists($roleid, $allroles)) {
        $cleanedroleids[] = $roleid;
    }
}
if (empty($cleanedroleids)) {
    $cleanedroleids = array_keys($allroles);
}

// Include the required JavaScript.
$PAGE->requires->js_init_call('M.tool_capability.init', array(get_string('search')));

// Log.
add_to_log(SITEID, "admin", "tool capability", "tool/capability/index.php?capability=$capability", $capability);

// Print the header.
admin_externalpage_setup('toolcapability');
echo $OUTPUT->header();

// Prepare the list of capabilities to choose from
$allcapabilities = fetch_context_capabilities($systemcontext);
$capabilitychoices = array();
foreach ($allcapabilities as $cap) {
    $capabilitychoices[$cap->name] = $cap->name . ': ' . get_capability_string($cap->name);
}

// Prepare the list of roles to choose from
$rolechoices = array('0' => get_string('all'));
foreach ($allroles as $role) {
    $rolechoices[$role->id] = $role->localname;
}
if (count($cleanedroleids) == count($allroles)) {
    // Select 'All', rather than each role individually.
    $selectedroleids = array('0');
} else {
    $selectedroleids = $cleanedroleids;
}

// Print the settings form.
echo $OUTPUT->box_start('generalbox boxwidthwide boxaligncenter');
echo '<form method="get" action="" id="settingsform"><div>';
echo $OUTPUT->heading(get_string('reportsettings', 'tool_capability'));
echo '<p id="intro">', get_string('intro', 'tool_capability') , '</p>';
echo '<p><label for="menucapability"> ' . get_string('capabilitylabel', 'tool_capability') . '</label></p>';
echo  html_writer::select($capabilitychoices, 'capability', $capability, array(''=>'choose'), array('size'=>10));
echo '<p><label for="menuroles"> ' . get_string('roleslabel', 'tool_capability') . '</label></p>';
echo  html_writer::select($rolechoices, 'roles[]', $selectedroleids, false, array('size'=>10, 'multiple'=>'multiple'));
echo '<p><input type="submit" id="settingssubmit" value="' . get_string('getreport', 'tool_capability') . '" /></p>';
echo '</div></form>';
echo $OUTPUT->box_end();

// If we have a capability, generate the report.
if ($capability) {

    // Work out the bits needed for the SQL WHERE clauses.
    $params = array($capability);
    $sqlroletest = '';
    if (count($cleanedroleids) != count($allroles)) {
        list($sqlroletest, $roleparams) = $DB->get_in_or_equal($cleanedroleids);
        $params = array_merge($params, $roleparams);
        $sqlroletest = 'AND roleid ' . $sqlroletest;
    }

    // Get all the role_capabilities rows for this capability - that is, all
    // role definitions, and all role overrides.
    $rolecaps = $DB->get_records_sql("
            SELECT id, roleid, contextid, permission
            FROM {role_capabilities}
            WHERE capability = ? $sqlroletest", $params);

    // In order to display a nice tree of contexts, we need to get all the
    // ancestors of all the contexts in the query we just did.
    $relevantpaths = $DB->get_records_sql_menu("
            SELECT DISTINCT con.path, 1
            FROM {context} con JOIN {role_capabilities} rc ON rc.contextid = con.id
            WHERE capability = ? $sqlroletest", $params);
    $requiredcontexts = array($systemcontext->id);
    foreach ($relevantpaths as $path => $notused) {
        $requiredcontexts = array_merge($requiredcontexts, explode('/', trim($path, '/')));
    }
    $requiredcontexts = array_unique($requiredcontexts);

    // Now load those contexts.
    list($sqlcontexttest, $contextparams) = $DB->get_in_or_equal($requiredcontexts);
    $contexts = get_sorted_contexts('ctx.id ' . $sqlcontexttest, $contextparams);

    // Prepare some empty arrays to hold the data we are about to compute.
    foreach ($contexts as $conid => $con) {
        $contexts[$conid]->children = array();
        $contexts[$conid]->rolecapabilities = array();
    }

    // Put the contexts into a tree structure.
    foreach ($contexts as $conid => $con) {
        $context = context::instance_by_id($conid);
        $parentcontextid = get_parent_contextid($context);
        if ($parentcontextid) {
            $contexts[$parentcontextid]->children[] = $conid;
        }
    }

    // Put the role capabilities into the context tree.
    foreach ($rolecaps as $rolecap) {
        $contexts[$rolecap->contextid]->rolecapabilities[$rolecap->roleid] = $rolecap->permission;
    }

    // Fill in any missing rolecaps for the system context.
    foreach ($cleanedroleids as $roleid) {
        if (!isset($contexts[$systemcontext->id]->rolecapabilities[$roleid])) {
            $contexts[$systemcontext->id]->rolecapabilities[$roleid] = CAP_INHERIT;
        }
    }

    // Print the report heading.
    echo $OUTPUT->heading(get_string('reportforcapability', 'tool_capability', get_capability_string($capability)), 2, 'main', 'report');
    if (count($cleanedroleids) != count($allroles)) {
        $rolenames = array();
        foreach ($cleanedroleids as $roleid) {
            $rolenames[] = $allroles[$roleid]->localname;
        }
        echo '<p>', get_string('forroles', 'tool_capability', implode(', ', $rolenames)), '</p>';
    }

    // Now, recursively print the contexts, and the role information.
    print_report_tree($systemcontext->id, $contexts, $allroles);
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
    if ($contextid == get_system_context()->id) {
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
        echo '<table class="generaltable rolecaps"><tbody>';
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
