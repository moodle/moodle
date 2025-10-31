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
 * Lets users configure which filters are active in a sub-context.
 *
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package    core
 * @subpackage filter
 */

require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/adminlib.php');

$contextid = required_param('contextid', PARAM_INT);
$forfilter = optional_param('filter', '', PARAM_SAFEDIR);
$returnto  = optional_param('return', null, PARAM_ALPHANUMEXT);

list($context, $course, $cm) = get_context_info_array($contextid);

// Check login and permissions.
require_login($course, false, $cm);
require_capability('moodle/filter:manage', $context);
$PAGE->set_context($context);

$args = ['contextid' => $contextid];
$baseurl = new moodle_url('/filter/manage.php', $args);
if (!empty($forfilter)) {
    $args['filter'] = $forfilter;
}
$PAGE->set_url($baseurl, $args);
if ($returnto !== null) {
    $baseurl->param('return', $returnto);
}

// This is a policy decision, rather than something that would be impossible to implement.
if (!in_array($context->contextlevel, [CONTEXT_COURSECAT, CONTEXT_COURSE, CONTEXT_MODULE])) {
    throw new \moodle_exception('cannotcustomisefiltersblockuser', 'error');
}

$isfrontpage = ($context->contextlevel == CONTEXT_COURSE && $context->instanceid == SITEID);

$contextname = $context->get_context_name();

if ($context->contextlevel == CONTEXT_COURSECAT) {
    core_course_category::page_setup();
} else if ($context->contextlevel == CONTEXT_COURSE) {
    $PAGE->set_heading($course->fullname);
} else if ($context->contextlevel == CONTEXT_MODULE) {
    // Must be module context.
    $PAGE->set_heading($PAGE->activityrecord->name);
}

// Check login and permissions.
require_login($course, false, $cm);
require_capability('moodle/filter:manage', $context);

$PAGE->set_context($context);

// Get the list of available filters.
$availablefilters = filter_get_available_in_context($context);
if (!$isfrontpage && empty($availablefilters)) {
    throw new \moodle_exception('nofiltersenabled', 'error');
}

// If we are handling local settings for a particular filter, start processing.
if ($forfilter) {
    if (!filter_has_local_settings($forfilter)) {
        throw new \moodle_exception('filterdoesnothavelocalconfig', 'error', $forfilter);
    }
    require_once($CFG->dirroot . '/filter/' . $forfilter . '/filterlocalsettings.php');
    $formname = $forfilter . '_filter_local_settings_form';
    $settingsform = new $formname($CFG->wwwroot . '/filter/manage.php', $forfilter, $context);
    if ($settingsform->is_cancelled()) {
        redirect($baseurl);
    } else if ($data = $settingsform->get_data()) {
        $settingsform->save_changes($data);
        redirect($baseurl);
    }
}

// Process any form submission.
if ($forfilter == '' && optional_param('savechanges', false, PARAM_BOOL) && confirm_sesskey()) {
    foreach ($availablefilters as $filter => $filterinfo) {
        $newstate = optional_param($filter, false, PARAM_INT);
        if ($newstate !== false && $newstate != $filterinfo->localstate) {
            filter_set_local_state($filter, $context->id, $newstate);
        }
    }
    redirect($baseurl, get_string('changessaved'), 1);
}

// Work out an appropriate page title.
if ($forfilter) {
    $a = new stdClass;
    $a->filter = filter_get_name($forfilter);
    $a->context = $contextname;
    $title = get_string('filtersettingsforin', 'filters', $a);
} else {
    $title = get_string('filtersettingsin', 'filters', $contextname);
}

// Print the header and tabs.
$PAGE->set_cacheable(false);
$PAGE->set_title($title);
$PAGE->set_pagelayout('admin');
$PAGE->activityheader->disable();
echo $OUTPUT->header();

// Print heading.
echo $OUTPUT->heading_with_help($title, 'filtersettings', 'filters');

if (empty($availablefilters)) {
    echo '<p class="centerpara">' . get_string('nofiltersenabled', 'filters') . "</p>\n";
} else if ($forfilter) {
    $current = filter_get_local_config($forfilter, $contextid);
    $settingsform->set_data((object) $current);
    $settingsform->display();
} else {
    $settingscol = false;
    foreach ($availablefilters as $filter => $notused) {
        $hassettings = filter_has_local_settings($filter);
        $availablefilters[$filter]->hassettings = $hassettings;
        $settingscol = $settingscol || $hassettings;
    }

    $strsettings = get_string('settings');
    $stroff = get_string('off', 'filters');
    $stron = get_string('on', 'filters');
    $strdefaultoff = get_string('defaultx', 'filters', $stroff);
    $strdefaulton = get_string('defaultx', 'filters', $stron);
    $activechoices = [
        TEXTFILTER_INHERIT => '',
        TEXTFILTER_OFF => $stroff,
        TEXTFILTER_ON => $stron,
    ];

    echo html_writer::start_tag('form', ['action' => $baseurl->out_omit_querystring(), 'method' => 'post']);
    echo html_writer::start_tag('div');
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
    foreach ($baseurl->params() as $key => $value) {
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => $key, 'value' => $value]);
    }

    $table = new html_table();
    $table->head  = [get_string('filter'), get_string('isactive', 'filters')];
    $table->colclasses = ['leftalign', 'leftalign'];
    if ($settingscol) {
        $table->head[] = $strsettings;
        $table->colclasses[] = 'leftalign';
    }
    $table->id = 'frontpagefiltersettings';
    $table->attributes['class'] = 'admintable table generaltable table-hover';
    $table->data = [];

    // Iterate through filters adding to display table.
    foreach ($availablefilters as $filter => $filterinfo) {
        $row = [];

        // Filter name.
        $row[] = filter_get_name($filter);

        // Default/on/off choice.
        if ($filterinfo->inheritedstate == TEXTFILTER_ON) {
            $activechoices[TEXTFILTER_INHERIT] = $strdefaulton;
        } else {
            $activechoices[TEXTFILTER_INHERIT] = $strdefaultoff;
        }
        $select = html_writer::label($filterinfo->localstate, 'menu'. $filter, false, ['class' => 'accesshide']);
        $select .= html_writer::select($activechoices, $filter, $filterinfo->localstate, false);
        $row[] = $select;

        // Settings link, if required.
        if ($settingscol) {
            $settings = '';
            if ($filterinfo->hassettings) {
                $settings = '<a href="' . $baseurl->out(true, ['filter' => $filter]). '">' . $strsettings . '</a>';
            }
            $row[] = $settings;
        }

        $table->data[] = $row;
    }

    echo html_writer::table($table);
    echo html_writer::start_tag('div', ['class' => 'buttons']);
    $submitattr = ['type' => 'submit', 'name' => 'savechanges', 'value' => get_string('savechanges'), 'class' => 'btn btn-primary'];
    echo html_writer::empty_tag('input', $submitattr);
    echo html_writer::end_tag('div');
    echo html_writer::end_tag('div');
    echo html_writer::end_tag('form');
}

// Appropriate back link.
if (!$isfrontpage) {

    if ($context->contextlevel === CONTEXT_COURSECAT && $returnto === 'management') {
        $url = new moodle_url('/course/management.php', ['categoryid' => $context->instanceid]);
    } else {
        $url = $context->get_url();
    }

    echo html_writer::start_tag('div', ['class' => 'backlink']);
    echo html_writer::tag('a', get_string('backto', '', $contextname), ['href' => $url]);
    echo html_writer::end_tag('div');
}

echo $OUTPUT->footer();
