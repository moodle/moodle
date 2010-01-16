<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
 * Elucidates what has_capability does for a particular capability/user/context.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package roles
 *//** */

require(dirname(__FILE__) . '/../../config.php');

// Get parameters.
$userid = required_param('user', PARAM_INTEGER); // We use 0 here to mean not-logged-in.
$contextid = required_param('contextid', PARAM_INTEGER);
$capability = required_param('capability', PARAM_CAPABILITY);

$PAGE->set_url('/admin/roles/explain.php', array('user'=>$userid, 'contextid'=>$contextid, 'capability'=>$capability));

// Get the context and its parents.
$context = get_context_instance_by_id($contextid);
if (!$context) {
    print_error('unknowncontext');
}
$contextids = get_parent_contexts($context);
array_unshift($contextids, $context->id);
$contexts = array();
$number = count($contextids);
foreach ($contextids as $contextid) {
    $contexts[$contextid] = get_context_instance_by_id($contextid);
    $contexts[$contextid]->name = print_context_name($contexts[$contextid], true, true);
    $contexts[$contextid]->number = $number--;
}

// Validate the user id.
if ($userid) {
    $user = $DB->get_record('user', array('id' => $userid));
    if (!$user) {
        print_error('nosuchuser');
    }
} else {
    $frontpagecontext = get_context_instance(CONTEXT_COURSE, SITEID);
    if (!empty($CFG->forcelogin) ||
            ($context->contextlevel >= CONTEXT_COURSE && !in_array($frontpagecontext->id, $contextids))) {
        print_error('cannotgetherewithoutloggingin', 'role');
    }
}

// Check access permissions.
require_login();
if (!has_any_capability(array('moodle/role:assign', 'moodle/role:safeoverride',
        'moodle/role:override', 'moodle/role:assign'), $context)) {
    print_error('nopermissions', '', get_string('explainpermissions'));
}

// This duplicates code in load_all_capabilities and has_capability.
$systempath = '/' . SYSCONTEXTID;
if ($userid == 0) {
    if (!empty($CFG->notloggedinroleid)) {
        $accessdata = get_role_access($CFG->notloggedinroleid);
        $accessdata['ra'][$systempath] = array($CFG->notloggedinroleid);
    } else {
        $accessdata = array();
        $accessdata['ra'] = array();
        $accessdata['rdef'] = array();
        $accessdata['loaded'] = array();
    }
} else if (isguestuser($user)) {
    $guestrole = get_guest_role();
    $accessdata = get_role_access($guestrole->id);
    $accessdata['ra'][$systempath] = array($guestrole->id);
} else {
    $accessdata = load_user_accessdata($userid);
}
if ($context->contextlevel > CONTEXT_COURSE && !path_inaccessdata($context->path, $accessdata)) {
    load_subcontext($userid, $context, $accessdata);
}

// Load the roles we need.
$roleids = array();
foreach ($accessdata['ra'] as $roleassignments) {
    $roleids = array_merge($roleassignments, $roleids);
}
$roles = $DB->get_records_list('role', 'id', $roleids);
$rolenames = array();
foreach ($roles as $role) {
    $rolenames[$role->id] = $role->name;
}
$rolenames = role_fix_names($rolenames, $context);

// Pass over the data once, to find the cell that determines the result.
$userhascapability = has_capability($capability, $context, $userid, false);
$areprohibits = false;
$decisiveassigncon = 0;
$decisiveoverridecon = 0;
foreach ($contexts as $con) {
    if (!empty($accessdata['ra'][$con->path])) {
        // The array_unique here is to work around bug MDL-14817. Once that bug is
        // fixed, it can be removed
        $ras = array_unique($accessdata['ra'][$con->path]);
    } else {
        $ras = array();
    }
    $con->firstoverride = 0;
    foreach ($contexts as $ocon) {
        $summedpermission = 0;
        $gotsomething = false;
        foreach ($ras as $roleid) {
            if (isset($accessdata['rdef'][$ocon->path . ':' . $roleid][$capability])) {
                $perm = $accessdata['rdef'][$ocon->path . ':' . $roleid][$capability];
            } else {
                $perm = CAP_INHERIT;
            }
            if ($perm && !$gotsomething) {
                $gotsomething = true;
                $con->firstoverride = $ocon->id;
            }
            if ($perm == CAP_PROHIBIT) {
                $areprohibits = true;
                $decisiveassigncon = 0;
                $decisiveoverridecon = 0;
                break;
            }
            $summedpermission += $perm;
        }
        if (!$areprohibits && !$decisiveassigncon && $summedpermission) {
            $decisiveassigncon = $con->id;
            $decisiveoverridecon = $ocon->id;
            break;
        } else if ($gotsomething) {
            break;
        }
    }
}
if (!$areprohibits && !$decisiveassigncon) {
    $decisiveassigncon = SYSCONTEXTID;
    $decisiveoverridecon = SYSCONTEXTID;
}

// Make a fake role to simplify rendering the table below.
$rolenames[0] = get_string('none');

// Prepare some arrays of strings.
$cssclasses = array(
    CAP_INHERIT => 'inherit',
    CAP_ALLOW => 'allow',
    CAP_PREVENT => 'prevent',
    CAP_PROHIBIT => 'prohibit',
    '' => ''
);
$strperm = array(
    CAP_INHERIT => get_string('inherit', 'role'),
    CAP_ALLOW => get_string('allow', 'role'),
    CAP_PREVENT => get_string('prevent', 'role'),
    CAP_PROHIBIT => get_string('prohibit', 'role'),
    '' => ''
);

// Start the output.
$PAGE->set_title(get_string('explainpermission', 'role'));
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('explainpermission', 'role'));

// Print a summary of what we are doing.
$a = new stdClass;
if ($userid) {
    $a->fullname = fullname($user);
} else {
    $a->fullname = get_string('nobody');
}
$a->capability = $capability;
$a->context = reset($contexts)->name;
if ($userhascapability) {
    echo '<p>' . get_string('whydoesuserhavecap', 'role', $a) . '</p>';
} else {
    echo '<p>' . get_string('whydoesusernothavecap', 'role', $a) . '</p>';
}

// Print the table header rows.
echo '<table class="generaltable explainpermissions"><thead>';
echo '<tr><th scope="col" colspan="2" class="header assignment">' . get_string('roleassignments', 'role') . '</th>';
if (count($contexts) > 1) {
    echo '<th scope="col" colspan="' . (count($contexts) - 1) . '" class="header">' . get_string('overridesbycontext', 'role') . '</th>';
}
echo '<th scope="col" rowspan="2" class="header">' . get_string('roledefinitions', 'role') . '</th>';
echo '</tr>';
echo '<tr class="row2"><th scope="col" class="header assignment">' . get_string('context', 'role') .
        '</th><th scope="col" class="header assignment">' . get_string('role') . '</th>';
foreach (array_slice($contexts, 0, count($contexts) - 1) as $con) {
    echo '<th scope="col" class="header overridecontext" title="' . $con->name . '">' . $con->number . '</th>';
}
echo '</tr></thead><tbody>';

// Now print the bulk of the table.
foreach ($contexts as $con) {
    if (!empty($accessdata['ra'][$con->path])) {
        // The array_unique here is to work around bug MDL-14817. Once that bug is
        // fixed, it can be removed
        $ras = array_unique($accessdata['ra'][$con->path]);
    } else {
        $ras = array(0);
    }
    $firstcell = '<th class="cell assignment" rowspan="' . count($ras) . '">' . $con->number . '. ' . $con->name . '</th>';
    $rowclass = ' class="newcontext"';
    foreach ($ras as $roleid) {
        $extraclass = '';
        if (!$roleid) {
            $extraclass = ' noroles';
        }
        echo '<tr' . $rowclass . '>' . $firstcell . '<th class="cell assignment' . $extraclass . '" scope="row">' . $rolenames[$roleid] . '</th>';
        $overridden = false;
        foreach ($contexts as $ocon) {
            if ($roleid == 0) {
                $perm = '';
            } else {
                if (isset($accessdata['rdef'][$ocon->path . ':' . $roleid][$capability])) {
                    $perm = $accessdata['rdef'][$ocon->path . ':' . $roleid][$capability];
                } else {
                    $perm = CAP_INHERIT;
                }
            }
            if ($perm === CAP_INHERIT && $ocon->id == SYSCONTEXTID) {
                $permission = get_string('notset', 'role');
            } else {
                $permission = $strperm[$perm];
            }
            $classes = $cssclasses[$perm];
            if (!$areprohibits && $decisiveassigncon == $con->id && $decisiveoverridecon == $ocon->id) {
                $classes .= ' decisive';
                if ($userhascapability) {
                    $classes .= ' has';
                } else {
                    $classes .= ' hasnot';
                }
            }
            if ($overridden) {
                $classes .= ' overridden';
            }
            echo '<td class="cell ' . $classes . '">' . $permission . '</td>';
            if ($con->firstoverride == $ocon->id) {
                $overridden = true;
            }
        }
        echo '</tr>';
        $firstcell = '';
        $rowclass = '';
    }
}
echo '</tbody></table>';

// Finish the page.
echo get_string('explainpermissionsinfo', 'role');
if ($userid && $capability != 'moodle/site:doanything' && !$userhascapability &&
        has_capability('moodle/site:doanything', $context, $userid)) {
    echo '<p>' . get_string('explainpermissionsdoanything', 'role', $capability) . '</p>';
}
echo $OUTPUT->close_window_button();
echo $OUTPUT->footer();
