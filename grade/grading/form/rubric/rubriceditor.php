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

        // Template for the whole rubric editor
        $classsuffix = $this->_flagFrozen ? 'frozen' : 'editable';
        $rubric_template = html_writer::start_tag('div', array('id' => 'rubriceditor-{NAME}', 'class' => 'clearfix form_rubric editor '.$classsuffix));
        $rubric_template .= html_writer::tag('div', '{CRITERIA}', array('class' => 'criteria', 'id' => '{NAME}-criteria'));
        if (!$this->_flagFrozen) {
            $rubric_template .= html_writer::empty_tag('input', array('type' => 'submit', 'name' => '{NAME}[addcriterion]', 'id' => '{NAME}-addcriterion', 'value' => get_string('addcriterion', 'gradingform_rubric')));
        }
        $rubric_template .= html_writer::end_tag('div');

        // Template for one criterion
        $criterion_template = html_writer::start_tag('div', array('class' => 'clearfix criterion{CRITERION-class}', 'id' => '{NAME}-{CRITERION-id}'));
        if (!$this->_flagFrozen) {
            $criterion_template .= html_writer::start_tag('div', array('class' => 'controls'));
            foreach (array('moveup', 'delete', 'movedown') as $key) {
                $button = html_writer::empty_tag('input', array('type' => 'submit', 'name' => '{NAME}[{CRITERION-id}]['.$key.']',
                    'id' => '{NAME}-{CRITERION-id}-'.$key, 'value' => get_string('criterion'.$key, 'gradingform_rubric')));
                $criterion_template .= html_writer::tag('div', $button, array('class' => $key));
            }
            $criterion_template .= html_writer::end_tag('div'); // .controls
            $criterion_template .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => '{NAME}[{CRITERION-id}][sortorder]', 'value' => '{CRITERION-sortorder}'));
            $description = html_writer::tag('textarea', '{CRITERION-description}', array('name' => '{NAME}[{CRITERION-id}][description]', 'cols' => '10', 'rows' => '5'));
        } else {
            if ($this->_persistantFreeze) {
                $criterion_template .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => '{NAME}[{CRITERION-id}][sortorder]', 'value' => '{CRITERION-sortorder}'));
                $criterion_template .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => '{NAME}[{CRITERION-id}][description]', 'value' => '{CRITERION-description}'));
            }
            $description = '{CRITERION-description}';
        }
        $criterion_template .= html_writer::tag('div', $description, array('class' => 'description', 'id' => '{NAME}-{CRITERION-id}-description'));
        $criterion_template .= html_writer::tag('div', '{LEVELS}', array('class' => 'levels', 'id' => '{NAME}-{CRITERION-id}-levels'));
        if (!$this->_flagFrozen) {
            $button = html_writer::empty_tag('input', array('type' => 'submit', 'name' => '{NAME}[{CRITERION-id}][levels][addlevel]',
                'id' => '{NAME}-{CRITERION-id}-addlevel', 'value' => get_string('criterionaddlevel', 'gradingform_rubric'))); //TODO '{NAME}-{CRITERION-id}-levels-addlevel
            $criterion_template .= html_writer::tag('div', $button, array('class' => 'addlevel'));
        }
        $criterion_template .= html_writer::end_tag('div'); // .criterion

        // Template for one level within one criterion
        $level_template = html_writer::start_tag('div', array('id' => '{NAME}-{CRITERION-id}-levels-{LEVEL-id}', 'class' => 'level{LEVEL-class}'));
        if (!$this->_flagFrozen) {
            $definition = html_writer::tag('textarea', '{LEVEL-definition}', array('name' => '{NAME}[{CRITERION-id}][levels][{LEVEL-id}][definition]', 'cols' => '10', 'rows' => '4'));
            $score = html_writer::empty_tag('input', array('type' => 'text', 'name' => '{NAME}[{CRITERION-id}][levels][{LEVEL-id}][score]', 'size' => '4', 'value' => '{LEVEL-score}'));
        } else {
            if ($this->_persistantFreeze) {
                $level_template .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => '{NAME}[{CRITERION-id}][levels][{LEVEL-id}][definition]', 'value' => '{LEVEL-definition}'));
                $level_template .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => '{NAME}[{CRITERION-id}][levels][{LEVEL-id}][score]', 'value' => '{LEVEL-score}'));
            }
            $definition = '{LEVEL-definition}';
            $score = '{LEVEL-score}';
        }
        $score = html_writer::tag('span', $score, array('id' => '{NAME}-{CRITERION-id}-levels-{LEVEL-id}-score'));
        $level_template .= html_writer::tag('div', $definition, array('class' => 'definition', 'id' => '{NAME}-{CRITERION-id}-levels-{LEVEL-id}-definition'));
        $level_template .= html_writer::tag('div', $score. get_string('scorepostfix', 'gradingform_rubric'), array('class' => 'score'));
        if (!$this->_flagFrozen) {
            $button = html_writer::empty_tag('input', array('type' => 'submit', 'name' => '{NAME}[{CRITERION-id}][levels][{LEVEL-id}][delete]', 'id' => '{NAME}-{CRITERION-id}-levels-{LEVEL-id}-delete', 'value' => get_string('leveldelete', 'gradingform_rubric')));
            $level_template .= html_writer::tag('div', $button, array('class' => 'delete'));
        }
        $level_template .= html_writer::end_tag('div'); // .level

        $criterion_template = str_replace('{NAME}', $this->getName(), $criterion_template);
        $level_template = str_replace('{NAME}', $this->getName(), $level_template);
        $rubric_template = str_replace('{NAME}', $this->getName(), $rubric_template);

        if (!$this->_flagFrozen) {
            $module = array('name'=>'gradingform_rubriceditor', 'fullpath'=>'/grade/grading/form/rubric/js/rubriceditor.js',
                'strings' => array(array('confirmdeletecriterion', 'gradingform_rubric'), array('confirmdeletelevel', 'gradingform_rubric')));
            $PAGE->requires->js_init_call('M.gradingform_rubriceditor.init', array(array('name' => $this->getName(), 'criteriontemplate' => $criterion_template, 'leveltemplate' => $level_template)), true, $module);
        }
        $rubric_html = $rubric_template;
        $criteria = $this->prepare_non_js_data();
        $cnt = 0;
        foreach ($criteria as $id => $criterion) {
            $criterion_html = $criterion_template;
            $levelcnt = 0;
            foreach ($criterion['levels'] as $levelid => $level) {
                $cell_html = $level_template;
                $cell_html = str_replace('{LEVEL-id}', $levelid, $cell_html);
                $cell_html = str_replace('{LEVEL-definition}', htmlspecialchars($level['definition']), $cell_html);
                $cell_html = str_replace('{LEVEL-score}', htmlspecialchars($level['score']), $cell_html);
                $cell_html = str_replace('{LEVEL-class}', $this->get_css_class_suffix($levelcnt++, sizeof($criterion['levels']) -1), $cell_html);
                $criterion_html = str_replace('{LEVELS}', $cell_html.'{LEVELS}', $criterion_html);
            }
            $criterion_html = str_replace('{LEVELS}', '', $criterion_html);
            $criterion_html = str_replace('{CRITERION-id}', $id, $criterion_html);
            $criterion_html = str_replace('{CRITERION-description}', htmlspecialchars($criterion['description']), $criterion_html);
            $criterion_html = str_replace('{CRITERION-sortorder}', htmlspecialchars($criterion['sortorder']), $criterion_html);
            $criterion_html = str_replace('{CRITERION-class}', $this->get_css_class_suffix($cnt++, sizeof($criteria) -1), $criterion_html);
            $rubric_html = str_replace('{CRITERIA}', $criterion_html.'{CRITERIA}', $rubric_html);
        }
        $rubric_html = str_replace('{CRITERIA}', '', $rubric_html);
        $html .= $rubric_html;

        return $html;
    }

    function get_css_class_suffix($cnt, $maxcnt) {
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