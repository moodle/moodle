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
 * Form for resetting the status
 *
 * @package   tool_crawler
 * @author    Nicholas Hoobin <nicholashoobin@catalyst-au.net>
 * @copyright Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->dirroot .'/admin/tool/crawler/lib.php');
require_once($CFG->libdir . '/adminlib.php');

require_login(null, false);
require_capability('moodle/site:config', context_system::instance());
admin_externalpage_setup('tool_crawler_status');

$form = new \tool_crawler\form\reset(null);

$baseurl = new moodle_url('/admin/tool/crawler/');

if ($data = $form->get_data()) {

    if ($form->is_submitted()) {
        global $DB;

        unset_config('crawlstart', 'tool_crawler');
        unset_config('crawlend', 'tool_crawler');
        unset_config('crawltick', 'tool_crawler');
        $DB->delete_records('tool_crawler_url');
        $DB->delete_records('tool_crawler_edge');

        @unlink($CFG->dataroot . '/tool_crawler_cookies.txt');
        redirect($baseurl);
    }
} else if ($form->is_cancelled()) {
    redirect($baseurl);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('resetprogress_header', 'tool_crawler'));
require('tabs.php');
echo $tabs;
echo $form->display();
echo $OUTPUT->footer();
