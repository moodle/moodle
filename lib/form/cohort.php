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
 * Course selector field.
 *
 * Allows auto-complete ajax searching for cohort.
 *
 * @package   core_form
 * @copyright 2015 Damyon Wiese <damyon@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;
require_once($CFG->libdir . '/form/autocomplete.php');
require_once($CFG->dirroot . '/cohort/lib.php');

/**
 * Form field type for choosing a cohort.
 *
 * Allows auto-complete ajax searching for cohort.
 *
 * @package   core_form
 * @copyright 2016 Damyon Wiese <damyon@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_cohort extends MoodleQuickForm_autocomplete {

    /**
     * @var array $exclude Exclude a list of cohorts from the list (e.g. the current cohort).
     */
    protected $exclude = array();

    /**
     * @var int $contextid The context id to fetch cohorts in.
     */
    protected $contextid = 0;

    /**
     * @var boolean $allowmultiple Allow selecting more than one cohort.
     */
    protected $multiple = false;

    /**
     * @var array $requiredcapabilities Array of extra capabilities to check at the cohort context.
     */
    protected $requiredcapabilities = array();

    /**
     * Constructor
     *
     * @param string $elementname Element name
     * @param mixed $elementlabel Label(s) for an element
     * @param array $options Options to control the element's display
     *                       Valid options are:
     *                       'multiple' - boolean multi select
     *                       'exclude' - array or int, list of course ids to never show
     *                       'requiredcapabilities' - array of capabilities. Uses ANY to combine them.
     */
    public function __construct($elementname = null, $elementlabel = null, $options = array()) {
        if (isset($options['multiple'])) {
            $this->multiple = $options['multiple'];
        }
        if (isset($options['contextid'])) {
            $this->contextid = $options['contextid'];
        } else {
            $this->contextid = context_system::instance()->id;
        }
        if (isset($options['exclude'])) {
            $this->exclude = $options['exclude'];
            if (!is_array($this->exclude)) {
                $this->exclude = array($this->exclude);
            }
        }
        if (isset($options['requiredcapabilities'])) {
            $this->requiredcapabilities = $options['requiredcapabilities'];
        }

        $validattributes = array(
            'ajax' => 'core/form-cohort-selector',
            'data-exclude' => implode(',', $this->exclude),
            'data-contextid' => (int)$this->contextid
        );
        if ($this->multiple) {
            $validattributes['multiple'] = 'multiple';
        }
        if (isset($options['noselectionstring'])) {
            $validattributes['noselectionstring'] = $options['noselectionstring'];
        }
        if (isset($options['placeholder'])) {
            $validattributes['placeholder'] = $options['placeholder'];
        }

        parent::__construct($elementname, $elementlabel, array(), $validattributes);
    }

    /**
     * Set the value of this element. If values can be added or are unknown, we will
     * make sure they exist in the options array.
     * @param string|array $value The value to set.
     * @return boolean
     */
    public function setValue($value) {
        global $DB;
        $values = (array) $value;
        $cohortstofetch = array();

        foreach ($values as $onevalue) {
            if ($onevalue && !$this->optionExists($onevalue) &&
                    ($onevalue !== '_qf__force_multiselect_submission')) {
                array_push($cohortstofetch, $onevalue);
            }
        }

        if (empty($cohortstofetch)) {
            $this->setSelected($values);
            return true;
        }

        list($whereclause, $params) = $DB->get_in_or_equal($cohortstofetch, SQL_PARAMS_NAMED, 'id');

        $list = $DB->get_records_select('cohort', 'id ' . $whereclause, $params, 'name');

        $currentcontext = context_helper::instance_by_id($this->contextid);
        foreach ($list as $cohort) {
            // Make sure we can see the cohort.
            if (!cohort_can_view_cohort($cohort, $currentcontext)) {
                continue;
            }
            $label = format_string($cohort->name, true, ['context' => $currentcontext]);
            $this->addOption($label, $cohort->id);
        }

        $this->setSelected($values);
        return true;
    }
}
