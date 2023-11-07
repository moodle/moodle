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
 * Default activity completion form
 *
 * @package     core_completion
 * @copyright   2017 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_completion_defaultedit_form extends core_completion_edit_base_form {
    /** @var array */
    protected $modules;
    /** @var array */
    protected $_modnames;

    public function __construct(
        $action = null,
        $customdata = null,
        $method = 'post',
        $target = '',
        $attributes = null,
        $editable = true,
        $ajaxformdata = null
    ) {
        $this->modules = $customdata['modules'];
        if ($modname = $this->get_module_name()) {
            // Set the form suffix to the module name so that the form identifier is unique for each module type.
            $this->set_suffix('_' . $modname);
        }

        parent::__construct($action, $customdata, $method, $target, $attributes, $editable, $ajaxformdata);
    }


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
        foreach ($this->modules as $module) {
            $this->_modnames[$module->name] = $module->formattedname;
        }
        return $this->_modnames;
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

        $modnames = array_keys($this->get_module_names());
        $modname = $modnames[0];
        $course = $this->course;

        $modmoodleform = "$CFG->dirroot/mod/$modname/mod_form.php";
        if (file_exists($modmoodleform)) {
            require_once($modmoodleform);
        } else {
            throw new \moodle_exception('noformdesc');
        }

        list($module, $context, $cw, $cmrec, $data) = prepare_new_moduleinfo_data($course, $modname, 0, $this->get_suffix());
        $data->return = 0;
        $data->sr = 0;
        $data->add = $modname;

        // Initialise the form but discard all JS requirements it adds, our form has already added them.
        $mformclassname = 'mod_'.$modname.'_mod_form';
        $PAGE->start_collecting_javascript_requirements();
        $this->_moduleform = new $mformclassname($data, 0, $cmrec, $course);
        $this->_moduleform->set_suffix('_' . $modname);
        $PAGE->end_collecting_javascript_requirements();

        return $this->_moduleform;
    }

    /**
     * Form definition,
     */
    public function definition() {
        $course = $this->_customdata['course'];
        $this->course = is_numeric($course) ? get_course($course) : $course;
        $this->modules = $this->_customdata['modules'];

        $mform = $this->_form;

        foreach ($this->modules as $modid => $module) {
            $mform->addElement('hidden', 'modids['.$modid.']', $modid);
            $mform->setType('modids['.$modid.']', PARAM_INT);
        }

        parent::definition();

        $modform = $this->get_module_form();
        if ($modform) {
            $modnames = array_keys($this->get_module_names());
            $modname = $modnames[0];
            // Pre-fill the form with the current completion rules of the first selected module type.
            list($module, $context, $cw, $cmrec, $data) = prepare_new_moduleinfo_data(
                $this->course,
                $modname,
                0,
                $this->get_suffix()
            );
            $data = (array)$data;
            try {
                $modform->data_preprocessing($data);
            } catch (moodle_exception $e) {
                debugging(
                    'data_preprocessing function of module ' . $modnames[0] .
                    ' should be fixed so it can be shown together with other Default activity completion forms',
                    DEBUG_DEVELOPER
                );
            }
            // Unset fields that will conflict with this form and set data to this form.
            unset($data['cmid']);
            unset($data['modids']);
            unset($data['id']);
            $this->set_data($data);
        }
    }

    /**
     * This method has been overridden because the form identifier must be unique for each module type.
     * Otherwise, the form will display the same data for each module type once it's submitted.
     */
    protected function get_form_identifier() {
        return parent::get_form_identifier() . $this->get_suffix();
    }
}
