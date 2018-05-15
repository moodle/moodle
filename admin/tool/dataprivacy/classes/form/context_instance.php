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
 * This file contains the form add/update context instance data.
 *
 * @package   tool_dataprivacy
 * @copyright 2018 David Monllao
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_dataprivacy\form;
defined('MOODLE_INTERNAL') || die();

use tool_dataprivacy\api;
use tool_dataprivacy\data_registry;

/**
 * Context instance data form.
 *
 * @package   tool_dataprivacy
 * @copyright 2018 David Monllao
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class context_instance extends \core\form\persistent {

    /**
     * @var The persistent class.
     */
    protected static $persistentclass = 'tool_dataprivacy\\context_instance';

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {
        $this->_form->setDisableShortforms();

        $this->_form->addElement('header', 'contextname', $this->_customdata['contextname']);

        $subjectscope = implode(', ', $this->_customdata['subjectscope']);
        if (empty($subjectscope)) {
            $subjectscope = get_string('noassignedroles', 'tool_dataprivacy');
        }
        $this->_form->addElement('static', 'subjectscope', get_string('subjectscope', 'tool_dataprivacy'), $subjectscope);
        $this->_form->addHelpButton('subjectscope', 'subjectscope', 'tool_dataprivacy');

        $this->add_purpose_category($this->_customdata['context']->contextlevel);

        $this->_form->addElement('hidden', 'contextid');
        $this->_form->setType('contextid', PARAM_INT);

        parent::add_action_buttons(false, get_string('savechanges'));
    }

    /**
     * Adds purpose and category selectors.
     *
     * @param int $contextlevel Apply this context level defaults. False for no defaults.
     * @return null
     */
    protected function add_purpose_category($contextlevel = false) {

        $mform = $this->_form;

        $addcategorytext = $this->get_add_element_content(get_string('addcategory', 'tool_dataprivacy'));
        $categoryselect = $mform->createElement('select', 'categoryid', null, $this->_customdata['categories']);
        $addcategory = $mform->createElement('button', 'addcategory', $addcategorytext, ['data-add-element' => 'category']);
        $mform->addElement('group', 'categorygroup', get_string('category', 'tool_dataprivacy'),
            [$categoryselect, $addcategory], null, false);
        $mform->addHelpButton('categorygroup', 'category', 'tool_dataprivacy');
        $mform->setType('categoryid', PARAM_INT);
        $mform->setDefault('categoryid', 0);

        $addpurposetext = $this->get_add_element_content(get_string('addpurpose', 'tool_dataprivacy'));
        $purposeselect = $mform->createElement('select', 'purposeid', null, $this->_customdata['purposes']);
        $addpurpose = $mform->createElement('button', 'addpurpose', $addpurposetext, ['data-add-element' => 'purpose']);
        $mform->addElement('group', 'purposegroup', get_string('purpose', 'tool_dataprivacy'),
            [$purposeselect, $addpurpose], null, false);
        $mform->addHelpButton('purposegroup', 'purpose', 'tool_dataprivacy');
        $mform->setType('purposeid', PARAM_INT);
        $mform->setDefault('purposeid', 0);

        if (!empty($this->_customdata['currentretentionperiod'])) {
            $mform->addElement('static', 'retention_current', get_string('retentionperiod', 'tool_dataprivacy'),
                $this->_customdata['currentretentionperiod']);
            $mform->addHelpButton('retention_current', 'retentionperiod', 'tool_dataprivacy');
        }
    }

    /**
     * Returns the 'add' label.
     *
     * It depends on the theme in use.
     *
     * @param string $label
     * @return \renderable|string
     */
    private function get_add_element_content($label) {
        global $PAGE, $OUTPUT;

        $bs4 = false;

        $theme = $PAGE->theme;
        if ($theme->name === 'boost') {
            $bs4 = true;
        } else {
            foreach ($theme->parents as $basetheme) {
                if ($basetheme === 'boost') {
                    $bs4 = true;
                }
            }
        }

        if (!$bs4) {
            return $label;
        }
        return $OUTPUT->pix_icon('e/insert', $label);
    }

    /**
     * Returns the customdata array for the provided context instance.
     *
     * @param \context $context
     * @return array
     */
    public static function get_context_instance_customdata(\context $context) {

        $persistent = \tool_dataprivacy\context_instance::get_record_by_contextid($context->id, false);
        if (!$persistent) {
            $persistent = new \tool_dataprivacy\context_instance();
            $persistent->set('contextid', $context->id);
        }

        $purposeoptions = \tool_dataprivacy\output\data_registry_page::purpose_options(
            api::get_purposes()
        );
        $categoryoptions = \tool_dataprivacy\output\data_registry_page::category_options(
            api::get_categories()
        );

        $customdata = [
            'context' => $context,
            'subjectscope' => data_registry::get_subject_scope($context),
            'contextname' => $context->get_context_name(),
            'persistent' => $persistent,
            'purposes' => $purposeoptions,
            'categories' => $categoryoptions,
        ];

        $effectivepurpose = api::get_effective_context_purpose($context);
        if ($effectivepurpose) {

            $customdata['currentretentionperiod'] = self::get_retention_display_text($effectivepurpose, $context->contextlevel,
                $context);

            $customdata['purposeretentionperiods'] = [];
            foreach ($purposeoptions as $optionvalue => $unused) {
                // Get the effective purpose if $optionvalue would be the selected value.
                $purpose = api::get_effective_context_purpose($context, $optionvalue);

                $retentionperiod = self::get_retention_display_text(
                    $purpose,
                    $context->contextlevel,
                    $context
                );
                $customdata['purposeretentionperiods'][$optionvalue] = $retentionperiod;
            }
        }

        return $customdata;
    }

    /**
     * Returns the purpose display text.
     *
     * @param \tool_dataprivacy\purpose $effectivepurpose
     * @param int $retentioncontextlevel
     * @param \context $context The context, just for displaying (filters) purposes.
     * @return string
     */
    protected static function get_retention_display_text(\tool_dataprivacy\purpose $effectivepurpose, $retentioncontextlevel, \context $context) {
        global $PAGE;

        $renderer = $PAGE->get_renderer('tool_dataprivacy');

        $exporter = new \tool_dataprivacy\external\purpose_exporter($effectivepurpose, ['context' => $context]);
        $exportedpurpose = $exporter->export($renderer);

        switch ($retentioncontextlevel) {
            case CONTEXT_COURSE:
            case CONTEXT_MODULE:
            case CONTEXT_BLOCK:
                $str = get_string('effectiveretentionperiodcourse', 'tool_dataprivacy',
                    $exportedpurpose->formattedretentionperiod);
                break;
            case CONTEXT_USER:
                $str = get_string('effectiveretentionperioduser', 'tool_dataprivacy',
                    $exportedpurpose->formattedretentionperiod);
                break;
            default:
                $str = $exportedpurpose->formattedretentionperiod;
        }

        return $str;
    }

}
