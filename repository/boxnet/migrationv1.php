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
 * Box.net APIv1 migration tool.
 *
 * This tool is intended to migrate the references of the APIv1 of Box.net
 * as this API is going end of life in December 14th 2013. As there is no
 * way to support the references in the APIv2, we will convert those old
 * references to local files.
 *
 * This operation can take a long time depending on the number of references
 * used and their size.
 *
 * @package    repository_boxnet
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @todo       Deprecate/remove this tool after the 14th of December 2013.
 */

define('NO_OUTPUT_BUFFERING', true);

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/repository/boxnet/locallib.php');

require_login();
require_capability('moodle/site:config', context_system::instance());

$title = get_string('migrationtool', 'repository_boxnet');
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('maintenance');
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_url(new moodle_url('/repository/boxnet/migrationv1.php'));
$PAGE->navbar->add($title);

$confirm = optional_param('confirm', false, PARAM_BOOL);

echo $OUTPUT->header();
echo $OUTPUT->heading('Reference migration tool');

if ($confirm && confirm_sesskey()) {
    echo html_writer::start_tag('pre', array());
    repository_boxnet_migrate_references_from_apiv1();
    echo html_writer::end_tag('pre', array());
} else {
    $a = new stdClass();
    $a->docsurl = get_docs_url('Box.net_APIv1_migration');
    echo html_writer::tag('p', get_string('migrationinfo', 'repository_boxnet', $a));
    $execurl = new moodle_url('/repository/boxnet/migrationv1.php', array('confirm' => 1, 'sesskey' => sesskey()));
    $button = new single_button($execurl, get_string('runthemigrationnow', 'repository_boxnet'));
    echo $OUTPUT->render($button);
}

echo $OUTPUT->footer();
