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
 * @package    core
 * @subpackage tag
 * @copyright  2007 Luiz Cruz <luiz.laydner@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once('lib.php');
require_once('locallib.php');

require_login();

if (empty($CFG->usetags)) {
    print_error('tagsaredisabled', 'tag');
}

$query   = optional_param('query', '', PARAM_RAW);
$page    = optional_param('page', 0, PARAM_INT); // which page to show
$perpage = optional_param('perpage', 18, PARAM_INT);

$params = array();
if ($query !== '') {
    $params['query'] = $query;
}
if ($page !== 0) {
    $params['page'] = $page;
}
if ($perpage !== 18) {
    $params['perpage'] = $perpage;
}
$PAGE->set_url(new moodle_url('/tag/search.php', $params));
$PAGE->set_context(get_context_instance(CONTEXT_SYSTEM));
$PAGE->set_pagelayout('standard');

$systemcontext = get_context_instance(CONTEXT_SYSTEM);
$manage_link = '&nbsp;';

$PAGE->set_title(get_string('tags', 'tag'));
$PAGE->set_heading($SITE->fullname.': '.$PAGE->title);
echo $OUTPUT->header();

if ( has_capability('moodle/tag:manage',$systemcontext) ) {
    echo '<div class="managelink"><a href="'. $CFG->wwwroot .'/tag/manage.php">' . get_string('managetags', 'tag') . '</a></div>' ;
}

echo $OUTPUT->heading(get_string('searchtags', 'tag'), 2);

tag_print_search_box();

if(!empty($query)) {
     tag_print_search_results($query, $page, $perpage);
}

echo '<br/><br/>';

echo $OUTPUT->box_start('generalbox', 'big-tag-cloud-box');
tag_print_cloud(150);
echo $OUTPUT->box_end();

echo $OUTPUT->footer();
