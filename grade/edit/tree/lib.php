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

    public $uses_extra_credit = false;

    public $uses_weight = false;

    public $table;

    public $categories = array();
    /**
     * Constructor
     */
    public function __construct($gtree, $moving=false, $gpr) {
        global $USER, $OUTPUT, $COURSE;

        $this->gtree = $gtree;
        $this->moving = $moving;
        $this->gpr = $gpr;
        $this->deepest_level = $this->get_deepest_level($this->gtree->top_element);

        $this->columns = array(grade_edit_tree_column::factory('name', array('deepest_level' => $this->deepest_level)),
                               grade_edit_tree_column::factory('aggregation', array('flag' => true)));

        if ($this->uses_weight) {
            $this->columns[] = grade_edit_tree_column::factory('weight', array('adv' => 'aggregationcoef'));
        }
        if ($this->uses_extra_credit) {
            $this->columns[] = grade_edit_tree_column::factory('extracredit', array('adv' => 'aggregationcoef'));
        }

        $this->columns[] = grade_edit_tree_column::factory('range'); // This is not a setting... How do we deal with it?
        $this->columns[] = grade_edit_tree_column::factory('aggregateonlygraded', array('flag' => true));
        $this->columns[] = grade_edit_tree_column::factory('aggregatesubcats', array('flag' => true));
        $this->columns[] = grade_edit_tree_column::factory('aggregateoutcomes', array('flag' => true));
        $this->columns[] = grade_edit_tree_column::factory('droplow', array('flag' => true));
        $this->columns[] = grade_edit_tree_column::factory('keephigh', array('flag' => true));
        $this->columns[] = grade_edit_tree_column::factory('multfactor', array('adv' => true));
        $this->columns[] = grade_edit_tree_column::factory('plusfactor', array('adv' => true));
        $this->columns[] = grade_edit_tree_column::factory('actions');
        $this->columns[] = grade_edit_tree_column::factory('select');

        $mode = ($USER->gradeediting[$COURSE->id]) ? 'advanced' : 'simple';

        $widthstyle = '';
        if ($mode == 'simple') {
            $widthstyle = ' style="width:auto;" ';
        }

        $this->table = new html_table();
        $this->table->id = "grade_edit_tree_table";
        $this->table->cellpadding = 5;
        $this->table->attributes['class'] = 'generaltable ' . $mode;
        $this->table->style = $widthstyle;

        foreach ($this->columns as $column) {
            if (!($this->moving && $column->hide_when_moving) && !$column->is_hidden($mode)) {
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
        global $CFG, $COURSE, $USER, $OUTPUT;

        $object = $element['object'];
        $eid    = $element['eid'];
        $object->name = $this->gtree->get_element_header($element, true, true, false);
        $object->stripped_name = $this->gtree->get_element_header($element, false, false, false);

        $is_category_item = false;
        if ($element['type'] == 'categoryitem' || $element['type'] == 'courseitem') {
            $is_category_item = true;
        }

        $rowclasses = array();
        foreach ($parents as $parent_eid) {
            $rowclasses[] = $parent_eid;
        }

        $actions = '';

        if (!$is_category_item) {
            $actions .= $this->gtree->get_edit_icon($element, $this->gpr);
        }

        $actions .= $this->gtree->get_calculation_icon($element, $this->gpr);

        if ($element['type'] == 'item' or ($element['type'] == 'category' and $element['depth'] > 1)) {
            if ($this->element_deletable($element)) {
                $aurl = new moodle_url('index.php', array('id' => $COURSE->id, 'action' => 'delete', 'eid' => $eid, 'sesskey' => sesskey()));
                $actions .= $OUTPUT->action_icon($aurl, new pix_icon('t/delete', get_string('delete')));
            }

            $aurl = new moodle_url('index.php', array('id' => $COURSE->id, 'action' => 'moveselect', 'eid' => $eid, 'sesskey' => sesskey()));
            $actions .= $OUTPUT->action_icon($aurl, new pix_icon('t/move', get_string('move')));
        }

        $actions .= $this->gtree->get_hiding_icon($element, $this->gpr);
        $actions .= $this->gtree->get_locking_icon($element, $this->gpr);

        $mode = ($USER->gradeediting[$COURSE->id]) ? 'advanced' : 'simple';

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
            $cell->attributes['class'] = $element['type'] . ' moving';
            $cell->text = $object->name.' ('.get_string('move').')';
            return array(new html_table_row(array($cell)));
        }

        if ($element['type'] == 'category') {
            $level++;
            $this->categories[$object->id] = $object->stripped_name;
            $category = grade_category::fetch(array('id' => $object->id));
            $item = $category->get_grade_item();

            // Add aggregation coef input if not a course item and if parent category has correct aggregation type
            $dimmed = ($item->is_hidden()) ? 'dimmed' : '';

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
                    $actions = ''; // no action icons when moving

                    $aurl = new moodle_url('index.php', array('id' => $COURSE->id, 'action' => 'move', 'eid' => $this->moving, 'moveafter' => $child_eid, 'sesskey' => sesskey()));
                    if ($first) {
                        $aurl->params($first);
                    }

                    $cell = new html_table_cell();
                    $cell->colspan = 12;

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

            $levelclass = "level$level";

            $courseclass = '';
            if ($level == 1) {
                $courseclass = 'coursecategory';
            }

            $row = new html_table_row();
            $row->attributes['class'] = $courseclass . ' category ' . $dimmed;
            foreach ($rowclasses as $class) {
                $row->attributes['class'] .= ' ' . $class;
            }

            $headercell = new html_table_cell();
            $headercell->header = true;
            $headercell->scope = 'row';
            $headercell->attributes['title'] = $object->stripped_name;
            $headercell->attributes['class'] = 'cell rowspan ' . $levelclass;
            $headercell->rowspan = $row_count + 1;
            $row->cells[] = $headercell;

            foreach ($this->columns as $column) {
                if (!($this->moving && $column->hide_when_moving) && !$column->is_hidden($mode)) {
                    $row->cells[] = $column->get_category_cell($category, $levelclass, array('id' => $id, 'name' => $object->name, 'level' => $level, 'actions' => $actions, 'eid' => $eid));
                }
            }

            $returnrows[] = $row;

            $returnrows = array_merge($returnrows, $html_children);

            // Print a coloured row to show the end of the category across the table
            $endcell = new html_table_cell();
            $endcell->colspan = (19 - $level);
            $endcell->attributes['class'] = 'colspan ' . $levelclass;

            $returnrows[] = new html_table_row(array($endcell));;

        } else { // Dealing with a grade item

            $item = grade_item::fetch(array('id' => $object->id));
            $element['type'] = 'item';
            $element['object'] = $item;

            $categoryitemclass = '';
            if ($item->itemtype == 'category') {
                $categoryitemclass = 'categoryitem';
            }

            $dimmed = ($item->is_hidden()) ? "dimmed_text" : "";
            $gradeitemrow = new html_table_row();
            $gradeitemrow->attributes['class'] = $categoryitemclass . ' item ' . $dimmed;
            foreach ($rowclasses as $class) {
                $gradeitemrow->attributes['class'] .= ' ' . $class;
            }

            foreach ($this->columns as $column) {
                if (!($this->moving && $column->hide_when_moving) && !$column->is_hidden($mode)) {
                    $gradeitemrow->cells[] = $column->get_item_cell($item, array('id' => $id, 'name' => $object->name, 'level' => $level, 'actions' => $actions,
                                                                 'element' => $element, 'eid' => $eid, 'itemtype' => $object->itemtype));
                }
            }

            $returnrows[] = $gradeitemrow;
        }

        return $returnrows;

    }

    /**
     * Given a grade_item object, returns a labelled input if an aggregation coefficient (weight or extra credit) applies to it.
     * @param grade_item $item
     * @param string type "extra" or "weight": the type of the column hosting the weight input
     * @return string HTML
     */
    static function get_weight_input($item, $type) {
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

        if ((($aggcoef == 'aggregationcoefweight' || $aggcoef == 'aggregationcoef') && $type == 'weight') ||
            ($aggcoef == 'aggregationcoefextraweight' && $type == 'extra')) {
            return '<label class="accesshide" for="aggregationcoef_'.$item->id.'">'.
                get_string('extracreditvalue', 'grades', $item->itemname).'</label>'.
                '<input type="text" size="6" id="aggregationcoef_'.$item->id.'" name="aggregationcoef_'.$item->id.'"
                value="'.grade_edit_tree::format_number($item->aggregationcoef).'" />';
        } elseif ($aggcoef == 'aggregationcoefextrasum' && $type == 'extra') {
            $checked = ($item->aggregationcoef > 0) ? 'checked="checked"' : '';
            return '<input type="hidden" name="extracredit_'.$item->id.'" value="0" />
                <label class="accesshide" for="extracredit_'.$item->id.'">'.
                get_string('extracreditvalue', 'grades', $item->itemname).'</label>
                <input type="checkbox" id="extracredit_'.$item->id.'" name="extracredit_'.$item->id.'" value="1" '."$checked />\n";
        } else {
            return '';
        }
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

        if(!$after_el = $this->gtree->locate_element("c$moveafter")) {
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
            if ($coefstring == 'aggregationcoefweight') {
                $this->uses_weight = true;
            } elseif ($coefstring ==  'aggregationcoefextraweight' || $coefstring == 'aggregationcoefextrasum') {
                $this->uses_extra_credit = true;
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

    public abstract function get_category_cell($category, $levelclass, $params);

    public abstract function get_item_cell($item, $params);

    public abstract function is_hidden($mode='simple');

    public function __construct() {
        $this->headercell = new html_table_cell();
        $this->headercell->header = true;
        $this->headercell->style = 'whitespace: normal;';
        $this->headercell->attributes['class'] = 'header';

        $this->categorycell = new html_table_cell();
        $this->categorycell->attributes['class']  = 'cell';

        $this->itemcell = new html_table_cell();
        $this->itemcell->attributes['class'] = 'cell';
    }
}

abstract class grade_edit_tree_column_category extends grade_edit_tree_column {

    public $forced;
    public $advanced;

    public function __construct($name) {
        global $CFG;
        $this->forced = (int)$CFG->{"grade_$name"."_flag"} & 1;
        $this->advanced = (int)$CFG->{"grade_$name"."_flag"} & 2;
        parent::__construct();
    }

    public function is_hidden($mode='simple') {
        global $CFG;
        if ($mode == 'simple') {
            return $this->advanced;
        } elseif ($mode == 'advanced') {
            if ($this->forced && $CFG->grade_hideforcedsettings) {
                return true;
            } else {
                return false;
            }
        }
    }
}

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
        $headercell->attributes['class'] .= ' name';
        $headercell->colspan = $this->deepest_level + 1;
        $headercell->text = get_string('name');
        return $headercell;
    }

    public function get_category_cell($category, $levelclass, $params) {
        global $OUTPUT;
        if (empty($params['name']) || empty($params['level'])) {
            throw new Exception('Array key (name or level) missing from 3rd param of grade_edit_tree_column_name::get_category_cell($category, $levelclass, $params)');
        }
        $categorycell = clone($this->categorycell);
        $categorycell->attributes['class'] .= ' name ' . $levelclass;
        $categorycell->colspan = ($this->deepest_level +1) - $params['level'];
        $categorycell->text = $OUTPUT->heading($params['name'], 4);
        return $categorycell;
    }

    public function get_item_cell($item, $params) {
        global $CFG;

        if (empty($params['element']) || empty($params['name']) || empty($params['level'])) {
            throw new Exception('Array key (name, level or element) missing from 2nd param of grade_edit_tree_column_name::get_item_cell($item, $params)');
        }

        $name = $params['name'];

        $itemcell = clone($this->itemcell);
        $itemcell->attributes['class'] .= ' name';
        $itemcell->colspan = ($this->deepest_level + 1) - $params['level'];
        $itemcell->text = $name;
        return $itemcell;
    }

    public function is_hidden($mode='simple') {
        return false;
    }
}

class grade_edit_tree_column_aggregation extends grade_edit_tree_column_category {

    public function __construct($params) {
        parent::__construct('aggregation');
    }

    public function get_header_cell() {
        global $OUTPUT;
        $headercell = clone($this->headercell);
        $headercell->text = get_string('aggregation', 'grades').$OUTPUT->help_icon('aggregation', 'grades');
        return $headercell;
    }

    public function get_category_cell($category, $levelclass, $params) {
        global $CFG, $OUTPUT;
        if (empty($params['id'])) {
            throw new Exception('Array key (id) missing from 3rd param of grade_edit_tree_column_aggregation::get_category_cell($category, $levelclass, $params)');
        }

        $options = array(GRADE_AGGREGATE_MEAN             => get_string('aggregatemean', 'grades'),
                         GRADE_AGGREGATE_WEIGHTED_MEAN    => get_string('aggregateweightedmean', 'grades'),
                         GRADE_AGGREGATE_WEIGHTED_MEAN2   => get_string('aggregateweightedmean2', 'grades'),
                         GRADE_AGGREGATE_EXTRACREDIT_MEAN => get_string('aggregateextracreditmean', 'grades'),
                         GRADE_AGGREGATE_MEDIAN           => get_string('aggregatemedian', 'grades'),
                         GRADE_AGGREGATE_MIN              => get_string('aggregatemin', 'grades'),
                         GRADE_AGGREGATE_MAX              => get_string('aggregatemax', 'grades'),
                         GRADE_AGGREGATE_MODE             => get_string('aggregatemode', 'grades'),
                         GRADE_AGGREGATE_SUM              => get_string('aggregatesum', 'grades'));

        $visible = explode(',', $CFG->grade_aggregations_visible);
        foreach ($options as $constant => $string) {
            if (!in_array($constant, $visible) && $constant != $category->aggregation) {
                unset($options[$constant]);
            }
        }

        if ($this->forced) {
            $aggregation = $options[$category->aggregation];
        } else {
            $attributes = array();
            $attributes['id'] = 'aggregation_'.$category->id;
            $aggregation = html_writer::label(get_string('aggregation', 'grades'), 'aggregation_'.$category->id, false, array('class' => 'accesshide'));
            $aggregation .= html_writer::select($options, 'aggregation_'.$category->id, $category->aggregation, null, $attributes);
            $action = new component_action('change', 'update_category_aggregation', array('courseid' => $params['id'], 'category' => $category->id, 'sesskey' => sesskey()));
            $OUTPUT->add_action_handler($action, 'aggregation_'.$category->id);
        }

        $categorycell = clone($this->categorycell);
        $categorycell->attributes['class'] .= ' ' . $levelclass;
        $categorycell->text = $aggregation;
        return $categorycell;

    }

    public function get_item_cell($item, $params) {
        $itemcell = clone($this->itemcell);
        $itemcell->text = ' - ';
        return $itemcell;
    }
}

class grade_edit_tree_column_extracredit extends grade_edit_tree_column {

    public function get_header_cell() {
        global $OUTPUT;
        $headercell = clone($this->headercell);
        $headercell->text = get_string('aggregationcoefextra', 'grades').$OUTPUT->help_icon('aggregationcoefextra', 'grades');
        return $headercell;
    }

    public function get_category_cell($category, $levelclass, $params) {
        $item = $category->get_grade_item();
        $categorycell = clone($this->categorycell);
        $categorycell->attributes['class'] .= ' ' . $levelclass;
        $categorycell->text = grade_edit_tree::get_weight_input($item, 'extra');
        return $categorycell;
    }

    public function get_item_cell($item, $params) {
        if (empty($params['element'])) {
            throw new Exception('Array key (element) missing from 2nd param of grade_edit_tree_column_weightorextracredit::get_item_cell($item, $params)');
        }

        $itemcell = clone($this->itemcell);
        $itemcell->text = '&nbsp;';

        if (!in_array($params['element']['object']->itemtype, array('courseitem', 'categoryitem', 'category'))) {
            $itemcell->text = grade_edit_tree::get_weight_input($item, 'extra');
        }

        return $itemcell;
    }

    public function is_hidden($mode='simple') {
        global $CFG;
        if ($mode == 'simple') {
            return strstr($CFG->grade_item_advanced, 'aggregationcoef');
        } elseif ($mode == 'advanced') {
            return false;
        }
    }
}

class grade_edit_tree_column_weight extends grade_edit_tree_column {

    public function get_header_cell() {
        global $OUTPUT;
        $headercell = clone($this->headercell);
        $headercell->text = get_string('weightuc', 'grades').$OUTPUT->help_icon('aggregationcoefweight', 'grades');
        return $headercell;
    }

    public function get_category_cell($category, $levelclass, $params) {

        $item = $category->get_grade_item();
        $categorycell = clone($this->categorycell);
        $categorycell->attributes['class']  .= ' ' . $levelclass;
        $categorycell->text = grade_edit_tree::get_weight_input($item, 'weight');
        return $categorycell;
    }

    public function get_item_cell($item, $params) {
        if (empty($params['element'])) {
            throw new Exception('Array key (element) missing from 2nd param of grade_edit_tree_column_weightorextracredit::get_item_cell($item, $params)');
        }
        $itemcell = clone($this->itemcell);
        $itemcell->text = '&nbsp;';

        if (!in_array($params['element']['object']->itemtype, array('courseitem', 'categoryitem', 'category'))) {
            $itemcell->text = grade_edit_tree::get_weight_input($item, 'weight');
        }

        return $itemcell;
    }

    public function is_hidden($mode='simple') {
        global $CFG;
        if ($mode == 'simple') {
            return strstr($CFG->grade_item_advanced, 'aggregationcoef');
        } elseif ($mode == 'advanced') {
            return false;
        }
    }
}

class grade_edit_tree_column_range extends grade_edit_tree_column {

    public function get_header_cell() {
        $headercell = clone($this->headercell);
        $headercell->text = get_string('maxgrade', 'grades');
        return $headercell;
    }

    public function get_category_cell($category, $levelclass, $params) {
        $categorycell = clone($this->categorycell);
        $categorycell->attributes['class'] .= ' range ' . $levelclass;
        $categorycell->text = ' - ';
        return $categorycell;
    }

    public function get_item_cell($item, $params) {
        global $DB, $OUTPUT;

        // If the parent aggregation is Sum of Grades, this cannot be changed
        $parent_cat = $item->get_parent_category();
        if ($parent_cat->aggregation == GRADE_AGGREGATE_SUM) {
            $grademax = format_float($item->grademax, $item->get_decimals());
        } elseif ($item->gradetype == GRADE_TYPE_SCALE) {
            $scale = $DB->get_record('scale', array('id' => $item->scaleid));
            $scale_items = null;
            if (empty($scale)) { //if the item is using a scale that's been removed
                $scale_items = array();
            } else {
                $scale_items = explode(',', $scale->scale);
            }
            $grademax = end($scale_items) . ' (' . count($scale_items) . ')';
        } elseif ($item->is_external_item()) {
            $grademax = format_float($item->grademax, $item->get_decimals());
        } else {
            $grademax = '<label class="accesshide" for="grademax'.$item->id.'">'.get_string('grademax', 'grades').'</label>
                <input type="text" size="6" id="grademax'.$item->id.'" name="grademax_'.$item->id.'" value="'.
                format_float($item->grademax, $item->get_decimals()).'" />';
        }

        $itemcell = clone($this->itemcell);
        $itemcell->text = $grademax;
        return $itemcell;
    }

    public function is_hidden($mode='simple') {
        global $CFG;
        if ($mode == 'simple') {
            return strstr($CFG->grade_item_advanced, 'grademax');
        } elseif ($mode == 'advanced') {
            return false;
        }
    }
}

class grade_edit_tree_column_aggregateonlygraded extends grade_edit_tree_column_category {

    public function __construct($params) {
        parent::__construct('aggregateonlygraded');
    }

    public function get_header_cell() {
        global $OUTPUT;
        $headercell = clone($this->headercell);
        $headercell->style .= 'width: 40px;';
        $headercell->text = get_string('aggregateonlygraded', 'grades')
                . $OUTPUT->help_icon('aggregateonlygraded', 'grades');
        return $headercell;
    }

    public function get_category_cell($category, $levelclass, $params) {
        $onlygradedcheck = ($category->aggregateonlygraded == 1) ? 'checked="checked"' : '';
        $hidden = '<input type="hidden" name="aggregateonlygraded_'.$category->id.'" value="0" />';
        $aggregateonlygraded = '<label class="accesshide" for="aggregateonlygraded_'.$category->id.'">'.
                get_string('aggregateonlygraded', 'grades').'</label>
                <input type="checkbox" id="aggregateonlygraded_'.$category->id.'" name="aggregateonlygraded_'.
                $category->id.'" value="1" '.$onlygradedcheck . ' />';

        if ($this->forced) {
            $aggregateonlygraded = ($category->aggregateonlygraded) ? get_string('yes') : get_string('no');
        }

        $categorycell = clone($this->categorycell);
        $categorycell->attributes['class'] .= ' ' . $levelclass;
        $categorycell->text = $hidden.$aggregateonlygraded;
        return $categorycell;
    }

    public function get_item_cell($item, $params) {
        $itemcell = clone($this->itemcell);
        $itemcell->text = ' - ';
        return $itemcell;
    }
}

class grade_edit_tree_column_aggregatesubcats extends grade_edit_tree_column_category {

    public function __construct($params) {
        parent::__construct('aggregatesubcats');
    }

    public function get_header_cell() {
        global $OUTPUT;
        $headercell = clone($this->headercell);
        $headercell->style .= 'width: 40px;';
        $headercell->text = get_string('aggregatesubcats', 'grades')
              .$OUTPUT->help_icon('aggregatesubcats', 'grades');
        return $headercell;
    }

    public function get_category_cell($category, $levelclass, $params) {
        $subcatscheck = ($category->aggregatesubcats == 1) ? 'checked="checked"' : '';
        $hidden = '<input type="hidden" name="aggregatesubcats_'.$category->id.'" value="0" />';
        $aggregatesubcats = '<label class="accesshide" for="aggregatesubcats_'.$category->id.'">'.
                get_string('aggregatesubcats', 'grades').'</label>
                <input type="checkbox" id="aggregatesubcats_'.$category->id.'" name="aggregatesubcats_'.$category->id.
                '" value="1" ' . $subcatscheck.' />';

        if ($this->forced) {
            $aggregatesubcats = ($category->aggregatesubcats) ? get_string('yes') : get_string('no');
        }

        $categorycell = clone($this->categorycell);
        $categorycell->attributes['class'] .= ' ' . $levelclass;
        $categorycell->text = $hidden.$aggregatesubcats;
        return $categorycell;

    }

    public function get_item_cell($item, $params) {
        $itemcell = clone($this->itemcell);
        $itemcell->text = ' - ';
        return $itemcell;
    }
}

class grade_edit_tree_column_aggregateoutcomes extends grade_edit_tree_column_category {

    public function __construct($params) {
        parent::__construct('aggregateoutcomes');
    }

    public function get_header_cell() {
        global $OUTPUT;
        $headercell = clone($this->headercell);
        $headercell->style .= 'width: 40px;';
        $headercell->text = get_string('aggregateoutcomes', 'grades')
              .$OUTPUT->help_icon('aggregateoutcomes', 'grades');
        return $headercell;
    }

    public function get_category_cell($category, $levelclass, $params) {
        $outcomescheck = ($category->aggregateoutcomes == 1) ? 'checked="checked"' : '';
        $hidden = '<input type="hidden" name="aggregateoutcomes_'.$category->id.'" value="0" />';
        $aggregateoutcomes = '<label class="accesshide" for="aggregateoutcomes_'.$category->id.'">'.
                get_string('aggregateoutcomes', 'grades').'</label>
                <input type="checkbox" id="aggregateoutcomes_'.$category->id.'" name="aggregateoutcomes_'.$category->id.
                '" value="1" ' . $outcomescheck.' />';

        if ($this->forced) {
            $aggregateoutcomes = ($category->aggregateoutcomes) ? get_string('yes') : get_string('no');
        }

        $categorycell = clone($this->categorycell);
        $categorycell->attributes['class'] .= ' ' . $levelclass;
        $categorycell->text = $hidden.$aggregateoutcomes;
        return $categorycell;
    }

    public function get_item_cell($item, $params) {
        $itemcell = clone($this->itemcell);
        $itemcell->text = ' - ';
        return $itemcell;
    }

    public function is_hidden($mode='simple') {
        global $CFG;
        if ($CFG->enableoutcomes) {
            return parent::is_hidden($mode);
        } else {
            return true;
        }
    }
}

class grade_edit_tree_column_droplow extends grade_edit_tree_column_category {

    public function __construct($params) {
        parent::__construct('droplow');
    }

    public function get_header_cell() {
        global $OUTPUT;
        $headercell = clone($this->headercell);
        $headercell->text = get_string('droplow', 'grades').$OUTPUT->help_icon('droplow', 'grades');
        return $headercell;
    }

    public function get_category_cell($category, $levelclass, $params) {
        $droplow = '<label class="accesshide" for="droplow_' . $category->id.'">' . get_string('droplowestvalue', 'grades') . '</label>';
        $droplow .= '<input type="text" size="3" id="droplow_' . $category->id . '" name="droplow_' . $category->id . '" value="'
                . $category->droplow.'" />';

        if ($this->forced) {
            $droplow = $category->droplow;
        }

        $categorycell = clone($this->categorycell);
        $categorycell->attributes['class']  .= ' ' . $levelclass;
        $categorycell->text = $droplow;
        return $categorycell;
    }

    public function get_item_cell($item, $params) {
        $itemcell = clone($this->itemcell);
        $itemcell->text = ' - ';
        return $itemcell;
    }
}

class grade_edit_tree_column_keephigh extends grade_edit_tree_column_category {

    public function __construct($params) {
        parent::__construct('keephigh');
    }

    public function get_header_cell() {
        global $OUTPUT;
        $headercell = clone($this->headercell);
        $headercell->text = get_string('keephigh', 'grades').$OUTPUT->help_icon('keephigh', 'grades');
        return $headercell;
    }

    public function get_category_cell($category, $levelclass, $params) {
        $keephigh = '<label class="accesshide" for="keephigh_'.$category->id.'">'.get_string('keephigh', 'grades').'</label>';
        $keephigh .= '<input type="text" size="3" id="keephigh_'.$category->id.'" name="keephigh_'.$category->id.'" value="'.
                $category->keephigh.'" />';

        if ($this->forced) {
            $keephigh = $category->keephigh;
        }

        $categorycell = clone($this->categorycell);
        $categorycell->attributes['class'] .= ' ' . $levelclass;
        $categorycell->text = $keephigh;
        return $categorycell;
    }

    public function get_item_cell($item, $params) {
        $itemcell = clone($this->itemcell);
        $itemcell->text = ' - ';
        return $itemcell;
    }
}

class grade_edit_tree_column_multfactor extends grade_edit_tree_column {

    public function __construct($params) {
        parent::__construct();
    }

    public function get_header_cell() {
        global $OUTPUT;
        $headercell = clone($this->headercell);
        $headercell->text = get_string('multfactor', 'grades').$OUTPUT->help_icon('multfactor', 'grades');
        return $headercell;
    }

    public function get_category_cell($category, $levelclass, $params) {
        $categorycell = clone($this->categorycell);
        $categorycell->attributes['class'] .= ' ' . $levelclass;
        $categorycell->text = ' - ';
        return $categorycell;
    }

    public function get_item_cell($item, $params) {
        global $OUTPUT;

        $itemcell = clone($this->itemcell);
        if (!$item->is_raw_used()) {
            $itemcell->text = '&nbsp;';
            return $itemcell;
        }
        $multfactor = '<label class="accesshide" for="multfactor'.$item->id.'">'.
                get_string('multfactorvalue', 'grades', $item->itemname).'</label>
                <input type="text" size="4" id="multfactor'.$item->id.'" name="multfactor_'.$item->id.'" value="'.
                grade_edit_tree::format_number($item->multfactor).'" />';

        $itemcell->text = $multfactor;
        return $itemcell;
    }

    public function is_hidden($mode='simple') {
        global $CFG;
        if ($mode == 'simple') {
            return strstr($CFG->grade_item_advanced, 'multfactor');
        } elseif ($mode == 'advanced') {
            return false;
        }
    }
}

class grade_edit_tree_column_plusfactor extends grade_edit_tree_column {

    public function get_header_cell() {
        global $OUTPUT;
        $headercell = clone($this->headercell);
        $headercell->text = get_string('plusfactor', 'grades').$OUTPUT->help_icon('plusfactor', 'grades');
        return $headercell;
    }

    public function get_category_cell($category, $levelclass, $params) {
        $categorycell = clone($this->categorycell);
        $categorycell->attributes['class'] .= ' ' . $levelclass;
        $categorycell->text = ' - ';
        return $categorycell;

    }

    public function get_item_cell($item, $params) {
        global $OUTPUT;

        $itemcell = clone($this->itemcell);
        if (!$item->is_raw_used()) {
            $itemcell->text = '&nbsp;';
            return $itemcell;
        }

        $plusfactor = '<label class="accesshide" for="plusfactor_'. $item->id . '">'.
                get_string('plusfactorvalue', 'grades', $item->itemname).'</label>
                <input type="text" size="4" id="plusfactor_'.$item->id.'" name="plusfactor_'.$item->id.'" value="'.
                grade_edit_tree::format_number($item->plusfactor).'" />';

        $itemcell->text = $plusfactor;
        return $itemcell;

    }

    public function is_hidden($mode='simple') {
        global $CFG;
        if ($mode == 'simple') {
            return strstr($CFG->grade_item_advanced, 'plusfactor');
        } elseif ($mode == 'advanced') {
            return false;
        }
    }
}

class grade_edit_tree_column_actions extends grade_edit_tree_column {

    public function __construct($params) {
        parent::__construct();
    }

    public function get_header_cell() {
        $headercell = clone($this->headercell);
        $headercell->attributes['class'] .= ' actions';
        $headercell->text = get_string('actions');
        return $headercell;
    }

    public function get_category_cell($category, $levelclass, $params) {

        if (empty($params['actions'])) {
            throw new Exception('Array key (actions) missing from 3rd param of grade_edit_tree_column_actions::get_category_actions($category, $levelclass, $params)');
        }

        $categorycell = clone($this->categorycell);
        $categorycell->attributes['class'] .= ' ' . $levelclass;
        $categorycell->text = $params['actions'];
        return $categorycell;
    }

    public function get_item_cell($item, $params) {
        if (empty($params['actions'])) {
            throw new Exception('Array key (actions) missing from 2nd param of grade_edit_tree_column_actions::get_item_cell($item, $params)');
        }
        $itemcell = clone($this->itemcell);
        $itemcell->attributes['class'] .= ' actions';
        $itemcell->text = $params['actions'];
        return $itemcell;
    }

    public function is_hidden($mode='simple') {
        return false;
    }
}

class grade_edit_tree_column_select extends grade_edit_tree_column {

    public function get_header_cell() {
        $headercell = clone($this->headercell);
        $headercell->attributes['class'] .= ' selection';
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

        $categorycell = clone($this->categorycell);
        $categorycell->attributes['class'] .= ' last ' . $levelclass;
        $categorycell->style .= 'text-align: center;';
        $categorycell->text = $OUTPUT->render($selectall) . '<br />' . $OUTPUT->render($selectnone);
        return $categorycell;
    }

    public function get_item_cell($item, $params) {
        if (empty($params['itemtype']) || empty($params['eid'])) {
            error('Array key (itemtype or eid) missing from 2nd param of grade_edit_tree_column_select::get_item_cell($item, $params)');
        }
        $itemselect = '';

        if ($params['itemtype'] != 'course' && $params['itemtype'] != 'category') {
            $itemselect = '<label class="accesshide" for="select_'.$params['eid'].'">'.
                get_string('select', 'grades', $item->itemname).'</label>
                <input class="itemselect" type="checkbox" name="select_'.$params['eid'].'" id="select_'.$params['eid'].
                '" onchange="toggleCategorySelector();"/>'; // TODO: convert to YUI handler
        }
        //html_writer::table() will wrap the item cell contents in a <TD> so don't do it here
        return $itemselect;
    }

    public function is_hidden($mode='simple') {
        return false;
    }
}

