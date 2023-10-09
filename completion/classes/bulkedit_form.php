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
 * Bulk edit activity completion form
 *
 * @package     core_completion
 * @copyright   2017 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Bulk edit activity completion form
 *
 * @package     core_completion
 * @copyright   2017 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_completion_bulkedit_form extends core_completion_edit_base_form {
    /** @var cm_info[] list of selected course modules */
    protected $cms = [];
    /** @var array Do not use directly, call $this->get_module_names() */
    protected $_modnames = null;

    /**
     * Returns list of types of selected modules
     *
     * @return array modname=>modfullname
     */
    protected function get_module_names() {
        if ($this->_modnames !== null) {
            return $this->_modnames;
        }
        $this->_modnames = [];
        foreach ($this->cms as $cm) {
            $this->_modnames[$cm->modname] = $cm->modfullname;
        }
        return $this->_modnames;
    }

    /**
     * It will return the course module when $cms has only one course module; otherwise, null will be returned.
     *
     * @return cm_info|null
     */
    protected function get_cm(): ?cm_info {
        if (count($this->cms) === 1) {
            return reset($this->cms);
        }

        // If there are multiple modules, so none will be selected.
        return null;
    }

    /**
     * Returns an instance of component-specific module form for the first selected module
     *
     * @return moodleform_mod|null
     */
    protected function get_module_form() {
        global $CFG, $PAGE;

        if ($this->_moduleform) {
            return $this->_moduleform;
        }

        $cm = reset($this->cms);
        $course = $this->course;
        $modname = $cm->modname;

        $modmoodleform = "$CFG->dirroot/mod/$modname/mod_form.php";
        if (file_exists($modmoodleform)) {
            require_once($modmoodleform);
        } else {
            throw new \moodle_exception('noformdesc');
        }

        list($cmrec, $context, $module, $data, $cw) = get_moduleinfo_data($cm, $course);
        $data->return = 0;
        $data->sr = 0;
        $data->update = $modname;

        // Initialise the form but discard all JS requirements it adds, our form has already added them.
        $mformclassname = 'mod_'.$modname.'_mod_form';
        $PAGE->start_collecting_javascript_requirements();
        $this->_moduleform = new $mformclassname($data, 0, $cmrec, $course);
        $PAGE->end_collecting_javascript_requirements();

        return $this->_moduleform;
    }

    /**
     * Form definition
     */
    public function definition() {
        $this->cms = $this->_customdata['cms'];
        $cm = reset($this->cms); // First selected course module.
        $this->course = $cm->get_course();

        $mform = $this->_form;

        $idx = 0;
        foreach ($this->cms as $cm) {
            $mform->addElement('hidden', 'cmid['.$idx.']', $cm->id);
            $mform->setType('cmid['.$idx.']', PARAM_INT);
            $idx++;
        }

        parent::definition();

        $modform = $this->get_module_form();
        if ($modform) {
            // Pre-fill the form with the current completion rules of the first selected module.
            list($cmrec, $context, $module, $data, $cw) = get_moduleinfo_data($cm->get_course_module_record(), $this->course);
            $data = (array)$data;
            $modform->data_preprocessing($data);
            // Unset fields that will conflict with this form and set data to this form.
            unset($data['cmid']);
            unset($data['id']);
            $this->set_data($data);
        }
    }

    /**
     * Form validation
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files) {
        global $CFG;
        $errors = parent::validation($data, $files);

        // Completion: Don't let them choose automatic completion without turning
        // on some conditions.
        if (array_key_exists('completion', $data) &&
                $data['completion'] == COMPLETION_TRACKING_AUTOMATIC &&
                (!empty($data['completionusegrade']) || !empty($data['completionpassgrade']))) {
            require_once($CFG->libdir.'/gradelib.php');
            $moduleswithoutgradeitem = [];
            foreach ($this->cms as $cm) {
                $item = grade_item::fetch(array('courseid' => $cm->course, 'itemtype' => 'mod',
                    'itemmodule' => $cm->modname, 'iteminstance' => $cm->instance,
                    'itemnumber' => 0));
                if (!$item) {
                    $moduleswithoutgradeitem[] = $cm->get_formatted_name();
                }
            }
            if ($moduleswithoutgradeitem) {
                $errors['completionusegrade'] = get_string('nogradeitem', 'completion', join(', ', $moduleswithoutgradeitem));
            }
        }

        return $errors;
    }
}
