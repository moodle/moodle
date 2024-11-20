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
 * Search and replace http -> https throughout all texts in the whole database
 *
 * @package    tool_httpsreplace
 * @copyright Copyright (c) 2016 Blackboard Inc. (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('toolhttpsreplace');

$context = context_system::instance();

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/admin/tool/httpsreplace/index.php'));
$PAGE->set_title(get_string('pageheader', 'tool_httpsreplace'));
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('pageheader', 'tool_httpsreplace'));

if (!$DB->replace_all_text_supported()) {
    echo $OUTPUT->notification(get_string('notimplemented', 'tool_httpsreplace'));
    echo $OUTPUT->footer();
    die;
}

if (!is_https()) {
    echo $OUTPUT->notification(get_string('httpwarning', 'tool_httpsreplace'), 'warning');
}

$form = new \tool_httpsreplace\form();

$finder = new \tool_httpsreplace\url_finder();

$PAGE->set_cacheable(false);
$progressbar = new progress_bar();

// Preemptively reset the navcache before closing, so it remains the same on shutdown.
navigation_cache::destroy_volatile_caches();
\core\session\manager::write_close();

// Prepare for streamed output.
echo $OUTPUT->footer();
echo $OUTPUT->select_element_for_append();

if (!$data = $form->get_data()) {

    echo $progressbar->create();

    $results = $finder->http_link_stats($progressbar);

    $progressbar->update_full(100, get_string('complete', 'tool_httpsreplace'));

    if (empty($results)) {
        echo '<p>'.get_string('oktoprocede', 'tool_httpsreplace').'</p>';
    } else {
        arsort($results);
        $table = new html_table();
        $table->id = 'plugins-check';
        $table->head = array(
            get_string('domain', 'tool_httpsreplace'),
            get_string('count', 'tool_httpsreplace'),
        );
        $data = array();
        foreach ($results as $domain => $count) {
            $cleandomain = format_text($domain, FORMAT_PLAIN);
            $data[] = [$cleandomain, $count];
        }
        $table->data = $data;
        echo html_writer::table($table);
        echo get_string('domainexplainhelp', 'tool_httpsreplace');
    }
    echo $OUTPUT->notification(get_string('takeabackupwarning', 'tool_httpsreplace'), 'warning');
    $form->display();
} else {
    // Scroll to the end when finished.
    $PAGE->requires->js_init_code("window.scrollTo(0, document.body.scrollHeight);");

    echo html_writer::tag('p', get_string('replacing', 'tool_httpsreplace'));

    echo $progressbar->create();

    echo $OUTPUT->box_start();
    $finder->upgrade_http_links($progressbar);
    echo $OUTPUT->box_end();

    $progressbar->update_full(100, get_string('complete', 'tool_httpsreplace'));

    echo $OUTPUT->continue_button(new moodle_url('/admin/settings.php', ['section' => 'httpsecurity']));
}
