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

admin_externalpage_setup('purgecaches');

require_login();
require_capability('moodle/site:config', context_system::instance());

if ($confirm) {
    require_sesskey();

    // Valid request. Purge, and redisplay the form so it is easy to purge again
    // in the near future.
    purge_all_caches();
    redirect(new moodle_url('/admin/purgecaches.php'), get_string('purgecachesfinished', 'admin'));

} else {
    // Show a confirm form.
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('purgecaches', 'admin'));

    $url = new moodle_url('/admin/purgecaches.php', array('sesskey'=>sesskey(), 'confirm'=>1));
    $button = new single_button($url, get_string('purgecaches','admin'), 'post');

    // Cancel button takes them back to the page the were on, if possible,
    // otherwise to the site home page.
    $return = new moodle_url('/');
    if (isset($_SERVER['HTTP_REFERER']) and !empty($_SERVER['HTTP_REFERER'])) {
        if ($_SERVER['HTTP_REFERER'] !== "$CFG->wwwroot/$CFG->admin/purgecaches.php") {
            $return = $_SERVER['HTTP_REFERER'];
        }
    }

    echo $OUTPUT->confirm(get_string('purgecachesconfirm', 'admin'), $button, $return);
    echo $OUTPUT->footer();
}
