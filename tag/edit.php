<?php

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
$systemcontext   = get_context_instance(CONTEXT_SYSTEM);
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

$PAGE->requires->yui2_lib('animation');
$PAGE->requires->yui2_lib('autocomplete');

$tagname = tag_display_name($tag);

// set the relatedtags field of the $tag object that will be passed to the form
$tag->relatedtags = tag_get_related_tags_csv(tag_get_related_tags($tag->id, TAG_RELATED_MANUAL), TAG_RETURN_TEXT);

if (can_use_html_editor()) {
    $options = new object();
    $options->smiley = false;
    $options->filter = false;

    // convert and remove any XSS
    $tag->description       = format_text($tag->description, $tag->descriptionformat, $options);
    $tag->descriptionformat = FORMAT_HTML;
}

$errorstring = '';

$editoroptions = array('maxfiles'=>EDITOR_UNLIMITED_FILES, 'maxbytes'=>$CFG->maxbytes, 'trusttext'=>false);
$tag = file_prepare_standard_editor($tag, 'description', $editoroptions, $systemcontext, 'tag_description', $tag->id);

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
        $tagnew->name = array_shift(tag_normalize($tagnew->rawname, TAG_CASE_LOWER));

        if ($tag->name != $tagnew->name) {  // The name has changed, let's make sure it's not another existing tag
            if (tag_get_id($tagnew->name)) {   // Something exists already, so flag an error
                $errorstring = s($tagnew->rawname).': '.get_string('namesalreadybeeingused', 'tag');
            }
        }
    }

    if (empty($errorstring)) {    // All is OK, let's save it

        $tagnew = file_postupdate_standard_editor($tagnew, 'description', $editoroptions, $systemcontext, 'tag_description', $tag->id);

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
$PAGE->set_title(get_string('tag', 'tag') . ' - '. $tagname);
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
