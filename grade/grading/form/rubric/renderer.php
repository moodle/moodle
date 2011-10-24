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
 * @package    gradingform
 * @subpackage rubric
 * @copyright  2011 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

/**
 * Grading method plugin renderer
 */
class gradingform_rubric_renderer {

    /**
     *
     * @param int $mode @see gradingform_rubric_controller
     * @return string
     */
    public function criterion_template($mode, $elementname = '{NAME}', $criterion = null, $levels_str = '{LEVELS}', $value = null) {
        // TODO description format, remark format
        if ($criterion === null || !is_array($criterion) || !array_key_exists('id', $criterion)) {
            $criterion = array('id' => '{CRITERION-id}', 'description' => '{CRITERION-description}', 'sortorder' => '{CRITERION-sortorder}', 'class' => '{CRITERION-class}');
        } else {
            foreach (array('sortorder', 'description', 'class') as $key) {
                // set missing array elements to empty strings to avoid warnings
                if (!array_key_exists($key, $criterion)) {
                    $criterion[$key] = '';
                }
            }
        }
        $criterion_template = html_writer::start_tag('div', array('class' => 'clearfix criterion'. $criterion['class'], 'id' => '{NAME}-{CRITERION-id}'));
        if ($mode == gradingform_rubric_controller::DISPLAY_EDIT_FULL) {
            $criterion_template .= html_writer::start_tag('div', array('class' => 'controls'));
            foreach (array('moveup', 'delete', 'movedown') as $key) {
                $value = get_string('criterion'.$key, 'gradingform_rubric');
                $button = html_writer::empty_tag('input', array('type' => 'submit', 'name' => '{NAME}[{CRITERION-id}]['.$key.']',
                    'id' => '{NAME}-{CRITERION-id}-'.$key, 'value' => $value, 'title' => $value));
                $criterion_template .= html_writer::tag('div', $button, array('class' => $key));
            }
            $criterion_template .= html_writer::end_tag('div'); // .controls
            $criterion_template .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => '{NAME}[{CRITERION-id}][sortorder]', 'value' => $criterion['sortorder']));
            $description = html_writer::tag('textarea', htmlspecialchars($criterion['description']), array('name' => '{NAME}[{CRITERION-id}][description]', 'cols' => '10', 'rows' => '5'));
        } else {
            if ($mode == gradingform_rubric_controller::DISPLAY_EDIT_FROZEN) {
                $criterion_template .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => '{NAME}[{CRITERION-id}][sortorder]', 'value' => $criterion['sortorder']));
                $criterion_template .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => '{NAME}[{CRITERION-id}][description]', 'value' => $criterion['description']));
            }
            $description = $criterion['description'];
        }
        $criterion_template .= html_writer::tag('div', $description, array('class' => 'description', 'id' => '{NAME}-{CRITERION-id}-description'));
        $criterion_template .= html_writer::tag('div', $levels_str, array('class' => 'clearfix levels', 'id' => '{NAME}-{CRITERION-id}-levels'));
        if ($mode == gradingform_rubric_controller::DISPLAY_EDIT_FULL) {
            $value = get_string('criterionaddlevel', 'gradingform_rubric');
            $button = html_writer::empty_tag('input', array('type' => 'submit', 'name' => '{NAME}[{CRITERION-id}][levels][addlevel]',
                'id' => '{NAME}-{CRITERION-id}-levels-addlevel', 'value' => $value, 'title' => $value)); //TODO '{NAME}-{CRITERION-id}-levels-addlevel
            $criterion_template .= html_writer::tag('div', $button, array('class' => 'addlevel'));
        }
        if (isset($value['remark'])) {
            $currentremark = $value['remark'];
        } else {
            $currentremark = '';
        }
        if ($mode == gradingform_rubric_controller::DISPLAY_EVAL) {
            $input = html_writer::tag('textarea', htmlspecialchars($currentremark), array('name' => '{NAME}[criteria][{CRITERION-id}][remark]', 'cols' => '10', 'rows' => '5'));
            $criterion_template .= html_writer::tag('div', $input, array('class' => 'remark'));
        }
        if ($mode == gradingform_rubric_controller::DISPLAY_EVAL_FROZEN) {
            $criterion_template .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => '{NAME}[criteria][{CRITERION-id}][remark]', 'value' => $currentremark));
        }
        if ($mode == gradingform_rubric_controller::DISPLAY_REVIEW) {
            $criterion_template .= html_writer::tag('div', $currentremark, array('class' => 'remark')); // TODO maybe some prefix here like 'Teacher remark:'
        }
        $criterion_template .= html_writer::end_tag('div'); // .criterion

        $criterion_template = str_replace('{NAME}', $elementname, $criterion_template);
        $criterion_template = str_replace('{CRITERION-id}', $criterion['id'], $criterion_template);
        return $criterion_template;
    }

    public function level_template($mode, $elementname = '{NAME}', $criterionid = '{CRITERION-id}', $level = null) {
        // TODO definition format
        if (!isset($level['id'])) {
            $level = array('id' => '{LEVEL-id}', 'definition' => '{LEVEL-definition}', 'score' => '{LEVEL-score}', 'class' => '{LEVEL-class}', 'checked' => false);
        } else {
            foreach (array('score', 'definition', 'class', 'checked') as $key) {
                // set missing array elements to empty strings to avoid warnings
                if (!array_key_exists($key, $level)) {
                    $level[$key] = '';
                }
            }
        }

        // Template for one level within one criterion
        $level_template = html_writer::start_tag('div', array('id' => '{NAME}-{CRITERION-id}-levels-{LEVEL-id}', 'class' => 'clearfix level'. $level['class']));
        if ($mode == gradingform_rubric_controller::DISPLAY_EDIT_FULL) {
            $definition = html_writer::tag('textarea', htmlspecialchars($level['definition']), array('name' => '{NAME}[{CRITERION-id}][levels][{LEVEL-id}][definition]', 'cols' => '10', 'rows' => '4'));
            $score = html_writer::empty_tag('input', array('type' => 'text', 'name' => '{NAME}[{CRITERION-id}][levels][{LEVEL-id}][score]', 'size' => '4', 'value' => $level['score']));
        } else {
            if ($mode == gradingform_rubric_controller::DISPLAY_EDIT_FROZEN) {
                $level_template .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => '{NAME}[{CRITERION-id}][levels][{LEVEL-id}][definition]', 'value' => $level['definition']));
                $level_template .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => '{NAME}[{CRITERION-id}][levels][{LEVEL-id}][score]', 'value' => $level['score']));
            }
            $definition = $level['definition'];
            $score = $level['score'];
        }
        if ($mode == gradingform_rubric_controller::DISPLAY_EVAL) {
            $input = html_writer::empty_tag('input', array('type' => 'radio', 'name' => '{NAME}[criteria][{CRITERION-id}][levelid]', 'value' => $level['id']) +
                    ($level['checked'] ? array('checked' => 'checked') : array()));
            $level_template .= html_writer::tag('div', $input, array('class' => 'radio'));
        }
        if ($mode == gradingform_rubric_controller::DISPLAY_EVAL_FROZEN && $level['checked']) {
            $level_template .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => '{NAME}[criteria][{CRITERION-id}][levelid]', 'value' => $level['id']));
        }
        $score = html_writer::tag('span', $score, array('id' => '{NAME}-{CRITERION-id}-levels-{LEVEL-id}-score'));
        $level_template .= html_writer::tag('div', $definition, array('class' => 'definition', 'id' => '{NAME}-{CRITERION-id}-levels-{LEVEL-id}-definition'));
        $level_template .= html_writer::tag('div', $score. get_string('scorepostfix', 'gradingform_rubric'), array('class' => 'score'));
        if ($mode == gradingform_rubric_controller::DISPLAY_EDIT_FULL) {
            $value = get_string('leveldelete', 'gradingform_rubric');
            $button = html_writer::empty_tag('input', array('type' => 'submit', 'name' => '{NAME}[{CRITERION-id}][levels][{LEVEL-id}][delete]', 'id' => '{NAME}-{CRITERION-id}-levels-{LEVEL-id}-delete', 'value' => $value, 'title' => $value));
            $level_template .= html_writer::tag('div', $button, array('class' => 'delete'));
        }
        $level_template .= html_writer::end_tag('div'); // .level

        $level_template = str_replace('{NAME}', $elementname, $level_template);
        $level_template = str_replace('{CRITERION-id}', $criterionid, $level_template);
        $level_template = str_replace('{LEVEL-id}', $level['id'], $level_template);
        return $level_template;
    }

    protected function rubric_template($mode, $elementname = '{NAME}', $criteria_str = '{CRITERIA}') {
        $classsuffix = ''; // CSS suffix for class of the main div. Depends on the mode
        switch ($mode) {
            case gradingform_rubric_controller::DISPLAY_EDIT_FULL:
                $classsuffix = ' editor editable'; break;
            case gradingform_rubric_controller::DISPLAY_EDIT_FROZEN:
                $classsuffix = ' editor frozen';  break;
            case gradingform_rubric_controller::DISPLAY_PREVIEW:
                $classsuffix = ' editor preview';  break;
            case gradingform_rubric_controller::DISPLAY_EVAL:
                $classsuffix = ' evaluate editable'; break;
            case gradingform_rubric_controller::DISPLAY_EVAL_FROZEN:
                $classsuffix = ' evaluate frozen';  break;
            case gradingform_rubric_controller::DISPLAY_REVIEW:
                $classsuffix = ' review';  break;
        }

        $rubric_template = html_writer::start_tag('div', array('id' => 'rubric-{NAME}', 'class' => 'clearfix form_rubric'.$classsuffix));
        $rubric_template .= html_writer::tag('div', $criteria_str, array('class' => 'criteria', 'id' => '{NAME}-criteria'));
        if ($mode == gradingform_rubric_controller::DISPLAY_EDIT_FULL) {
            $value = get_string('addcriterion', 'gradingform_rubric');
            $input = html_writer::empty_tag('input', array('type' => 'submit', 'name' => '{NAME}[addcriterion]', 'id' => '{NAME}-addcriterion', 'value' => $value, 'title' => $value));
            $rubric_template .= html_writer::tag('div', $input, array('class' => 'addcriterion'));
        }
        $rubric_template .= html_writer::end_tag('div');

        return str_replace('{NAME}', $elementname, $rubric_template);
    }

    /**
     * Returns html code for displaying the rubric in the specified mode
     *
     * @param array $criteria
     * @param int $mode
     * @param string $elementname
     * @param array $values
     * @return string
     */
    public function display_rubric($criteria, $mode, $elementname = null, $values = null) {
        $criteria_str = '';
        $cnt = 0;
        foreach ($criteria as $id => $criterion) {
            $criterion['class'] = $this->get_css_class_suffix($cnt++, sizeof($criteria) -1);
            $levels_str = '';
            $levelcnt = 0;
            if (isset($values['criteria'][$id])) {
                $criterionvalue = $values['criteria'][$id];
            } else {
                $criterionvalue = null;
            }
            foreach ($criterion['levels'] as $levelid => $level) {
                $level['score'] = (float)$level['score']; // otherwise the display will look like 1.00000
                $level['class'] = $this->get_css_class_suffix($levelcnt++, sizeof($criterion['levels']) -1);
                $level['checked'] = (isset($criterionvalue['levelid']) && ((int)$criterionvalue['levelid'] === $levelid));
                if ($level['checked'] && ($mode == gradingform_rubric_controller::DISPLAY_EVAL_FROZEN || $mode == gradingform_rubric_controller::DISPLAY_REVIEW)) {
                    $level['class'] .= ' checked';
                    //in mode DISPLAY_EVAL the class 'checked' will be added by JS if it is enabled. If it is not enabled, the 'checked' class will only confuse
                }
                $levels_str .= $this->level_template($mode, $elementname, $id, $level);
            }
            $criteria_str .= $this->criterion_template($mode, $elementname, $criterion, $levels_str, $criterionvalue);
        }
        return $this->rubric_template($mode, $elementname, $criteria_str);
    }

    /**
     * Help function to return CSS class names for element (first/last/even/odd)
     *
     * @param <type> $cnt
     * @param <type> $maxcnt
     * @return string
     */
    private function get_css_class_suffix($cnt, $maxcnt) {
        $class = '';
        if ($cnt == 0) {
            $class .= ' first';
        }
        if ($cnt == $maxcnt) {
            $class .= ' last';
        }
        if ($cnt%2) {
            $class .= ' odd';
        } else {
            $class .= ' even';
        }
        return $class;
    }

    /**
     * Displays for the student the list of instances or default content if no instances found
     *
     * @param array $instances array of objects of type gradingform_rubric_instance
     * @param string $defaultcontent default string that would be displayed without advanced grading
     * @return string
     */
    public function display_instances($instances, $defaultcontent) {
        if (sizeof($instances)) {
            $rv = html_writer::start_tag('div', array('class' => 'advancedgrade'));
            $idx = 0;
            foreach ($instances as $instance) {
                $rv .= $this->display_instance($instance, $idx++);
            }
            $rv .= html_writer::end_tag('div');
        }
        return $rv. $defaultcontent;
    }

    /**
     * Displays one grading instance
     *
     * @param gradingform_rubric_instance $instance
     * @param int idx unique number of instance on page
     */
    public function display_instance(gradingform_rubric_instance $instance, $idx) {
        $criteria = $instance->get_controller()->get_definition()->rubric_criteria;
        $values = $instance->get_rubric_filling();
        return $this->display_rubric($criteria, gradingform_rubric_controller::DISPLAY_REVIEW, 'rubric'.$idx, $values);
    }
}
