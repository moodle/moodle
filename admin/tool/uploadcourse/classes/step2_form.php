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
 * Bulk course upload step 2.
 *
 * @package    tool_uploadcourse
 * @copyright  2011 Piers Harding
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/lib.php');

/**
 * Specify course upload details.
 *
 * @package    tool_uploadcourse
 * @copyright  2011 Piers Harding
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_uploadcourse_step2_form extends tool_uploadcourse_base_form {

    /**
     * The standard form definiton.
     * @return void.
     */
    public function definition () {
        global $CFG;

        $mform   = $this->_form;
        $data    = $this->_customdata['data'];
        $courseconfig = get_config('moodlecourse');

        // Import options.
        $this->add_import_options();

        // Course options.
        $mform->addElement('header', 'courseoptionshdr', get_string('courseprocess', 'tool_uploadcourse'));
        $mform->setExpanded('courseoptionshdr', true);

        $mform->addElement('text', 'options[shortnametemplate]', get_string('shortnametemplate', 'tool_uploadcourse'),
            'maxlength="100" size="20"');
        $mform->setType('options[shortnametemplate]', PARAM_RAW);
        $mform->addHelpButton('options[shortnametemplate]', 'shortnametemplate', 'tool_uploadcourse');
        $mform->disabledIf('options[shortnametemplate]', 'options[mode]', 'eq', tool_uploadcourse_processor::MODE_CREATE_OR_UPDATE);
        $mform->disabledIf('options[shortnametemplate]', 'options[mode]', 'eq', tool_uploadcourse_processor::MODE_UPDATE_ONLY);

        // Restore file is not in the array options on purpose, because formslib can't handle it!
        $contextid = $this->_customdata['contextid'];
        $mform->addElement('hidden', 'contextid', $contextid);
        $mform->setType('contextid', PARAM_INT);
        $mform->addElement('filepicker', 'restorefile', get_string('templatefile', 'tool_uploadcourse'));
        $mform->addHelpButton('restorefile', 'templatefile', 'tool_uploadcourse');

        $mform->addElement('text', 'options[templatecourse]', get_string('coursetemplatename', 'tool_uploadcourse'));
        $mform->setType('options[templatecourse]', PARAM_TEXT);
        $mform->addHelpButton('options[templatecourse]', 'coursetemplatename', 'tool_uploadcourse');

        $mform->addElement('selectyesno', 'options[reset]', get_string('reset', 'tool_uploadcourse'));
        $mform->setDefault('options[reset]', 0);
        $mform->disabledIf('options[reset]', 'options[mode]', 'eq', tool_uploadcourse_processor::MODE_CREATE_NEW);
        $mform->disabledIf('options[reset]', 'options[mode]', 'eq', tool_uploadcourse_processor::MODE_CREATE_ALL);
        $mform->disabledIf('options[reset]', 'options[allowresets]', 'eq', 0);
        $mform->addHelpButton('options[reset]', 'reset', 'tool_uploadcourse');

        // Default values.
        $mform->addElement('header', 'defaultheader', get_string('defaultvalues', 'tool_uploadcourse'));
        $mform->setExpanded('defaultheader', true);

        $displaylist = coursecat::make_categories_list('moodle/course:create');
        $mform->addElement('select', 'defaults[category]', get_string('coursecategory'), $displaylist);
        $mform->addHelpButton('defaults[category]', 'coursecategory');

        $choices = array();
        $choices['0'] = get_string('hide');
        $choices['1'] = get_string('show');
        $mform->addElement('select', 'defaults[visible]', get_string('visible'), $choices);
        $mform->addHelpButton('defaults[visible]', 'visible');
        $mform->setDefault('defaults[visible]', $courseconfig->visible);

        $mform->addElement('date_selector', 'defaults[startdate]', get_string('startdate'));
        $mform->addHelpButton('defaults[startdate]', 'startdate');
        $mform->setDefault('defaults[startdate]', time() + 3600 * 24);

        $mform->addElement('date_selector', 'defaults[enddate]', get_string('enddate'), array('optional' => true));
        $mform->addHelpButton('defaults[enddate]', 'enddate');

        $courseformats = get_sorted_course_formats(true);
        $formcourseformats = array();
        foreach ($courseformats as $courseformat) {
            $formcourseformats[$courseformat] = get_string('pluginname', "format_$courseformat");
        }
        $mform->addElement('select', 'defaults[format]', get_string('format'), $formcourseformats);
        $mform->addHelpButton('defaults[format]', 'format');
        $mform->setDefault('defaults[format]', $courseconfig->format);

        if (!empty($CFG->allowcoursethemes)) {
            $themeobjects = get_list_of_themes();
            $themes=array();
            $themes[''] = get_string('forceno');
            foreach ($themeobjects as $key => $theme) {
                if (empty($theme->hidefromselector)) {
                    $themes[$key] = get_string('pluginname', 'theme_'.$theme->name);
                }
            }
            $mform->addElement('select', 'defaults[theme]', get_string('forcetheme'), $themes);
        }

        $languages = array();
        $languages[''] = get_string('forceno');
        $languages += get_string_manager()->get_list_of_translations();
        $mform->addElement('select', 'defaults[lang]', get_string('forcelanguage'), $languages);
        $mform->setDefault('defaults[lang]', $courseconfig->lang);

        $options = range(0, 10);
        $mform->addElement('select', 'defaults[newsitems]', get_string('newsitemsnumber'), $options);
        $mform->addHelpButton('defaults[newsitems]', 'newsitemsnumber');
        $mform->setDefault('defaults[newsitems]', $courseconfig->newsitems);

        $mform->addElement('selectyesno', 'defaults[showgrades]', get_string('showgrades'));
        $mform->addHelpButton('defaults[showgrades]', 'showgrades');
        $mform->setDefault('defaults[showgrades]', $courseconfig->showgrades);

        $mform->addElement('selectyesno', 'defaults[showreports]', get_string('showreports'));
        $mform->addHelpButton('defaults[showreports]', 'showreports');
        $mform->setDefault('defaults[showreports]', $courseconfig->showreports);

        if (!empty($CFG->legacyfilesinnewcourses)) {
            $mform->addElement('select', 'defaults[legacyfiles]', get_string('courselegacyfiles'), $choices);
            $mform->addHelpButton('defaults[legacyfiles]', 'courselegacyfiles');
            if (!isset($courseconfig->legacyfiles)) {
                $courseconfig->legacyfiles = 0;
            }
            $mform->setDefault('defaults[legacyfiles]', $courseconfig->legacyfiles);
        }

        $choices = get_max_upload_sizes($CFG->maxbytes);
        $mform->addElement('select', 'defaults[maxbytes]', get_string('maximumupload'), $choices);
        $mform->addHelpButton('defaults[maxbytes]', 'maximumupload');
        $mform->setDefault('defaults[maxbytes]', $courseconfig->maxbytes);

        $choices = array();
        $choices[NOGROUPS] = get_string('groupsnone', 'group');
        $choices[SEPARATEGROUPS] = get_string('groupsseparate', 'group');
        $choices[VISIBLEGROUPS] = get_string('groupsvisible', 'group');
        $mform->addElement('select', 'defaults[groupmode]', get_string('groupmode', 'group'), $choices);
        $mform->addHelpButton('defaults[groupmode]', 'groupmode', 'group');
        $mform->setDefault('defaults[groupmode]', $courseconfig->groupmode);

        $mform->addElement('selectyesno', 'defaults[groupmodeforce]', get_string('groupmodeforce', 'group'));
        $mform->addHelpButton('defaults[groupmodeforce]', 'groupmodeforce', 'group');
        $mform->setDefault('defaults[groupmodeforce]', $courseconfig->groupmodeforce);

        // Hidden fields.
        $mform->addElement('hidden', 'importid');
        $mform->setType('importid', PARAM_INT);

        $mform->addElement('hidden', 'previewrows');
        $mform->setType('previewrows', PARAM_INT);

        $this->add_action_buttons(true, get_string('uploadcourses', 'tool_uploadcourse'));

        $this->set_data($data);
    }

    /**
     * Add actopm buttons.
     *
     * @param bool $cancel whether to show cancel button, default true
     * @param string $submitlabel label for submit button, defaults to get_string('savechanges')
     * @return void
     */
    public function add_action_buttons($cancel = true, $submitlabel = null) {
        $mform =& $this->_form;
        $buttonarray = array();
        $buttonarray[] = &$mform->createElement('submit', 'showpreview', get_string('preview', 'tool_uploadcourse'));
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', $submitlabel);
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }

    /**
     * Sets the enddate default after set_data is called.
     */
    public function definition_after_data() {

        $mform = $this->_form;

        // The default end date depends on the course format.
        $format = course_get_format((object)array('format' => get_config('moodlecourse', 'format')));

        $enddate = $format->get_default_course_enddate($mform, array('startdate' => 'defaults[startdate]'));
        // We add 1 day like we do above in startdate.
        $mform->setDefault('defaults[enddate]', $enddate + 3600 * 24);
    }

    /**
     * Validation.
     *
     * @param array $data
     * @param array $files
     * @return array the errors that were found
     */
    public function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);

        if ($errorcode = course_validate_dates($data['defaults'])) {
            $errors['defaults[enddate]'] = get_string($errorcode, 'error');
        }

        return $errors;
    }
}
