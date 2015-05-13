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
 * A library of classes used by the grade edit pages
 *
 * @package   core_grades
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class grade_edit_tree {
    public $columns = array();

    /**
     * @var grade_tree $gtree   @see grade/lib.php
     */
    public $gtree;

    /**
     * @var grade_plugin_return @see grade/lib.php
     */
    public $gpr;

    /**
     * @var string              $moving The eid of the category or item being moved
     */
    public $moving;

    public $deepest_level;

    public $uses_weight = false;

    public $table;

    public $categories = array();

    /**
     * Show calculator icons next to manual grade items
     * @var bool $show_calculations
     */
    private $show_calculations;

    /**
     * Constructor
     */
    public function __construct($gtree, $moving=false, $gpr) {
        global $USER, $OUTPUT, $COURSE;

        $systemdefault = get_config('moodle', 'grade_report_showcalculations');
        $this->show_calculations = get_user_preferences('grade_report_showcalculations', $systemdefault);

        $this->gtree = $gtree;
        $this->moving = $moving;
        $this->gpr = $gpr;
        $this->deepest_level = $this->get_deepest_level($this->gtree->top_element);

        $this->columns = array(grade_edit_tree_column::factory('name', array('deepest_level' => $this->deepest_level)));

        if ($this->uses_weight) {
            $this->columns[] = grade_edit_tree_column::factory('weight', array('adv' => 'weight'));
        }

        $this->columns[] = grade_edit_tree_column::factory('range'); // This is not a setting... How do we deal with it?
        $this->columns[] = grade_edit_tree_column::factory('actions');

        if ($this->deepest_level > 1) {
            $this->columns[] = grade_edit_tree_column::factory('select');
        }

        $this->table = new html_table();
        $this->table->id = "grade_edit_tree_table";
        $this->table->attributes['class'] = 'generaltable simple setup-grades';
        if ($this->moving) {
            $this->table->attributes['class'] .= ' moving';
        }

        foreach ($this->columns as $column) {
            if (!($this->moving && $column->hide_when_moving)) {
                $this->table->head[] = $column->get_header_cell();
            }
        }

        $rowcount = 0;
        $this->table->data = $this->build_html_tree($this->gtree->top_element, true, array(), 0, $rowcount);
    }

    /**
     * Recursive function for building the table holding the grade categories and items,
     * with CSS indentation and styles.
     *
     * @param array   $element The current tree element being rendered
     * @param boolean $totals Whether or not to print category grade items (category totals)
     * @param array   $parents An array of parent categories for the current element (used for indentation and row classes)
     *
     * @return string HTML
     */
    public function build_html_tree($element, $totals, $parents, $level, &$row_count) {
        global $CFG, $COURSE, $PAGE, $OUTPUT;

        $object = $element['object'];
        $eid    = $element['eid'];
        $object->name = $this->gtree->get_element_header($element, true, true, true, true, true);
        $object->stripped_name = $this->gtree->get_element_header($element, false, false, false);

        $is_category_item = false;
        if ($element['type'] == 'categoryitem' || $element['type'] == 'courseitem') {
            $is_category_item = true;
        }

        $rowclasses = array();
        foreach ($parents as $parent_eid) {
            $rowclasses[] = $parent_eid;
        }

        $moveaction = '';
        $actionsmenu = new action_menu();
        $actionsmenu->initialise_js($PAGE);
        $actionsmenu->set_menu_trigger(get_string('edit'));
        $actionsmenu->set_owner_selector('grade-item-' . $eid);
        $actionsmenu->set_alignment(action_menu::TL, action_menu::BL);

        if (!$is_category_item && ($icon = $this->gtree->get_edit_icon($element, $this->gpr, true))) {
            $actionsmenu->add($icon);
        }
        // MDL-49281 if grade_item already has calculation, it should be editable even if global setting is off.
        $type = $element['type'];
        $iscalculated = ($type == 'item' or $type == 'courseitem' or $type == 'categoryitem') && $object->is_calculated();
        $icon = $this->gtree->get_calculation_icon($element, $this->gpr, true);
        if ($iscalculated || ($this->show_calculations && $icon)) {
            $actionsmenu->add($icon);
        }

        if ($element['type'] == 'item' or ($element['type'] == 'category' and $element['depth'] > 1)) {
            if ($this->element_deletable($element)) {
                $aurl = new moodle_url('index.php', array('id' => $COURSE->id, 'action' => 'delete', 'eid' => $eid, 'sesskey' => sesskey()));
                $icon = new action_menu_link_secondary($aurl, new pix_icon('t/delete', get_string('delete')), get_string('delete'));
                $actionsmenu->add($icon);
            }

            $aurl = new moodle_url('index.php', array('id' => $COURSE->id, 'action' => 'moveselect', 'eid' => $eid, 'sesskey' => sesskey()));
            $moveaction .= $OUTPUT->action_icon($aurl, new pix_icon('t/move', get_string('move')));
        }

        if ($icon = $this->gtree->get_hiding_icon($element, $this->gpr, true)) {
            $actionsmenu->add($icon);
        }

        if ($icon = $this->gtree->get_reset_icon($element, $this->gpr, true)) {
            $actionsmenu->add($icon);
        }

        $actions = $OUTPUT->render($actionsmenu);

        $returnrows = array();
        $root = false;

        $id = required_param('id', PARAM_INT);

        /// prepare move target if needed
        $last = '';

        /// print the list items now
        if ($this->moving == $eid) {
            // do not diplay children
            $cell = new html_table_cell();
            $cell->colspan = 12;
            $cell->attributes['class'] = $element['type'] . ' moving column-name level' .
                ($level + 1) . ' level' . ($level % 2 ? 'even' : 'odd');
            $cell->text = $object->name.' ('.get_string('move').')';
            return array(new html_table_row(array($cell)));
        }

        if ($element['type'] == 'category') {
            $level++;
            $this->categories[$object->id] = $object->stripped_name;
            $category = grade_category::fetch(array('id' => $object->id));
            $item = $category->get_grade_item();

            // Add aggregation coef input if not a course item and if parent category has correct aggregation type
            $dimmed = ($item->is_hidden()) ? 'dimmed_text' : '';

            // Before we print the category's row, we must find out how many rows will appear below it (for the filler cell's rowspan)
            $aggregation_position = grade_get_setting($COURSE->id, 'aggregationposition', $CFG->grade_aggregationposition);
            $category_total_data = null; // Used if aggregationposition is set to "last", so we can print it last

            $html_children = array();

            $row_count = 0;

            foreach($element['children'] as $child_el) {
                $moveto = null;

                if (empty($child_el['object']->itemtype)) {
                    $child_el['object']->itemtype = false;
                }

                if (($child_el['object']->itemtype == 'course' || $child_el['object']->itemtype == 'category') && !$totals) {
                    continue;
                }

                $child_eid = $child_el['eid'];
                $first = '';

                if ($child_el['object']->itemtype == 'course' || $child_el['object']->itemtype == 'category') {
                    $first = array('first' => 1);
                    $child_eid = $eid;
                }

                if ($this->moving && $this->moving != $child_eid) {

                    $strmove     = get_string('move');
                    $strmovehere = get_string('movehere');
                    $actions = $moveaction = ''; // no action icons when moving

                    $aurl = new moodle_url('index.php', array('id' => $COURSE->id, 'action' => 'move', 'eid' => $this->moving, 'moveafter' => $child_eid, 'sesskey' => sesskey()));
                    if ($first) {
                        $aurl->params($first);
                    }

                    $cell = new html_table_cell();
                    $cell->colspan = 12;
                    $cell->attributes['class'] = 'movehere level' . ($level + 1) . ' level' . ($level % 2 ? 'even' : 'odd');

                    $icon = new pix_icon('movehere', $strmovehere, null, array('class'=>'movetarget'));
                    $cell->text = $OUTPUT->action_icon($aurl, $icon);

                    $moveto = new html_table_row(array($cell));
                }

                $newparents = $parents;
                $newparents[] = $eid;

                $row_count++;
                $child_row_count = 0;

                // If moving, do not print course and category totals, but still print the moveto target box
                if ($this->moving && ($child_el['object']->itemtype == 'course' || $child_el['object']->itemtype == 'category')) {
                    $html_children[] = $moveto;
                } elseif ($child_el['object']->itemtype == 'course' || $child_el['object']->itemtype == 'category') {
                    // We don't build the item yet because we first need to know the deepest level of categories (for category/name colspans)
                    $category_total_item = $this->build_html_tree($child_el, $totals, $newparents, $level, $child_row_count);
                    if (!$aggregation_position) {
                        $html_children = array_merge($html_children, $category_total_item);
                    }
                } else {
                    $html_children = array_merge($html_children, $this->build_html_tree($child_el, $totals, $newparents, $level, $child_row_count));
                    if (!empty($moveto)) {
                        $html_children[] = $moveto;
                    }

                    if ($this->moving) {
                        $row_count++;
                    }
                }

                $row_count += $child_row_count;

                // If the child is a category, increment row_count by one more (for the extra coloured row)
                if ($child_el['type'] == 'category') {
                    $row_count++;
                }
            }

            // Print category total at the end if aggregation position is "last" (1)
            if (!empty($category_total_item) && $aggregation_position) {
                $html_children = array_merge($html_children, $category_total_item);
            }

            // Determine if we are at the root
            if (isset($element['object']->grade_item) && $element['object']->grade_item->is_course_item()) {
                $root = true;
            }

            $levelclass = "level$level level" . ($level % 2 ? 'odd' : 'even');

            $courseclass = '';
            if ($level == 1) {
                $courseclass = 'coursecategory';
            }

            $row = new html_table_row();
            $row->id = 'grade-item-' . $eid;
            $row->attributes['class'] = $courseclass . ' category ' . $dimmed;
            foreach ($rowclasses as $class) {
                $row->attributes['class'] .= ' ' . $class;
            }

            $headercell = new html_table_cell();
            $headercell->header = true;
            $headercell->scope = 'row';
            $headercell->attributes['title'] = $object->stripped_name;
            $headercell->attributes['class'] = 'cell column-rowspan rowspan ' . $levelclass;
            $headercell->rowspan = $row_count + 1;
            $row->cells[] = $headercell;

            foreach ($this->columns as $column) {
                if (!($this->moving && $column->hide_when_moving)) {
                    $row->cells[] = $column->get_category_cell($category, $levelclass, array('id' => $id,
                        'name' => $object->name, 'level' => $level, 'actions' => $actions,
                        'moveaction' => $moveaction, 'eid' => $eid));
                }
            }

            $returnrows[] = $row;

            $returnrows = array_merge($returnrows, $html_children);

            // Print a coloured row to show the end of the category across the table
            $endcell = new html_table_cell();
            $endcell->colspan = (19 - $level);
            $endcell->attributes['class'] = 'emptyrow colspan ' . $levelclass;

            $returnrows[] = new html_table_row(array($endcell));

        } else { // Dealing with a grade item

            $item = grade_item::fetch(array('id' => $object->id));
            $element['type'] = 'item';
            $element['object'] = $item;

            $categoryitemclass = '';
            if ($item->itemtype == 'category') {
                $categoryitemclass = 'categoryitem';
            }
            if ($item->itemtype == 'course') {
                $categoryitemclass = 'courseitem';
            }

            $dimmed = ($item->is_hidden()) ? "dimmed_text" : "";
            $gradeitemrow = new html_table_row();
            $gradeitemrow->id = 'grade-item-' . $eid;
            $gradeitemrow->attributes['class'] = $categoryitemclass . ' item ' . $dimmed;
            foreach ($rowclasses as $class) {
                $gradeitemrow->attributes['class'] .= ' ' . $class;
            }

            foreach ($this->columns as $column) {
                if (!($this->moving && $column->hide_when_moving)) {
                    $gradeitemrow->cells[] = $column->get_item_cell($item, array('id' => $id, 'name' => $object->name,
                        'level' => $level, 'actions' => $actions, 'element' => $element, 'eid' => $eid,
                        'moveaction' => $moveaction, 'itemtype' => $object->itemtype));
                }
            }

            $returnrows[] = $gradeitemrow;
        }

        return $returnrows;

    }

    /**
     * Given a grade_item object, returns a labelled input if an aggregation coefficient (weight or extra credit) applies to it.
     * @param grade_item $item
     * @return string HTML
     */
    static function get_weight_input($item) {
        global $OUTPUT;

        if (!is_object($item) || get_class($item) !== 'grade_item') {
            throw new Exception('grade_edit_tree::get_weight_input($item) was given a variable that is not of the required type (grade_item object)');
            return false;
        }

        if ($item->is_course_item()) {
            return '';
        }

        $parent_category = $item->get_parent_category();
        $parent_category->apply_forced_settings();
        $aggcoef = $item->get_coefstring();

        $itemname = $item->itemname;
        if ($item->is_category_item()) {
            // Remember, the parent category of a category item is the category itself.
            $itemname = $parent_category->get_name();
        }
        $str = '';

        if ($aggcoef == 'aggregationcoefweight' || $aggcoef == 'aggregationcoef' || $aggcoef == 'aggregationcoefextraweight') {
            return '<label class="accesshide" for="weight_'.$item->id.'">'.
                get_string('extracreditvalue', 'grades', $itemname).'</label>'.
                '<input type="text" size="6" id="weight_'.$item->id.'" name="weight_'.$item->id.'"
                value="'.grade_edit_tree::format_number($item->aggregationcoef).'" />';
        } else if ($aggcoef == 'aggregationcoefextraweightsum') {

            $checkboxname = 'weightoverride_' . $item->id;
            $checkboxlbl = html_writer::tag('label', get_string('overrideweightofa', 'grades', $itemname),
                array('for' => $checkboxname, 'class' => 'accesshide'));
            $checkbox = html_writer::empty_tag('input', array('name' => $checkboxname,
                'type' => 'hidden', 'value' => 0));
            $checkbox .= html_writer::empty_tag('input', array('name' => $checkboxname,
                'type' => 'checkbox', 'value' => 1, 'id' => $checkboxname, 'class' => 'weightoverride',
                'checked' => ($item->weightoverride ? 'checked' : null)));

            $name = 'weight_' . $item->id;
            $hiddenlabel = html_writer::tag(
                'label',
                get_string('weightofa', 'grades', $itemname),
                array(
                    'class' => 'accesshide',
                    'for' => $name
                )
            );

            $input = html_writer::empty_tag(
                'input',
                array(
                    'type' =>   'text',
                    'size' =>   6,
                    'id' =>     $name,
                    'name' =>   $name,
                    'value' =>  grade_edit_tree::format_number($item->aggregationcoef2 * 100.0),
                    'disabled' => ($item->weightoverride ? null : 'disabled')
                )
            );

            $str .= $checkboxlbl . $checkbox . $hiddenlabel . $input;
        }

        return $str;
    }

    //Trims trailing zeros
    //Used on the 'categories and items' page for grade items settings like aggregation co-efficient
    //Grader report has its own decimal place settings so they are handled elsewhere
    static function format_number($number) {
        $formatted = rtrim(format_float($number, 4),'0');
        if (substr($formatted, -1)==get_string('decsep', 'langconfig')) { //if last char is the decimal point
            $formatted .= '0';
        }
        return $formatted;
    }

    /**
     * Given an element of the grade tree, returns whether it is deletable or not (only manual grade items are deletable)
     *
     * @param array $element
     * @return bool
     */
    function element_deletable($element) {
        global $COURSE;

        if ($element['type'] != 'item') {
            return true;
        }

        $grade_item = $element['object'];

        if ($grade_item->itemtype != 'mod' or $grade_item->is_outcome_item() or $grade_item->gradetype == GRADE_TYPE_NONE) {
            return true;
        }

        $modinfo = get_fast_modinfo($COURSE);
        if (!isset($modinfo->instances[$grade_item->itemmodule][$grade_item->iteminstance])) {
            // module does not exist
            return true;
        }

        return false;
    }

    /**
     * Given the grade tree and an array of element ids (e.g. c15, i42), and expecting the 'moveafter' URL param,
     * moves the selected items to the requested location. Then redirects the user to the given $returnurl
     *
     * @param object $gtree The grade tree (a recursive representation of the grade categories and grade items)
     * @param array $eids
     * @param string $returnurl
     */
    function move_elements($eids, $returnurl) {
        $moveafter = required_param('moveafter', PARAM_INT);

        if (!is_array($eids)) {
            $eids = array($eids);
        }

        if(!$after_el = $this->gtree->locate_element("cg$moveafter")) {
            print_error('invalidelementid', '', $returnurl);
        }

        $after = $after_el['object'];
        $parent = $after;
        $sortorder = $after->get_sortorder();

        foreach ($eids as $eid) {
            if (!$element = $this->gtree->locate_element($eid)) {
                print_error('invalidelementid', '', $returnurl);
            }
            $object = $element['object'];

            $object->set_parent($parent->id);
            $object->move_after_sortorder($sortorder);
            $sortorder++;
        }

        redirect($returnurl, '', 0);
    }

    /**
     * Recurses through the entire grade tree to find and return the maximum depth of the tree.
     * This should be run only once from the root element (course category), and is used for the
     * indentation of the Name column's cells (colspan)
     *
     * @param array $element An array of values representing a grade tree's element (all grade items in this case)
     * @param int $level The level of the current recursion
     * @param int $deepest_level A value passed to each subsequent level of recursion and incremented if $level > $deepest_level
     * @return int Deepest level
     */
    function get_deepest_level($element, $level=0, $deepest_level=1) {
        $object = $element['object'];

        $level++;
        $coefstring = $element['object']->get_coefstring();
        if ($element['type'] == 'category') {
            if ($coefstring == 'aggregationcoefweight' || $coefstring == 'aggregationcoefextraweightsum' ||
                    $coefstring == 'aggregationcoefextraweight') {
                $this->uses_weight = true;
            }

            foreach($element['children'] as $child_el) {
                if ($level > $deepest_level) {
                    $deepest_level = $level;
                }
                $deepest_level = $this->get_deepest_level($child_el, $level, $deepest_level);
            }
        }

        return $deepest_level;
    }
}

/**
 * Class grade_edit_tree_column
 *
 * @package   core_grades
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class grade_edit_tree_column {
    public $forced;
    public $hidden;
    public $forced_hidden;
    public $advanced_hidden;
    public $hide_when_moving = true;
    /**
     * html_table_cell object used as a template for header cells in all categories.
     * It must be cloned before being used.
     * @var html_table_cell $headercell
     */
    public $headercell;
    /**
     * html_table_cell object used as a template for category cells in all categories.
     * It must be cloned before being used.
     * @var html_table_cell $categorycell
     */
    public $categorycell;
    /**
     * html_table_cell object used as a template for item cells in all categories.
     * It must be cloned before being used.
     * @var html_table_cell $itemcell
     */
    public $itemcell;

    public static function factory($name, $params=array()) {
        $class_name = "grade_edit_tree_column_$name";
        if (class_exists($class_name)) {
            return new $class_name($params);
        }
    }

    public abstract function get_header_cell();

    public function get_category_cell($category, $levelclass, $params) {
        $cell = clone($this->categorycell);
        $cell->attributes['class'] .= ' ' . $levelclass;
        $cell->attributes['text'] = '';
        return $cell;
    }

    public function get_item_cell($item, $params) {
        $cell = clone($this->itemcell);
        $cell->attributes['text'] = '';
        if (isset($params['level'])) {
            $level = $params['level'] + (($item->itemtype == 'category' || $item->itemtype == 'course') ? 0 : 1);
            $cell->attributes['class'] .= ' level' . $level;
            $cell->attributes['class'] .= ' level' . ($level % 2 ? 'odd' : 'even');
        }
        return $cell;
    }

    public function __construct() {
        $this->headercell = new html_table_cell();
        $this->headercell->header = true;
        $this->headercell->attributes['class'] = 'header';

        $this->categorycell = new html_table_cell();
        $this->categorycell->attributes['class']  = 'cell';

        $this->itemcell = new html_table_cell();
        $this->itemcell->attributes['class'] = 'cell';

        if (preg_match('/^grade_edit_tree_column_(\w*)$/', get_class($this), $matches)) {
            $this->headercell->attributes['class'] .= ' column-' . $matches[1];
            $this->categorycell->attributes['class'] .= ' column-' . $matches[1];
            $this->itemcell->attributes['class'] .= ' column-' . $matches[1];
        }
    }
}

/**
 * Class grade_edit_tree_column_name
 *
 * @package   core_grades
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade_edit_tree_column_name extends grade_edit_tree_column {
    public $forced = false;
    public $hidden = false;
    public $forced_hidden = false;
    public $advanced_hidden = false;
    public $deepest_level = 1;
    public $hide_when_moving = false;

    public function __construct($params) {
        if (empty($params['deepest_level'])) {
            throw new Exception('Tried to instantiate a grade_edit_tree_column_name object without the "deepest_level" param!');
        }

        $this->deepest_level = $params['deepest_level'];
        parent::__construct();
    }

    public function get_header_cell() {
        $headercell = clone($this->headercell);
        $headercell->colspan = $this->deepest_level + 1;
        $headercell->text = get_string('name');
        return $headercell;
    }

    public function get_category_cell($category, $levelclass, $params) {
        global $OUTPUT;
        if (empty($params['name']) || empty($params['level'])) {
            throw new Exception('Array key (name or level) missing from 3rd param of grade_edit_tree_column_name::get_category_cell($category, $levelclass, $params)');
        }
        $moveaction = isset($params['moveaction']) ? $params['moveaction'] : '';
        $categorycell = parent::get_category_cell($category, $levelclass, $params);
        $categorycell->colspan = ($this->deepest_level +1) - $params['level'];
        $categorycell->text = $OUTPUT->heading($moveaction . $params['name'], 4);
        return $categorycell;
    }

    public function get_item_cell($item, $params) {
        global $CFG;

        if (empty($params['element']) || empty($params['name']) || empty($params['level'])) {
            throw new Exception('Array key (name, level or element) missing from 2nd param of grade_edit_tree_column_name::get_item_cell($item, $params)');
        }

        $name = $params['name'];
        $moveaction = isset($params['moveaction']) ? $params['moveaction'] : '';

        $itemcell = parent::get_item_cell($item, $params);
        $itemcell->colspan = ($this->deepest_level + 1) - $params['level'];
        $itemcell->text = $moveaction . $name;
        return $itemcell;
    }
}

/**
 * Class grade_edit_tree_column_weight
 *
 * @package   core_grades
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade_edit_tree_column_weight extends grade_edit_tree_column {

    public function get_header_cell() {
        global $OUTPUT;
        $headercell = clone($this->headercell);
        $headercell->text = get_string('weights', 'grades').$OUTPUT->help_icon('aggregationcoefweight', 'grades');
        return $headercell;
    }

    public function get_category_cell($category, $levelclass, $params) {

        $item = $category->get_grade_item();
        $categorycell = parent::get_category_cell($category, $levelclass, $params);
        $categorycell->text = grade_edit_tree::get_weight_input($item);
        return $categorycell;
    }

    public function get_item_cell($item, $params) {
        global $CFG;
        if (empty($params['element'])) {
            throw new Exception('Array key (element) missing from 2nd param of grade_edit_tree_column_weightorextracredit::get_item_cell($item, $params)');
        }
        $itemcell = parent::get_item_cell($item, $params);
        $itemcell->text = '&nbsp;';
        $object = $params['element']['object'];

        if (!in_array($object->itemtype, array('courseitem', 'categoryitem', 'category'))
                && !in_array($object->gradetype, array(GRADE_TYPE_NONE, GRADE_TYPE_TEXT))
                && (!$object->is_outcome_item() || $object->load_parent_category()->aggregateoutcomes)
                && ($object->gradetype != GRADE_TYPE_SCALE || !empty($CFG->grade_includescalesinaggregation))) {
            $itemcell->text = grade_edit_tree::get_weight_input($item);
        }

        return $itemcell;
    }
}

/**
 * Class grade_edit_tree_column_range
 *
 * @package   core_grades
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade_edit_tree_column_range extends grade_edit_tree_column {

    public function get_header_cell() {
        $headercell = clone($this->headercell);
        $headercell->text = get_string('maxgrade', 'grades');
        return $headercell;
    }

    public function get_category_cell($category, $levelclass, $params) {
        $categorycell = parent::get_category_cell($category, $levelclass, $params);
        $categorycell->text = ' - ';
        return $categorycell;
    }

    public function get_item_cell($item, $params) {
        global $DB, $OUTPUT;

        // If the parent aggregation is Natural, we should show the number, even for scales, as that value is used...
        // ...in the computation. For text grades, the grademax is not used, so we can still show the no value string.
        $parentcat = $item->get_parent_category();
        if ($item->gradetype == GRADE_TYPE_TEXT) {
            $grademax = ' - ';
        } else if ($item->gradetype == GRADE_TYPE_SCALE) {
            $scale = $DB->get_record('scale', array('id' => $item->scaleid));
            $scale_items = null;
            if (empty($scale)) { //if the item is using a scale that's been removed
                $scale_items = array();
            } else {
                $scale_items = explode(',', $scale->scale);
            }
            if ($parentcat->aggregation == GRADE_AGGREGATE_SUM) {
                $grademax = end($scale_items) . ' (' .
                        format_float($item->grademax, $item->get_decimals()) . ')';
            } else {
                $grademax = end($scale_items) . ' (' . count($scale_items) . ')';
            }
        } else {
            $grademax = format_float($item->grademax, $item->get_decimals());
        }

        $isextracredit = false;
        if ($item->aggregationcoef > 0) {
            // For category grade items, we need the grandparent category.
            // The parent is just category the grade item represents.
            if ($item->is_category_item()) {
                $grandparentcat = $parentcat->get_parent_category();
                if ($grandparentcat->is_extracredit_used()) {
                    $isextracredit = true;
                }
            } else if ($parentcat->is_extracredit_used()) {
                $isextracredit = true;
            }
        }
        if ($isextracredit) {
            $grademax .= ' ' . html_writer::tag('abbr', get_string('aggregationcoefextrasumabbr', 'grades'),
                array('title' => get_string('aggregationcoefextrasum', 'grades')));
        }

        $itemcell = parent::get_item_cell($item, $params);
        $itemcell->text = $grademax;
        return $itemcell;
    }
}

/**
 * Class grade_edit_tree_column_actions
 *
 * @package   core_grades
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade_edit_tree_column_actions extends grade_edit_tree_column {

    public function __construct($params) {
        parent::__construct();
    }

    public function get_header_cell() {
        $headercell = clone($this->headercell);
        $headercell->text = get_string('actions');
        return $headercell;
    }

    public function get_category_cell($category, $levelclass, $params) {

        if (empty($params['actions'])) {
            throw new Exception('Array key (actions) missing from 3rd param of grade_edit_tree_column_actions::get_category_actions($category, $levelclass, $params)');
        }

        $categorycell = parent::get_category_cell($category, $levelclass, $params);
        $categorycell->text = $params['actions'];
        return $categorycell;
    }

    public function get_item_cell($item, $params) {
        if (empty($params['actions'])) {
            throw new Exception('Array key (actions) missing from 2nd param of grade_edit_tree_column_actions::get_item_cell($item, $params)');
        }
        $itemcell = parent::get_item_cell($item, $params);
        $itemcell->text = $params['actions'];
        return $itemcell;
    }
}

/**
 * Class grade_edit_tree_column_select
 *
 * @package   core_grades
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade_edit_tree_column_select extends grade_edit_tree_column {

    public function get_header_cell() {
        $headercell = clone($this->headercell);
        $headercell->text = get_string('select');
        return $headercell;
    }

    public function get_category_cell($category, $levelclass, $params) {
        global $OUTPUT;
        if (empty($params['eid'])) {
            throw new Exception('Array key (eid) missing from 3rd param of grade_edit_tree_column_select::get_category_cell($category, $levelclass, $params)');
        }
        $selectall  = new action_link(new moodle_url('#'), get_string('all'), new component_action('click', 'togglecheckboxes', array('eid' => $params['eid'], 'check' => true)));
        $selectnone = new action_link(new moodle_url('#'), get_string('none'), new component_action('click', 'togglecheckboxes', array('eid' => $params['eid'], 'check' => false)));

        $categorycell = parent::get_category_cell($category, $levelclass, $params);
        $categorycell->text = $OUTPUT->render($selectall) . ' / ' . $OUTPUT->render($selectnone);
        return $categorycell;
    }

    public function get_item_cell($item, $params) {
        if (empty($params['itemtype']) || empty($params['eid'])) {
            error('Array key (itemtype or eid) missing from 2nd param of grade_edit_tree_column_select::get_item_cell($item, $params)');
        }
        $itemcell = parent::get_item_cell($item, $params);

        if ($params['itemtype'] != 'course' && $params['itemtype'] != 'category') {
            $itemcell->text = '<label class="accesshide" for="select_'.$params['eid'].'">'.
                get_string('select', 'grades', $item->itemname).'</label>
                <input class="itemselect ignoredirty" type="checkbox" name="select_'.$params['eid'].'" id="select_'.$params['eid'].
                '" onchange="toggleCategorySelector();"/>'; // TODO: convert to YUI handler
        }
        return $itemcell;
    }
}

