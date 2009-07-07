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

require_once '../../../config.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->dirroot.'/grade/report/lib.php'; // for preferences
require_once $CFG->dirroot.'/grade/edit/tree/lib.php';

require_js(array('yui_yahoo', 'yui_dom', 'yui_event', 'yui_json', 'yui_connection', 'yui_dragdrop', 'yui_treeview', 'yui_element',
            $CFG->wwwroot.'/grade/edit/tree/functions.js'));

$courseid        = required_param('id', PARAM_INT);
$action          = optional_param('action', 0, PARAM_ALPHA);
$eid             = optional_param('eid', 0, PARAM_ALPHANUM);
$category        = optional_param('category', null, PARAM_INT);
$aggregationtype = optional_param('aggregationtype', null, PARAM_INT);
$showadvanced    = optional_param('showadvanced', -1, PARAM_BOOL); // sticky editting mode

/// Make sure they can even access this course
if (!$course = get_record('course', 'id', $courseid)) {
    print_error('nocourseid');
}

require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $course->id);
require_capability('moodle/grade:manage', $context);

/// return tracking object
$gpr = new grade_plugin_return(array('type'=>'edit', 'plugin'=>'tree', 'courseid'=>$courseid));
$returnurl = $gpr->get_return_url(null);

/// Build editing on/off buttons
if (!isset($USER->gradeediting)) {
    $USER->gradeediting = array();
}

$current_view = '';

if (has_capability('moodle/grade:manage', $context)) {
    if (!isset($USER->gradeediting[$course->id])) {
        $USER->gradeediting[$course->id] = 0;
    }

    if ($showadvanced == 1) {
        $USER->gradeediting[$course->id] = 1;
    } else if ($showadvanced == 0) {
        $USER->gradeediting[$course->id] = 0;
    }

    // page params for the turn editting on
    $options = $gpr->get_options();
    $options['sesskey'] = sesskey();

    if ($USER->gradeediting[$course->id]) {
        $options['showadvanced'] = 0;
        $current_view = 'fullview';
    } else {
        $options['showadvanced'] = 1;
        $current_view = 'simpleview';
    }

} else {
    $USER->gradeediting[$course->id] = 0;
    $buttons = '';
}

// Change category aggregation if requested
if (!is_null($category) && !is_null($aggregationtype) && confirm_sesskey()) {
    if (!$grade_category = grade_category::fetch(array('id'=>$category, 'courseid'=>$courseid))) {
        error('Incorrect category id!');
    }
    $data->aggregation = $aggregationtype;
    grade_category::set_properties($grade_category, $data);
    $grade_category->update();
    grade_regrade_final_grades($courseid);
}

//first make sure we have proper final grades - we need it for locking changes
grade_regrade_final_grades($courseid);

// get the grading tree object
// note: total must be first for moving to work correctly, if you want it last moving code must be rewritten!
$gtree = new grade_tree($courseid, false, false);

if (empty($eid)) {
    $element = null;
    $object  = null;

} else {
    if (!$element = $gtree->locate_element($eid)) {
        print_error('invalidelementid', '', $returnurl);
    }
    $object = $element['object'];
}

$switch = grade_get_setting($course->id, 'aggregationposition', $CFG->grade_aggregationposition);

$strgrades             = get_string('grades');
$strgraderreport       = get_string('graderreport', 'grades');
$strcategoriesedit     = get_string('categoriesedit', 'grades');
$strcategoriesanditems = get_string('categoriesanditems', 'grades');

$navigation = grade_build_nav(__FILE__, $strcategoriesanditems, array('courseid' => $courseid));
$moving = false;

$grade_edit_tree = new grade_edit_tree($gtree, $moving, $gpr);

switch ($action) {
    case 'delete':
        if ($eid && confirm_sesskey()) {
            if (!$grade_edit_tree->element_deletable($element)) {
                // no deleting of external activities - they would be recreated anyway!
                // exception is activity without grading or misconfigured activities
                break;
            }
            $confirm = optional_param('confirm', 0, PARAM_BOOL);

            if ($confirm) {
                $object->delete('grade/report/grader/category');
                redirect($returnurl);

            } else {
                print_header_simple($strgrades . ': ' . $strgraderreport, ': ' . $strcategoriesedit, $navigation, '', '', true, null, navmenu($course));
                $strdeletecheckfull = get_string('deletecheck', '', $object->get_name());
                $optionsyes = array('eid'=>$eid, 'confirm'=>1, 'sesskey'=>sesskey(), 'id'=>$course->id, 'action'=>'delete');
                $optionsno  = array('id'=>$course->id);
                notice_yesno($strdeletecheckfull, 'index.php', 'index.php', $optionsyes, $optionsno, 'post', 'get');
                print_footer($course);
                die;
            }
        }
        break;

    case 'autosort':
        //TODO: implement autosorting based on order of mods on course page, categories first, manual items last
        break;

    case 'synclegacy':
        grade_grab_legacy_grades($course->id);
        redirect($returnurl);

    case 'move':
        if ($eid and confirm_sesskey()) {
            $moveafter = required_param('moveafter', PARAM_ALPHANUM);
            $first = optional_param('first', false,  PARAM_BOOL); // If First is set to 1, it means the target is the first child of the category $moveafter

            if(!$after_el = $gtree->locate_element($moveafter)) {
                print_error('invalidelementid', '', $returnurl);
            }

            $after = $after_el['object'];
            $sortorder = $after->get_sortorder();

            if (!$first) {
                $parent = $after->get_parent_category();
                $object->set_parent($parent->id);
            } else {
                $object->set_parent($after->id);
            }

            $object->move_after_sortorder($sortorder);

            redirect($returnurl);
        }
        break;

    case 'moveselect':
        if ($eid and confirm_sesskey()) {
            $grade_edit_tree->moving = $eid;
            $moving=true;
        }
        break;

    default:
        break;
}

// Hide advanced columns if moving
if ($grade_edit_tree->moving) {
    $original_gradeediting = $USER->gradeediting[$course->id];
    $USER->gradeediting[$course->id] = 0;
}

$CFG->stylesheets[] = $CFG->wwwroot.'/grade/edit/tree/tree.css';

$current_view_str = '';
if ($current_view != '') {
    if ($current_view == 'simpleview') {
        $current_view_str = get_string('simpleview', 'grades');
    } elseif ($current_view == 'fullview') {
        $current_view_str = get_string('fullview', 'grades');
    }
}

print_grade_page_head($courseid, 'edittree', $current_view, get_string('categoriesedit', 'grades') . ': ' . $current_view_str);

if ($data = data_submitted() and confirm_sesskey()) {
    // Perform bulk actions first
    if (!empty($data->bulkmove)) {
        $elements = array();

        foreach ($data as $key => $value) {
            if (preg_match('/select_(i[0-9]*)/', $key, $matches)) {
                $elements[] = $matches[1];
            }
        }

        $grade_edit_tree->move_elements($elements, $returnurl);
    }

    // Category and item field updates
    foreach ($data as $key => $value) {
        // Grade category text inputs
        if (preg_match('/^(aggregation|droplow|keephigh)_([0-9]+)$/', $key, $matches)) {
            $param = $matches[1];
            $aid   = $matches[2];

            // Do not allow negative values
            $value = clean_param($value, PARAM_INT);
            $value = ($value < 0) ? 0 : $value;

            $grade_category = grade_category::fetch(array('id'=>$aid, 'courseid'=>$courseid));
            $grade_category->$param = $value;

            $grade_category->update();
            grade_regrade_final_grades($courseid);

        // Grade item text inputs
        } elseif (preg_match('/^(grademax|aggregationcoef|multfactor|plusfactor)_([0-9]+)$/', $key, $matches)) {
            $param = $matches[1];
            $aid   = $matches[2];

            $value = unformat_float($value);
            $value = clean_param($value, PARAM_NUMBER);

            $grade_item = grade_item::fetch(array('id'=>$aid, 'courseid'=>$courseid));

            if ($param === 'grademax' and $value < $grade_item->grademin) {
                // better not allow values lower than grade min
                $value = $grade_item->grademin;
            }
            $grade_item->$param = $value;

            $grade_item->update();
            grade_regrade_final_grades($courseid);

        // Grade item checkbox inputs
        } elseif (preg_match('/^extracredit_([0-9]+)$/', $key, $matches)) { // Sum extra credit checkbox
            $aid   = $matches[1];
            $value = clean_param($value, PARAM_BOOL);

            $grade_item = grade_item::fetch(array('id'=>$aid, 'courseid'=>$courseid));
            $grade_item->aggregationcoef = $value;

            $grade_item->update();
            grade_regrade_final_grades($courseid);

        // Grade category checkbox inputs
        } elseif (preg_match('/^aggregate(onlygraded|subcats|outcomes)_([0-9]+)$/', $key, $matches)) {
            $param = 'aggregate'.$matches[1];
            $aid    = $matches[2];
            $value = clean_param($value, PARAM_BOOL);

            $grade_category = grade_category::fetch(array('id'=>$aid, 'courseid'=>$courseid));
            $grade_category->$param = $value;

            $grade_category->update();
            grade_regrade_final_grades($courseid);
        }
    }
}

// Print Table of categories and items
print_box_start('gradetreebox generalbox');

echo '<form id="gradetreeform" method="post" action="'.$returnurl.'">';
echo '<div>';
echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';

// Build up an array of categories for move drop-down (by reference)
$categories = array();
$level     = 0;
$row_count = 0;
echo $grade_edit_tree->build_html_tree($gtree->top_element, true, array(), $categories, $level, $row_count);

echo '<div id="gradetreesubmit">';
if (!$moving) {
    echo '<input class="advanced" type="submit" value="'.get_string('savechanges').'" />';
}

// We don't print a bulk move menu if there are no other categories than course category
if (!$moving && count($categories) > 1) {
    echo '<br /><br />';
    echo '<input type="hidden" name="bulkmove" value="0" id="bulkmoveinput" />';
    echo get_string('moveselectedto', 'grades') . ' ';
    echo choose_from_menu($categories, 'moveafter', '', 'choose',
            'document.getElementById(\'bulkmoveinput\').value=1;document.getElementById(\'gradetreeform\').submit()', 0, true, true);
    echo '<div id="noscriptgradetreeform" style="display: inline;">
            <input type="submit" value="'.get_string('go').'" />
          </div>
          <script type="text/javascript">
            //<![CDATA[
                document.getElementById("noscriptgradetreeform").style.display= "none";
            //]]>
          </script>';
}

echo '</div>';

echo '</div></form>';

print_box_end();

// Print action buttons
echo '<div class="buttons">';

if ($moving) {
    print_single_button('index.php', array('id'=>$course->id), get_string('cancel'), 'get');
} else {
    print_single_button('category.php', array('courseid'=>$course->id), get_string('addcategory', 'grades'), 'get');
    print_single_button('item.php', array('courseid'=>$course->id), get_string('additem', 'grades'), 'get');

    if (!empty($CFG->enableoutcomes)) {
        print_single_button('outcomeitem.php', array('courseid'=>$course->id), get_string('addoutcomeitem', 'grades'), 'get');
    }

    if ($legacy = grade_get_legacy_modules($course->id)) {
        //print_single_button('index.php', array('id'=>$course->id, 'action'=>'autosort'), get_string('autosort', 'grades'), 'get');
        echo "<br /><br />";
        print_single_button('index.php', array('id'=>$course->id, 'action'=>'synclegacy'), get_string('synclegacygrades', 'grades').' ('.implode(', ', $legacy).')', 'get');
        helpbutton('synclegacygrades', get_string('synclegacygrades', 'grades'), 'grade');
    }
}

echo '</div>';

print_footer($course);

// Restore original show/hide preference if moving
if ($moving) {
    $USER->gradeediting[$course->id] = $original_gradeediting;
}
die;

?>
