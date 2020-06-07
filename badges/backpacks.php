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
 * Display a list of badge backpacks for the site.
 *
 * @package    core_badges
 * @copyright  2019 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/badgeslib.php');
$context = context_system::instance();
$PAGE->set_context($context);

require_login(0, false);
require_capability('moodle/badges:manageglobalsettings', $context);
// There should be an admin setting to completely turn off badges.
$output = $PAGE->get_renderer('core', 'badges');

$id = optional_param('id', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$confirm = optional_param('confirm', 1, PARAM_BOOL);

$PAGE->set_pagelayout('admin');
$url = new moodle_url('/badges/backpacks.php');

if (empty($CFG->badges_allowexternalbackpack)) {
    redirect($CFG->wwwroot);
}

$PAGE->set_url($url);
$PAGE->set_title(get_string('managebackpacks', 'badges'));
$PAGE->set_heading($SITE->fullname);

$msg = '';
$msgtype = 'error';
if ($action == 'delete' && $confirm && confirm_sesskey()) {
    if (badges_delete_site_backpack($id)) {
        $msg = get_string('sitebackpackdeleted', 'badges');
        $msgtype = 'notifysuccess';
    } else {
        $msg = get_string('sitebackpacknotdeleted', 'badges');
    }
}

if ($action == 'edit') {
    $backpack = null;
    if (!empty($id)) {
        $backpack = badges_get_site_backpack($id);
    }
    $form = new \core_badges\form\external_backpack(null, ['externalbackpack' => $backpack]);
    if ($form->is_cancelled()) {
        redirect($url);
    } else if ($data = $form->get_data()) {
        require_sesskey();
        if (!empty($data->id)) {
            badges_update_site_backpack($data->id, $data);
        } else {
            badges_create_site_backpack($data);
        }
        redirect($url);
    }

    echo $OUTPUT->header();
    echo $output->heading(get_string('managebackpacks', 'badges'));

    $form->display();
} else {
    echo $OUTPUT->header();
    echo $output->heading(get_string('managebackpacks', 'badges'));

    if ($msg) {
        echo $OUTPUT->notification($msg, $msgtype);
    }
    $page = new \core_badges\output\external_backpacks_page($url);
    echo $output->render($page);
}

echo $OUTPUT->footer();
