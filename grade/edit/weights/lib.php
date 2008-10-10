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

function get_tree_json(&$gtree, $element, $totals=false) {

    $return_array = array();

    $object = $element['object'];
    $eid    = $element['eid'];
    $object->name = $gtree->get_element_header($element, false, false, false);

    $return_array['item'] = $object;

    if ($element['type'] == 'category') {
        foreach($element['children'] as $child_el) {
            if (!empty($child_el['object']->itemtype) && ($child_el['object']->itemtype == 'course' || $child_el['object']->itemtype == 'category') && !$totals) {
                continue;
            }
            $return_array['children'][] = get_tree_json($gtree, $child_el);
        }
    }

    return $return_array;
}

function build_html_tree($tree, $element=null) {
    global $CFG;

    $options = array(GRADE_AGGREGATE_MEAN            =>get_string('aggregatemean', 'grades'),
                     GRADE_AGGREGATE_WEIGHTED_MEAN   =>get_string('aggregateweightedmean', 'grades'),
                     GRADE_AGGREGATE_WEIGHTED_MEAN2  =>get_string('aggregateweightedmean2', 'grades'),
                     GRADE_AGGREGATE_EXTRACREDIT_MEAN=>get_string('aggregateextracreditmean', 'grades'),
                     GRADE_AGGREGATE_MEDIAN          =>get_string('aggregatemedian', 'grades'),
                     GRADE_AGGREGATE_MIN             =>get_string('aggregatemin', 'grades'),
                     GRADE_AGGREGATE_MAX             =>get_string('aggregatemax', 'grades'),
                     GRADE_AGGREGATE_MODE            =>get_string('aggregatemode', 'grades'),
                     GRADE_AGGREGATE_SUM             =>get_string('aggregatesum', 'grades'));

    $html = '';
    $root = false;

    if (is_null($element)) {
        $html .= "<ul>\n";
        $element = $tree;
        $root = true;
    }


    $id = required_param('id', PARAM_INT);

    if (!empty($element['children'])) { // Grade category
        $category = grade_category::fetch(array('id' => $element['item']->id));
        $item = $category->get_grade_item();

        $html .= "<li class=\"category\">\n";
        $script = "window.location='index.php?id=$id&amp;category={$category->id}&amp;aggregationtype='+this.value";
        $aggregation_type = choose_from_menu($options, 'aggregation_type_'.$category->id, $category->aggregation, get_string('choose'), $script, 0, true);

        $onlygradedcheck = ($category->aggregateonlygraded == 1) ? 'checked="checked"' : '';
        $subcatscheck = ($category->aggregatesubcats == 1) ? 'checked="checked"' : '';
        $outcomescheck = ($category->aggregateoutcomes == 1) ? 'checked="checked"' : '';

        $aggregateonlygraded = '<label for="aggregateonlygraded_'.$category->id.'">'
                           . '<img src="'.$CFG->pixpath.'/t/nonempty.gif" class="icon caticon" alt="'.get_string('aggregateonlygraded', 'grades').'" '
                                . 'title="'.get_string('aggregateonlygraded', 'grades').'" /></label>'
                           . '<input type="checkbox" id="aggregateonlygraded_'.$category->id.'" name="aggregateonlygraded_'.$category->id.'" '
                                . $onlygradedcheck . ' />';

        $aggregatesubcats = '<label for="aggregatesubcats_'.$category->id.'">'
                           . '<img src="'.$CFG->pixpath.'/t/sigmaplus.gif" class="icon caticon" alt="'.get_string('aggregatesubcats', 'grades').'" '
                                . 'title="'.get_string('aggregatesubcats', 'grades').'" /></label>'
                           . '<input type="checkbox" id="aggregatesubcats_'.$category->id.'" name="aggregatesubcats_'.$category->id.'" '
                                . $subcatscheck.' />';

        $aggregateoutcomes = '<label for="aggregateoutcomes_'.$category->id.'">'
                           . '<img src="'.$CFG->pixpath.'/t/outcomes.gif" class="icon caticon" alt="'.get_string('aggregateoutcomes', 'grades').'" '
                                . 'title="'.get_string('aggregateoutcomes', 'grades').'" /></label>'
                           . '<input type="checkbox" id="aggregateoutcomes_'.$category->id.'" name="aggregateoutcomes_'.$category->id.'" '
                                . $outcomescheck.' />';

        $hidden = '<input type="hidden" name="aggregateonlygraded_original_'.$category->id.'" value="'.$category->aggregateonlygraded.'" />';
        $hidden .= '<input type="hidden" name="aggregatesubcats_original_'.$category->id.'" value="'.$category->aggregatesubcats.'" />';
        $hidden .= '<input type="hidden" name="aggregateoutcomes_original_'.$category->id.'" value="'.$category->aggregateoutcomes.'" />';

        // Add aggregation coef input if not a course item and if parent category has correct aggregation type
        $aggcoef_input = get_weight_input($item);

        $html .= '<span class="name">' . $element['item']->name . '</span>'
              . $aggregation_type . $aggregateonlygraded . $aggregatesubcats . $aggregateoutcomes . $aggcoef_input . $hidden . "<ul>\n";

        foreach ($element['children'] as $child) {
            $html .= build_html_tree($tree, $child);
        }

        $html .= "</ul>\n";

    } else { // Dealing with a grade item
        $html .= "<li>\n";

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
        $html .= '<span class="gradeitem">' . "\n$aggcoef_input\n{$element['item']->name} (" . $item->get_formatted_range() . ")</span>\n";
    }

    $html .= "</li>\n";

    if ($root) {
        $html .= "</ul>\n";
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
            return '<label class="weight" for="weight_'.$item->id.'">'.get_string($aggcoef, 'grades').'</label>'
                           . '<input type="text" size="6" id="weight_'.$item->id.'" name="weight_'.$item->id.'" value="'.$item->aggregationcoef.'" />';
        } elseif ($aggcoef == 'aggregationcoefextrasum' ) {
            $checked = ($item->aggregationcoef > 0) ? 'checked="checked"' : '';
            $extracredit = ($item->aggregationcoef > 0) ? 1 : 0;

            return '<label class="weight" for="extracredit_'.$item->id.'">'.get_string($aggcoef, 'grades').'</label>'
                           . '<input type="checkbox" id="extracredit_'.$item->id.'" name="extracredit_'.$item->id.'" ' . "$checked />\n"
                           . '<input type="hidden" name="extracredit_original_'.$item->id.'" value="'.$extracredit.'" />';
        } else {
            return '';
        }
    }
}
?>
