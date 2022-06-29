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
 * Edit a calculated grade item
 *
 * @package   core_grades
 * @copyright 2007 Petr Skoda
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once '../../../config.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->libdir.'/mathslib.php';
require_once 'calculation_form.php';

$courseid  = required_param('courseid', PARAM_INT);
$id        = required_param('id', PARAM_INT);
$section   = optional_param('section', 'calculation', PARAM_ALPHA);
$idnumbers = optional_param_array('idnumbers', null, PARAM_RAW);

$url = new moodle_url('/grade/edit/tree/calculation.php', array('id'=>$id, 'courseid'=>$courseid));
if ($section !== 'calculation') {
    $url->param('section', $section);
}
$PAGE->set_url($url);

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourseid');
}

require_login($course);
$context = context_course::instance($course->id);
require_capability('moodle/grade:manage', $context);

$PAGE->set_pagelayout('admin');
navigation_node::override_active_url(new moodle_url('/grade/edit/tree/index.php',
    array('id'=>$course->id)));

// default return url
$gpr = new grade_plugin_return();
$returnurl = $gpr->get_return_url($CFG->wwwroot.'/grade/report/index.php?id='.$course->id);

if (!$grade_item = grade_item::fetch(array('id'=>$id, 'courseid'=>$course->id))) {
    print_error('invaliditemid');
}

// activity items and items without grade can not have calculation
if ($grade_item->is_external_item() or ($grade_item->gradetype != GRADE_TYPE_VALUE and $grade_item->gradetype != GRADE_TYPE_SCALE)) {
    redirect($returnurl, get_string('errornocalculationallowed', 'grades'));
}

$mform = new edit_calculation_form(null, array('gpr'=>$gpr, 'itemid' => $grade_item->id));

if ($mform->is_cancelled()) {
    redirect($returnurl);

}

$calculation = calc_formula::localize($grade_item->calculation);
$calculation = grade_item::denormalize_formula($calculation, $grade_item->courseid);
$mform->set_data(array('courseid'=>$grade_item->courseid, 'calculation'=>$calculation, 'id'=>$grade_item->id, 'itemname'=>$grade_item->itemname));

$errors = array();

if ($data = $mform->get_data()) {
    $calculation = calc_formula::unlocalize($data->calculation);
    $grade_item->set_calculation($calculation);

    redirect($returnurl);

} elseif (!empty($section) AND $section='idnumbers' AND !empty($idnumbers)) { // Handle idnumbers separately (non-mform)
    //first validate and store the new idnumbers
    foreach ($idnumbers as $giid => $value) {
        if ($gi = grade_item::fetch(array('id' => $giid))) {
            if ($gi->itemtype == 'mod') {
                $cm = get_coursemodule_from_instance($gi->itemmodule, $gi->iteminstance, $gi->courseid);
            } else {
                $cm = null;
            }

            if (!grade_verify_idnumber($value, $COURSE->id, $gi, $cm)) {
                $errors[$giid] = get_string('idnumbertaken');
                continue;
            }

            if (empty($gi->idnumber) and !$gi->add_idnumber($idnumbers[$gi->id])) {
                $errors[$giid] = get_string('error');
                continue;
            }
        } else {
            $errors[$giid] = 'Could not fetch the grade_item with id=' . $giid;
        }
    }
}

$gtree = new grade_tree($course->id, false, false);

$strgrades          = get_string('grades');
$strgraderreport    = get_string('graderreport', 'grades');
$strcalculationedit = get_string('editcalculation', 'grades');

$PAGE->navbar->add($strcalculationedit);
print_grade_page_head($courseid, 'settings', null, $strcalculationedit, false, false, false);

$mform->display();
// Now show the gradetree with the idnumbers add/edit form
echo '
<form class="mform" id="mform2" method="post" action="' . $CFG->wwwroot . '/grade/edit/tree/calculation.php?courseid='.$courseid.'&amp;id='.$id.'">
    <div style="display: none;">
        <input type="hidden" value="'.$id.'" name="id"/>
        <input type="hidden" value="'.$courseid.'" name="courseid"/>
        <input type="hidden" value="'.$gpr->type.'" name="gpr_type"/>
        <input type="hidden" value="'.$gpr->plugin.'" name="gpr_plugin"/>
        <input type="hidden" value="'.$gpr->courseid.'" name="gpr_courseid"/>
        <input type="hidden" value="'.sesskey().'" name="sesskey"/>
        <input type="hidden" value="idnumbers" name="section"/>
    </div>

    <fieldset id="idnumbers" class="clearfix">
        <legend class="ftoggler">'.get_string('idnumbers', 'grades').'</legend>
        <div class="fcontainer clearfix">
            <ul>
            ' . get_grade_tree($gtree, $gtree->top_element, $id, $errors) . '
            </ul>
        </div>
    </fieldset>
    <div class="fitem" style="text-align: center;">
        <input id="id_addidnumbers" type="submit" class="btn btn-secondary" value="' . get_string('addidnumbers', 'grades') . '" name="addidnumbers" />
    </div>
</form>';

echo $OUTPUT->footer();
die();


/**
 * Simplified version of the print_grade_tree() recursive function found in grade/edit/tree/index.php
 * Only prints a tree with a basic icon for each element, and an edit field for
 * items without an idnumber.
 * @param object $gtree
 * @param object $element
 * @param int $current_itemid The itemid of this page: should be excluded from the tree
 * @param array $errors An array of idnumbers => error
 * @return string
 */
function get_grade_tree(&$gtree, $element, $current_itemid=null, $errors=null) {
    global $CFG;

    $object     = $element['object'];
    $eid        = $element['eid'];
    $type       = $element['type'];
    $grade_item = $object->get_grade_item();

    $name = $object->get_name();
    $return_string = '';

    //TODO: improve outcome visualisation
    if ($type == 'item' and !empty($object->outcomeid)) {
        $name = $name.' ('.get_string('outcome', 'grades').')';
    }

    $idnumber = $object->get_idnumber();

    // Don't show idnumber or input field for current item if given to function. Highlight the item instead.
    if ($type != 'category') {
        if (is_null($current_itemid) OR $grade_item->id != $current_itemid) {
            if ($idnumber) {
                $name .= ": [[$idnumber]]";
            } else {
                $closingdiv = '';
                if (!empty($errors[$grade_item->id])) {
                    $name .= '<div class="error"><span class="error">' . $errors[$grade_item->id].'</span><br />'."\n";
                    $closingdiv = "</div>\n";
                }
                $name .= '<label class="accesshide" for="id_idnumber_' . $grade_item->id . '">' . get_string('gradeitems', 'grades')  .'</label>';
                $name .= '<input class="idnumber" id="id_idnumber_'.$grade_item->id.'" type="text" name="idnumbers['.$grade_item->id.']" />' . "\n";
                $name .= $closingdiv;
            }
        } else {
            $name = "<strong>$name</strong>";
        }
    }

    $icon = $gtree->get_element_icon($element, true);
    $last = '';
    $catcourseitem = ($element['type'] == 'courseitem' or $element['type'] == 'categoryitem');

    if ($type != 'category') {
        $return_string .= '<li class="'.$type.'">'.$icon.$name.'</li>' . "\n";
    } else {
        $return_string .= '<li class="'.$type.'">'.$icon.$name . "\n";
        $return_string .= '<ul class="catlevel'.$element['depth'].'">'."\n";
        $last = null;
        foreach($element['children'] as $child_el) {
            $return_string .= get_grade_tree($gtree, $child_el, $current_itemid, $errors);
        }
        if ($last) {
            $return_string .= get_grade_tree($gtree, $last, $current_itemid, $errors);
        }
        $return_string .= '</ul></li>'."\n";
    }

    return $return_string;
}


