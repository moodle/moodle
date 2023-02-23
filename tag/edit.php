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
 * @package    core_tag
 * @category   tag
 * @copyright  2007 Luiz Cruz <luiz.laydner@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once('lib.php');
require_once('edit_form.php');

$tagid = optional_param('id', 0, PARAM_INT);
$tagname = optional_param('tag', '', PARAM_TAG);
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);

require_login();

if (empty($CFG->usetags)) {
    throw new \moodle_exception('tagsaredisabled', 'tag');
}

//Editing a tag requires moodle/tag:edit capability
$systemcontext   = context_system::instance();
require_capability('moodle/tag:edit', $systemcontext);

if ($tagname) {
    $tagcollid = optional_param('tc', 0, PARAM_INT);
    if (!$tagcollid) {
        // Tag name specified but tag collection was not. Try to guess it.
        $tags = core_tag_tag::guess_by_name($tagname, '*');
        if (count($tags) > 1) {
            // This tag was found in more than one collection, redirect to search.
            redirect(new moodle_url('/tag/search.php', array('tag' => $tagname)));
        } else if (count($tags) == 1) {
            $tag = reset($tags);
        }
    } else {
        if (!$tag = core_tag_tag::get_by_name($tagcollid, $tagname, '*')) {
            redirect(new moodle_url('/tag/search.php', array('tagcollid' => $tagcollid)));
        }
    }
} else if ($tagid) {
    $tag = core_tag_tag::get($tagid, '*');
}

if (empty($tag)) {
    redirect(new moodle_url('/tag/search.php'));
}

$PAGE->set_url($tag->get_view_url());
$PAGE->set_subpage($tag->id);
$PAGE->set_context($systemcontext);
$PAGE->set_blocks_editing_capability('moodle/tag:editblocks');
$PAGE->set_pagelayout('standard');

$tagname = $tag->get_display_name();
$tagcollid = $tag->tagcollid;

// set the relatedtags field of the $tag object that will be passed to the form
$data = $tag->to_object();
$data->relatedtags = core_tag_tag::get_item_tags_array('core', 'tag', $tag->id);

$options = new stdClass();
$options->smiley = false;
$options->filter = false;

// convert and remove any XSS
$data->description       = format_text($tag->description, $tag->descriptionformat, $options);
$data->descriptionformat = FORMAT_HTML;

$errorstring = '';

$editoroptions = array(
    'maxfiles'  => EDITOR_UNLIMITED_FILES,
    'maxbytes'  => $CFG->maxbytes,
    'trusttext' => false,
    'context'   => $systemcontext,
    'subdirs'   => file_area_contains_subdirs($systemcontext, 'tag', 'description', $tag->id),
);
$data = file_prepare_standard_editor($data, 'description', $editoroptions, $systemcontext, 'tag', 'description', $data->id);

$tagform = new tag_edit_form(null, array('editoroptions' => $editoroptions, 'tag' => $tag));
$data->returnurl = $returnurl;

$tagform->set_data($data);

if ($tagform->is_cancelled()) {
    redirect($returnurl ? new moodle_url($returnurl) : $tag->get_view_url());
} else if ($tagnew = $tagform->get_data()) {
    // If new data has been sent, update the tag record.
    $updatedata = array();

    if (has_capability('moodle/tag:manage', $systemcontext)) {
        $updatedata['isstandard'] = empty($tagnew->isstandard) ? 0 : 1;
        $updatedata['rawname'] = $tagnew->rawname;
    }

    $tagnew = file_postupdate_standard_editor($tagnew, 'description', $editoroptions,
            $systemcontext, 'tag', 'description', $tag->id);
    $updatedata['description'] = $tagnew->description;
    $updatedata['descriptionformat'] = $tagnew->descriptionformat;

    // Update name, description and whether it is a standard tag.
    $tag->update($updatedata);

    // Updated related tags.
    $tag->set_related_tags($tagnew->relatedtags);

    redirect($returnurl ? new moodle_url($returnurl) : $tag->get_view_url());
}

navigation_node::override_active_url(new moodle_url('/tag/search.php'));
$PAGE->navbar->add($tagname);
$PAGE->navbar->add(get_string('edit'));
$PAGE->set_title(get_string('tag', 'tag') . ' - '. $tagname);
$PAGE->set_heading($COURSE->fullname);
echo $OUTPUT->header();
echo $OUTPUT->heading($tagname, 2);

if (!empty($errorstring)) {
    echo $OUTPUT->notification($errorstring);
}

$tagform->display();

echo $OUTPUT->footer();
