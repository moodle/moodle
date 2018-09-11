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



// "Navigate by concept tags" page (viewable by both student and teacher)

// A table
// Left column is concept tag, right column contains the (clickable) items
// Untagged items are listed at the bottom (with the left column saying "[no tag]")
// You can pass in a tag name, and it will filter the table to just that tag

// Algorithm:
// get all concept tags defined, with all items for each tag (including untagged items assigned to tag '[no tag]')
// start table
// For each concept tag
// - write left column, with the concept tag inside
// - write right column
//   - for each tagged element, write a clickable link, with a separator
// end table

require_once('../../config.php');
require_once('lib.php');


// possible parameter names
$CONTAG_TAG_NAME_KEY_NAME = 'tag_name';

// Set up necessary parameters
$courseid = required_param('id', PARAM_INT);

$url = new moodle_url('/blocks/contag/view.php', array('id'=>$courseid));
$PAGE->set_url($url);

// SECURITY: Basic access control checks
if (!$course = $DB->get_record('course', array('id'=> $courseid))){
    print_error('courseidunknown','block_contag');
}

require_login($course->id); // SECURITY: make sure the user has access to this course and is logged in

// other parameters
$single_tag_name=null;
$get_keys = array_keys($_GET);
if (in_array($CONTAG_TAG_NAME_KEY_NAME, $get_keys)){ // have 'tag_name'? it means we will filter the table by it
    $single_tag_name=$_GET[$CONTAG_TAG_NAME_KEY_NAME];
    if (!contag_get_tag_id($courseid, $single_tag_name)){ // ERROR CHECK: does it exist?
        contag_print_warning(get_string('unknown_tag_name_left', 'block_contag') . htmlspecialchars($single_tag_name) . get_string('unknown_tag_name_right', 'block_contag'));
        $single_tag_name=null;
    }
 }

// Set up necessary strings
$formheader = get_string('viewingformheader', 'block_contag');

// Print page elements
$navigation = build_navigation($formheader);
print_header_simple("$formheader", "", $navigation, "", "", true, "");
$OUTPUT->heading(get_string('viewingformpageheading','block_contag'));

$context = get_context_instance(CONTEXT_COURSE, $courseid);
if (has_capability('block/contag:view', $context)){ // can they view tags?
        
    /* BEGIN TABLE */
    $tags_with_their_items = contag_get_all_tags_with_their_items($courseid);
    
    $table = new stdClass();
    $table->head = array(get_string('table_concept_heading', 'block_contag'), get_string('table_items_heading', 'block_contag'));
    $table->data[] = array();
    
    // build table data
	$tag_anchor_links = array();
    foreach($tags_with_their_items as $tag){
        if ((!$single_tag_name) || ($single_tag_name == $tag->tag_name)){ // if we are looking at a single tag, then show only if this is the right one
			$curr_tag_name = contag_get_tag_name($tag);
			$tag_anchor_links[]="<a href=\"#".$curr_tag_name."\">".$curr_tag_name."</a>";
            usort($tag->items, "contag_items_by_type_and_display_name_cmp"); // sort the subarray
			$bullet_str = ""; // this routine formats the resources list into a bullet list
			foreach($tag->items as $item){
				$bullet_str .= "<li>" . contag_get_html_link_for_item($item) . "</li>";
			}
			if ($bullet_str != ""){ // only make it an html list if there's something to show!
				$bullet_str = "<ul>" . $bullet_str . "</ul>";
			}
            $table->data[] = array("<a name=\"".$curr_tag_name."\"></a>".$curr_tag_name, $bullet_str); // add row - second column combines all items (processed to links) with ", "
        }
    }
    
	if (!$single_tag_name && !empty($tag_anchor_links)){
		print("<center><p>Jump to: " . implode(", ",$tag_anchor_links)."</p></center>");
	}
    contag_print_table($table);
    /* END TABLE */
    
    if ($single_tag_name && count($tags_with_their_items) > 1){ // are we filtering (and more tags exist)?
        print('<br/><span style="font-size: 80%; font-style: italic; padding: 50px;">Currently showing only 1 out of '.count($tags_with_their_items).' tags. <a href="view.php?id='.$courseid.'">Show all tags</a>.</span>');
    }
 } else {
    print("You do not have permissions to navigate by concept tags.");
 }
$OUTPUT->footer($course);

?>