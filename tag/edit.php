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

$tag_id = optional_param('id', 0, PARAM_INT);
$tag_name = optional_param('tag', '', PARAM_TAG);

require_login();

if (empty($CFG->usetags)) {
    print_error('tagsaredisabled', 'tag');
}

//Editing a tag requires moodle/tag:edit capability
$systemcontext   = context_system::instance();
require_capability('moodle/tag:edit', $systemcontext);

if ($tag_name) {
    $tag = tag_get('name', $tag_name, '*');
} else if ($tag_id) {
    $tag = tag_get('id', $tag_id, '*');
}

if (empty($tag)) {
    redirect($CFG->wwwroot.'/tag/search.php');
}

$PAGE->set_url('/tag/index.php', array('id' => $tag->id));
$PAGE->set_subpage($tag->id);
$PAGE->set_context($systemcontext);
$PAGE->set_blocks_editing_capability('moodle/tag:editblocks');
$PAGE->set_pagelayout('base');

$tagname = tag_display_name($tag);

// set the relatedtags field of the $tag object that will be passed to the form
$tag->relatedtags = tag_get_related_tags_csv(tag_get_related_tags($tag->id, TAG_RELATED_MANUAL), TAG_RETURN_TEXT);

if (can_use_html_editor()) {
    $options = new stdClass();
    $options->smiley = false;
    $options->filter = false;

    // convert and remove any XSS
    $tag->description       = format_text($tag->description, $tag->descriptionformat, $options);
    $tag->descriptionformat = FORMAT_HTML;
}

$errorstring = '';

$editoroptions = array(
    'maxfiles'  => EDITOR_UNLIMITED_FILES,
    'maxbytes'  => $CFG->maxbytes,
    'trusttext' => false,
    'context'   => $systemcontext
);
$tag = file_prepare_standard_editor($tag, 'description', $editoroptions, $systemcontext, 'tag', 'description', $tag->id);

$tagform = new tag_edit_form(null, compact('editoroptions'));
if ( $tag->tagtype == 'official' ) {
    $tag->tagtype = '1';
} else {
    $tag->tagtype = '0';
}

$tagform->set_data($tag);

// If new data has been sent, update the tag record
if ($tagnew = $tagform->get_data()) {

    if (has_capability('moodle/tag:manage', $systemcontext)) {
        if (($tag->tagtype != 'default') && (!isset($tagnew->tagtype) || ($tagnew->tagtype != '1'))) {
            tag_type_set($tag->id, 'default');

        } elseif (($tag->tagtype != 'official') && ($tagnew->tagtype == '1')) {
            tag_type_set($tag->id, 'official');
        }
    }

    if (!has_capability('moodle/tag:manage', $systemcontext) && !has_capability('moodle/tag:edit', $systemcontext)) {
        unset($tagnew->name);
        unset($tagnew->rawname);

    } else {  // They might be trying to change the rawname, make sure it's a change that doesn't affect name
        $norm = tag_normalize($tagnew->rawname, TAG_CASE_LOWER);
        $tagnew->name = array_shift($norm);

        if ($tag->name != $tagnew->name) {  // The name has changed, let's make sure it's not another existing tag
            if (tag_get_id($tagnew->name)) {   // Something exists already, so flag an error
                $errorstring = s($tagnew->rawname).': '.get_string('namesalreadybeeingused', 'tag');
            }
        }
    }

    if (empty($errorstring)) {    // All is OK, let's save it

        $tagnew = file_postupdate_standard_editor($tagnew, 'description', $editoroptions, $systemcontext, 'tag', 'description', $tag->id);

        tag_description_set($tag_id, $tagnew->description, $tagnew->descriptionformat);

        $tagnew->timemodified = time();

        if (has_capability('moodle/tag:manage', $systemcontext)) {
            // rename tag
            if(!tag_rename($tag->id, $tagnew->rawname)) {
                print_error('errorupdatingrecord', 'tag');
            }
        }

        //log tag changes activity
        //if tag name exist from form, renaming is allow.  record log action as rename
        //otherwise, record log action as update
        if (isset($tagnew->name) && ($tag->name != $tagnew->name)){
            add_to_log($COURSE->id, 'tag', 'update', 'index.php?id='. $tag->id, $tag->name . '->'. $tagnew->name);

        } elseif ($tag->description != $tagnew->description) {
            add_to_log($COURSE->id, 'tag', 'update', 'index.php?id='. $tag->id, $tag->name);
        }

        //updated related tags
        tag_set('tag', $tagnew->id, explode(',', trim($tagnew->relatedtags)));
        //print_object($tagnew); die();

        redirect($CFG->wwwroot.'/tag/index.php?tag='.rawurlencode($tag->name)); // must use $tag here, as the name isn't in the edit form
    }
}

$PAGE->navbar->add(get_string('tags', 'tag'), new moodle_url('/tag/search.php'));
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

if (ajaxenabled()) {
    $PAGE->requires->js('/tag/tag.js');
    $PAGE->requires->js_function_call('init_tag_autocomplete', null, true);
}
echo $OUTPUT->footer();
