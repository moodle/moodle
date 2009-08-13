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

class grade_edit_tree {
    var $columns = array();

    /**
     * @var object $gtree          @see grade/lib.php
     */
    var $gtree;

    /**
     * @var grade_plugin_return @see grade/lib.php
     */
    var $gpr;

    /**
     * @var string              $moving The eid of the category or item being moved
     */
    var $moving;

    var $deepest_level;

    var $uses_extra_credit = false;

    var $uses_weight = false;

    /**
     * Constructor
     */
    function grade_edit_tree($gtree, $moving=false, $gpr) {
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
    }

    /**
     * Recursive function for building the table holding the grade categories and items,
     * with CSS indentation and styles.
     *
     * @param array               $element The current tree element being rendered
     * @param boolean             $totals Whether or not to print category grade items (category totals)
     * @param array               $parents An array of parent categories for the current element (used for indentation and row classes)
     *
     * @return string HTML
     */
    function build_html_tree($element, $totals, $parents, &$categories, $level, &$row_count) {
        global $CFG, $COURSE, $USER;

        $object = $element['object'];
        $eid    = $element['eid'];
        $object->name = $this->gtree->get_element_header($element, true, true, false);
        $object->stripped_name = $this->gtree->get_element_header($element, false, false, false);

        $is_category_item = false;
        if ($element['type'] == 'categoryitem' || $element['type'] == 'courseitem') {
            $is_category_item = true;
        }

        $rowclasses = '';
        foreach ($parents as $parent_eid) {
            $rowclasses .= " $parent_eid ";
        }

        $actions = '';

        if (!$is_category_item) {
            $actions .= $this->gtree->get_edit_icon($element, $this->gpr);
        }

        $actions .= $this->gtree->get_calculation_icon($element, $this->gpr);

        if ($element['type'] == 'item' or ($element['type'] == 'category' and $element['depth'] > 1)) {
            if ($this->element_deletable($element)) {
                $actions .= '<a href="index.php?id='.$COURSE->id.'&amp;action=delete&amp;eid='
                         . $eid.'&amp;sesskey='.sesskey().'"><img src="'.$CFG->pixpath.'/t/delete.gif" class="iconsmall" alt="'
                         . get_string('delete').'" title="'.get_string('delete').'"/></a>';
            }
            $actions .= '<a href="index.php?id='.$COURSE->id.'&amp;action=moveselect&amp;eid='
                     . $eid.'&amp;sesskey='.sesskey().'"><img src="'.$CFG->pixpath.'/t/move.gif" class="iconsmall" alt="'
                     . get_string('move').'" title="'.get_string('move').'"/></a>';
        }

        $actions .= $this->gtree->get_hiding_icon($element, $this->gpr);
        $actions .= $this->gtree->get_locking_icon($element, $this->gpr);

        $mode = ($USER->gradeediting[$COURSE->id]) ? 'advanced' : 'simple';

        $html = '';
        $root = false;


        $id = required_param('id', PARAM_INT);

        /// prepare move target if needed
        $last = '';

        /// print the list items now
        if ($this->moving == $eid) {

            // do not diplay children
            return '<tr><td colspan="12" class="'.$element['type'].' moving">'.$object->name.' ('.get_string('move').')</td></tr>';

        }

        if ($element['type'] == 'category') {
            $level++;
            $categories[$object->id] = $object->stripped_name;
            $category = grade_category::fetch(array('id' => $object->id));
            $item = $category->get_grade_item();

            // Add aggregation coef input if not a course item and if parent category has correct aggregation type
            $dimmed = ($item->is_hidden()) ? " dimmed " : "";

            // Before we print the category's row, we must find out how many rows will appear below it (for the filler cell's rowspan)
            $aggregation_position = grade_get_setting($COURSE->id, 'aggregationposition', $CFG->grade_aggregationposition);
            $category_total_data = null; // Used if aggregationposition is set to "last", so we can print it last

            $html_children = '';

            $row_count = 0;

            foreach($element['children'] as $child_el) {
                $moveto = '';

                if (empty($child_el['object']->itemtype)) {
                    $child_el['object']->itemtype = false;
                }

                if (($child_el['object']->itemtype == 'course' || $child_el['object']->itemtype == 'category') && !$totals) {
                    continue;
                }

                $child_eid    = $child_el['eid'];
                $first = '';

                if ($child_el['object']->itemtype == 'course' || $child_el['object']->itemtype == 'category') {
                    $first = '&amp;first=1';
                    $child_eid = $eid;
                }

                if ($this->moving && $this->moving != $child_eid) {

                    $strmove     = get_string('move');
                    $strmovehere = get_string('movehere');
                    $actions = ''; // no action icons when moving

                    $moveto = '<tr><td colspan="12"><a href="index.php?id='.$COURSE->id.'&amp;action=move&amp;eid='.$this->moving.'&amp;moveafter='
                            . $child_eid.'&amp;sesskey='.sesskey().$first.'"><img class="movetarget" src="'.$CFG->wwwroot.'/pix/movehere.gif" alt="'
                            . $strmovehere.'" title="'.s($strmovehere).'" /></a></td></tr>';
                }

                $newparents = $parents;
                $newparents[] = $eid;

                $row_count++;
                $child_row_count = 0;

                // If moving, do not print course and category totals, but still print the moveto target box
                if ($this->moving && ($child_el['object']->itemtype == 'course' || $child_el['object']->itemtype == 'category')) {
                    $html_children .= $moveto;
                } elseif ($child_el['object']->itemtype == 'course' || $child_el['object']->itemtype == 'category') {
                    // We don't build the item yet because we first need to know the deepest level of categories (for category/name colspans)
                    $category_total_item = $this->build_html_tree($child_el, $totals, $newparents, $categories, $level, $child_row_count);
                    if (!$aggregation_position) {
                        $html_children .= $category_total_item;
                    }
                } else {
                    $html_children .= $this->build_html_tree($child_el, $totals, $newparents, $categories, $level, $child_row_count) . $moveto;

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
                $html_children .= $category_total_item;
            }

            // now build the header
            if (isset($element['object']->grade_item) && $element['object']->grade_item->is_course_item()) {
                // Reduce width if advanced elements are not shown
                $width_style = '';

                if ($mode == 'simple') {
                    $width_style = ' style="width:auto;" ';
                }

                $html .= '<table cellpadding="5" class="generaltable" '.$width_style.'>
                            <tr>';

                foreach ($this->columns as $column) {
                    if (!($this->moving && $column->hide_when_moving) && !$column->is_hidden($mode)) {
                        $html .= $column->get_header_cell();
                    }
                }

                $html .= '</tr>';
                $root = true;
            }

            $row_count_offset = 0;

            if (empty($category_total_item) && !$this->moving) {
                $row_count_offset = -1;
            }

            $levelclass = " level$level ";

            $html .= '
                    <tr class="category '.$dimmed.$rowclasses.'">
                      <th scope="row" title="'.s($object->stripped_name).'" class="cell rowspan '.$levelclass.'" rowspan="'.($row_count+1+$row_count_offset).'"></th>';

            foreach ($this->columns as $column) {
                if (!($this->moving && $column->hide_when_moving) && !$column->is_hidden($mode)) {
                    $html .= $column->get_category_cell($category, $levelclass, array('id' => $id, 'name' => $object->name, 'level' => $level, 'actions' => $actions, 'eid' => $eid));
                }
            }

            $html .= '</tr>';

            $html .= $html_children;

            // Print a coloured row to show the end of the category accross the table
            $html .= '<tr><td colspan="'.(19 - $level).'" class="colspan '.$levelclass.'"></td></tr>';

        } else { // Dealing with a grade item

            $item = grade_item::fetch(array('id' => $object->id));
            $element['type'] = 'item';
            $element['object'] = $item;

            // Determine aggregation coef element

            $dimmed = ($item->is_hidden()) ? " dimmed_text " : "";
            $html .= '<tr class="item'.$dimmed.$rowclasses.'">';

            foreach ($this->columns as $column) {
                if (!($this->moving && $column->hide_when_moving) && !$column->is_hidden($mode)) {
                    $html .= $column->get_item_cell($item, array('id' => $id, 'name' => $object->name, 'level' => $level, 'actions' => $actions,
                                                                 'element' => $element, 'eid' => $eid, 'itemtype' => $object->itemtype));
                }
            }

            $html .= '</tr>';
        }


        if ($root) {
            $html .= "</table>\n";
        }

        return $html;

    }

    /**
     * Given a grade_item object, returns a labelled input if an aggregation coefficient (weight or extra credit) applies to it.
     * @param grade_item $item
     * @param string type "extra" or "weight": the type of the column hosting the weight input
     * @return string HTML
     */
    function get_weight_input($item, $type) {
        if (!is_object($item) || get_class($item) !== 'grade_item') {
            error('grade_edit_tree::get_weight_input($item) was given a variable that is not of the required type (grade_item object)');
        }

        if ($item->is_course_item()) {
            return '';
        }

        $parent_category = $item->get_parent_category();
        $parent_category->apply_forced_settings();
        $aggcoef = $item->get_coefstring();

        if ((($aggcoef == 'aggregationcoefweight' || $aggcoef == 'aggregationcoef') && $type == 'weight') ||
            ($aggcoef == 'aggregationcoefextra' && $type == 'extra')) {
            return '<input type="text" size="6" id="aggregationcoef_'.$item->id.'" name="aggregationcoef_'.$item->id.'"
                value="'.format_float($item->aggregationcoef, 4).'" />';
        } elseif ($aggcoef == 'aggregationcoefextrasum' && $type == 'extra') {
            $checked = ($item->aggregationcoef > 0) ? 'checked="checked"' : '';
            return '<input type="hidden" name="extracredit_'.$item->id.'" value="0" />
                    <input type="checkbox" id="extracredit_'.$item->id.'" name="extracredit_'.$item->id.'" value="1" '."$checked />\n";
        } else {
            return '';
        }
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
            } elseif ($coefstring ==  'aggregationcoefextra' || $coefstring == 'aggregationcoefextrasum') {
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

class grade_edit_tree_column {
    var $forced;
    var $hidden;
    var $forced_hidden;
    var $advanced_hidden;
    var $hide_when_moving = true;

    function factory($name, $params=array()) {
        $class_name = "grade_edit_tree_column_$name";
        if (class_exists($class_name)) {
            return new $class_name($params);
        }
    }

    function get_header_cell() {}

    function get_category_cell($category, $levelclass, $params) {}

    function get_item_cell($item, $params) {}

    function is_hidden($mode='simple') {}
}

class grade_edit_tree_column_category extends grade_edit_tree_column {

    var $forced;
    var $advanced;

    function grade_edit_tree_column_category($name) {
        global $CFG;
        $this->forced = (int)$CFG->{"grade_$name"."_flag"} & 1;
        $this->advanced = (int)$CFG->{"grade_$name"."_flag"} & 2;
    }

    function is_hidden($mode='simple') {
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
    var $forced = false;
    var $hidden = false;
    var $forced_hidden = false;
    var $advanced_hidden = false;
    var $deepest_level = 1;
    var $hide_when_moving = false;

    function grade_edit_tree_column_name($params) {
        if (empty($params['deepest_level'])) {
            error('Tried to instantiate a grade_edit_tree_column_name object without the "deepest_level" param!');
        }

        $this->deepest_level = $params['deepest_level'];
    }

    function get_header_cell() {
        return '<th class="header name" colspan="'.($this->deepest_level + 1).'" scope="col">'.get_string('name').'</th>';
    }

    function get_category_cell($category, $levelclass, $params) {
        if (empty($params['name']) || empty($params['level'])) {
            error('Array key (name or level) missing from 3rd param of grade_edit_tree_column_name::get_category_cell($category, $levelclass, $params)');
        }

        return '<td class="cell name '.$levelclass.'" colspan="'.(($this->deepest_level +1) - $params['level']).'"><h4>' . $params['name'] . "</h4></td>\n";
    }

    function get_item_cell($item, $params) {
        global $CFG;

        if (empty($params['element']) || empty($params['name']) || empty($params['level'])) {
            error('Array key (name, level or element) missing from 2nd param of grade_edit_tree_column_name::get_item_cell($item, $params)');
        }

        $name = $params['name'];

        return '<td class="cell name" colspan="'.(($this->deepest_level + 1) - $params['level']).'">' . $name . '</td>';
    }

    function is_hidden($mode='simple') {
        return false;
    }
}

class grade_edit_tree_column_aggregation extends grade_edit_tree_column_category {

    function grade_edit_tree_column_aggregation($params) {
        parent::grade_edit_tree_column_category('aggregation');
    }

    function get_header_cell() {
        return '<th class="header" scope="col">'.get_string('aggregation', 'grades').helpbutton('aggregation', 'aggregation', 'grade', true, false, '', true).'</th>';
    }

    function get_category_cell($category, $levelclass, $params) {
        global $CFG;
        if (empty($params['id'])) {
            error('Array key (id) missing from 3rd param of grade_edit_tree_column_aggregation::get_category_cell($category, $levelclass, $params)');
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

        $script = "window.location='index.php?id={$params['id']}&amp;category={$category->id}&amp;aggregationtype='+this.value+'&amp;sesskey=" . sesskey()."';";
        $aggregation = choose_from_menu($options, 'aggregation_'.$category->id, $category->aggregation, null, $script, 0, true);

        if ($this->forced) {
            $aggregation = $options[$category->aggregation];
        }

        return '<td class="cell '.$levelclass.'">' . $aggregation . '</td>';

    }

    function get_item_cell($item, $params) {
          return '<td class="cell"> - </td>';
    }
}

class grade_edit_tree_column_extracredit extends grade_edit_tree_column {

    function get_header_cell() {
        return '<th class="header" scope="col">'.get_string('extracredit', 'grades').helpbutton('aggregationcoefcombo', 'aggregationcoefcombo', 'grade', true, false, '', true).'</th>';
    }

    function get_category_cell($category, $levelclass, $params) {

        $item = $category->get_grade_item();
        $aggcoef_input = grade_edit_tree::get_weight_input($item, 'extra');
        return '<td class="cell '.$levelclass.'">' . $aggcoef_input . '</td>';
    }

    function get_item_cell($item, $params) {
        if (empty($params['element'])) {
            error('Array key (element) missing from 2nd param of grade_edit_tree_column_weightorextracredit::get_item_cell($item, $params)');
        }

        $html = '<td class="cell">';

        if (!in_array($params['element']['object']->itemtype, array('courseitem', 'categoryitem', 'category'))) {
            $html .= grade_edit_tree::get_weight_input($item, 'extra');
        }

        return $html.'</td>';
    }

    function is_hidden($mode='simple') {
        global $CFG;
        if ($mode == 'simple') {
            return strstr($CFG->grade_item_advanced, 'aggregationcoef');
        } elseif ($mode == 'advanced') {
            return false;
        }
    }
}

class grade_edit_tree_column_weight extends grade_edit_tree_column {

    function get_header_cell() {
        return '<th class="header" scope="col">'.get_string('weightuc', 'grades').helpbutton('aggregationcoefweight', 'aggregationcoefweight', 'grade', true, false, '', true).'</th>';
    }

    function get_category_cell($category, $levelclass, $params) {

        $item = $category->get_grade_item();
        $aggcoef_input = grade_edit_tree::get_weight_input($item, 'weight');
        return '<td class="cell '.$levelclass.'">' . $aggcoef_input . '</td>';
    }

    function get_item_cell($item, $params) {
        if (empty($params['element'])) {
            error('Array key (element) missing from 2nd param of grade_edit_tree_column_weightorextracredit::get_item_cell($item, $params)');
        }

        $html = '<td class="cell">';

        if (!in_array($params['element']['object']->itemtype, array('courseitem', 'categoryitem', 'category'))) {
            $html .= grade_edit_tree::get_weight_input($item, 'weight');
        }

        return $html.'</td>';
    }

    function is_hidden($mode='simple') {
        global $CFG;
        if ($mode == 'simple') {
            return strstr($CFG->grade_item_advanced, 'aggregationcoef');
        } elseif ($mode == 'advanced') {
            return false;
        }
    }
}

class grade_edit_tree_column_range extends grade_edit_tree_column {

    function get_header_cell() {
        return '<th class="header" scope="col">'.get_string('maxgrade', 'grades').'</th>';
    }

    function get_category_cell($category, $levelclass, $params) {
        return '<td class="cell range '.$levelclass.'"> - </td>';
    }

    function get_item_cell($item, $params) {

        // If the parent aggregation is Sum of Grades, this cannot be changed
        $parent_cat = $item->get_parent_category();
        if ($parent_cat->aggregation == GRADE_AGGREGATE_SUM) {
            $grademax = format_float($item->grademax, $item->get_decimals());
        } elseif ($item->gradetype == GRADE_TYPE_SCALE) {
            $scale = get_record('scale', 'id', $item->scaleid);
            $scale_items = explode(',', $scale->scale);
            $grademax = end($scale_items) . ' (' . count($scale_items) . ')';
        } elseif ($item->is_external_item()) {
            $grademax = format_float($item->grademax, $item->get_decimals());
        } else {
            $grademax = '<input type="text" size="4" id="grademax'.$item->id.'" name="grademax_'.$item->id.'" value="'.format_float($item->grademax, $item->get_decimals()).'" />';
        }

        return '<td class="cell">'.$grademax.'</td>';
    }

    function is_hidden($mode='simple') {
        global $CFG;
        if ($mode == 'simple') {
            return strstr($CFG->grade_item_advanced, 'grademax');
        } elseif ($mode == 'advanced') {
            return false;
        }
    }
}

class grade_edit_tree_column_aggregateonlygraded extends grade_edit_tree_column_category {

    function grade_edit_tree_column_aggregateonlygraded($params) {
        parent::grade_edit_tree_column_category('aggregateonlygraded');
    }

    function get_header_cell() {
        return '<th class="header" style="width: 40px" scope="col">'.get_string('aggregateonlygraded', 'grades')
              .helpbutton('aggregateonlygraded', 'aggregateonlygraded', 'grade', true, false, '', true).'</th>';
    }

    function get_category_cell($category, $levelclass, $params) {
        $onlygradedcheck = ($category->aggregateonlygraded == 1) ? 'checked="checked"' : '';
        $hidden = '<input type="hidden" name="aggregateonlygraded_'.$category->id.'" value="0" />';
        $aggregateonlygraded ='<input type="checkbox" id="aggregateonlygraded_'.$category->id.'" name="aggregateonlygraded_'.$category->id.'" value="1" '.$onlygradedcheck . ' />';

        if ($this->forced) {
            $aggregateonlygraded = ($category->aggregateonlygraded) ? get_string('yes') : get_string('no');
        }

        return '<td class="cell '.$levelclass.'">'.$hidden.$aggregateonlygraded.'</td>';
    }

    function get_item_cell($item, $params) {
        return '<td class="cell"> - </td>';
    }
}

class grade_edit_tree_column_aggregatesubcats extends grade_edit_tree_column_category {

    function grade_edit_tree_column_aggregatesubcats($params) {
        parent::grade_edit_tree_column_category('aggregatesubcats');
    }

    function get_header_cell() {
        return '<th class="header" style="width: 40px" scope="col">'.get_string('aggregatesubcats', 'grades')
              .helpbutton('aggregatesubcats', 'aggregatesubcats', 'grade', true, false, '', true).'</th>';
    }

    function get_category_cell($category, $levelclass, $params) {
        $subcatscheck = ($category->aggregatesubcats == 1) ? 'checked="checked"' : '';
        $hidden = '<input type="hidden" name="aggregatesubcats_'.$category->id.'" value="0" />';
        $aggregatesubcats = '<input type="checkbox" id="aggregatesubcats_'.$category->id.'" name="aggregatesubcats_'.$category->id.'" value="1" ' . $subcatscheck.' />';

        if ($this->forced) {
            $aggregatesubcats = ($category->aggregatesubcats) ? get_string('yes') : get_string('no');
        }

        return '<td class="cell '.$levelclass.'">'.$hidden.$aggregatesubcats.'</td>';

    }

    function get_item_cell($item, $params) {
        return '<td class="cell"> - </td>';
    }
}

class grade_edit_tree_column_aggregateoutcomes extends grade_edit_tree_column_category {

    function grade_edit_tree_column_aggregateoutcomes($params) {
        parent::grade_edit_tree_column_category('aggregateoutcomes');
    }

    function get_header_cell() {
        return '<th class="header" style="width: 40px" scope="col">'.get_string('aggregateoutcomes', 'grades')
              .helpbutton('aggregateoutcomes', 'aggregateoutcomes', 'grade', true, false, '', true).'</th>';
    }

    function get_category_cell($category, $levelclass, $params) {

        $outcomescheck = ($category->aggregateoutcomes == 1) ? 'checked="checked"' : '';
        $hidden = '<input type="hidden" name="aggregateoutcomes_'.$category->id.'" value="0" />';
        $aggregateoutcomes = '<input type="checkbox" id="aggregateoutcomes_'.$category->id.'" name="aggregateoutcomes_'.$category->id.'" value="1" ' . $outcomescheck.' />';

        if ($this->forced) {
            $aggregateoutcomes = ($category->aggregateoutcomes) ? get_string('yes') : get_string('no');
        }

        return '<td class="cell '.$levelclass.'">'.$hidden.$aggregateoutcomes.'</td>';
    }

    function get_item_cell($item, $params) {
        return '<td class="cell"> - </td>';
    }

    function is_hidden($mode='simple') {
        global $CFG;
        if ($CFG->enableoutcomes) {
            return parent::is_hidden($mode);
        } else {
            return true;
        }
    }
}

class grade_edit_tree_column_droplow extends grade_edit_tree_column_category {

    function grade_edit_tree_column_droplow($params) {
        parent::grade_edit_tree_column_category('droplow');
    }

    function get_header_cell() {
        return '<th class="header" scope="col">'.get_string('droplow', 'grades').helpbutton('droplow', 'droplow', 'grade', true, false, '', true).'</th>';
    }

    function get_category_cell($category, $levelclass, $params) {
        $droplow = '<input type="text" size="3" id="droplow_'.$category->id.'" name="droplow_'.$category->id.'" value="'.$category->droplow.'" />';

        if ($this->forced) {
            $droplow = $category->droplow;
        }

        return '<td class="cell '.$levelclass.'">' . $droplow . '</td>';
    }

    function get_item_cell($item, $params) {
        return '<td class="cell"> - </td>';
    }
}

class grade_edit_tree_column_keephigh extends grade_edit_tree_column_category {

    function grade_edit_tree_column_keephigh($params) {
        parent::grade_edit_tree_column_category('keephigh');
    }

    function get_header_cell() {
        return '<th class="header" scope="col">'.get_string('keephigh', 'grades').helpbutton('keephigh', 'keephigh', 'grade', true, false, '', true).'</th>';
    }

    function get_category_cell($category, $levelclass, $params) {
        $keephigh = '<input type="text" size="3" id="keephigh_'.$category->id.'" name="keephigh_'.$category->id.'" value="'.$category->keephigh.'" />';

        if ($this->forced) {
            $keephigh = $category->keephigh;
        }

        return '<td class="cell '.$levelclass.'">' . $keephigh . '</td>';
    }

    function get_item_cell($item, $params) {
        return '<td class="cell"> - </td>';
    }
}

class grade_edit_tree_column_multfactor extends grade_edit_tree_column {

    function grade_edit_tree_column_multfactor($params) {

    }

    function get_header_cell() {
        return '<th class="header" scope="col">'.get_string('multfactor', 'grades').helpbutton('multfactor', 'multfactor', 'grade', true, false, '', true).'</th>';
    }

    function get_category_cell($category, $levelclass, $params) {

        return '<td class="cell '.$levelclass.'"> - </td>';
    }

    function get_item_cell($item, $params) {
        if (!$item->is_raw_used()) {
            return '<td class="cell">&nbsp;</td>';
        }
        $multfactor = '<input type="text" size="4" id="multfactor'.$item->id.'" name="multfactor_'.$item->id.'" value="'.format_float($item->multfactor, 4).'" />';
        return '<td class="cell">'.$multfactor.'</td>';
    }

    function is_hidden($mode='simple') {
        global $CFG;
        if ($mode == 'simple') {
            return strstr($CFG->grade_item_advanced, 'multfactor');
        } elseif ($mode == 'advanced') {
            return false;
        }
    }
}

class grade_edit_tree_column_plusfactor extends grade_edit_tree_column {

    function get_header_cell() {
        return '<th class="header" scope="col">'.get_string('plusfactor', 'grades').helpbutton('plusfactor', 'plusfactor', 'grade', true, false, '', true).'</th>';
    }

    function get_category_cell($category, $levelclass, $params) {
        return '<td class="cell '.$levelclass.'"> - </td>';

    }

    function get_item_cell($item, $params) {
        if (!$item->is_raw_used()) {
            return '<td class="cell">&nbsp;</td>';
        }
        $plusfactor = '<input type="text" size="4" id="plusfactor_'.$item->id.'" name="plusfactor_'.$item->id.'" value="'.format_float($item->plusfactor, 4).'" />';
        return '<td class="cell">'.$plusfactor.'</td>';

    }

    function is_hidden($mode='simple') {
        global $CFG;
        if ($mode == 'simple') {
            return strstr($CFG->grade_item_advanced, 'plusfactor');
        } elseif ($mode == 'advanced') {
            return false;
        }
    }
}

class grade_edit_tree_column_actions extends grade_edit_tree_column {

    function grade_edit_tree_column_actions($params) {

    }

    function get_header_cell() {
        return '<th class="header actions" scope="col">'.get_string('actions').'</th>';
    }

    function get_category_cell($category, $levelclass, $params) {

        if (empty($params['actions'])) {
            error('Array key (actions) missing from 3rd param of grade_edit_tree_column_actions::get_category_actions($category, $levelclass, $params)');
        }

        return '<td class="cell actions '.$levelclass.'">' . $params['actions'] . '</td>';
    }

    function get_item_cell($item, $params) {
        if (empty($params['actions'])) {
            error('Array key (actions) missing from 2nd param of grade_edit_tree_column_actions::get_item_cell($item, $params)');
        }
        return '<td class="cell actions">' . $params['actions'] . '</td>';
    }

    function is_hidden($mode='simple') {
        return false;
    }
}

class grade_edit_tree_column_select extends grade_edit_tree_column {

    function get_header_cell() {
        return '<th class="header selection" scope="col">'.get_string('select').'</th>';
    }

    function get_category_cell($category, $levelclass, $params) {

        if (empty($params['eid'])) {
            error('Array key (eid) missing from 3rd param of grade_edit_tree_column_select::get_category_cell($category, $levelclass, $params)');
        }

        return '<td class="cell last  '.$levelclass.'" style="text-align: center">
                    <span class="actionlink" onclick="togglecheckboxes(\''.$params['eid'].'\', true);">'.get_string('all').'</span><br />
                    <span class="actionlink" onclick="togglecheckboxes(\''.$params['eid'].'\', false);">'.get_string('none').'</span>
                </td>';
    }

    function get_item_cell($item, $params) {
        if (empty($params['itemtype']) || empty($params['eid'])) {
            error('Array key (itemtype or eid) missing from 2nd param of grade_edit_tree_column_select::get_item_cell($item, $params)');
        }
        $itemselect = '';

        if ($params['itemtype'] != 'course' && $params['itemtype'] != 'category') {
            $itemselect = '<input class="itemselect" type="checkbox" name="select_'.$params['eid'].'" onchange="toggleCategorySelector();"/>';
        }
        return '<td class="cell last selection">' . $itemselect . '</td>';
    }

    function is_hidden($mode='simple') {
        return false;
    }
}
?>
