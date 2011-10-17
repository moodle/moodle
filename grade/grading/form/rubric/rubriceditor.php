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

require_once("HTML/QuickForm/input.php");

// register file-related rules
if (class_exists('HTML_QuickForm')) {
    HTML_QuickForm::registerRule('rubriceditorcompleted', 'callback', '_ruleIsCompleted', 'MoodleQuickForm_rubriceditor');
}

class MoodleQuickForm_rubriceditor extends HTML_QuickForm_input {
    public $_helpbutton = '';

    function MoodleQuickForm_rubriceditor($elementName=null, $elementLabel=null, $attributes=null) {
        parent::HTML_QuickForm_input($elementName, $elementLabel, $attributes);
    }

    function getHelpButton() {
        return $this->_helpbutton;
    }

    function getElementTemplateType() {
        return 'default';
    }

    function toHtml() {
        global $PAGE;
        $html = $this->_getTabs();
        $renderer = $PAGE->get_renderer('gradingform_rubric');
        if (!$this->_flagFrozen) {
            $mode = gradingform_rubric_controller::DISPLAY_EDIT_FULL;
            $module = array('name'=>'gradingform_rubriceditor', 'fullpath'=>'/grade/grading/form/rubric/js/rubriceditor.js',
                'strings' => array(array('confirmdeletecriterion', 'gradingform_rubric'), array('confirmdeletelevel', 'gradingform_rubric'),
                    array('criterionempty', 'gradingform_rubric'), array('levelempty', 'gradingform_rubric')
                    ));
            $PAGE->requires->js_init_call('M.gradingform_rubriceditor.init', array(
                array('name' => $this->getName(),
                    'criteriontemplate' => $renderer->criterion_template($mode, $this->getName()),
                    'leveltemplate' => $renderer->level_template($mode, $this->getName())
                   )),
                true, $module);
        } else {
            if ($this->_persistantFreeze) {
                $mode = gradingform_rubric_controller::DISPLAY_EDIT_FROZEN;
            } else {
                $mode = gradingform_rubric_controller::DISPLAY_PREVIEW;
            }
        }
        $html .= $renderer->display_rubric($this->prepare_non_js_data(), $mode, $this->getName());
        return $html;
    }

    function prepare_non_js_data() {
        $return = array();
        $criteria = $this->getValue();
        if (empty($criteria)) {
            $criteria = array();
        }
        $lastaction = null;
        $lastid = null;
        foreach ($criteria as $id => $criterion) {
            if ($id == 'addcriterion') {
                $id = $this->get_next_id(array_keys($criteria));
                $criterion = array('description' => '');
            }
            $levels = array();
            if (array_key_exists('levels', $criterion)) {
                foreach ($criterion['levels'] as $levelid => $level) {
                    if ($levelid == 'addlevel') {
                        $levelid = $this->get_next_id(array_keys($criterion['levels']));
                        $level = array(
                            'definition' => '',
                            'score' => 0,
                        );
                    }
                    if (!array_key_exists('delete', $level)) {
                        $levels[$levelid] = $level;
                    }
                }
            }
            $criterion['levels'] = $levels;
            if (array_key_exists('moveup', $criterion) || $lastaction == 'movedown') {
                unset($criterion['moveup']);
                if ($lastid !== null) {
                    $lastcriterion = $return[$lastid];
                    unset($return[$lastid]);
                    $return[$id] = $criterion;
                    $return[$lastid] = $lastcriterion;
                } else {
                    $return[$id] = $criterion;
                }
                $lastaction = null;
                $lastid = $id;
            } else if (array_key_exists('delete', $criterion)) {
            } else {
                if (array_key_exists('movedown', $criterion)) {
                    unset($criterion['movedown']);
                    $lastaction = 'movedown';
                }
                $return[$id] = $criterion;
                $lastid = $id;
            }
        }
        $csortorder = 1;
        foreach (array_keys($return) as $id) {
            $return[$id]['sortorder'] = $csortorder++;
        }
        return $return;
    }

    function get_next_id($ids) {
        $maxid = 0;
        foreach ($ids as $id) {
            if (preg_match('/^NEWID(\d+)$/', $id, $matches) && ((int)$matches[1]) > $maxid) {
                $maxid = (int)$matches[1];
            }
        }
        return 'NEWID'.($maxid+1);
    }

    function _ruleIsCompleted($elementValue) {
        //echo "_ruleIsCompleted";
        if (is_array($elementValue)) {
            foreach ($elementValue as $criterionid => $criterion) {
                if ($criterionid == 'addcriterion') {
                    return false;
                }
                if (array_key_exists('moveup', $criterion) || array_key_exists('movedown', $criterion) || array_key_exists('delete', $criterion)) {
                    return false;
                }
                if (array_key_exists('levels', $criterion) && is_array($criterion['levels'])) {
                    foreach ($criterion['levels'] as $levelid => $level) {
                        if ($levelid == 'addlevel') {
                            return false;
                        }
                        if (array_key_exists('delete', $level)) {
                            return false;
                        }
                    }
                }
            }
        }
        //TODO check everything is filled
        //echo "<pre>";print_r($elementValue);echo "</pre>";
        return true;
    }

    function onQuickFormEvent($event, $arg, &$caller)
    {
        $name = $this->getName();
        if ($name && $caller->elementExists($name)) {
            $caller->addRule($name, '', 'rubriceditorcompleted');
        }
        return parent::onQuickFormEvent($event, $arg, $caller);
    }

}