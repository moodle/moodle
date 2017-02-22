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
 * File contains definition of class MoodleQuickForm_rubriceditor
 *
 * @package    gradingform_rubric
 * @copyright  2011 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("HTML/QuickForm/input.php");

/**
 * Form element for handling rubric editor
 *
 * The rubric editor is defined as a separate form element. This allows us to render
 * criteria, levels and buttons using the rubric's own renderer. Also, the required
 * Javascript library is included, which processes, on the client, buttons needed
 * for reordering, adding and deleting criteria.
 *
 * If Javascript is disabled when one of those special buttons is pressed, the form
 * element is not validated and, instead of submitting the form, we process button presses.
 *
 * @package    gradingform_rubric
 * @copyright  2011 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_rubriceditor extends HTML_QuickForm_input {
    /** @var string help message */
    public $_helpbutton = '';
    /** @var string|bool stores the result of the last validation: null - undefined, false - no errors, string - error(s) text */
    protected $validationerrors = null;
    /** @var bool if element has already been validated **/
    protected $wasvalidated = false;
    /** @var bool If non-submit (JS) button was pressed: null - unknown, true/false - button was/wasn't pressed */
    protected $nonjsbuttonpressed = false;
    /** @var bool Message to display in front of the editor (that there exist grades on this rubric being edited) */
    protected $regradeconfirmation = false;

    /**
     * Constructor for rubric editor
     *
     * @param string $elementName
     * @param string $elementLabel
     * @param array $attributes
     */
    public function __construct($elementName=null, $elementLabel=null, $attributes=null) {
        parent::__construct($elementName, $elementLabel, $attributes);
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function MoodleQuickForm_rubriceditor($elementName=null, $elementLabel=null, $attributes=null) {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct($elementName, $elementLabel, $attributes);
    }

    /**
     * get html for help button
     *
     * @return string html for help button
     */
    public function getHelpButton() {
        return $this->_helpbutton;
    }

    /**
     * The renderer will take care itself about different display in normal and frozen states
     *
     * @return string
     */
    public function getElementTemplateType() {
        return 'default';
    }

    /**
     * Specifies that confirmation about re-grading needs to be added to this rubric editor.
     * $changelevel is saved in $this->regradeconfirmation and retrieved in toHtml()
     *
     * @see gradingform_rubric_controller::update_or_check_rubric()
     * @param int $changelevel
     */
    public function add_regrade_confirmation($changelevel) {
        $this->regradeconfirmation = $changelevel;
    }

    /**
     * Returns html string to display this element
     *
     * @return string
     */
    public function toHtml() {
        global $PAGE;
        $html = $this->_getTabs();
        $renderer = $PAGE->get_renderer('gradingform_rubric');
        $data = $this->prepare_data(null, $this->wasvalidated);
        if (!$this->_flagFrozen) {
            $mode = gradingform_rubric_controller::DISPLAY_EDIT_FULL;
            $module = array('name'=>'gradingform_rubriceditor', 'fullpath'=>'/grade/grading/form/rubric/js/rubriceditor.js',
                'requires' => array('base', 'dom', 'event', 'event-touch', 'escape'),
                'strings' => array(array('confirmdeletecriterion', 'gradingform_rubric'), array('confirmdeletelevel', 'gradingform_rubric'),
                    array('criterionempty', 'gradingform_rubric'), array('levelempty', 'gradingform_rubric')
                ));
            $PAGE->requires->js_init_call('M.gradingform_rubriceditor.init', array(
                array('name' => $this->getName(),
                    'criteriontemplate' => $renderer->criterion_template($mode, $data['options'], $this->getName()),
                    'leveltemplate' => $renderer->level_template($mode, $data['options'], $this->getName())
                   )),
                true, $module);
        } else {
            // Rubric is frozen, no javascript needed
            if ($this->_persistantFreeze) {
                $mode = gradingform_rubric_controller::DISPLAY_EDIT_FROZEN;
            } else {
                $mode = gradingform_rubric_controller::DISPLAY_PREVIEW;
            }
        }
        if ($this->regradeconfirmation) {
            if (!isset($data['regrade'])) {
                $data['regrade'] = 1;
            }
            $html .= $renderer->display_regrade_confirmation($this->getName(), $this->regradeconfirmation, $data['regrade']);
        }
        if ($this->validationerrors) {
            $html .= html_writer::div($renderer->notification($this->validationerrors));
        }
        $html .= $renderer->display_rubric($data['criteria'], $data['options'], $mode, $this->getName());
        return $html;
    }

    /**
     * Prepares the data passed in $_POST:
     * - processes the pressed buttons 'addlevel', 'addcriterion', 'moveup', 'movedown', 'delete' (when JavaScript is disabled)
     *   sets $this->nonjsbuttonpressed to true/false if such button was pressed
     * - if options not passed (i.e. we create a new rubric) fills the options array with the default values
     * - if options are passed completes the options array with unchecked checkboxes
     * - if $withvalidation is set, adds 'error_xxx' attributes to elements that contain errors and creates an error string
     *   and stores it in $this->validationerrors
     *
     * @param array $value
     * @param boolean $withvalidation whether to enable data validation
     * @return array
     */
    protected function prepare_data($value = null, $withvalidation = false) {
        if (null === $value) {
            $value = $this->getValue();
        }
        if ($this->nonjsbuttonpressed === null) {
            $this->nonjsbuttonpressed = false;
        }
        $totalscore = 0;
        $errors = array();
        $return = array('criteria' => array(), 'options' => gradingform_rubric_controller::get_default_options());
        if (!isset($value['criteria'])) {
            $value['criteria'] = array();
            $errors['err_nocriteria'] = 1;
        }
        // If options are present in $value, replace default values with submitted values
        if (!empty($value['options'])) {
            foreach (array_keys($return['options']) as $option) {
                // special treatment for checkboxes
                if (!empty($value['options'][$option])) {
                    $return['options'][$option] = $value['options'][$option];
                } else {
                    $return['options'][$option] = null;
                }
            }
        }
        if (is_array($value)) {
            // for other array keys of $value no special treatmeant neeeded, copy them to return value as is
            foreach (array_keys($value) as $key) {
                if ($key != 'options' && $key != 'criteria') {
                    $return[$key] = $value[$key];
                }
            }
        }

        // iterate through criteria
        $lastaction = null;
        $lastid = null;
        $overallminscore = $overallmaxscore = 0;
        foreach ($value['criteria'] as $id => $criterion) {
            if ($id == 'addcriterion') {
                $id = $this->get_next_id(array_keys($value['criteria']));
                $criterion = array('description' => '', 'levels' => array());
                $i = 0;
                // when adding new criterion copy the number of levels and their scores from the last criterion
                if (!empty($value['criteria'][$lastid]['levels'])) {
                    foreach ($value['criteria'][$lastid]['levels'] as $lastlevel) {
                        $criterion['levels']['NEWID'.($i++)]['score'] = $lastlevel['score'];
                    }
                } else {
                    $criterion['levels']['NEWID'.($i++)]['score'] = 0;
                }
                // add more levels so there are at least 3 in the new criterion. Increment by 1 the score for each next one
                for ($i=$i; $i<3; $i++) {
                    $criterion['levels']['NEWID'.$i]['score'] = $criterion['levels']['NEWID'.($i-1)]['score'] + 1;
                }
                // set other necessary fields (definition) for the levels in the new criterion
                foreach (array_keys($criterion['levels']) as $i) {
                    $criterion['levels'][$i]['definition'] = '';
                }
                $this->nonjsbuttonpressed = true;
            }
            $levels = array();
            $minscore = $maxscore = null;
            if (array_key_exists('levels', $criterion)) {
                foreach ($criterion['levels'] as $levelid => $level) {
                    if ($levelid == 'addlevel') {
                        $levelid = $this->get_next_id(array_keys($criterion['levels']));
                        $level = array(
                            'definition' => '',
                            'score' => 0,
                        );
                        foreach ($criterion['levels'] as $lastlevel) {
                            if (isset($lastlevel['score'])) {
                                $level['score'] = max($level['score'], ceil(unformat_float($lastlevel['score'])) + 1);
                            }
                        }
                        $this->nonjsbuttonpressed = true;
                    }
                    if (!array_key_exists('delete', $level)) {
                        $score = unformat_float($level['score'], true);
                        if ($withvalidation) {
                            if (!strlen(trim($level['definition']))) {
                                $errors['err_nodefinition'] = 1;
                                $level['error_definition'] = true;
                            }
                            if ($score === null || $score === false) {
                                $errors['err_scoreformat'] = 1;
                                $level['error_score'] = true;
                            }
                        }
                        $levels[$levelid] = $level;
                        if ($minscore === null || $score < $minscore) {
                            $minscore = $score;
                        }
                        if ($maxscore === null || $score > $maxscore) {
                            $maxscore = $score;
                        }
                    } else {
                        $this->nonjsbuttonpressed = true;
                    }
                }
            }
            $totalscore += (float)$maxscore;
            $criterion['levels'] = $levels;
            if ($withvalidation && !array_key_exists('delete', $criterion)) {
                if (count($levels)<2) {
                    $errors['err_mintwolevels'] = 1;
                    $criterion['error_levels'] = true;
                }
                if (!strlen(trim($criterion['description']))) {
                    $errors['err_nodescription'] = 1;
                    $criterion['error_description'] = true;
                }
                $overallmaxscore += $maxscore;
                $overallminscore += $minscore;
            }
            if (array_key_exists('moveup', $criterion) || $lastaction == 'movedown') {
                unset($criterion['moveup']);
                if ($lastid !== null) {
                    $lastcriterion = $return['criteria'][$lastid];
                    unset($return['criteria'][$lastid]);
                    $return['criteria'][$id] = $criterion;
                    $return['criteria'][$lastid] = $lastcriterion;
                } else {
                    $return['criteria'][$id] = $criterion;
                }
                $lastaction = null;
                $lastid = $id;
                $this->nonjsbuttonpressed = true;
            } else if (array_key_exists('delete', $criterion)) {
                $this->nonjsbuttonpressed = true;
            } else {
                if (array_key_exists('movedown', $criterion)) {
                    unset($criterion['movedown']);
                    $lastaction = 'movedown';
                    $this->nonjsbuttonpressed = true;
                }
                $return['criteria'][$id] = $criterion;
                $lastid = $id;
            }
        }

        if ($totalscore <= 0) {
            $errors['err_totalscore'] = 1;
        }

        // add sort order field to criteria
        $csortorder = 1;
        foreach (array_keys($return['criteria']) as $id) {
            $return['criteria'][$id]['sortorder'] = $csortorder++;
        }

        // create validation error string (if needed)
        if ($withvalidation) {
            if (!$return['options']['lockzeropoints']) {
                if ($overallminscore == $overallmaxscore) {
                    $errors['err_novariations'] = 1;
                }
            }
            if (count($errors)) {
                $rv = array();
                foreach ($errors as $error => $v) {
                    $rv[] = get_string($error, 'gradingform_rubric');
                }
                $this->validationerrors = join('<br/ >', $rv);
            } else {
                $this->validationerrors = false;
            }
            $this->wasvalidated = true;
        }
        return $return;
    }

    /**
     * Scans array $ids to find the biggest element ! NEWID*, increments it by 1 and returns
     *
     * @param array $ids
     * @return string
     */
    protected function get_next_id($ids) {
        $maxid = 0;
        foreach ($ids as $id) {
            if (preg_match('/^NEWID(\d+)$/', $id, $matches) && ((int)$matches[1]) > $maxid) {
                $maxid = (int)$matches[1];
            }
        }
        return 'NEWID'.($maxid+1);
    }

    /**
     * Checks if a submit button was pressed which is supposed to be processed on client side by JS
     * but user seem to have disabled JS in the browser.
     * (buttons 'add criteria', 'add level', 'move up', 'move down', etc.)
     * In this case the form containing this element is prevented from being submitted
     *
     * @param array $value
     * @return boolean true if non-submit button was pressed and not processed by JS
     */
    public function non_js_button_pressed($value) {
        if ($this->nonjsbuttonpressed === null) {
            $this->prepare_data($value);
        }
        return $this->nonjsbuttonpressed;
    }

    /**
     * Validates that rubric has at least one criterion, at least two levels within one criterion,
     * each level has a valid score, all levels have filled definitions and all criteria
     * have filled descriptions
     *
     * @param array $value
     * @return string|false error text or false if no errors found
     */
    public function validate($value) {
        if (!$this->wasvalidated) {
            $this->prepare_data($value, true);
        }
        return $this->validationerrors;
    }

    /**
     * Prepares the data for saving
     *
     * @see prepare_data()
     * @param array $submitValues
     * @param boolean $assoc
     * @return array
     */
    public function exportValue(&$submitValues, $assoc = false) {
        $value =  $this->prepare_data($this->_findValue($submitValues));
        return $this->_prepareValue($value, $assoc);
    }
}
