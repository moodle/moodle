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
use grade_outcome;
use grade_plugin_return;
use moodle_url;

require_once($CFG->dirroot.'/grade/lib.php');

/**
 * Prints the add outcome gradebook form.
 *
 * @copyright 2023 Mathew May <mathew.solutions>
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package core_grades
 */
class add_outcome extends dynamic_form {

    /** Grade plugin return tracking object.
     * @var object $gpr
     */
    public $gpr;

    /**
     * Helper function to grab the current grade outcome item based on information within the form.
     *
     * @return array
     * @throws \moodle_exception
     */
    private function get_gradeitem(): array {
        $courseid = $this->optional_param('courseid', null, PARAM_INT);
        $id = $this->optional_param('itemid', null, PARAM_INT);

        if ($gradeitem = grade_item::fetch(['id' => $id, 'courseid' => $courseid])) {
            // Redirect if outcomeid not present.
            if (empty($gradeitem->outcomeid)) {
                $url = new moodle_url('/grade/edit/tree/item.php', ['id' => $id, 'courseid' => $courseid]);
                redirect($this->gpr->add_url_params($url));
            }
            $item = $gradeitem->get_record_data();
            $parentcategory = $gradeitem->get_parent_category();
            if ($item->itemtype == 'mod') {
                $cm = get_coursemodule_from_instance($item->itemmodule, $item->iteminstance, $item->courseid);
                $item->cmid = $cm->id;
            } else {
                $item->cmid = 0;
            }
        } else {
            $gradeitem = new grade_item(['courseid' => $courseid, 'itemtype' => 'manual'], false);
            $item = $gradeitem->get_record_data();
            $parentcategory = grade_category::fetch_course_category($courseid);
        }
        $item->parentcategory = $parentcategory->id;

        if ($item->hidden > 1) {
            $item->hiddenuntil = $item->hidden;
            $item->hidden = 0;
        } else {
            $item->hiddenuntil = 0;
        }

        $item->locked = !empty($item->locked);

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

        // Allow setting of outcomes on module items too.
        $outcomeoptions = [];
        if ($outcomes = grade_outcome::fetch_all_available($courseid)) {
            foreach ($outcomes as $outcome) {
                $outcomeoptions[$outcome->id] = $outcome->get_name();
            }
        }

        // Visible elements.
        $mform->addElement('text', 'itemname', get_string('itemname', 'grades'));
        $mform->addRule('itemname', get_string('required'), 'required', null, 'client');
        $mform->setType('itemname', PARAM_TEXT);

        $mform->addElement('selectwithlink', 'outcomeid', get_string('outcome', 'grades'), $outcomeoptions);
        $mform->addHelpButton('outcomeid', 'outcome', 'grades');
        $mform->addRule('outcomeid', get_string('required'), 'required');

        $options = [0 => get_string('none')];
        if ($coursemods = get_course_mods($courseid)) {
            foreach ($coursemods as $coursemod) {
                if ($mod = get_coursemodule_from_id($coursemod->modname, $coursemod->id)) {
                    $options[$coursemod->id] = format_string($mod->name);
                }
            }
        }
        $mform->addElement('select', 'cmid', get_string('linkedactivity', 'grades'), $options);
        $mform->addHelpButton('cmid', 'linkedactivity', 'grades');
        $mform->setDefault('cmid', 0);

        // Hiding.
        $mform->addElement('checkbox', 'hidden', get_string('hidden', 'grades'));
        $mform->addHelpButton('hidden', 'hidden', 'grades');

        // Locking.
        $mform->addElement('advcheckbox', 'locked', get_string('locked', 'grades'));
        $mform->addHelpButton('locked', 'locked', 'grades');

        // Parent category related settings.
        $mform->addElement('advcheckbox', 'weightoverride', get_string('adjustedweight', 'grades'));
        $mform->addHelpButton('weightoverride', 'weightoverride', 'grades');

        $mform->addElement('text', 'aggregationcoef2', get_string('weight', 'grades'));
        $mform->addHelpButton('aggregationcoef2', 'weight', 'grades');
        $mform->setType('aggregationcoef2', PARAM_RAW);
        $mform->hideIf('aggregationcoef2', 'weightoverride');

        $options = [];
        $coefstring = '';
        $categories = grade_category::fetch_all(['courseid' => $courseid]);
        foreach ($categories as $cat) {
            $cat->apply_forced_settings();
            $options[$cat->id] = $cat->get_name();
            if ($cat->is_aggregationcoef_used()) {
                if ($cat->aggregation == GRADE_AGGREGATE_WEIGHTED_MEAN) {
                    $coefstring = ($coefstring == '' || $coefstring == 'aggregationcoefweight') ?
                        'aggregationcoefweight' : 'aggregationcoef';
                } else if ($cat->aggregation == GRADE_AGGREGATE_WEIGHTED_MEAN2) {
                    $coefstring = ($coefstring == '' || $coefstring == 'aggregationcoefextrasum') ?
                        'aggregationcoefextrasum' : 'aggregationcoef';
                } else if ($cat->aggregation == GRADE_AGGREGATE_EXTRACREDIT_MEAN) {
                    $coefstring = ($coefstring == '' || $coefstring == 'aggregationcoefextraweight') ?
                        'aggregationcoefextraweight' : 'aggregationcoef';
                } else if ($cat->aggregation == GRADE_AGGREGATE_SUM) {
                    $coefstring = ($coefstring == '' || $coefstring == 'aggregationcoefextrasum') ?
                        'aggregationcoefextrasum' : 'aggregationcoef';
                } else {
                    $coefstring = 'aggregationcoef';
                }
            } else {
                $mform->disabledIf('aggregationcoef', 'parentcategory', 'eq', $cat->id);
            }
        }

        if (count($categories) > 1) {
            $mform->addElement('select', 'parentcategory', get_string('gradecategory', 'grades'), $options);
            $mform->disabledIf('parentcategory', 'cmid', 'noteq', 0);
        }

        if ($coefstring !== '') {
            if ($coefstring == 'aggregationcoefextrasum' || $coefstring == 'aggregationcoefextraweightsum') {
                $coefstring = 'aggregationcoefextrasum';
                $mform->addElement('checkbox', 'aggregationcoef', get_string($coefstring, 'grades'));
            } else {
                $mform->addElement('text', 'aggregationcoef', get_string($coefstring, 'grades'));
            }
            $mform->addHelpButton('aggregationcoef', $coefstring, 'grades');
        }

        // Remove the aggregation coef element if not needed.
        if ($gradeitem->is_course_item()) {
            if ($mform->elementExists('parentcategory')) {
                $mform->removeElement('parentcategory');
            }
            if ($mform->elementExists('aggregationcoef')) {
                $mform->removeElement('aggregationcoef');
            }

        } else {
            // If we wanted to change parent of existing item - we would have to verify there are no circular references in parents.
            if ($id > -1 && $mform->elementExists('parentcategory')) {
                $mform->hardFreeze('parentcategory');
            }

            $parentcategory = $gradeitem->get_parent_category();
            if (!$parentcategory) {
                // If we do not have an id, we are creating a new grade item.

                // Assign the course category to this grade item.
                $parentcategory = grade_category::fetch_course_category($courseid);
                $gradeitem->parent_category = $parentcategory;
            }

            $parentcategory->apply_forced_settings();

            if (!$parentcategory->is_aggregationcoef_used() || !$parentcategory->aggregateoutcomes) {
                if ($mform->elementExists('aggregationcoef')) {
                    $mform->removeElement('aggregationcoef');
                }
            } else {
                // Fix label if needed.
                $agg_el =& $mform->getElement('aggregationcoef');
                $aggcoef = '';
                if ($parentcategory->aggregation == GRADE_AGGREGATE_WEIGHTED_MEAN) {
                    $aggcoef = 'aggregationcoefweight';

                } else if ($parentcategory->aggregation == GRADE_AGGREGATE_WEIGHTED_MEAN2) {
                    $aggcoef = 'aggregationcoefextrasum';

                } else if ($parentcategory->aggregation == GRADE_AGGREGATE_EXTRACREDIT_MEAN) {
                    $aggcoef = 'aggregationcoefextraweight';

                } else if ($parentcategory->aggregation == GRADE_AGGREGATE_SUM) {
                    $aggcoef = 'aggregationcoefextrasum';
                }

                if ($aggcoef !== '') {
                    $agg_el->setLabel(get_string($aggcoef, 'grades'));
                    $mform->addHelpButton('aggregationcoef', $aggcoef, 'grades');
                }
            }

            // Remove the natural weighting fields for other aggregations,
            // or when the category does not aggregate outcomes.
            if ($parentcategory->aggregation != GRADE_AGGREGATE_SUM ||
                !$parentcategory->aggregateoutcomes) {
                if ($mform->elementExists('weightoverride')) {
                    $mform->removeElement('weightoverride');
                }
                if ($mform->elementExists('aggregationcoef2')) {
                    $mform->removeElement('aggregationcoef2');
                }
            }
        }

        $url = new moodle_url('/grade/edit/tree/outcomeitem.php', ['id' => $id, 'courseid' => $courseid]);
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
        global $DB;
        $data = $this->get_data();

        $url = $this->gpr->get_return_url('index.php?id=' . $data->courseid);
        $local = $this->get_gradeitem();
        $gradeitem = $local['gradeitem'];
        $item = $local['item'];
        $parentcategory = grade_category::fetch_course_category($data->courseid);

        // Form submission handling.
        // If unset, give the aggregation values a default based on parent aggregation method.
        $defaults = grade_category::get_default_aggregation_coefficient_values($parentcategory->aggregation);
        if (!isset($data->aggregationcoef) || $data->aggregationcoef == '') {
            $data->aggregationcoef = $defaults['aggregationcoef'];
        }
        if (!isset($data->weightoverride)) {
            $data->weightoverride = $defaults['weightoverride'];
        }

        if (property_exists($data, 'calculation')) {
            $data->calculation = grade_item::normalize_formula($data->calculation, $data->courseid);
        }

        $hide = empty($data->hiddenuntil) ? 0 : $data->hiddenuntil;
        if (!$hide) {
            $hide = empty($data->hidden) ? 0 : $data->hidden;
        }

        $locked   = empty($data->locked) ? 0 : $data->locked;
        $locktime = empty($data->locktime) ? 0 : $data->locktime;

        $convert = ['gradepass', 'aggregationcoef', 'aggregationcoef2'];
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

        grade_item::set_properties($gradeitem, $data);

        // Link this outcome item to the user specified linked activity.
        if (empty($data->cmid) || $data->cmid == 0) {
            // Manual item.
            $gradeitem->itemtype     = 'manual';
            $gradeitem->itemmodule   = null;
            $gradeitem->iteminstance = null;
            $gradeitem->itemnumber   = 0;

        } else {
            $params = [$data->cmid];
            $module = $DB->get_record_sql("SELECT cm.*, m.name as modname
                                    FROM {modules} m, {course_modules} cm
                                   WHERE cm.id = ? AND cm.module = m.id ", $params);
            $gradeitem->itemtype     = 'mod';
            $gradeitem->itemmodule   = $module->modname;
            $gradeitem->iteminstance = $module->instance;

            if ($items = grade_item::fetch_all(['itemtype' => 'mod', 'itemmodule' => $gradeitem->itemmodule,
                'iteminstance' => $gradeitem->iteminstance, 'courseid' => $data->courseid])) {
                if (!empty($gradeitem->id) && in_array($gradeitem, $items)) {
                    // No change needed.
                } else {
                    $max = 999;
                    foreach ($items as $item) {
                        if (empty($item->outcomeid)) {
                            continue;
                        }
                        if ($item->itemnumber > $max) {
                            $max = $item->itemnumber;
                        }
                    }
                    $gradeitem->itemnumber = $max + 1;
                }
            } else {
                $gradeitem->itemnumber = 1000;
            }
        }

        // Fix scale used.
        $outcome = grade_outcome::fetch(['id' => $data->outcomeid]);
        $gradeitem->gradetype = GRADE_TYPE_SCALE;
        $gradeitem->scaleid = $outcome->scaleid; // TODO: we might recalculate existing outcome grades when changing scale.

        if (empty($gradeitem->id)) {
            $gradeitem->insert();
            // Move next to activity if adding linked outcome.
            if ($gradeitem->itemtype == 'mod') {
                if ($linkeditem = grade_item::fetch(['itemtype' => 'mod', 'itemmodule' => $gradeitem->itemmodule,
                    'iteminstance' => $gradeitem->iteminstance, 'itemnumber' => 0, 'courseid' => $data->courseid])) {
                    $gradeitem->set_parent($linkeditem->categoryid);
                    $gradeitem->move_after_sortorder($linkeditem->sortorder);
                }
            } else {
                // Set parent if needed.
                if (isset($data->parentcategory)) {
                    $gradeitem->set_parent($data->parentcategory, false);
                }
            }

        } else {
            $gradeitem->update();
        }

        if ($item->cancontrolvisibility) {
            // Update hiding flag.
            $gradeitem->set_hidden($hide, true);
        }

        $gradeitem->set_locktime($locktime); // Locktime first - it might be removed when unlocking.
        $gradeitem->set_locked($locked, false, true);
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
        $item = $local['item'];

        if (!grade_verify_idnumber($gradeitem->id, $item->courseid, $gradeitem)) {
            $errors['idnumber'] = get_string('idnumbertaken');
        }
        return $errors;
    }
}
