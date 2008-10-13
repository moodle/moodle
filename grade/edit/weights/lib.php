<?php  // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards  Martin Dougiamas  http://moodle.com       //
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

function get_tree_json(&$gtree, $element, $totals=false, $gpr) {
    global $CFG, $COURSE;

    $return_array = array();

    $object = $element['object'];
    $eid    = $element['eid'];
    $object->name = $gtree->get_element_header($element, false, false, false);

    $return_array['item'] = $object;

    $return_array['item']->actions = $gtree->get_edit_icon($element, $gpr);
    $return_array['item']->actions .= $gtree->get_calculation_icon($element, $gpr);

    if ($element['type'] == 'item' or ($element['type'] == 'category' and $element['depth'] > 1)) {
        if (element_deletable($element)) {
            $return_array['item']->actions .= '<a href="index.php?id='.$COURSE->id.'&amp;action=delete&amp;eid='
                     . $eid.'&amp;sesskey='.sesskey().'"><img src="'.$CFG->pixpath.'/t/delete.gif" class="iconsmall" alt="'
                     . get_string('delete').'" title="'.get_string('delete').'"/></a>';
        }
        $return_array['item']->actions .= '<a href="index.php?id='.$COURSE->id.'&amp;action=moveselect&amp;eid='
                 . $eid.'&amp;sesskey='.sesskey().'"><img src="'.$CFG->pixpath.'/t/move.gif" class="iconsmall" alt="'
                 . get_string('move').'" title="'.get_string('move').'"/></a>';
    }

    $return_array['item']->actions .= $gtree->get_hiding_icon($element, $gpr);
    $return_array['item']->actions .= $gtree->get_locking_icon($element, $gpr);

    if ($element['type'] == 'category') {
        foreach($element['children'] as $child_el) {
            if (!empty($child_el['object']->itemtype) && ($child_el['object']->itemtype == 'course' || $child_el['object']->itemtype == 'category') && !$totals) {
                continue;
            }
            $return_array['children'][] = get_tree_json($gtree, $child_el, $totals, $gpr);
        }
    }

    return $return_array;
}

function build_html_tree($tree, $element=null, $level=0) {
    global $CFG;

    $options = array(GRADE_AGGREGATE_MEAN             => get_string('aggregatemean', 'grades'),
                     GRADE_AGGREGATE_WEIGHTED_MEAN    => get_string('aggregateweightedmean', 'grades'),
                     GRADE_AGGREGATE_WEIGHTED_MEAN2   => get_string('aggregateweightedmean2', 'grades'),
                     GRADE_AGGREGATE_EXTRACREDIT_MEAN => get_string('aggregateextracreditmean', 'grades'),
                     GRADE_AGGREGATE_MEDIAN           => get_string('aggregatemedian', 'grades'),
                     GRADE_AGGREGATE_MIN              => get_string('aggregatemin', 'grades'),
                     GRADE_AGGREGATE_MAX              => get_string('aggregatemax', 'grades'),
                     GRADE_AGGREGATE_MODE             => get_string('aggregatemode', 'grades'),
                     GRADE_AGGREGATE_SUM              => get_string('aggregatesum', 'grades'));

    $html = '';
    $root = false;

    if (is_null($element)) {
        $html .= '<table cellpadding="5" class="generaltable">
                    <tr>
                        <th class="header name">'.get_string('name').'</th>
                        <th class="header advanced">'.get_string('aggregation', 'grades').'</th>
                        <th class="header advanced">'.get_string('weightorextracredit', 'grades').'</th>
                        <th class="header advanced">'.get_string('range', 'grades').'</th>
                        <th class="header advanced" style="width: 40px">'.get_string('aggregateonlygraded', 'grades').'</th>
                        <th class="header advanced" style="width: 40px">'.get_string('aggregatesubcats', 'grades').'</th>
                        <th class="header advanced" style="width: 40px">'.get_string('aggregateoutcomes', 'grades').'</th>
                        <th class="header advanced">'.get_string('droplow', 'grades').'</th>
                        <th class="header advanced">'.get_string('keephigh', 'grades').'</th>
                        <th class="header advanced">'.get_string('multfactor', 'grades').'</th>
                        <th class="header advanced">'.get_string('plusfactor', 'grades').'</th>
                        <th class="header actions">'.get_string('actions').'</th>
                    </tr>';
        $element = $tree;
        $root = true;
    }


    $id = required_param('id', PARAM_INT);

    if (!empty($element['children'])) { // Grade category
        $category = grade_category::fetch(array('id' => $element['item']->id));
        $item = $category->get_grade_item();

        $script = "window.location='index.php?id=$id&amp;category={$category->id}&amp;aggregationtype='+this.value";
        $aggregation_type = choose_from_menu($options, 'aggregation_type_'.$category->id, $category->aggregation, get_string('choose'), $script, 0, true);

        $onlygradedcheck = ($category->aggregateonlygraded == 1) ? 'checked="checked"' : '';
        $subcatscheck = ($category->aggregatesubcats == 1) ? 'checked="checked"' : '';
        $outcomescheck = ($category->aggregateoutcomes == 1) ? 'checked="checked"' : '';

        $aggregateonlygraded ='<input type="checkbox" id="aggregateonlygraded_'.$category->id.'" name="aggregateonlygraded_'.$category->id.'" '.$onlygradedcheck . ' />';
        $aggregatesubcats = '<input type="checkbox" id="aggregatesubcats_'.$category->id.'" name="aggregatesubcats_'.$category->id.'" ' . $subcatscheck.' />';
        $aggregateoutcomes = '<input type="checkbox" id="aggregateoutcomes_'.$category->id.'" name="aggregateoutcomes_'.$category->id.'" ' . $outcomescheck.' />';

        $droplow = '<input type="text" size="3" id="droplow_'.$category->id.'" name="droplow_'.$category->id.'" value="'.$category->droplow.'" />';
        $keephigh = '<input type="text" size="3" id="keephigh_'.$category->id.'" name="keephigh_'.$category->id.'" value="'.$category->keephigh.'" />';

        $hidden = '<input type="hidden" name="aggregateonlygraded_original_'.$category->id.'" value="'.$category->aggregateonlygraded.'" />';
        $hidden .= '<input type="hidden" name="aggregatesubcats_original_'.$category->id.'" value="'.$category->aggregatesubcats.'" />';
        $hidden .= '<input type="hidden" name="aggregateoutcomes_original_'.$category->id.'" value="'.$category->aggregateoutcomes.'" />';

        // Add aggregation coef input if not a course item and if parent category has correct aggregation type
        $aggcoef_input = get_weight_input($item);

        $html .= '
                <tr class="category">
                  <td class="cell name" style="padding-left:' . ($level * 20)
                  . 'px; background: #DDDDDD url(img/ln.gif) no-repeat scroll ' . (($level - 1) * 20) . 'px 8px">' . $element['item']->name . $hidden . '</td>
                  <td class="cell advanced">' . $aggregation_type . '</td>
                  <td class="cell advanced">' . $aggcoef_input . '</td>
                  <td class="cell advanced">' . $item->get_formatted_range() . '</td>
                  <td class="cell advanced">' . $aggregateonlygraded . '</td>
                  <td class="cell advanced">' . $aggregatesubcats . '</td>
                  <td class="cell advanced">' . $aggregateoutcomes . '</td>
                  <td class="cell advanced">' . $droplow . '</td>
                  <td class="cell advanced">' . $keephigh . '</td>
                  <td class="cell advanced"> - </td>
                  <td class="cell advanced"> - </td>
                  <td class="cell actions">' . $element['item']->actions . '</td>
                </tr>
                ';

        foreach ($element['children'] as $child) {
            $html .= build_html_tree($tree, $child, $level+1);
        }

    } else { // Dealing with a grade item

        $item = grade_item::fetch(array('id' => $element['item']->id));
        $element['type'] = 'item';
        $element['object'] = $item;

        $element['item']->name = grade_structure::get_element_icon($element). $element['item']->name;

        if (!empty($item->itemmodule) && $cm = get_coursemodule_from_instance($item->itemmodule, $item->iteminstance, $item->courseid)) {

            $dir = $CFG->dirroot.'/mod/'.$item->itemmodule;

            if (file_exists($dir.'/grade.php')) {
                $url = $CFG->wwwroot.'/mod/'.$item->itemmodule.'/grade.php?id='.$cm->id;
            } else {
                $url = $CFG->wwwroot.'/mod/'.$item->itemmodule.'/view.php?id='.$cm->id;
            }

            $element['item']->name = '<a href="'.$url.'">'.$element['item']->name.'</a>';
        }

        // Determine aggregation coef element
        $aggcoef_input = get_weight_input($item);
        $multfactor = '<input type="text" size="3" id="multfactor'.$item->id.'" name="multfactor'.$item->id.'" value="'.$item->multfactor.'" />';
        $plusfactor = '<input type="text" size="3" id="plusfactor_'.$item->id.'" name="plusfactor_'.$item->id.'" value="'.$item->plusfactor.'" />';

        $html .= '
                  <tr class="item">
                      <td class="cell name" style="padding-left:' . ($level * 20)
                      . 'px; background: #FFFFFF url(img/ln.gif) no-repeat scroll ' . (($level - 1) * 20) . 'px 8px">' . $element['item']->name . '</td>
                      <td class="cell advanced"> - </td>
                      <td class="cell advanced">' . $aggcoef_input . '</td>
                      <td class="cell advanced">' . $item->get_formatted_range() . '</td>
                      <td class="cell advanced"> - </td>
                      <td class="cell advanced"> - </td>
                      <td class="cell advanced"> - </td>
                      <td class="cell advanced"> - </td>
                      <td class="cell advanced"> - </td>
                      <td class="cell advanced">'.$multfactor.'</td>
                      <td class="cell advanced">'.$plusfactor.'</td>
                      <td class="cell actions">' . $element['item']->actions . '</td>
                  </tr>
                  ';
    }


    if ($root) {
        $html .= "</table>\n";
    }

    return $html;
}

/**
 * Given a grade_item object, returns a labelled input if an aggregation coefficient (weight or extra credit) applies to it.
 * @param grade_item $item
 * @return string HTML
 */
function get_weight_input($item) {
    if ($item->is_course_item()) {
        return '';
    }

    $parent_category = $item->get_parent_category();

    if ($item->is_category_item()) {
        $parent_category = $parent_category->get_parent_category();
    }

    $parent_category->apply_forced_settings();

    if ($parent_category->is_aggregationcoef_used()) {
        $aggcoef = '';

        if ($parent_category->aggregation == GRADE_AGGREGATE_WEIGHTED_MEAN) {
            $aggcoef = 'aggregationcoefweight';
        } elseif ($parent_category->aggregation == GRADE_AGGREGATE_EXTRACREDIT_MEAN) {
            $aggcoef = 'aggregationcoefextra';
        } elseif ($parent_category->aggregation == GRADE_AGGREGATE_SUM) {
            $aggcoef = 'aggregationcoefextrasum';
        }

        if ($aggcoef == 'aggregationcoefweight' || $aggcoef == 'aggregationcoefextra') {
            return '<input type="text" size="6" id="weight_'.$item->id.'" name="weight_'.$item->id.'" value="'.$item->aggregationcoef.'" />';
        } elseif ($aggcoef == 'aggregationcoefextrasum' ) {
            $checked = ($item->aggregationcoef > 0) ? 'checked="checked"' : '';
            $extracredit = ($item->aggregationcoef > 0) ? 1 : 0;

            return '<input type="checkbox" id="extracredit_'.$item->id.'" name="extracredit_'.$item->id.'" ' . "$checked />\n"
                           . '<input type="hidden" name="extracredit_original_'.$item->id.'" value="'.$extracredit.'" />';
        } else {
            return '';
        }
    }
}

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

?>
