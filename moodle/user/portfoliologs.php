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
 * This file is part of the User section Moodle
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_user
 */

require_once(__DIR__ . '/../config.php');

if (empty($CFG->enableportfolios)) {
    print_error('disabled', 'portfolio');
}

require_once($CFG->libdir . '/portfoliolib.php');
require_once($CFG->libdir . '/portfolio/exporter.php');

$courseid = optional_param('courseid', SITEID, PARAM_INT);
$page     = optional_param('page', 0, PARAM_INT);
$perpage  = optional_param('perpage', 10, PARAM_INT);

if (! $course = $DB->get_record("course", array("id" => $courseid))) {
    print_error('invalidcourseid');
}

require_login($course, false);

$user = $USER;
$fullname = fullname($user);
$strportfolios = get_string('portfolios', 'portfolio');

$url = new moodle_url('/user/portfoliologs.php', array('courseid' => $courseid));

navigation_node::override_active_url(new moodle_url('/user/portfoliologs.php', array('courseid' => $courseid)));

if ($page !== 0) {
    $url->param('page', $page);
}
if ($perpage !== 0) {
    $url->param('perpage', $perpage);
}

$PAGE->set_url($url);
$PAGE->set_title(get_string('logs', 'portfolio'));
$PAGE->set_heading($fullname);
$PAGE->set_context(context_user::instance($user->id));
$PAGE->set_pagelayout('report');

echo $OUTPUT->header();

$showroles = 1;
$somethingprinted = false;

echo $OUTPUT->box_start();

$queued = $DB->get_records('portfolio_tempdata', array('userid' => $USER->id), 'expirytime DESC', 'id, expirytime');
if (count($queued) > 0) {
    $table = new html_table();
    $table->head = array(
        get_string('displayarea', 'portfolio'),
        get_string('plugin', 'portfolio'),
        get_string('displayinfo', 'portfolio'),
        get_string('displayexpiry', 'portfolio'),
        '',
    );
    $table->data = array();
    $now = time();
    foreach ($queued as $q) {
        $e = portfolio_exporter::rewaken_object($q->id);
        $e->verify_rewaken(true);
        $queued = $e->get('queued');
        $baseurl = new moodle_url('/portfolio/add.php', array('id' => $q->id, 'logreturn' => 1, 'sesskey' => sesskey()));

        $iconstr = $OUTPUT->action_icon(new moodle_url($baseurl, array('cancel' => 1)), new pix_icon('t/stop', get_string('cancel')));

        if (!$e->get('queued') && $e->get('expirytime') > $now) {
            $iconstr .= $OUTPUT->action_icon($baseurl, new pix_icon('t/go', get_string('continue')));
        }
        $table->data[] = array(
            $e->get('caller')->display_name(),
            (($e->get('instance')) ? $e->get('instance')->get('name') : get_string('noinstanceyet', 'portfolio')),
            $e->get('caller')->heading_summary(),
            userdate($q->expirytime),
            $iconstr,
        );
        unset($e); // This could potentially be quite big, so free it.
    }
    echo $OUTPUT->heading(get_string('queuesummary', 'portfolio'));
    echo html_writer::table($table);
    $somethingprinted = true;
}
// Paging - get total count separately.
$logcount = $DB->count_records('portfolio_log', array('userid' => $USER->id));
if ($logcount > 0) {
    $table = new html_table();
    $table->head = array(
        get_string('plugin', 'portfolio'),
        get_string('displayarea', 'portfolio'),
        get_string('transfertime', 'portfolio'),
    );
    $logs = $DB->get_records('portfolio_log', array('userid' => $USER->id), 'time DESC', '*', ($page * $perpage), $perpage);
    foreach ($logs as $log) {
        if (!empty($log->caller_file)) {
            portfolio_include_callback_file($log->caller_file);
        } else if (!empty($log->caller_component)) {
            portfolio_include_callback_file($log->caller_component);
        } else { // Errrmahgerrrd - this should never happen. Skipping.
            continue;
        }
        $class = $log->caller_class;
        $pluginname = '';
        try {
            $plugin = portfolio_instance($log->portfolio);
            $url = $plugin->resolve_static_continue_url($log->continueurl);
            if ($url) {
                $pluginname = '<a href="' . $url . '">' . $plugin->get('name') . '</a>';
            } else {
                $pluginname = $plugin->get('name');
            }
        } catch (portfolio_exception $e) { // May have been deleted.
            $pluginname = get_string('unknownplugin', 'portfolio');
        }

        $table->data[] = array(
            $pluginname,
            '<a href="' . $log->returnurl . '">' . call_user_func(array($class, 'display_name')) . '</a>',
            userdate($log->time),
        );
    }
    echo $OUTPUT->heading(get_string('logsummary', 'portfolio'));
    $pagingbar = new paging_bar($logcount, $page, $perpage, $CFG->wwwroot . '/user/portfoliologs.php?');
    echo $OUTPUT->render($pagingbar);
    echo html_writer::table($table);
    echo $OUTPUT->render($pagingbar);
    $somethingprinted = true;
}
if (!$somethingprinted) {
    echo $OUTPUT->heading($strportfolios);
    echo get_string('nologs', 'portfolio');
}
echo $OUTPUT->box_end();
echo $OUTPUT->footer();


