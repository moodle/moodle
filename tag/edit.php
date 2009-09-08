 <?php // $Id$

require_once('../config.php');
require_once('lib.php');
require_once('edit_form.php');

require_js(array('yui_dom-event', 'yui_connection', 'yui_animation', 'yui_autocomplete'));

require_login();

if (empty($CFG->usetags)) {
    print_error('tagsaredisabled', 'tag');
}

$tag_id = optional_param('id', 0, PARAM_INT);
$tag_name = optional_param('tag', '', PARAM_TAG);

if ($tag_name) {
    $tag = tag_get('name', $tag_name, '*');
} else if ($tag_id) {
    $tag = tag_get('id', $tag_id, '*');
}

if (empty($tag)) {
    redirect($CFG->wwwroot.'/tag/search.php');
}

$tagname = tag_display_name($tag);

//Editing a tag requires moodle/tag:edit capability
$systemcontext   = get_context_instance(CONTEXT_SYSTEM);
require_capability('moodle/tag:edit', $systemcontext);

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

$tagform = new tag_edit_form();
if ( $tag->tagtype == 'official' ) {
    $tag->tagtype = '1';
} else {
    $tag->tagtype = '0';
}
$tagform->set_data($tag);

// If new data has been sent, update the tag record
if ($tagnew = $tagform->get_data()) {

    tag_description_set($tag_id, stripslashes($tagnew->description), $tagnew->descriptionformat);

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

        $tagnew->timemodified = time();

        if (has_capability('moodle/tag:manage', $systemcontext)) {
            // rename tag
            if(!tag_rename($tag->id, $tagnew->rawname)) {
                error('Error updating tag record');
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


$navlinks = array();
$navlinks[] = array('name' => get_string('tags', 'tag'), 'link' => "{$CFG->wwwroot}/tag/search.php", 'type' => '');
$navlinks[] = array('name' => $tagname, 'link' => '', 'type' => '');

$navigation = build_navigation($navlinks);
print_header_simple(get_string('tag', 'tag') . ' - '. $tagname, '', $navigation);

print_heading($tagname, '', 2);

if (!empty($errorstring)) {
    notify($errorstring);
}

$tagform->display();

if (ajaxenabled()) {
?>

<script type="text/javascript">

// An XHR DataSource
var myServer = "./tag_autocomplete.php";
var myDataSource = new YAHOO.widget.DS_XHR(myServer, ["\n", "\t"]);
myDataSource.responseType = YAHOO.widget.DS_XHR.TYPE_FLAT;
myDataSource.maxCacheEntries = 60;
myDataSource.queryMatchSubset = true;

var myAutoComp = new YAHOO.widget.AutoComplete("id_relatedtags","relatedtags-autocomplete", myDataSource);
myAutoComp.delimChar = ",";
myAutoComp.maxResultsDisplayed = 20;
myAutoComp.minQueryLength = 2;
myAutoComp.allowBrowserAutocomplete = false;
myAutoComp.formatResult = function(aResultItem, sQuery) {
    return aResultItem[1];
}
</script>

<?php
}
print_footer();

?>
