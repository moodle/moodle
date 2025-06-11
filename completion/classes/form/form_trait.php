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

namespace core_completion\form;

use core_grades\component_gradeitems;
use cm_info;

/**
 * Completion trait helper, with methods to add completion elements and validate them.
 *
 * @package    core_completion
 * @since      Moodle 4.3
 * @copyright  2023 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait form_trait {

    /** @var string The suffix to be added to the completion elements when creating them (for example, 'completion_assign'). */
    protected $suffix = '';

    /**
     * Called during validation.
     * Override this method to indicate, based on the data, whether a custom completion rule is selected or not.
     *
     * @param array $data Input data (not yet validated)
     * @return bool True if one or more rules are enabled; false if none are.
     */
    abstract protected function completion_rule_enabled($data);

    /**
     * Add completion elements to the form and return the list of element ids.
     *
     * @return array Array of string IDs of added items, empty array if none
     */
    abstract protected function add_completion_rules();

    /**
     * Get the form associated to this class, where the completion elements will be added.
     * This method must be overriden by the class using this trait if it doesn't include a _form property.
     *
     * @return \MoodleQuickForm
     * @throws \coding_exception If the class does not have a _form property.
     */
    protected function get_form(): \MoodleQuickForm {
        if (property_exists($this, '_form')) {
            return $this->_form;
        }

        throw new \coding_exception('This class does not have a _form property. Please, add it or override the get_form() method.');
    }

    /**
     * Set the suffix to be added to the completion elements when creating them (for example, 'completion_assign').
     *
     * @param string $suffix
     */
    public function set_suffix(string $suffix): void {
        $this->suffix = $suffix;
    }

    /**
     * Get the suffix to be added to the completion elements when creating them (for example, 'completion_assign').
     *
     * @return string The suffix
     */
    public function get_suffix(): string {
        return $this->suffix;
    }

    /**
     * Add completion elements to the form.
     *
     * @param string|null $modname The module name (for example, 'assign'). If null and form is moodleform_mod, the parameters are
     *                             overriden with the expected values from the form.
     * @param bool $supportviews True if the module supports views and false otherwise.
     * @param bool $supportgrades True if the module supports grades and false otherwise.
     * @param bool $rating True if the rating feature is enabled and false otherwise.
     * @param int|null $courseid Course where to add completion elements.
     * @throws \coding_exception If the form is not moodleform_mod and $modname is null.
     */
    protected function add_completion_elements(
        ?string $modname = null,
        bool $supportviews = false,
        bool $supportgrades = false,
        bool $rating = false,
        ?int $courseid = null
    ): void {
        global $SITE;

        $mform = $this->get_form();
        if ($modname === null) {
            if ($this instanceof \moodleform_mod) {
                // By default, all the modules can be initiatized with the same parameters.
                $modname = $this->_modname;
                $supportviews = plugin_supports('mod', $modname, FEATURE_COMPLETION_TRACKS_VIEWS, false);
                $supportgrades = plugin_supports('mod', $modname, FEATURE_GRADE_HAS_GRADE, false);
                $rating = $this->_features->rating;
            } else {
                throw new \coding_exception('You must specify the modname parameter if you are not using a moodleform_mod.');
            }
        }

        // Unlock button if people have completed it. The button will be removed later in definition_after_data if they haven't.
        // The unlock buttons don't need suffix because they are only displayed in the module settings page.
        $mform->addElement('submit', 'unlockcompletion', get_string('unlockcompletion', 'completion'));
        $mform->registerNoSubmitButton('unlockcompletion');
        $mform->addElement('hidden', 'completionunlocked', 0);
        $mform->setType('completionunlocked', PARAM_INT);

        $trackingdefault = COMPLETION_TRACKING_NONE;

        // Get the sufix to add to the completion elements name.
        $suffix = $this->get_suffix();

        $completionel = 'completion' . $suffix;
        $mform->addElement(
            'radio',
            $completionel,
            '',
            get_string('completion_none', 'completion'),
            COMPLETION_TRACKING_NONE,
            ['class' => 'left-indented']
        );
        $mform->addElement(
            'radio',
            $completionel,
            '',
            get_string('completion_manual', 'completion'),
            COMPLETION_TRACKING_MANUAL,
            ['class' => 'left-indented']
        );

        $allconditionsel = 'allconditions' . $suffix;
        $allconditions = $mform->createElement(
            'static',
            $allconditionsel,
            '',
            get_string('allconditions', 'completion'));

        $conditionsgroupel = 'conditionsgroup' . $suffix;
        $mform->addGroup([$allconditions], $conditionsgroupel, '', null, false);
        $mform->hideIf($conditionsgroupel, $completionel, 'ne', COMPLETION_TRACKING_AUTOMATIC);

        $mform->setType($completionel, PARAM_INT);
        $mform->setDefault($completionel, COMPLETION_TRACKING_NONE);

        // Automatic completion once you view it.
        if ($supportviews) {
            $completionviewel = 'completionview' . $suffix;
            $mform->addElement(
                'checkbox',
                $completionviewel,
                '',
                get_string('completionview_desc', 'completion')
            );
            $mform->hideIf($completionviewel, $completionel, 'ne', COMPLETION_TRACKING_AUTOMATIC);
            // Check by default if automatic completion tracking is set.
            if ($trackingdefault == COMPLETION_TRACKING_AUTOMATIC) {
                $mform->setDefault($completionviewel, 1);
            }
        }

        // Automatic completion according to module-specific rules.
        $customcompletionelements = $this->add_completion_rules();
        if (property_exists($this, '_customcompletionelements')) {
            $this->_customcompletionelements = $customcompletionelements;
        }

        if ($customcompletionelements !== null) {
            foreach ($customcompletionelements as $element) {
                $mform->hideIf($element, $completionel, 'ne', COMPLETION_TRACKING_AUTOMATIC);
            }
        }

        // If the activity supports grading, the grade elements must be added.
        if ($supportgrades) {
            $this->add_completiongrade_elements($modname, $rating);
        }

        $autocompletionpossible = $supportviews || $supportgrades || (count($customcompletionelements) > 0);

        // Automatic option only appears if possible.
        if ($autocompletionpossible) {
            $automatic = $mform->createElement(
                'radio',
                $completionel,
                '',
                get_string('completion_automatic', 'completion'),
                COMPLETION_TRACKING_AUTOMATIC,
                ['class' => 'left-indented']
            );
            $mform->insertElementBefore($automatic, $conditionsgroupel);
        }

        // Completion expected at particular date? (For progress tracking).
        // We don't show completion expected at site level default completion.
        if ($courseid != $SITE->id) {
            $completionexpectedel = 'completionexpected' . $suffix;
            $mform->addElement('date_time_selector', $completionexpectedel, get_string('completionexpected', 'completion'),
                ['optional' => true]);
            $a = get_string('pluginname', $modname);
            $mform->addHelpButton($completionexpectedel, 'completionexpected', 'completion', '', false, $a);
            $mform->hideIf($completionexpectedel, $completionel, 'eq', COMPLETION_TRACKING_NONE);
        }
    }

    /**
     * Add completion grade elements to the form.
     *
     * @param string $modname The name of the module (for example, 'assign').
     * @param bool $rating True if the rating feature is enabled and false otherwise.
     */
    protected function add_completiongrade_elements(
        string $modname,
        bool $rating = false
    ): void {
        $mform = $this->get_form();

        // Get the sufix to add to the completion elements name.
        $suffix = $this->get_suffix();

        $completionel = 'completion' . $suffix;
        $completionelementexists = $mform->elementExists($completionel);
        $component = "mod_{$modname}";
        $itemnames = component_gradeitems::get_itemname_mapping_for_component($component);

        $indentation = ['parentclass' => 'ms-2'];
        $receiveagradeel = 'receiveagrade' . $suffix;
        $completionusegradeel = 'completionusegrade' . $suffix;
        $completionpassgradeel = 'completionpassgrade' . $suffix;

        if (count($itemnames) === 1) {
            // Only one gradeitem in this activity.
            // We use the completionusegrade field here.
            $mform->addElement(
                'checkbox',
                $completionusegradeel,
                '',
                get_string('completionusegrade_desc', 'completion')
            );

            // Complete if the user has reached any grade.
            $mform->addElement(
                'radio',
                $completionpassgradeel,
                null,
                get_string('completionanygrade_desc', 'completion'),
                0,
                $indentation
            );

            // Complete if the user has reached the pass grade.
            $mform->addElement(
                'radio',
                $completionpassgradeel,
                null,
                get_string('completionpassgrade_desc', 'completion'),
                1,
                $indentation
            );
            $mform->hideIf($completionpassgradeel, $completionusegradeel, 'notchecked');

            if ($completionelementexists) {
                $mform->hideIf($completionpassgradeel, $completionel, 'ne', COMPLETION_TRACKING_AUTOMATIC);
                $mform->hideIf($completionusegradeel, $completionel, 'ne', COMPLETION_TRACKING_AUTOMATIC);
            }

            // The disabledIf logic differs between ratings and other grade items due to different field types.
            if ($rating) {
                // If using the rating system, there is no grade unless ratings are enabled.
                $mform->hideIf($completionusegradeel, 'assessed', 'eq', 0);
                $mform->hideIf($completionusegradeel, 'assessed', 'eq', 0);
            } else {
                // All other field types use the '$gradefieldname' field's modgrade_type.
                $itemnumbers = array_keys($itemnames);
                $itemnumber = array_shift($itemnumbers);
                $gradefieldname = component_gradeitems::get_field_name_for_itemnumber($component, $itemnumber, 'grade');
                $mform->hideIf($completionusegradeel, "{$gradefieldname}[modgrade_type]", 'eq', 'none');
                $mform->hideIf($completionusegradeel, "{$gradefieldname}[modgrade_type]", 'eq', 'none');
            }
        } else if (count($itemnames) > 1) {
            // There are multiple grade items in this activity.
            // Show them all.
            $options = [];
            foreach ($itemnames as $itemnumber => $itemname) {
                $options[$itemnumber] = get_string("grade_{$itemname}_name", $component);
            }

            $group = [$mform->createElement(
                'checkbox',
                $completionusegradeel,
                null,
                get_string('completionusegrade_desc', 'completion')
            )];
            $completiongradeitemnumberel = 'completiongradeitemnumber' . $suffix;
            $group[] =& $mform->createElement(
                'select',
                $completiongradeitemnumberel,
                '',
                $options
            );
            $receiveagradegroupel = 'receiveagradegroup' . $suffix;
            $mform->addGroup($group, $receiveagradegroupel, '', [' '], false);
            if ($completionelementexists) {
                $mform->hideIf($completionusegradeel, $completionel, 'ne', COMPLETION_TRACKING_AUTOMATIC);
                $mform->hideIf($receiveagradegroupel, $completionel, 'ne', COMPLETION_TRACKING_AUTOMATIC);
            }
            $mform->hideIf($completiongradeitemnumberel, $completionusegradeel, 'notchecked');

            // Complete if the user has reached any grade.
            $mform->addElement(
                'radio',
                $completionpassgradeel,
                null,
                get_string('completionanygrade_desc', 'completion'),
                0,
                $indentation
            );
            // Complete if the user has reached the pass grade.
            $mform->addElement(
                'radio',
                $completionpassgradeel,
                null,
                get_string('completionpassgrade_desc', 'completion'),
                1,
                $indentation
            );
            $mform->hideIf($completionpassgradeel, $completionusegradeel, 'notchecked');

            if ($completionelementexists) {
                $mform->hideIf($completiongradeitemnumberel, $completionel, 'ne', COMPLETION_TRACKING_AUTOMATIC);
                $mform->hideIf($completionpassgradeel, $completionel, 'ne', COMPLETION_TRACKING_AUTOMATIC);
            }
        }

        $customgradingelements = $this->add_completiongrade_rules();
        if (property_exists($this, '_customcompletionelements')) {
            $this->_customcompletionelements = array_merge($this->_customcompletionelements, $customgradingelements);
        }
        if ($completionelementexists) {
            foreach ($customgradingelements as $customgradingelement) {
                $mform->hideIf($customgradingelement, $completionel, 'ne', COMPLETION_TRACKING_AUTOMATIC);
            }
        }
    }

    /**
     * Add completion grading elements to the form and return the list of element ids.
     *
     * @return array Array of string IDs of added items, empty array if none
     */
    abstract public function add_completiongrade_rules(): array;

    /**
     * Perform some extra validation for completion settings.
     *
     * @param array $data Array of ["fieldname" => value] of submitted data.
     * @return array List of ["element_name" => "error_description"] if there are errors or an empty array if everything is OK.
     */
    protected function validate_completion(array $data): array {
        $errors = [];

        // Get the sufix to add to the completion elements name.
        $suffix = $this->get_suffix();

        $completionel = 'completion' . $suffix;
        // Completion: Don't let them choose automatic completion without turning on some conditions.
        $automaticcompletion = array_key_exists($completionel, $data) && $data[$completionel] == COMPLETION_TRACKING_AUTOMATIC;
        // Ignore this check when completion settings are locked, as the options are then disabled.
        // The unlock buttons don't need suffix because they are only displayed in the module settings page.
        $automaticcompletion = $automaticcompletion && !empty($data['completionunlocked']);
        if ($automaticcompletion) {
            // View to complete.
            $completionviewel = 'completionview' . $suffix;
            $rulesenabled = !empty($data[$completionviewel]);

            // Use grade to complete (only one grade item).
            $completionusegradeel = 'completionusegrade' . $suffix;
            $completionpassgradeel = 'completionpassgrade' . $suffix;
            $rulesenabled = $rulesenabled || !empty($data[$completionusegradeel]) || !empty($data[$completionpassgradeel]);

            // Use grade to complete (specific grade item).
            $completiongradeitemnumberel = 'completiongradeitemnumber' . $suffix;
            if (!$rulesenabled && isset($data[$completiongradeitemnumberel])) {
                $rulesenabled = $data[$completiongradeitemnumberel] != '';
            }

            // Module-specific completion rules.
            $rulesenabled = $rulesenabled || $this->completion_rule_enabled($data);

            if (!$rulesenabled) {
                // No rules are enabled. Can't set automatically completed without rules.
                $errors[$completionel] = get_string('badautocompletion', 'completion');
            }
        }

        return $errors;
    }

    /**
     * It should be called from the definition_after_data() to setup the completion settings in the form.
     *
     * @param cm_info|null $cm The course module associated to this form.
     */
    protected function definition_after_data_completion(?cm_info $cm = null): void {
        global $COURSE, $SITE;
        $mform = $this->get_form();

        $completion = new \completion_info($COURSE);
        // We use $SITE course for site default activity completion,
        // so users could set default values regardless of whether completion is enabled or not.".
        if ($completion->is_enabled() || $COURSE->id == $SITE->id) {
            $suffix = $this->get_suffix();

            // If anybody has completed the activity, these options will be 'locked'.
            // We use $SITE course for site default activity completion, so we don't need any unlock button.
            $completedcount = (empty($cm) || $COURSE->id == $SITE->id) ? 0 : $completion->count_user_data($cm);
            $freeze = false;
            if (!$completedcount) {
                // The unlock buttons don't need suffix because they are only displayed in the module settings page.
                if ($mform->elementExists('unlockcompletion')) {
                    $mform->removeElement('unlockcompletion');
                }
                // Automatically set to unlocked. Note: this is necessary in order to make it recalculate completion once
                // the option is changed, maybe someone has completed it now.
                if ($mform->elementExists('completionunlocked')) {
                    $mform->getElement('completionunlocked')->setValue(1);
                }
            } else {
                // Has the element been unlocked, either by the button being pressed in this request, or the field already
                // being set from a previous one?
                if ($mform->exportValue('unlockcompletion') || $mform->exportValue('completionunlocked')) {
                    // Yes, add in warning text and set the hidden variable.
                    $completedunlockedel = $mform->createElement(
                        'static',
                        'completedunlocked',
                        get_string('completedunlocked', 'completion'),
                        get_string('completedunlockedtext', 'completion')
                    );
                    $mform->insertElementBefore($completedunlockedel, 'unlockcompletion');
                    $mform->removeElement('unlockcompletion');
                    $mform->getElement('completionunlocked')->setValue(1);
                } else {
                    // No, add in the warning text with the count (now we know it) before the unlock button.
                    $completedwarningel = $mform->createElement(
                        'static',
                        'completedwarning',
                        get_string('completedwarning', 'completion'),
                        get_string('completedwarningtext', 'completion', $completedcount)
                    );
                    $mform->insertElementBefore($completedwarningel, 'unlockcompletion');
                    $freeze = true;
                }
            }

            if ($freeze) {
                $completionel = 'completion' . $suffix;
                $mform->freeze($completionel);
                $completionviewel = 'completionview' . $suffix;
                if ($mform->elementExists($completionviewel)) {
                    // Don't use hardFreeze or checkbox value gets lost.
                    $mform->freeze($completionviewel);
                }
                $completionusegradeel = 'completionusegrade' . $suffix;
                if ($mform->elementExists($completionusegradeel)) {
                    $mform->freeze($completionusegradeel);
                }
                $completionpassgradeel = 'completionpassgrade' . $suffix;
                if ($mform->elementExists($completionpassgradeel)) {
                    $mform->freeze($completionpassgradeel);

                    // Has the completion pass grade completion criteria been set? If it has, then we shouldn't change
                    // any of the modules "gradepass" type fields.
                    if ($mform->exportValue($completionpassgradeel)) {

                        // Some modules define separate "gradepass" fields for each of their grade items.
                        $gradepassfieldels = array_merge(['gradepass'], array_map(
                            fn(string $gradeitem) => "{$gradeitem}gradepass",
                            component_gradeitems::get_itemname_mapping_for_component("mod_{$this->_modname}"),
                        ));

                        foreach ($gradepassfieldels as $gradepassfieldel) {
                            if ($mform->elementExists($gradepassfieldel)) {
                                $mform->freeze($gradepassfieldel);
                            }
                        }
                    }
                }
                $completiongradeitemnumberel = 'completiongradeitemnumber' . $suffix;
                if ($mform->elementExists($completiongradeitemnumberel)) {
                    $mform->freeze($completiongradeitemnumberel);
                }
                if (property_exists($this, '_customcompletionelements')) {
                    $mform->freeze($this->_customcompletionelements);
                }
            }
        }
    }
}
