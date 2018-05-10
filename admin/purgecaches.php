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
 * This script triggers a full purging of system caches,
 * this is useful mostly for developers who did not disable the caching.
 *
 * @package    core
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');

$confirm = optional_param('confirm', 0, PARAM_BOOL);
$returnurl = optional_param('returnurl', null, PARAM_LOCALURL);

admin_externalpage_setup('purgecaches');

// If we have got here as a confirmed aciton, do it.
if ($confirm && confirm_sesskey()) {

    // Valid request. Purge, and redirect the user back to where they came from.
    purge_all_caches();

    if ($returnurl) {
        $returnurl = $CFG->wwwroot . $returnurl;
    } else {
        $returnurl = new moodle_url('/admin/purgecaches.php');
    }
    redirect($returnurl, get_string('purgecachesfinished', 'admin'));
}

// Otherwise, show a button to actually purge the caches.
$actionurl = new moodle_url('/admin/purgecaches.php', array('sesskey'=>sesskey(), 'confirm'=>1));
if ($returnurl) {
    $actionurl->param('returnurl', $returnurl);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('purgecaches', 'admin'));

echo $OUTPUT->box_start('generalbox', 'notice');
echo html_writer::tag('p', get_string('purgecachesconfirm', 'admin'));
echo $OUTPUT->single_button($actionurl, get_string('purgecaches', 'admin'), 'post');
echo $OUTPUT->box_end();

echo $OUTPUT->footer();
