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

namespace core_grades\form;

defined('MOODLE_INTERNAL') || die;

use context;
use context_course;
use core_form\dynamic_form;
use grade_category;
use grade_item;
use grade_plugin_return;
use grade_scale;
use moodle_url;

require_once($CFG->dirroot.'/grade/lib.php');

/**
 * Prints the add item gradebook form
 *
 * @copyright 2023 Mathew May <mathew.solutions>
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package core_grades
 */
class add_item extends dynamic_form {

    /** Grade plugin return tracking object.
     * @var object $gpr
     */
    public $gpr;

    /**
     * Helper function to grab the current grade item based on information within the form.
     *
     * @return array
     * @throws \moodle_exception
     */
    private function get_gradeitem(): array {
        $courseid = $this->optional_param('courseid', null, PARAM_INT);
        $id = $this->optional_param('itemid', null, PARAM_INT);

        if ($gradeitem = grade_item::fetch(['id' => $id, 'courseid' => $courseid])) {
            $item = $gradeitem->get_record_data();
            $parentcategory = $gradeitem->get_parent_category();
        } else {
            $gradeitem = new grade_item(['courseid' => $courseid, 'itemtype' => 'manual'], false);
            $item = $gradeitem->get_record_data();
            $parentcategory = grade_category::fetch_course_category($courseid);
        }
        $item->parentcategory = $parentcategory->id;
        $decimalpoints = $gradeitem->get_decimals();

        if ($item->hidden > 1) {
            $item->hiddenuntil = $item->hidden;
            $item->hidden = 0;
        } else {
            $item->hiddenuntil = 0;
        }

        $item->locked = !empty($item->locked);

        $item->grademax   = format_float($item->grademax, $decimalpoints);
        $item->grademin   = format_float($item->grademin, $decimalpoints);

        if ($parentcategory->aggregation == GRADE_AGGREGATE_SUM || $parentcategory->aggregation == GRADE_AGGREGATE_WEIGHTED_MEAN2) {
            $item->aggregationcoef = $item->aggregationcoef == 0 ? 0 : 1;
        } else {
            $item->aggregationcoef = format_float($item->aggregationcoef, 4);
        }
        if ($parentcategory->aggregation == GRADE_AGGREGATE_SUM) {
            $item->aggregationcoef2 = format_float($item->aggregationcoef2 * 100.0);
        }
        $item->cancontrolvisibility = $gradeitem->can_control_visibility();
        return [
            'gradeitem' => $gradeitem,
            'item' => $item
        ];
    }

    /**
     * Form definition
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    protected function definition() {
        global $CFG;
        $courseid = $this->optional_param('courseid', null, PARAM_INT);
        $id = $this->optional_param('itemid', 0, PARAM_INT);
        $gprplugin = $this->optional_param('gpr_plugin', '', PARAM_TEXT);

        if ($gprplugin && ($gprplugin !== 'tree')) {
            $this->gpr = new grade_plugin_return(['type' => 'report', 'plugin' => $gprplugin, 'courseid' => $courseid]);
        } else {
            $this->gpr = new grade_plugin_return(['type' => 'edit', 'plugin' => 'tree', 'courseid' => $courseid]);
        }

        $mform =& $this->_form;

        $local = $this->get_gradeitem();
        $gradeitem = $local['gradeitem'];
        $item = $local['item'];

        // Hidden elements.
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'courseid', $courseid);
        $mform->setType('courseid', PARAM_INT);
        $mform->addElement('hidden', 'itemid', $id);
        $mform->setType('itemid', PARAM_INT);
        $mform->addElement('hidden', 'itemtype', 'manual'); // All new items are manual only.
        $mform->setType('itemtype', PARAM_ALPHA);

        // Visible elements.
        $mform->addElement('text', 'itemname', get_string('itemname', 'grades'));
        $mform->setType('itemname', PARAM_TEXT);

        if (!empty($item->id)) {
            // If grades exist set a message so the user knows why they can not alter the grade type or scale.
            // We could never change the grade type for external items, so only need to show this for manual grade items.
            if ($gradeitem->has_grades() && !$gradeitem->is_external_item()) {
                // Set a message so the user knows why they can not alter the grade type or scale.
                if ($gradeitem->gradetype == GRADE_TYPE_SCALE) {
                    $gradesexistmsg = get_string('modgradecantchangegradetyporscalemsg', 'grades');
                } else {
                    $gradesexistmsg = get_string('modgradecantchangegradetypemsg', 'grades');
                }

                $gradesexisthtml = '<div class=\'alert\'>' . $gradesexistmsg . '</div>';
                $mform->addElement('static', 'gradesexistmsg', '', $gradesexisthtml);
            }
        }

        // Manual grade items cannot have grade type GRADE_TYPE_NONE.
        $mform->addElement('select', 'gradetype', get_string('gradetype', 'grades'), [
            GRADE_TYPE_VALUE => get_string('typevalue', 'grades'),
            GRADE_TYPE_SCALE => get_string('typescale', 'grades'),
            GRADE_TYPE_TEXT => get_string('typetext', 'grades')
        ]);
        $mform->addHelpButton('gradetype', 'gradetype', 'grades');
        $mform->setDefault('gradetype', GRADE_TYPE_VALUE);

        $options = [0 => get_string('usenoscale', 'grades')];
        if ($scales = grade_scale::fetch_all_local($courseid)) {
            foreach ($scales as $scale) {
                $options[$scale->id] = $scale->get_name();
            }
        }
        if ($scales = grade_scale::fetch_all_global()) {
            foreach ($scales as $scale) {
                $options[$scale->id] = $scale->get_name();
            }
        }
        $mform->addElement('select', 'scaleid', get_string('scale'), $options);
        $mform->addHelpButton('scaleid', 'typescale', 'grades');
        $mform->hideIf('scaleid', 'gradetype', 'noteq', GRADE_TYPE_SCALE);

        $mform->addElement('select', 'rescalegrades', get_string('modgraderescalegrades', 'grades'), [
            '' => get_string('choose'),
            'no' => get_string('no'),
            'yes' => get_string('yes')
        ]);
        $mform->addHelpButton('rescalegrades', 'modgraderescalegrades', 'grades');
        $mform->hideIf('rescalegrades', 'gradetype', 'noteq', GRADE_TYPE_VALUE);

        $mform->addElement('float', 'grademax', get_string('grademax', 'grades'));
        $mform->addHelpButton('grademax', 'grademax', 'grades');
        $mform->hideIf('grademax', 'gradetype', 'noteq', GRADE_TYPE_VALUE);

        if (get_config('moodle', 'grade_report_showmin')) {
            $mform->addElement('float', 'grademin', get_string('grademin', 'grades'));
            $mform->addHelpButton('grademin', 'grademin', 'grades');
            $mform->hideIf('grademin', 'gradetype', 'noteq', GRADE_TYPE_VALUE);
        }

        // Hiding.
        if ($item->cancontrolvisibility) {
            $mform->addElement('advcheckbox', 'hidden', get_string('hidden', 'grades'), '', [], [0, 1]);
            $mform->hideIf('hidden', 'hiddenuntil[enabled]', 'checked');
        } else {
            $mform->addElement('static', 'hidden', get_string('hidden', 'grades'),
                get_string('componentcontrolsvisibility', 'grades'));
            // Unset hidden to avoid data override.
            unset($item->hidden);
        }
        $mform->addHelpButton('hidden', 'hidden', 'grades');

        // Locking.
        $mform->addElement('advcheckbox', 'locked', get_string('locked', 'grades'));
        $mform->addHelpButton('locked', 'locked', 'grades');

        // Weight overrides.
        $mform->addElement('advcheckbox', 'weightoverride', get_string('adjustedweight', 'grades'));
        $mform->addHelpButton('weightoverride', 'weightoverride', 'grades');
        $mform->hideIf('weightoverride', 'gradetype', 'eq', GRADE_TYPE_NONE);
        $mform->hideIf('weightoverride', 'gradetype', 'eq', GRADE_TYPE_TEXT);

        // Parent category related settings.
        $mform->addElement('float', 'aggregationcoef2', get_string('weight', 'grades'));
        $mform->addHelpButton('aggregationcoef2', 'weight', 'grades');
        $mform->hideIf('aggregationcoef2', 'weightoverride');
        $mform->hideIf('aggregationcoef2', 'gradetype', 'eq', GRADE_TYPE_NONE);
        $mform->hideIf('aggregationcoef2', 'gradetype', 'eq', GRADE_TYPE_TEXT);

        $options = [];
        $categories = grade_category::fetch_all(['courseid' => $courseid]);

        foreach ($categories as $cat) {
            $cat->apply_forced_settings();
            $options[$cat->id] = $cat->get_name();
        }

        if (count($categories) > 1) {
            $mform->addElement('select', 'parentcategory', get_string('gradecategory', 'grades'), $options);
        }

        $parentcategory = $gradeitem->get_parent_category();
        if (!$parentcategory) {
            // If we do not have an id, we are creating a new grade item.

            // Assign the course category to this grade item.
            $parentcategory = grade_category::fetch_course_category($courseid);
            $gradeitem->parent_category = $parentcategory;
        }

        if ($gradeitem->is_external_item()) {
            // Following items are set up from modules and should not be overrided by user.
            if ($mform->elementExists('grademin')) {
                // The site setting grade_report_showmin may have prevented grademin being added to the form.
                $mform->hardFreeze('grademin');
            }
            $mform->hardFreeze('itemname,gradetype,grademax,scaleid');

            // For external items we can not change the grade type, even if no grades exist, so if it is set to
            // scale, then remove the grademax and grademin fields from the form - no point displaying them.
            if ($gradeitem->gradetype == GRADE_TYPE_SCALE) {
                $mform->removeElement('grademax');
                if ($mform->elementExists('grademin')) {
                    $mform->removeElement('grademin');
                }
            } else { // Not using scale, so remove it.
                $mform->removeElement('scaleid');
            }

            // Always remove the rescale grades element if it's an external item.
            $mform->removeElement('rescalegrades');
        } else if ($gradeitem->has_grades()) {
            // Can't change the grade type or the scale if there are grades.
            $mform->hardFreeze('gradetype, scaleid');

            // If we are using scales then remove the unnecessary rescale and grade fields.
            if ($gradeitem->gradetype == GRADE_TYPE_SCALE) {
                $mform->removeElement('rescalegrades');
                $mform->removeElement('grademax');
                if ($mform->elementExists('grademin')) {
                    $mform->removeElement('grademin');
                }
            } else { // Remove the scale field.
                $mform->removeElement('scaleid');
                // Set the maximum grade to disabled unless a grade is chosen.
                $mform->hideIf('grademax', 'rescalegrades', 'eq', '');
            }
        } else {
            // Remove rescale element if there are no grades.
            $mform->removeElement('rescalegrades');
        }

        // If we wanted to change parent of existing item - we would have to verify there are no circular references in parents!!!
        if ($id > -1 && $mform->elementExists('parentcategory')) {
            $mform->hardFreeze('parentcategory');
        }

        $parentcategory->apply_forced_settings();

        if (!$parentcategory->is_aggregationcoef_used()) {
            if ($mform->elementExists('aggregationcoef')) {
                $mform->removeElement('aggregationcoef');
            }

        } else {
            $coefstring = $gradeitem->get_coefstring();

            if ($coefstring !== '') {
                if ($coefstring == 'aggregationcoefextrasum' || $coefstring == 'aggregationcoefextraweightsum') {
                    // The advcheckbox is not compatible with disabledIf!
                    $coefstring = 'aggregationcoefextrasum';
                    $element =& $mform->createElement('checkbox', 'aggregationcoef', get_string($coefstring, 'grades'));
                } else {
                    $element =& $mform->createElement('text', 'aggregationcoef', get_string($coefstring, 'grades'));
                    $mform->setType('aggregationcoef', PARAM_FLOAT);
                }
                if ($mform->elementExists('parentcategory')) {
                    $mform->insertElementBefore($element, 'parentcategory');
                } else {
                    $mform->insertElementBefore($element, 'aggregationcoef2');
                }
                $mform->addHelpButton('aggregationcoef', $coefstring, 'grades');
            }
            $mform->hideIf('aggregationcoef', 'gradetype', 'eq', GRADE_TYPE_NONE);
            $mform->hideIf('aggregationcoef', 'gradetype', 'eq', GRADE_TYPE_TEXT);
            $mform->hideIf('aggregationcoef', 'parentcategory', 'eq', $parentcategory->id);
        }

        // Remove fields used by natural weighting if the parent category is not using natural weighting.
        // Or if the item is a scale and scales are not used in aggregation.
        if ($parentcategory->aggregation != GRADE_AGGREGATE_SUM
            || (empty($CFG->grade_includescalesinaggregation) && $gradeitem->gradetype == GRADE_TYPE_SCALE)) {
            if ($mform->elementExists('weightoverride')) {
                $mform->removeElement('weightoverride');
            }
            if ($mform->elementExists('aggregationcoef2')) {
                $mform->removeElement('aggregationcoef2');
            }
        }

        if ($category = $gradeitem->get_item_category()) {
            if ($category->aggregation == GRADE_AGGREGATE_SUM) {
                if ($mform->elementExists('gradetype')) {
                    $mform->hardFreeze('gradetype');
                }
                if ($mform->elementExists('grademin')) {
                    $mform->hardFreeze('grademin');
                }
                if ($mform->elementExists('grademax')) {
                    $mform->hardFreeze('grademax');
                }
                if ($mform->elementExists('scaleid')) {
                    $mform->removeElement('scaleid');
                }
            }
        }

        $url = new moodle_url('/grade/edit/tree/item.php', ['id' => $id, 'courseid' => $courseid]);
        $url = $this->gpr->add_url_params($url);
        $url = '<a class="showadvancedform" href="' . $url . '">' . get_string('showmore', 'form') .'</a>';
        $mform->addElement('static', 'advancedform', $url);

        // Add return tracking info.
        $this->gpr->add_mform_elements($mform);

        $this->set_data($item);
    }

    /**
     * Return form context
     *
     * @return context
     */
    protected function get_context_for_dynamic_submission(): context {
        $courseid = $this->optional_param('courseid', null, PARAM_INT);
        return context_course::instance($courseid);
    }

    /**
     * Check if current user has access to this form, otherwise throw exception
     *
     * @return void
     * @throws \required_capability_exception
     */
    protected function check_access_for_dynamic_submission(): void {
        $courseid = $this->optional_param('courseid', null, PARAM_INT);
        require_capability('moodle/grade:manage', context_course::instance($courseid));
    }

    /**
     * Load in existing data as form defaults
     *
     * @return void
     */
    public function set_data_for_dynamic_submission(): void {
        $this->set_data((object)[
            'courseid' => $this->optional_param('courseid', null, PARAM_INT),
            'itemid' => $this->optional_param('itemid', null, PARAM_INT)
        ]);
    }

    /**
     * Returns url to set in $PAGE->set_url() when form is being rendered or submitted via AJAX
     *
     * @return moodle_url
     * @throws \moodle_exception
     */
    protected function get_page_url_for_dynamic_submission(): moodle_url {
        $params = [
            'id' => $this->optional_param('courseid', null, PARAM_INT),
            'itemid' => $this->optional_param('itemid', null, PARAM_INT),
        ];
        return new moodle_url('/grade/edit/tree/index.php', $params);
    }

    /**
     * Process the form submission, used if form was submitted via AJAX
     *
     * @return array
     * @throws \moodle_exception
     */
    public function process_dynamic_submission() {
        $data = $this->get_data();

        $url = $this->gpr->get_return_url('index.php?id=' . $data->courseid);
        $local = $this->get_gradeitem();
        $gradeitem = $local['gradeitem'];
        $item = $local['item'];
        $parentcategory = grade_category::fetch_course_category($data->courseid);

        // Form submission handling.

        // This is a new item, and the category chosen is different than the default category.
        if (empty($gradeitem->id) && isset($data->parentcategory) && $parentcategory->id != $data->parentcategory) {
            $parentcategory = grade_category::fetch(['id' => $data->parentcategory]);
        }

        // If unset, give the aggregation values a default based on parent aggregation method.
        $defaults = grade_category::get_default_aggregation_coefficient_values($parentcategory->aggregation);
        if (!isset($data->aggregationcoef) || $data->aggregationcoef == '') {
            $data->aggregationcoef = $defaults['aggregationcoef'];
        }
        if (!isset($data->weightoverride)) {
            $data->weightoverride = $defaults['weightoverride'];
        }

        if (!isset($data->gradepass) || $data->gradepass == '') {
            $data->gradepass = 0;
        }

        if (!isset($data->grademin) || $data->grademin == '') {
            $data->grademin = 0;
        }

        $hide = empty($data->hiddenuntil) ? 0 : $data->hiddenuntil;
        if (!$hide) {
            $hide = empty($data->hidden) ? 0 : $data->hidden;
        }

        $locked   = empty($data->locked) ? 0 : $data->locked;
        $locktime = empty($data->locktime) ? 0 : $data->locktime;

        $convert = ['grademax', 'grademin', 'aggregationcoef', 'aggregationcoef2'];
        foreach ($convert as $param) {
            if (property_exists($data, $param)) {
                $data->$param = unformat_float($data->$param);
            }
        }
        if (isset($data->aggregationcoef2) && $parentcategory->aggregation == GRADE_AGGREGATE_SUM) {
            $data->aggregationcoef2 = $data->aggregationcoef2 / 100.0;
        } else {
            $data->aggregationcoef2 = $defaults['aggregationcoef2'];
        }

        $oldmin = $gradeitem->grademin;
        $oldmax = $gradeitem->grademax;
        grade_item::set_properties($gradeitem, $data);
        $gradeitem->outcomeid = null;

        // Handle null decimals value.
        if (!property_exists($data, 'decimals') || $data->decimals < 0) {
            $gradeitem->decimals = null;
        }

        if (empty($gradeitem->id)) {
            $gradeitem->itemtype = 'manual'; // All new items to be manual only.
            $gradeitem->insert();

            // Set parent if needed.
            if (isset($data->parentcategory)) {
                $gradeitem->set_parent($data->parentcategory, false);
            }

        } else {
            $gradeitem->update();

            if (!empty($data->rescalegrades) && $data->rescalegrades == 'yes') {
                $newmin = $gradeitem->grademin;
                $newmax = $gradeitem->grademax;
                $gradeitem->rescale_grades_keep_percentage($oldmin, $oldmax, $newmin, $newmax, 'gradebook');
            }
        }

        if ($item->cancontrolvisibility) {
            // Update hiding flag.
            $gradeitem->set_hidden($hide, true);
        }

        $gradeitem->set_locktime($locktime); // Locktime first - it might be removed when unlocking.
        $gradeitem->set_locked($locked);
        return [
            'result' => true,
            'url' => $url,
            'errors' => [],
        ];
    }

    /**
     * Form validation.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files): array {
        $errors = [];
        $local = $this->get_gradeitem();
        $gradeitem = $local['gradeitem'];

        if (isset($data['gradetype']) && $data['gradetype'] == GRADE_TYPE_SCALE) {
            if (empty($data['scaleid'])) {
                $errors['scaleid'] = get_string('missingscale', 'grades');
            }
        }

        // We need to make all the validations related with grademax and grademin
        // with them being correct floats, keeping the originals unmodified for
        // later validations / showing the form back...
        // TODO: Note that once MDL-73994 is fixed we'll have to re-visit this and
        // adapt the code below to the new values arriving here, without forgetting
        // the special case of empties and nulls.
        $grademax = isset($data['grademax']) ? unformat_float($data['grademax']) : null;
        $grademin = isset($data['grademin']) ? unformat_float($data['grademin']) : null;

        if (!is_null($grademin) && !is_null($grademax)) {
            if ($grademax == $grademin || $grademax < $grademin) {
                $errors['grademin'] = get_string('incorrectminmax', 'grades');
                $errors['grademax'] = get_string('incorrectminmax', 'grades');
            }
        }

        // We do not want the user to be able to change the grade type or scale for this item if grades exist.
        if ($gradeitem && $gradeitem->has_grades()) {
            // Check that grade type is set - should never not be set unless form has been modified.
            if (!isset($data['gradetype'])) {
                $errors['gradetype'] = get_string('modgradecantchangegradetype', 'grades');
            } else if ($data['gradetype'] !== $gradeitem->gradetype) { // Check if we are changing the grade type.
                $errors['gradetype'] = get_string('modgradecantchangegradetype', 'grades');
            } else if ($data['gradetype'] == GRADE_TYPE_SCALE) {
                // Check if we are changing the scale - can't do this when grades exist.
                if (isset($data['scaleid']) && ($data['scaleid'] !== $gradeitem->scaleid)) {
                    $errors['scaleid'] = get_string('modgradecantchangescale', 'grades');
                }
            }
        }
        if ($gradeitem) {
            if ($gradeitem->gradetype == GRADE_TYPE_VALUE) {
                if ((((bool) get_config('moodle', 'grade_report_showmin')) &&
                        grade_floats_different($grademin, $gradeitem->grademin)) ||
                    grade_floats_different($grademax, $gradeitem->grademax)) {
                    if ($gradeitem->has_grades() && empty($data['rescalegrades'])) {
                        $errors['rescalegrades'] = get_string('mustchooserescaleyesorno', 'grades');
                    }
                }
            }
        }
        return $errors;
    }
}
