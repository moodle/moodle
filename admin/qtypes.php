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
 * Allows the admin to manage question types.
 *
 * @package    moodlecore
 * @subpackage questionbank
 * @copyright  2008 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/tablelib.php');

// Check permissions.
require_login();
$systemcontext = context_system::instance();
require_capability('moodle/question:config', $systemcontext);
$canviewreports = has_capability('report/questioninstances:view', $systemcontext);

admin_externalpage_setup('manageqtypes');
$thispageurl = new moodle_url('/admin/qtypes.php');

$qtypes = question_bank::get_all_qtypes();
$pluginmanager = core_plugin_manager::instance();

// Get some data we will need - question counts and which types are needed.
$counts = $DB->get_records_sql("
        SELECT qtype, COUNT(1) as numquestions, SUM(hidden) as numhidden
        FROM {question} GROUP BY qtype", array());
$needed = array();
foreach ($qtypes as $qtypename => $qtype) {
    if (!isset($counts[$qtypename])) {
        $counts[$qtypename] = new stdClass;
        $counts[$qtypename]->numquestions = 0;
        $counts[$qtypename]->numhidden = 0;
    }
    $needed[$qtypename] = $counts[$qtypename]->numquestions > 0 ||
            $pluginmanager->other_plugins_that_require($qtype->plugin_name());
    $counts[$qtypename]->numquestions -= $counts[$qtypename]->numhidden;
}
$needed['missingtype'] = true; // The system needs the missing question type.
foreach ($counts as $qtypename => $count) {
    if (!isset($qtypes[$qtypename])) {
        $counts['missingtype']->numquestions += $count->numquestions - $count->numhidden;
        $counts['missingtype']->numhidden += $count->numhidden;
    }
}

// Work of the correct sort order.
$config = get_config('question');
$sortedqtypes = array();
foreach ($qtypes as $qtypename => $qtype) {
    $sortedqtypes[$qtypename] = $qtype->local_name();
}
$sortedqtypes = question_bank::sort_qtype_array($sortedqtypes, $config);

// Process actions ============================================================

// Disable.
if (($disable = optional_param('disable', '', PARAM_PLUGIN)) && confirm_sesskey()) {
    if (!isset($qtypes[$disable])) {
        print_error('unknownquestiontype', 'question', $thispageurl, $disable);
    }

    set_config($disable . '_disabled', 1, 'question');
    redirect($thispageurl);
}

// Enable.
if (($enable = optional_param('enable', '', PARAM_PLUGIN)) && confirm_sesskey()) {
    if (!isset($qtypes[$enable])) {
        print_error('unknownquestiontype', 'question', $thispageurl, $enable);
    }

    if (!$qtypes[$enable]->menu_name()) {
        print_error('cannotenable', 'question', $thispageurl, $enable);
    }

    unset_config($enable . '_disabled', 'question');
    redirect($thispageurl);
}

// Move up in order.
if (($up = optional_param('up', '', PARAM_PLUGIN)) && confirm_sesskey()) {
    if (!isset($qtypes[$up])) {
        print_error('unknownquestiontype', 'question', $thispageurl, $up);
    }

    $neworder = question_reorder_qtypes($sortedqtypes, $up, -1);
    question_save_qtype_order($neworder, $config);
    redirect($thispageurl);
}

// Move down in order.
if (($down = optional_param('down', '', PARAM_PLUGIN)) && confirm_sesskey()) {
    if (!isset($qtypes[$down])) {
        print_error('unknownquestiontype', 'question', $thispageurl, $down);
    }

    $neworder = question_reorder_qtypes($sortedqtypes, $down, +1);
    question_save_qtype_order($neworder, $config);
    redirect($thispageurl);
}

// End of process actions ==================================================

// Print the page heading.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('manageqtypes', 'admin'));

// Set up the table.
$table = new flexible_table('qtypeadmintable');
$table->define_baseurl($thispageurl);
$table->define_columns(array('questiontype', 'numquestions', 'version', 'requires',
        'availableto', 'uninstall', 'settings'));
$table->define_headers(array(get_string('questiontype', 'question'), get_string('numquestions', 'question'),
        get_string('version'), get_string('requires', 'admin'), get_string('availableq', 'question'),
        get_string('settings'), get_string('uninstallplugin', 'core_admin')));
$table->set_attribute('id', 'qtypes');
$table->set_attribute('class', 'admintable generaltable');
$table->setup();

// Add a row for each question type.
$createabletypes = question_bank::get_creatable_qtypes();
foreach ($sortedqtypes as $qtypename => $localname) {
    $qtype = $qtypes[$qtypename];
    $row = array();

    // Question icon and name.
    $fakequestion = new stdClass;
    $fakequestion->qtype = $qtypename;
    $icon = print_question_icon($fakequestion, true);
    $row[] = $icon . ' ' . $localname;

    // Number of questions of this type.
    if ($counts[$qtypename]->numquestions + $counts[$qtypename]->numhidden > 0) {
        if ($counts[$qtypename]->numhidden > 0) {
            $strcount = get_string('numquestionsandhidden', 'question', $counts[$qtypename]);
        } else {
            $strcount = $counts[$qtypename]->numquestions;
        }
        if ($canviewreports) {
            $row[] = html_writer::link(new moodle_url('/report/questioninstances/index.php',
                    array('qtype' => $qtypename)), $strcount, array('title' => get_string('showdetails', 'admin')));
        } else {
            $strcount;
        }
    } else {
        $row[] = 0;
    }

    // Question version number.
    $version = get_config('qtype_' . $qtypename, 'version');
    if ($version) {
        $row[] = $version;
    } else {
        $row[] = html_writer::tag('span', get_string('nodatabase', 'admin'), array('class' => 'disabled'));
    }

    // Other question types required by this one.
    $plugin = $pluginmanager->get_plugin_info($qtype->plugin_name());
    $requiredtypes = $plugin->get_other_required_plugins();
    $strtypes = array();
    if (!empty($requiredtypes)) {
        foreach ($requiredtypes as $required => $notused) {
            $strtypes[] = $pluginmanager->plugin_name($required);
        }
        $row[] = implode(', ', $strtypes);
    } else {
        $row[] = '';
    }

    // Are people allowed to create new questions of this type?
    $rowclass = '';
    if ($qtype->menu_name()) {
        $createable = isset($createabletypes[$qtypename]);
        $icons = question_types_enable_disable_icons($qtypename, $createable);
        if (!$createable) {
            $rowclass = 'dimmed_text';
        }
    } else {
        $icons = $OUTPUT->spacer();
    }

    // Move icons.
    $icons .= question_type_icon_html('up', $qtypename, 't/up', get_string('up'), '');
    $icons .= question_type_icon_html('down', $qtypename, 't/down', get_string('down'), '');
    $row[] = $icons;

    // Settings link, if available.
    $settings = admin_get_root()->locate('qtypesetting' . $qtypename);
    if ($settings instanceof admin_externalpage) {
        $row[] = html_writer::link($settings->url, get_string('settings'));
    } else if ($settings instanceof admin_settingpage) {
        $row[] = html_writer::link(new moodle_url('/admin/settings.php',
                array('section' => 'qtypesetting' . $qtypename)), get_string('settings'));
    } else {
        $row[] = '';
    }

    // Uninstall link, if available.
    if ($needed[$qtypename]) {
        $row[] = '';
    } else {
        $uninstallurl = core_plugin_manager::instance()->get_uninstall_url('qtype_'.$qtypename, 'manage');
        if ($uninstallurl) {
            $row[] = html_writer::link($uninstallurl, get_string('uninstallplugin', 'core_admin'),
                array('title' => get_string('uninstallqtype', 'question')));
        }
    }

    $table->add_data($row, $rowclass);
}

$table->finish_output();

echo $OUTPUT->footer();

function question_types_enable_disable_icons($qtypename, $createable) {
    if ($createable) {
        return question_type_icon_html('disable', $qtypename, 't/hide',
                get_string('enabled', 'question'), get_string('disable'));
    } else {
        return question_type_icon_html('enable', $qtypename, 't/show',
                get_string('disabled', 'question'), get_string('enable'));
    }
}

function question_type_icon_html($action, $qtypename, $icon, $alt, $tip) {
    global $OUTPUT;
    return $OUTPUT->action_icon(new moodle_url('/admin/qtypes.php',
            array($action => $qtypename, 'sesskey' => sesskey())),
            new pix_icon($icon, $alt, 'moodle', array('title' => '', 'class' => 'iconsmall')),
            null, array('title' => $tip));
}

