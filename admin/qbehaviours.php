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
 * Allows the admin to manage question behaviours.
 *
 * @package    moodlecore
 * @subpackage questionengine
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/tablelib.php');

// Check permissions.
require_login(null, false);
$systemcontext = context_system::instance();
require_capability('moodle/question:config', $systemcontext);

admin_externalpage_setup('manageqbehaviours');
$thispageurl = new moodle_url('/admin/qbehaviours.php');

$behaviours = core_component::get_plugin_list('qbehaviour');
$pluginmanager = core_plugin_manager::instance();

// Get some data we will need - question counts and which types are needed.
$counts = $DB->get_records_sql_menu("
        SELECT behaviour, COUNT(1)
        FROM {question_attempts} GROUP BY behaviour");
$needed = array();
$archetypal = array();
foreach ($behaviours as $behaviour => $notused) {
    if (!array_key_exists($behaviour, $counts)) {
        $counts[$behaviour] = 0;
    }
    $needed[$behaviour] = ($counts[$behaviour] > 0) ||
            $pluginmanager->other_plugins_that_require('qbehaviour_' . $behaviour);
    $archetypal[$behaviour] = question_engine::is_behaviour_archetypal($behaviour);
}
foreach ($counts as $behaviour => $count) {
    if (!array_key_exists($behaviour, $behaviours)) {
        $counts['missing'] += $count;
    }
}
$needed['missing'] = true;

// Work of the correct sort order.
$config = get_config('question');
$sortedbehaviours = array();
foreach ($behaviours as $behaviour => $notused) {
    $sortedbehaviours[$behaviour] = question_engine::get_behaviour_name($behaviour);
}
if (!empty($config->behavioursortorder)) {
    $sortedbehaviours = question_engine::sort_behaviours($sortedbehaviours,
            $config->behavioursortorder, '');
}

if (!empty($config->disabledbehaviours)) {
    $disabledbehaviours = explode(',', $config->disabledbehaviours);
} else {
    $disabledbehaviours = array();
}

// Process actions ============================================================

// Disable.
if (($disable = optional_param('disable', '', PARAM_PLUGIN)) && confirm_sesskey()) {
    if (!isset($behaviours[$disable])) {
        print_error('unknownbehaviour', 'question', $thispageurl, $disable);
    }

    if (array_search($disable, $disabledbehaviours) === false) {
        $disabledbehaviours[] = $disable;
        set_config('disabledbehaviours', implode(',', $disabledbehaviours), 'question');
    }
    core_plugin_manager::reset_caches();
    redirect($thispageurl);
}

// Enable.
if (($enable = optional_param('enable', '', PARAM_PLUGIN)) && confirm_sesskey()) {
    if (!isset($behaviours[$enable])) {
        print_error('unknownbehaviour', 'question', $thispageurl, $enable);
    }

    if (!$archetypal[$enable]) {
        print_error('cannotenablebehaviour', 'question', $thispageurl, $enable);
    }

    if (($key = array_search($enable, $disabledbehaviours)) !== false) {
        unset($disabledbehaviours[$key]);
        set_config('disabledbehaviours', implode(',', $disabledbehaviours), 'question');
    }
    core_plugin_manager::reset_caches();
    redirect($thispageurl);
}

// Move up in order.
if (($up = optional_param('up', '', PARAM_PLUGIN)) && confirm_sesskey()) {
    if (!isset($behaviours[$up])) {
        print_error('unknownbehaviour', 'question', $thispageurl, $up);
    }

    // This function works fine for behaviours, as well as qtypes.
    $neworder = question_reorder_qtypes($sortedbehaviours, $up, -1);
    set_config('behavioursortorder', implode(',', $neworder), 'question');
    redirect($thispageurl);
}

// Move down in order.
if (($down = optional_param('down', '', PARAM_PLUGIN)) && confirm_sesskey()) {
    if (!isset($behaviours[$down])) {
        print_error('unknownbehaviour', 'question', $thispageurl, $down);
    }

    // This function works fine for behaviours, as well as qtypes.
    $neworder = question_reorder_qtypes($sortedbehaviours, $down, +1);
    set_config('behavioursortorder', implode(',', $neworder), 'question');
    redirect($thispageurl);
}

// End of process actions ==================================================

// Print the page heading.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('manageqbehaviours', 'admin'));

// Set up the table.
$table = new flexible_table('qbehaviouradmintable');
$table->define_baseurl($thispageurl);
$table->define_columns(array('behaviour', 'numqas', 'version', 'requires',
        'available', 'uninstall'));
$table->define_headers(array(get_string('behaviour', 'question'), get_string('numqas', 'question'),
        get_string('version'), get_string('requires', 'admin'),
        get_string('availableq', 'question'), get_string('uninstallplugin', 'core_admin')));
$table->set_attribute('id', 'qbehaviours');
$table->set_attribute('class', 'generaltable admintable');
$table->setup();

// Add a row for each question type.
foreach ($sortedbehaviours as $behaviour => $behaviourname) {
    $row = array();

    // Question icon and name.
    $row[] = $behaviourname;

    // Count
    $row[] = $counts[$behaviour];

    // Question version number.
    $version = get_config('qbehaviour_' . $behaviour, 'version');
    if ($version) {
        $row[] = $version;
    } else {
        $row[] = html_writer::tag('span', get_string('nodatabase', 'admin'), array('class' => 'text-muted'));
    }

    // Other question types required by this one.
    $plugin = $pluginmanager->get_plugin_info('qbehaviour_' . $behaviour);
    $required = $plugin->get_other_required_plugins();
    if (!empty($required)) {
        $strrequired = array();
        foreach ($required as $component => $notused) {
            $strrequired[] = $pluginmanager->plugin_name($component);
        }
        $row[] = implode(', ', $strrequired);
    } else {
        $row[] = '';
    }

    // Are people allowed to select this behaviour?
    $rowclass = '';
    if ($archetypal[$behaviour]) {
        $enabled = array_search($behaviour, $disabledbehaviours) === false;
        $icons = question_behaviour_enable_disable_icons($behaviour, $enabled);
        if (!$enabled) {
            $rowclass = 'dimmed_text';
        }
    } else {
        $icons = $OUTPUT->spacer(array('class' => 'iconsmall'));
    }

    // Move icons.
    $icons .= question_behaviour_icon_html('up', $behaviour, 't/up', get_string('up'), null);
    $icons .= question_behaviour_icon_html('down', $behaviour, 't/down', get_string('down'), null);
    $row[] = $icons;

    // Delete link, if available.
    if ($needed[$behaviour]) {
        $row[] = '';
    } else {
        $uninstallurl = core_plugin_manager::instance()->get_uninstall_url('qbehaviour_'.$behaviour, 'manage');
        if ($uninstallurl) {
            $row[] = html_writer::link($uninstallurl, get_string('uninstallplugin', 'core_admin'),
                array('title' => get_string('uninstallbehaviour', 'question')));
        }
    }

    $table->add_data($row, $rowclass);
}

$table->finish_output();

echo $OUTPUT->footer();

function question_behaviour_enable_disable_icons($behaviour, $enabled) {
    if ($enabled) {
        return question_behaviour_icon_html('disable', $behaviour, 't/hide',
                get_string('enabled', 'question'), get_string('disable'));
    } else {
        return question_behaviour_icon_html('enable', $behaviour, 't/show',
                get_string('disabled', 'question'), get_string('enable'));
    }
}

function question_behaviour_icon_html($action, $behaviour, $icon, $alt, $tip) {
    global $OUTPUT;
    return $OUTPUT->action_icon(new moodle_url('/admin/qbehaviours.php',
            array($action => $behaviour, 'sesskey' => sesskey())),
            new pix_icon($icon, $alt, 'moodle', array('title' => '', 'class' => 'iconsmall')),
            null, array('title' => $tip));
}

