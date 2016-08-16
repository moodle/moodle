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
 * Filter management page.
 *
 * @package    core
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/adminlib.php');

$action = optional_param('action', '', PARAM_ALPHA);
$filterpath = optional_param('filterpath', '', PARAM_PLUGIN);

require_login();
$systemcontext = context_system::instance();
require_capability('moodle/site:config', $systemcontext);

admin_externalpage_setup('managefilters');

// Clean up bogus filter states first.
$plugininfos = core_plugin_manager::instance()->get_plugins_of_type('filter');
$filters = array();
$states = filter_get_global_states();
foreach ($states as $state) {
    if (!isset($plugininfos[$state->filter]) and !get_config('filter_'.$state->filter, 'version')) {
        // Purge messy leftovers after incorrectly uninstalled plugins and unfinished installs.
        $DB->delete_records('filter_active', array('filter' => $state->filter));
        $DB->delete_records('filter_config', array('filter' => $state->filter));
        error_log('Deleted bogus "filter_'.$state->filter.'" states and config data.');
    } else {
        $filters[$state->filter] = $state;
    }
}

// Add properly installed and upgraded filters to the global states table.
foreach ($plugininfos as $filter => $info) {
    if (isset($filters[$filter])) {
        continue;
    }
    /** @var \core\plugininfo\base $info */
    if ($info->is_installed_and_upgraded()) {
        filter_set_global_state($filter, TEXTFILTER_DISABLED);
        $states = filter_get_global_states();
        foreach ($states as $state) {
            if ($state->filter === $filter) {
                $filters[$filter] = $state;
                break;
            }
        }
    }
}

if ($action) {
    require_sesskey();
}

// Process actions.
switch ($action) {

    case 'setstate':
        if (isset($filters[$filterpath]) and $newstate = optional_param('newstate', '', PARAM_INT)) {
            filter_set_global_state($filterpath, $newstate);
            if ($newstate == TEXTFILTER_DISABLED) {
                filter_set_applies_to_strings($filterpath, false);
            }
        }
        break;

    case 'setapplyto':
        if (isset($filters[$filterpath])) {
            $applytostrings = optional_param('stringstoo', false, PARAM_BOOL);
            filter_set_applies_to_strings($filterpath, $applytostrings);
        }
        break;

    case 'down':
        if (isset($filters[$filterpath])) {
            filter_set_global_state($filterpath, $filters[$filterpath]->active, 1);
        }
        break;

    case 'up':
        if (isset($filters[$filterpath])) {
            $oldpos = $filters[$filterpath]->sortorder;
            filter_set_global_state($filterpath, $filters[$filterpath]->active, -1);
        }
        break;
}

// Reset caches and return.
if ($action) {
    reset_text_filters_cache();
    core_plugin_manager::reset_caches();
    redirect(new moodle_url('/admin/filters.php'));
}

// Print the page heading.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('filtersettings', 'admin'));

$states = filter_get_global_states();
$stringfilters = filter_get_string_filters();

$table = new html_table();
$table->head  = array(get_string('filter'), get_string('isactive', 'filters'),
        get_string('order'), get_string('applyto', 'filters'), get_string('settings'), get_string('uninstallplugin', 'core_admin'));
$table->colclasses = array ('leftalign', 'leftalign', 'centeralign', 'leftalign', 'leftalign', 'leftalign');
$table->attributes['class'] = 'admintable generaltable';
$table->id = 'filterssetting';
$table->data  = array();

$lastactive = null;
foreach ($states as $state) {
    if ($state->active != TEXTFILTER_DISABLED) {
        $lastactive = $state->filter;
    }
}

// Iterate through filters adding to display table.
$firstrow = true;
foreach ($states as $state) {
    $filter = $state->filter;
    if (!isset($plugininfos[$filter])) {
        continue;
    }
    $plugininfo = $plugininfos[$filter];
    $applytostrings = isset($stringfilters[$filter]) && $state->active != TEXTFILTER_DISABLED;
    $row = get_table_row($plugininfo, $state, $firstrow, $filter == $lastactive, $applytostrings);
    $table->data[] = $row;
    if ($state->active == TEXTFILTER_DISABLED) {
        $table->rowclasses[] = 'dimmed_text';
    } else {
        $table->rowclasses[] = '';
    }
    $firstrow = false;
}

echo html_writer::table($table);
echo '<p class="filtersettingnote">' . get_string('filterallwarning', 'filters') . '</p>';
echo $OUTPUT->footer();
die;


/**
 * Return action URL.
 *
 * @param string $filterpath
 * @param string $action
 * @return moodle_url
 */
function filters_action_url($filterpath, $action) {
    if ($action === 'delete') {
        return core_plugin_manager::instance()->get_uninstall_url('filter_'.$filterpath, 'manage');
    }
    return new moodle_url('/admin/filters.php', array('sesskey'=>sesskey(), 'filterpath'=>$filterpath, 'action'=>$action));
}

/**
 * Construct table record.
 *
 * @param \core\plugininfo\filter $plugininfo
 * @param stdClass $state
 * @param bool $isfirstrow
 * @param bool $islastactive
 * @param bool $applytostrings
 * @return array data
 */
function get_table_row(\core\plugininfo\filter $plugininfo, $state, $isfirstrow, $islastactive, $applytostrings) {
    global $OUTPUT;
    $row = array();
    $filter = $state->filter;
    $active = $plugininfo->is_installed_and_upgraded();

    static $activechoices;
    static $applytochoices;
    if (!isset($activechoices)) {
        $activechoices = array(
            TEXTFILTER_DISABLED => get_string('disabled', 'core_filters'),
            TEXTFILTER_OFF => get_string('offbutavailable', 'core_filters'),
            TEXTFILTER_ON => get_string('on', 'core_filters'),
        );
        $applytochoices = array(
            0 => get_string('content', 'core_filters'),
            1 => get_string('contentandheadings', 'core_filters'),
        );
    }

    // Filter name.
    $displayname = $plugininfo->displayname;
    if (!$plugininfo->rootdir) {
        $displayname = '<span class="error">' . $displayname . ' - ' . get_string('status_missing', 'core_plugin') . '</span>';
    } else if (!$active) {
        $displayname = '<span class="error">' . $displayname . ' - ' . get_string('error') . '</span>';
    }
    $row[] = $displayname;

    // Disable/off/on.
    $select = new single_select(filters_action_url($filter, 'setstate'), 'newstate', $activechoices, $state->active, null, 'active' . $filter);
    $select->set_label(get_string('isactive', 'filters'), array('class' => 'accesshide'));
    $row[] = $OUTPUT->render($select);

    // Re-order.
    $updown = '';
    $spacer = '<img src="' . $OUTPUT->pix_url('spacer') . '" class="iconsmall" alt="" />';
    if ($state->active != TEXTFILTER_DISABLED) {
        if (!$isfirstrow) {
            $updown .= $OUTPUT->action_icon(filters_action_url($filter, 'up'), new pix_icon('t/up', get_string('up'), '', array('class' => 'iconsmall')));
        } else {
            $updown .= $spacer;
        }
        if (!$islastactive) {
            $updown .= $OUTPUT->action_icon(filters_action_url($filter, 'down'), new pix_icon('t/down', get_string('down'), '', array('class' => 'iconsmall')));
        } else {
            $updown .= $spacer;
        }
    }
    $row[] = $updown;

    // Apply to strings.
    $select = new single_select(filters_action_url($filter, 'setapplyto'), 'stringstoo', $applytochoices, $applytostrings, null, 'applyto' . $filter);
    $select->set_label(get_string('applyto', 'filters'), array('class' => 'accesshide'));
    $select->disabled = ($state->active == TEXTFILTER_DISABLED);
    $row[] = $OUTPUT->render($select);

    // Settings link, if required.
    if ($active and filter_has_global_settings($filter)) {
        $row[] = html_writer::link(new moodle_url('/admin/settings.php', array('section'=>'filtersetting'.$filter)), get_string('settings'));
    } else {
        $row[] = '';
    }

    // Uninstall.
    $row[] = html_writer::link(filters_action_url($filter, 'delete'), get_string('uninstallplugin', 'core_admin'));

    return $row;
}
