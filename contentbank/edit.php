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
 * Create or update contents through the specific content type editor
 *
 * @package    core_contentbank
 * @copyright  2020 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../config.php');

require_login();

$contextid = required_param('contextid', PARAM_INT);
$pluginname = required_param('plugin', PARAM_PLUGIN);
$id = optional_param('id', null, PARAM_INT);
$library = optional_param('library', null, PARAM_RAW);

$context = context::instance_by_id($contextid, MUST_EXIST);

$cb = new \core_contentbank\contentbank();
if (!$cb->is_context_allowed($context)) {
    throw new \moodle_exception('contextnotallowed', 'core_contentbank');
}

require_capability('moodle/contentbank:access', $context);

$returnurl = new \moodle_url('/contentbank/view.php', ['id' => $id]);

if (!empty($id)) {
    $record = $DB->get_record('contentbank_content', ['id' => $id], '*', MUST_EXIST);
    $contentclass = "$record->contenttype\\content";
    $content = new $contentclass($record);
    // Set the heading title.
    $heading = $content->get_name();
    // The content type of the content overwrites the pluginname param value.
    $contenttypename = $content->get_content_type();
    $breadcrumbtitle = get_string('edit');
} else {
    $contenttypename = "contenttype_$pluginname";
    $heading = get_string('addinganew', 'moodle', get_string('description', $contenttypename));
    $content = null;
    $breadcrumbtitle = get_string('add');
}

// Check plugin is enabled.
$plugin = core_plugin_manager::instance()->get_plugin_info($contenttypename);
if (!$plugin || !$plugin->is_enabled()) {
    throw new \moodle_exception('unsupported', 'core_contentbank', $returnurl);
}

// Create content type instance.
$contenttypeclass = "$contenttypename\\contenttype";
if (class_exists($contenttypeclass)) {
    $contenttype = new $contenttypeclass($context);
} else {
    throw new \moodle_exception('unsupported', 'core_contentbank', $returnurl);
}

// Checks the user can edit this content and content type.
if (!$contenttype->can_edit($content)) {
    throw new \moodle_exception('contenttypenoedit', 'core_contentbank', $returnurl);
}

$values = [
    'contextid' => $contextid,
    'plugin' => $pluginname,
    'id' => $id,
    'heading' => $heading,
    'library' => $library
];

$title = get_string('contentbank');
\core_contentbank\helper::get_page_ready($context, $title, true);
if ($PAGE->course) {
    require_login($PAGE->course->id);
}

if ($context->contextlevel == CONTEXT_COURSECAT) {
    $PAGE->set_primary_active_tab('home');
}

$PAGE->set_url(new \moodle_url('/contentbank/edit.php', $values));
if ($context->id == \context_system::instance()->id) {
    $PAGE->set_context(context_course::instance($context->id));
} else {
    $PAGE->set_context($context);
}
if ($content) {
    $PAGE->navbar->add($content->get_name(), new \moodle_url('/contentbank/view.php', ['id' => $id]));
}
$PAGE->navbar->add($breadcrumbtitle);
$PAGE->set_title($title);
$PAGE->set_pagelayout('incourse');
$PAGE->set_secondary_active_tab('contentbank');

// Instantiate the content type form.
$editorclass = "$contenttypename\\form\\editor";
if (!class_exists($editorclass)) {
    throw new \moodle_exception('noformdesc');
}

$editorform = new $editorclass(null, $values);

if ($editorform->is_cancelled()) {
    if (empty($id)) {
        $returnurl = new \moodle_url('/contentbank/index.php', ['contextid' => $context->id]);
    }
    redirect($returnurl);
} else if ($data = $editorform->get_data()) {
    $id = $editorform->save_content($data);
    // Just in case we've created a new content.
    $returnurl->param('id', $id);
    redirect($returnurl);
}

echo $OUTPUT->header();
$editorform->display();
echo $OUTPUT->footer();
