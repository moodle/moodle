<?php
// $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 2001-2007  Martin Dougiamas  http://dougiamas.com       //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////
set_time_limit(0);
require_once '../../../config.php';
require_once $CFG->libdir . '/grade/grade_tree.php';
require_once $CFG->libdir . '/gradelib.php';

/**
 * Returns a HTML list with sorting arrows and insert boxes. This is a recursive method.
 * @param int $level The level of recursion
 * @param array $elements The elements to display in a list. Defaults to this->tree_array
 * @param int $source_sortorder A source sortorder, given when an element needs to be moved or inserted.
 * @param string $action 'move' or 'insert'
 * @param string $source_type 'topcat', 'subcat' or 'item'
 * @return string HTML code
 */
function get_edit_tree($gtree, $level = 1, $elements = NULL, $source_sortorder = NULL, $action = NULL, $source_type = NULL) {
	global $CFG;
	global $USER;

	$strmove = get_string("move");
	$strmoveup = get_string("moveup");
	$strmovedown = get_string("movedown");
	$strmovehere = get_string("movehere");
	$strcancel = get_string("cancel");
	$stredit = get_string("edit");
	$strdelete = get_string("delete");
	$strhide = get_string("hide");
	$strshow = get_string("show");
	$strlock = get_string("lock", 'grades');
	$strunlock = get_string("unlock", 'grades');
	$strnewcategory = get_string("newcategory", 'grades');
	$strcategoryname = get_string("categoryname", 'grades');
	$strcreatecategory = get_string("createcategory", 'grades');
	$strsubcategory = get_string("subcategory", 'grades');
	$stritems = get_string("items", 'grades');
	$strcategories = get_string("categories", 'grades');

	$list = '';
	$closing_form_tags = '';

	if (empty ($elements)) {
		$list .= '<form action="category.php" method="post">' . "\n";
		$list .= '<ul id="grade_edit_tree">' . "\n";
		$elements = $gtree->tree_array['children'];

		$element_type_options = '<select name="element_type">' . "\n";
		$element_type_options .= "<option value=\"items\">$stritems</option><option value=\"categories\">$strcategories</option>\n";
		$element_type_options .= "</select>\n";

		$strforelementtypes = get_string("forelementtypes", 'grades', $element_type_options);

		$closing_form_tags .= '<fieldset><legend>' . $strnewcategory . '</legend>' . "\n";
		$closing_form_tags .= '<input type="hidden" name="sesskey" value="' . $USER->sesskey . '" />' . "\n";
		$closing_form_tags .= '<input type="hidden" name="id" value="' . $gtree->courseid . '" />' . "\n";
		$closing_form_tags .= '<input type="hidden" name="action" value="create" />' . "\n";
		$closing_form_tags .= '<label for="category_name">' . $strcategoryname . '</label>' . "\n";
		$closing_form_tags .= '<input id="category_name" type="text" name="category_name" size="40" />' . "\n";
		$closing_form_tags .= '<input type="submit" value="' . $strcreatecategory . '" />' . "\n";
		$closing_form_tags .= $strforelementtypes;
		$closing_form_tags .= '</fieldset>' . "\n";
		$closing_form_tags .= "</form>\n";
	} else {
		$list = '<ul class="level' . $level . 'children">' . "\n";
	}

	$first = true;
	$count = 1;
	$last = false;
	$last_sortorder = null;

	if (count($elements) == 1) {
		$last = true;
	}

	foreach ($elements as $sortorder => $element) {
		$object = $element['object'];
        $previous_sortorder = $element['prev'];
        $next_sortorder     = $element['next'];

		$object_name = $object->get_name();
		$object_class = get_class($object);
		$object_parent = $object->get_parent_id();
		$element_type = $gtree->get_element_type($element);

		$highlight_class = '';

		if ($source_sortorder == $sortorder && !empty ($action)) {
			$highlight_class = ' selected_element ';
		}

		// Prepare item icon if appropriate
		$module_icon = '';
		if (!empty ($object->itemmodule)) {
			$module_icon = '<div class="moduleicon">' . '<label for="checkbox_select_' . $sortorder . '">' . '<img src="' . $CFG->modpixpath . '/' . $object->itemmodule . '/icon.gif" alt="' . $object->itemmodule . '" title="' . $object->itemmodule . '" /></label></div>';
		}

		// Add dimmed_text span around object name if set to hidden
		$hide_show = 'hide';
		if ($object->is_hidden()) {
			$object_name = '<span class="dimmed_text">' . $object_name . '</span>';
			$hide_show = 'show';
		}

		// Prepare lock/unlock string
		$lock_unlock = 'lock';
		if ($object->is_locked()) {
			$lock_unlock = 'unlock';
		}

		// Prepare select checkbox for subcats and items
		$select_checkbox = '';
		if ($element_type != 'topcat') {
			$group = 'items';
			if ($element_type == 'subcat') {
				$group = 'categories';
			}

			$select_checkbox = '<div class="select_checkbox">' . "\n" . '<input id="checkbox_select_' . $sortorder . '" type="checkbox" name="' . $group . '[' . $sortorder . ']" />' . "\n" . '</div>' . "\n";

			// Add a label around the object name to trigger the checkbox
			$object_name = '<label for="checkbox_select_' . $sortorder . '">' . $object_name . '</label>';
		}

		$list .= '<li class="level' . $level . 'element sortorder' . $object->get_sortorder() . $highlight_class . '">' . "\n" . $select_checkbox . $module_icon . $object_name;

		$list .= '<div class="icons">' . "\n";

		// Print up arrow
		if (!$first) {
			$list .= '<a href="category.php?' . "source=$sortorder&amp;moveup={$previous_sortorder}$gtree->commonvars\">\n";
			$list .= '<img src="' . $CFG->pixpath . '/t/up.gif" class="iconsmall" ' . 'alt="' . $strmoveup . '" title="' . $strmoveup . '" /></a>' . "\n";
		} else {
			$list .= '<img src="' . $CFG->wwwroot . '/pix/spacer.gif" class="iconsmall" alt="" /> ' . "\n";
		}

		// Print down arrow
		if (!$last) {
			$list .= '<a href="category.php?' . "source=$sortorder&amp;movedown={$next_sortorder}$gtree->commonvars\">\n";
			$list .= '<img src="' . $CFG->pixpath . '/t/down.gif" class="iconsmall" ' . 'alt="' . $strmovedown . '" title="' . $strmovedown . '" /></a>' . "\n";
		} else {
			$list .= '<img src="' . $CFG->wwwroot . '/pix/spacer.gif" class="iconsmall" alt="" /> ' . "\n";
		}

		// Print move icon
		if ($element_type != 'topcat') {
			$list .= '<a href="category.php?' . "source=$sortorder&amp;action=move&amp;type=$element_type$gtree->commonvars\">\n";
			$list .= '<img src="' . $CFG->pixpath . '/t/move.gif" class="iconsmall" alt="' . $strmove . '" title="' . $strmove . '" /></a>' . "\n";
		} else {
			$list .= '<img src="' . $CFG->wwwroot . '/pix/spacer.gif" class="iconsmall" alt="" /> ' . "\n";
		}

		// Print edit icon
		$list .= '<a href="category.php?' . "target=$sortorder&amp;action=edit$gtree->commonvars\">\n";
		$list .= '<img src="' . $CFG->pixpath . '/t/edit.gif" class="iconsmall" alt="' .
		$stredit . '" title="' . $stredit . '" /></a>' . "\n";

		// Print delete icon
		$list .= '<a href="category.php?' . "target=$sortorder&amp;action=delete$gtree->commonvars\">\n";
		$list .= '<img src="' . $CFG->pixpath . '/t/delete.gif" class="iconsmall" alt="' .
		$strdelete . '" title="' . $strdelete . '" /></a>' . "\n";

		// Print hide/show icon
		$list .= '<a href="category.php?' . "target=$sortorder&amp;action=$hide_show$gtree->commonvars\">\n";
		$list .= '<img src="' . $CFG->pixpath . '/t/' . $hide_show . '.gif" class="iconsmall" alt="' .
		${ 'str' . $hide_show } . '" title="' . ${ 'str' . $hide_show } . '" /></a>' . "\n";
		// Print lock/unlock icon
		$list .= '<a href="category.php?' . "target=$sortorder&amp;action=$lock_unlock$gtree->commonvars\">\n";
		$list .= '<img src="' . $CFG->pixpath . '/t/' . $lock_unlock . '.gif" class="iconsmall" alt="' .
		${ 'str' . $lock_unlock } . '" title="' . ${ 'str' . $lock_unlock } . '" /></a>' . "\n";

		$list .= '</div> <!-- end icons div -->';

		if (!empty ($element['children'])) {
			$list .= get_edit_tree($gtree, $level +1, $element['children'], $source_sortorder, $action, $source_type);
		}

		$list .= '</li>' . "\n";

		$first = false;
		$count++;
		if ($count == count($elements)) {
			$last = true;
		}

		$last_sortorder = $sortorder;
	}

	// Add an insertion box if source_sortorder is given and a few other constraints are satisfied
	if ($source_sortorder && !empty ($action)) {
		$moving_item_near_subcat = $element_type == 'subcat' && $source_type == 'item' && $level > 1;
		$moving_cat_to_lower_level = ($level == 2 && $source_type == 'topcat') || ($level > 2 && $source_type == 'subcat');
		$moving_subcat_near_item_in_cat = $element_type == 'item' && $source_type == 'subcat' && $level > 1;
		$moving_element_near_itself = $sortorder == $source_sortorder;

		if (!$moving_item_near_subcat && !$moving_cat_to_lower_level && !$moving_subcat_near_item_in_cat && !$moving_element_near_itself) {
			$list .= '<li class="insertion">' . "\n";
			$list .= '<a href="category.php?' . "source=$source_sortorder&amp;$action=$last_sortorder$gtree->commonvars\">\n";
			$list .= '<img class="movetarget" src="' . $CFG->wwwroot . '/pix/movehere.gif" alt="' . $strmovehere . '" title="' . $strmovehere . '" />' . "\n";
			$list .= "</a>\n</li>";
		}
	}

	$list .= '</ul>' . "\n$closing_form_tags";

	return $list;
}

$param = new stdClass();

$param->courseid = optional_param('id', 0, PARAM_INT);
$param->moveup = optional_param('moveup', 0, PARAM_INT);
$param->movedown = optional_param('movedown', 0, PARAM_INT);
$param->source = optional_param('source', 0, PARAM_INT);
$param->action = optional_param('action', 0, PARAM_ALPHA);
$param->move = optional_param('move', 0, PARAM_INT);
$param->type = optional_param('type', 0, PARAM_ALPHA);
$param->target = optional_param('target', 0, PARAM_INT);
$param->confirm = optional_param('confirm', 0, PARAM_INT);
$param->items = optional_param('items', 0, PARAM_INT);
$param->categories = optional_param('categories', 0, PARAM_INT);
$param->element_type = optional_param('element_type', 0, PARAM_ALPHA);
$param->category_name = optional_param('category_name', 0, PARAM_ALPHA);
$courseid = $param->courseid;

/// Make sure they can even access this course

if (!$course = get_record('course', 'id', $courseid)) {
	print_error('nocourseid');
}

require_login($course->id);

$context = get_context_instance(CONTEXT_COURSE, $course->id);

$strgrades = get_string('grades');
$strgraderreport = get_string('graderreport', 'grades');
$strcategoriesedit = get_string('categoriesedit', 'grades');

$crumbs[] = array (
	'name' => $strgrades,
	'link' => $CFG->wwwroot . '/grade/index.php?id=' . $courseid,
	'type' => 'misc'
);
$crumbs[] = array (
	'name' => $strgraderreport,
	'link' => $CFG->wwwroot . '/grade/report.php?id=' . $courseid . '&amp;report=grader',
	'type' => 'misc'
);
$crumbs[] = array (
	'name' => $strcategoriesedit,
	'link' => '',
	'type' => 'misc'
);

$navigation = build_navigation($crumbs);

print_header_simple($strgrades . ': ' . $strgraderreport, ': ' . $strcategoriesedit, $navigation, '', '', true, '', navmenu($course));

$gtree = new grade_tree($param->courseid, false);
$select_source = false;

if (!empty ($param->action) && !empty ($param->source) && confirm_sesskey()) {
	$element = $gtree->locate_element($param->source);
	$element_name = $element['object']->get_name();

	$strselectdestination = get_string('selectdestination', 'grades', $element_name);
	$strmovingelement = get_string('movingelement', 'grades', $element_name);
	$strcancel = get_string('cancel');

	print_heading($strselectdestination);

	echo $strmovingelement . ' (<a href="category.php?cancelmove=true' . $gtree->commonvars . '">' . $strcancel . '</a>)' . "\n";
}
elseif (!empty ($param->source) && confirm_sesskey()) {
	if (!empty ($param->moveup)) {
		$gtree->move_element($param->source, $param->moveup);
	}
	elseif (!empty ($param->movedown)) {
		$gtree->move_element($param->source, $param->movedown, 'after');
	}
	elseif (!empty ($param->move)) {
		$gtree->move_element($param->source, $param->move, 'after');
	}

}
elseif (!empty ($param->target) && !empty ($param->action) && confirm_sesskey()) {
	$element = $gtree->locate_element($param->target);
	switch ($param->action) {
		case 'edit' :
			break;
		case 'delete' :
			if ($param->confirm == 1) { // Perform the deletion
				$gtree->remove_element($param->target);
				// Print result message

			} else { // Print confirmation dialog
				$strdeletecheckfull = get_string('deletecheck', '', $element['object']->get_name());
				$linkyes = "category.php?target=$param->target&amp;action=delete&amp;confirm=1$gtree->commonvars";
				$linkno = "category.php?$gtree->commonvars";
				notice_yesno($strdeletecheckfull, $linkyes, $linkno);
			}
			break;

		case 'hide' :
			// TODO Implement calendar for selection of a date to hide element until
			if (!$element['object']->set_hidden(1)) {
				debugging("Could not update the element's hidden state!");
			} else {
				$gtree = new grade_tree($param->courseid, false);
			}
			break;
		case 'show' :
			if (!$element['object']->set_hidden(0)) {
				debugging("Could not update the element's hidden state!");
			} else {
                $gtree = new grade_tree($param->courseid, false);
			}
			break;
		case 'lock' :
			// TODO Implement calendar for selection of a date to lock element after
			if (!$element['object']->set_locked(1)) {
				debugging("Could not update the element's locked state!");
			} else {
                $gtree = new grade_tree($param->courseid, false);
			}
			break;
		case 'unlock' :
			if (!$element['object']->set_locked(0)) {
				debugging("Could not update the element's locked state!");
			} else {
                $gtree = new grade_tree($param->courseid, false);
			}
			break;
		default :
			break;
	}
	unset ($param->target);
}
elseif (!empty ($param->element_type) && !empty ($param->action) && $param->action == 'create' && confirm_sesskey()) {
	if (empty ($param->category_name)) {
		notice(get_string('createcategoryerror', 'grades') . ': ' . get_string('nocategoryname', 'grades'));
	}
	elseif ($param->element_type == 'items') {

		if (!empty ($param->items)) {
			$category = new grade_category();
			$category->fullname = $param->category_name;
			$category->courseid = $gtree->courseid;
			$category->insert();

			$items = array ();

			foreach ($param->items as $sortorder => $useless_var) {
				$element = $gtree->locate_element($sortorder);
				$items[$element['object']->id] = $element['object'];
			}

			if ($category->set_as_parent($items) && $category->update()) {
				$gtree = new grade_tree($param->courseid, false);
			} else { // creation of category didn't work, print a message
				debugging("Could not create a parent category over the items you selected..");
			}

		} else { // No items selected. Can't create a category over them...
			notice(get_string('createcategoryerror', 'grades') . ': ' . get_string('noselecteditems', 'grades'));
		}
	}
	elseif ($param->element_type == 'categories') {
		if (!empty ($param->categories)) {
			$category = new grade_category();
			$category->fullname = $param->category_name;
			$category->courseid = $gtree->courseid;
			$category->insert();

			$categories = array ();
			foreach ($param->categories as $sortorder => $useless_var) {
				$element = $gtree->locate_element($sortorder);
				$categories[$element['object']->id] = $element['object'];
			}

			if ($category->set_as_parent($categories) && $category->update()) {
				$gtree = new grade_tree($param->courseid, false);
			} else { // creation of category didn't work, print a message
				debugging("Could not create a parent category over the categories you selected..");
			}

		} else { // No categories selected. Can't create a category over them...
			notice(get_string('createcategoryerror', 'grades') . ': ' . get_string('noselectedcategories', 'grades'));
		}

	} else { // The element_type wasn't set properly, throw up an error

	}
}

print_heading(get_string('categoriesedit', 'grades'));
// Add tabs
$currenttab = 'editcategory';
include ('tabs.php');
echo get_edit_tree($gtree, 1, null, $param->source, $param->action, $param->type);
print_footer($course);
?>
