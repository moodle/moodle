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
 * This file contains the marking guide editor element
 *
 * @package    gradingform_guide
 * @copyright  2012 Dan Marsden <dan@danmarsden.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("HTML/QuickForm/input.php");

/**
 * The editor for the marking guide advanced grading plugin.
 *
 * @package    gradingform_guide
 * @copyright  2012 Dan Marsden <dan@danmarsden.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodlequickform_guideeditor extends HTML_QuickForm_input {
    /** @var string help message */
    public $_helpbutton = '';
    /** @var null|false|string stores the result of the last validation: null - undefined, false - no errors,
     * string - error(s) text */
    protected $validationerrors = null;
    /** @var bool if element has already been validated **/
    protected $wasvalidated = false;
    /** @var null|bool If non-submit (JS) button was pressed: null - unknown, true/false - button was/wasn't pressed */
    protected $nonjsbuttonpressed = false;
    /** @var string|false Message to display in front of the editor (that there exist grades on this guide being edited) */
    protected $regradeconfirmation = false;

    /**
     * Constructor
     *
     * @param string $elementname
     * @param string $elementlabel
     * @param array $attributes
     */
    public function moodlequickform_guideeditor($elementname=null, $elementlabel=null, $attributes=null) {
        parent::HTML_QuickForm_input($elementname, $elementlabel, $attributes);
    }

    /**
     * get html for help button
     *
     * @return  string html for help button
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
        $renderer = $PAGE->get_renderer('gradingform_guide');
        $data = $this->prepare_data(null, $this->wasvalidated);
        if (!$this->_flagFrozen) {
            $mode = gradingform_guide_controller::DISPLAY_EDIT_FULL;
            $module = array('name'=>'gradingform_guideeditor',
                'fullpath'=>'/grade/grading/form/guide/js/guideeditor.js',
                'requires' => array('base', 'dom', 'event', 'event-touch', 'escape'),
                'strings' => array(
                    array('confirmdeletecriterion', 'gradingform_guide'),
                    array('clicktoedit', 'gradingform_guide'),
                    array('clicktoeditname', 'gradingform_guide')
            ));
            $PAGE->requires->js_init_call('M.gradingform_guideeditor.init', array(
                array('name' => $this->getName(),
                    'criteriontemplate' => $renderer->criterion_template($mode, $data['options'], $this->getName()),
                    'commenttemplate' => $renderer->comment_template($mode, $this->getName())
                   )),
                true, $module);
        } else {
            // Guide is frozen, no javascript needed.
            if ($this->_persistantFreeze) {
                $mode = gradingform_guide_controller::DISPLAY_EDIT_FROZEN;
            } else {
                $mode = gradingform_guide_controller::DISPLAY_PREVIEW;
            }
        }
        if ($this->regradeconfirmation) {
            if (!isset($data['regrade'])) {
                $data['regrade'] = 1;
            }
            $html .= $renderer->display_regrade_confirmation($this->getName(), $this->regradeconfirmation, $data['regrade']);
        }
        if ($this->validationerrors) {
            $html .= $renderer->notification($this->validationerrors, 'error');
        }
        $html .= $renderer->display_guide($data['criteria'], $data['comments'], $data['options'], $mode, $this->getName());
        return $html;
    }
    /**
     * Prepares the data passed in $_POST:
     * - processes the pressed buttons 'addlevel', 'addcriterion', 'moveup', 'movedown', 'delete' (when JavaScript is disabled)
     *   sets $this->nonjsbuttonpressed to true/false if such button was pressed
     * - if options not passed (i.e. we create a new guide) fills the options array with the default values
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

        $errors = array();
        $return = array('criteria' => array(), 'options' => gradingform_guide_controller::get_default_options(),
            'comments' => array());
        if (!isset($value['criteria'])) {
            $value['criteria'] = array();
            $errors['err_nocriteria'] = 1;
        }
        // If options are present in $value, replace default values with submitted values.
        if (!empty($value['options'])) {
            foreach (array_keys($return['options']) as $option) {
                // Special treatment for checkboxes.
                if (!empty($value['options'][$option])) {
                    $return['options'][$option] = $value['options'][$option];
                } else {
                    $return['options'][$option] = null;
                }

            }
        }

        if (is_array($value)) {
            // For other array keys of $value no special treatmeant neeeded, copy them to return value as is.
            foreach (array_keys($value) as $key) {
                if ($key != 'options' && $key != 'criteria' && $key != 'comments') {
                    $return[$key] = $value[$key];
                }
            }
        }

        // Iterate through criteria.
        $lastaction = null;
        $lastid = null;
        foreach ($value['criteria'] as $id => $criterion) {
            if ($id == 'addcriterion') {
                $id = $this->get_next_id(array_keys($value['criteria']));
                $criterion = array('description' => '');
                $this->nonjsbuttonpressed = true;
            }

            if ($withvalidation && !array_key_exists('delete', $criterion)) {
                if (!strlen(trim($criterion['shortname']))) {
                    $errors['err_noshortname'] = 1;
                    $criterion['error_description'] = true;
                }
                if (!strlen(trim($criterion['maxscore']))) {
                    $errors['err_nomaxscore'] = 1;
                    $criterion['error_description'] = true;
                } else if (!is_numeric($criterion['maxscore']) || $criterion['maxscore'] < 0) {
                    $errors['err_maxscorenotnumeric'] = 1;
                    $criterion['error_description'] = true;
                }
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

        // Add sort order field to criteria.
        $csortorder = 1;
        foreach (array_keys($return['criteria']) as $id) {
            $return['criteria'][$id]['sortorder'] = $csortorder++;
        }

        // Iterate through comments.
        $lastaction = null;
        $lastid = null;
        if (!empty($value['comments'])) {
            foreach ($value['comments'] as $id => $comment) {
                if ($id == 'addcomment') {
                    $id = $this->get_next_id(array_keys($value['comments']));
                    $comment = array('description' => '');
                    $this->nonjsbuttonpressed = true;
                }

                if (array_key_exists('moveup', $comment) || $lastaction == 'movedown') {
                    unset($comment['moveup']);
                    if ($lastid !== null) {
                        $lastcomment = $return['comments'][$lastid];
                        unset($return['comments'][$lastid]);
                        $return['comments'][$id] = $comment;
                        $return['comments'][$lastid] = $lastcomment;
                    } else {
                        $return['comments'][$id] = $comment;
                    }
                    $lastaction = null;
                    $lastid = $id;
                    $this->nonjsbuttonpressed = true;
                } else if (array_key_exists('delete', $comment)) {
                    $this->nonjsbuttonpressed = true;
                } else {
                    if (array_key_exists('movedown', $comment)) {
                        unset($comment['movedown']);
                        $lastaction = 'movedown';
                        $this->nonjsbuttonpressed = true;
                    }
                    $return['comments'][$id] = $comment;
                    $lastid = $id;
                }
            }
            // Add sort order field to comments.
            $csortorder = 1;
            foreach (array_keys($return['comments']) as $id) {
                $return['comments'][$id]['sortorder'] = $csortorder++;
            }
        }
        // Create validation error string (if needed).
        if ($withvalidation) {
            if (count($errors)) {
                $rv = array();
                foreach ($errors as $error => $v) {
                    $rv[] = get_string($error, 'gradingform_guide');
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
     * (buttons 'add criteria', 'add level', 'move up', 'move down', 'add comment')
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
     * Validates that guide has at least one criterion, filled definitions and all criteria
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
     * @see prepare_data()
     *
     * @param array $submitvalues
     * @param boolean $assoc
     * @return array
     */
    public function exportValue(&$submitvalues, $assoc = false) {
        $value =  $this->prepare_data($this->_findValue($submitvalues));
        return $this->_prepareValue($value, $assoc);
    }
}
