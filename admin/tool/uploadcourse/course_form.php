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
 * Bulk course upload forms
 *
 * @package    tool_uploadcourse
 * @subpackage uploadcourse
 * @copyright  2007 Dan Poltawski
 * @copyright  2011 Piers Harding
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');


/**
 * Upload a file CVS file with course information.
 *
 * @copyright  2007 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_uploadcourse_form1 extends moodleform {
    /**
     * The standard form definiton
     * @return object $form
     */
    public function definition () {
        $mform = $this->_form;

        $mform->addElement('header', 'settingsheader', get_string('upload'));

        $mform->addElement('filepicker', 'coursefile', get_string('file'));
        $mform->addRule('coursefile', null, 'required');

        $choices = csv_import_reader::get_delimiter_list();
        $mform->addElement('select', 'delimiter_name', get_string('csvdelimiter', 'tool_uploadcourse'), $choices);
        if (array_key_exists('cfg', $choices)) {
            $mform->setDefault('delimiter_name', 'cfg');
        } else if (get_string('listsep', 'langconfig') == ';') {
            $mform->setDefault('delimiter_name', 'semicolon');
        } else {
            $mform->setDefault('delimiter_name', 'comma');
        }

        $choices = textlib::get_encodings();
        $mform->addElement('select', 'encoding', get_string('encoding', 'tool_uploadcourse'), $choices);
        $mform->setDefault('encoding', 'UTF-8');

        $choices = array('10'=>10, '20'=>20, '100'=>100, '1000'=>1000, '100000'=>100000);
        $mform->addElement('select', 'previewrows', get_string('rowpreviewnum', 'tool_uploadcourse'), $choices);
        $mform->setType('previewrows', PARAM_INT);

        $this->add_action_buttons(false, get_string('uploadcourses', 'tool_uploadcourse'));
    }
}


/**
 * Specify course upload details
 *
 * @copyright  2007 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_uploadcourse_form2 extends moodleform {
    /**
     * The standard form definiton
     * @return object $form
     */
    public function definition () {
        global $CFG, $COURSE, $DB;

        $mform   = $this->_form;
        $columns = $this->_customdata['columns'];
        $data    = $this->_customdata['data'];
        $courseconfig = get_config('moodlecourse');

        // I am the template course, why should it be the administrator? we have roles now, other ppl may use this script ;-).
        $templatecourse = $COURSE;

        // Upload settings and file.
        $mform->addElement('header', 'settingsheader', get_string('settings'));

        $choices = array(CC_COURSE_ADDNEW     => get_string('ccoptype_addnew', 'tool_uploadcourse'),
                         CC_COURSE_ADDINC     => get_string('ccoptype_addinc', 'tool_uploadcourse'),
                         CC_COURSE_ADD_UPDATE => get_string('ccoptype_addupdate', 'tool_uploadcourse'),
                         CC_COURSE_UPDATE     => get_string('ccoptype_update', 'tool_uploadcourse'));
        $mform->addElement('select', 'cctype', get_string('ccoptype', 'tool_uploadcourse'), $choices);

        $choices = array(CC_UPDATE_NOCHANGES    => get_string('nochanges', 'tool_uploadcourse'),
                         CC_UPDATE_FILEOVERRIDE => get_string('ccupdatefromfile', 'tool_uploadcourse'),
                         CC_UPDATE_ALLOVERRIDE  => get_string('ccupdateall', 'tool_uploadcourse'),
                         CC_UPDATE_MISSING      => get_string('ccupdatemissing', 'tool_uploadcourse'));
        $mform->addElement('select', 'ccupdatetype', get_string('ccupdatetype', 'tool_uploadcourse'), $choices);
        $mform->setDefault('ccupdatetype', CC_UPDATE_NOCHANGES);
        $mform->disabledIf('ccupdatetype', 'cctype', 'eq', CC_COURSE_ADDNEW);
        $mform->disabledIf('ccupdatetype', 'cctype', 'eq', CC_COURSE_ADDINC);

        $mform->addElement('selectyesno', 'ccallowrenames', get_string('allowrenames', 'tool_uploadcourse'));
        $mform->setDefault('ccallowrenames', 0);
        $mform->disabledIf('ccallowrenames', 'cctype', 'eq', CC_COURSE_ADDNEW);
        $mform->disabledIf('ccallowrenames', 'cctype', 'eq', CC_COURSE_ADDINC);

        $mform->addElement('selectyesno', 'ccallowdeletes', get_string('allowdeletes', 'tool_uploadcourse'));
        $mform->setDefault('ccallowdeletes', 0);
        $mform->disabledIf('ccallowdeletes', 'cctype', 'eq', CC_COURSE_ADDNEW);
        $mform->disabledIf('ccallowdeletes', 'cctype', 'eq', CC_COURSE_ADDINC);

        $mform->addElement('selectyesno', 'reset', get_string('reset', 'tool_uploadcourse'));
        $mform->setDefault('ccallowdeletes', 0);
        $mform->disabledIf('ccallowdeletes', 'cctype', 'eq', CC_COURSE_ADDNEW);
        $mform->disabledIf('ccallowdeletes', 'cctype', 'eq', CC_COURSE_ADDINC);

        $mform->addElement('selectyesno', 'ccstandardshortnames', get_string('ccstandardshortnames', 'tool_uploadcourse'));
        $mform->setDefault('ccstandardshortnames', 1);

        // Default values.
        $mform->addElement('header', 'defaultheader', get_string('defaultvalues', 'tool_uploadcourse'));
        $displaylist = array();
        $parentlist = array();
        make_categories_list($displaylist, $parentlist, 'moodle/course:create');
        $mform->addElement('select', 'cccategory', get_string('category'), $displaylist);
        $mform->addHelpButton('cccategory', 'category');

        $mform->addElement('text', 'ccshortname', get_string('ccshortnametemplate', 'tool_uploadcourse'),
                           'maxlength="100" size="20"');
        $mform->addHelpButton('ccshortname', 'shortnamecourse', 'tool_uploadcourse');
        $mform->disabledIf('ccshortname', 'cctype', 'eq', CC_COURSE_ADD_UPDATE);
        $mform->disabledIf('ccshortname', 'cctype', 'eq', CC_COURSE_UPDATE);

        $courseformats = get_plugin_list('format');
        $formcourseformats = array();
        foreach ($courseformats as $courseformat => $formatdir) {
            $formcourseformats[$courseformat] = get_string('pluginname', "format_$courseformat");
        }
        $mform->addElement('select', 'format', get_string('format'), $formcourseformats);
        $mform->addHelpButton('format', 'format');
        $mform->setDefault('format', $courseconfig->format);

        for ($i = 0; $i <= $courseconfig->maxsections; $i++) {
            $sectionmenu[$i] = "$i";
        }
        $mform->addElement('select', 'numsections', get_string('numberweeks'), $sectionmenu);
        $mform->setDefault('numsections', $courseconfig->numsections);

        $mform->addElement('date_selector', 'startdate', get_string('startdate'));
        $mform->addHelpButton('startdate', 'startdate');
        $mform->setDefault('startdate', time() + 3600 * 24);

        $choices = array();
        $choices['0'] = get_string('hiddensectionscollapsed');
        $choices['1'] = get_string('hiddensectionsinvisible');
        $mform->addElement('select', 'hiddensections', get_string('hiddensections'), $choices);
        $mform->addHelpButton('hiddensections', 'hiddensections');
        $mform->setDefault('hiddensections', $courseconfig->hiddensections);

        $options = range(0, 10);
        $mform->addElement('select', 'newsitems', get_string('newsitemsnumber'), $options);
        $mform->addHelpButton('newsitems', 'newsitemsnumber');
        $mform->setDefault('newsitems', $courseconfig->newsitems);

        $mform->addElement('selectyesno', 'showgrades', get_string('showgrades'));
        $mform->addHelpButton('showgrades', 'showgrades');
        $mform->setDefault('showgrades', $courseconfig->showgrades);

        $mform->addElement('selectyesno', 'showreports', get_string('showreports'));
        $mform->addHelpButton('showreports', 'showreports');
        $mform->setDefault('showreports', $courseconfig->showreports);

        $choices = get_max_upload_sizes($CFG->maxbytes);
        $mform->addElement('select', 'maxbytes', get_string('maximumupload'), $choices);
        $mform->addHelpButton('maxbytes', 'maximumupload');
        $mform->setDefault('maxbytes', $courseconfig->maxbytes);

        if (!empty($course->legacyfiles) or !empty($CFG->legacyfilesinnewcourses)) {
            if (empty($course->legacyfiles)) {
                // 0 or missing means no legacy files ever used in this course - new course or nobody turned on legacy files yet.
                $choices = array('0'=>get_string('no'), '2'=>get_string('yes'));
            } else {
                $choices = array('1'=>get_string('no'), '2'=>get_string('yes'));
            }
            $mform->addElement('select', 'legacyfiles', get_string('courselegacyfiles'), $choices);
            $mform->addHelpButton('legacyfiles', 'courselegacyfiles');
            if (!isset($courseconfig->legacyfiles)) {
                // In case this was not initialised properly due to switching of $CFG->legacyfilesinnewcourses.
                $courseconfig->legacyfiles = 0;
            }
            $mform->setDefault('legacyfiles', $courseconfig->legacyfiles);
        }

        if (!empty($CFG->allowcoursethemes)) {
            $themeobjects = get_list_of_themes();
            $themes=array();
            $themes[''] = get_string('forceno');
            foreach ($themeobjects as $key => $theme) {
                if (empty($theme->hidefromselector)) {
                    $themes[$key] = get_string('pluginname', 'theme_'.$theme->name);
                }
            }
            $mform->addElement('select', 'theme', get_string('forcetheme'), $themes);
        }
        $courseshortnames = $DB->get_records('course', null, $sort='shortname', 'id,shortname,idnumber');
        $formccourseshortnames = array('none' => get_string('none'));
        foreach ($courseshortnames as $course) {
            $formccourseshortnames[$course->shortname] = $course->shortname;
        }
        $mform->addElement('select', 'templatename', get_string('coursetemplatename', 'tool_uploadcourse'), $formccourseshortnames);
        $mform->addHelpButton('templatename', 'coursetemplatename', 'tool_uploadcourse');
        $mform->setDefault('templatename', 'none');

        $contextid = $this->_customdata['contextid'];
        $mform->addElement('hidden', 'contextid', $contextid);
        $mform->addElement('filepicker', 'restorefile', get_string('templatefile', 'tool_uploadcourse'));

        enrol_course_edit_form($mform, null, get_context_instance(CONTEXT_SYSTEM));

        $mform->addElement('header', '', get_string('groups', 'group'));

        $choices = array();
        $choices[NOGROUPS] = get_string('groupsnone', 'group');
        $choices[SEPARATEGROUPS] = get_string('groupsseparate', 'group');
        $choices[VISIBLEGROUPS] = get_string('groupsvisible', 'group');
        $mform->addElement('select', 'groupmode', get_string('groupmode', 'group'), $choices);
        $mform->addHelpButton('groupmode', 'groupmode', 'group');
        $mform->setDefault('groupmode', $courseconfig->groupmode);

        $choices = array();
        $choices['0'] = get_string('no');
        $choices['1'] = get_string('yes');
        $mform->addElement('select', 'groupmodeforce', get_string('groupmodeforce', 'group'), $choices);
        $mform->addHelpButton('groupmodeforce', 'groupmodeforce', 'group');
        $mform->setDefault('groupmodeforce', $courseconfig->groupmodeforce);

        // Default groupings selector.
        $options = array();
        $options[0] = get_string('none');
        $mform->addElement('select', 'defaultgroupingid', get_string('defaultgrouping', 'group'), $options);

        $mform->addElement('header', '', get_string('availability'));

        $choices = array();
        $choices['0'] = get_string('courseavailablenot');
        $choices['1'] = get_string('courseavailable');
        $mform->addElement('select', 'visible', get_string('availability'), $choices);
        $mform->addHelpButton('visible', 'availability');
        $mform->setDefault('visible', $courseconfig->visible);

        $mform->addElement('header', '', get_string('language'));

        $languages=array();
        $languages[''] = get_string('forceno');
        $languages += get_string_manager()->get_list_of_translations();
        $mform->addElement('select', 'lang', get_string('forcelanguage'), $languages);
        $mform->setDefault('lang', $courseconfig->lang);

        // Hidden fields.
        $mform->addElement('hidden', 'iid');
        $mform->setType('iid', PARAM_INT);

        $mform->addElement('hidden', 'previewrows');
        $mform->setType('previewrows', PARAM_INT);

        $this->add_action_buttons(true, get_string('uploadcourses', 'tool_uploadcourse'));

        $this->set_data($data);
    }

    /**
     * Form tweaks that depend on current data.
     */
    public function definition_after_data() {
        $mform   = $this->_form;
        $columns = $this->_customdata['columns'];

        foreach ($columns as $column) {
            if ($mform->elementExists($column)) {
                $mform->removeElement($column);
            }
        }

    }

    /**
     * Server side validation.
     * @param array $data - form data
     * @param object $files  - form files
     * @return array $errors - form errors
     */
    public function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);
        $columns = $this->_customdata['columns'];
        $optype  = $data['cctype'];

        // Look for other required data.
        if ($optype != CC_COURSE_UPDATE) {
            if (!in_array('fullname', $columns)) {
                if (isset($errors['cctype'])) {
                    $errors['cctype'] .= ' ';
                }
                $errors['cctype'] .= get_string('missingfield', 'error', 'fullname');
            }
            if (!in_array('summary', $columns)) {
                if (isset($errors['cctype'])) {
                    $errors['cctype'] .= ' ';
                }
                $errors['cctype'] .= get_string('missingfield', 'error', 'summary');
            }
        }
        if (!empty($data['templatename']) && $data['templatename'] != 'none') {
            if (!$template = $DB->get_record('course', array('shortname' => $data['templatename']))) {
                $errors['templatename'] = get_string('missingtemplate', 'tool_uploadcourse');
            }
        }

        return $errors;
    }

    /**
     * Used to reformat the data from the editor component
     *
     * @return stdClass
     */
    public function get_data() {
        $data = parent::get_data();

        if ($data !== null and isset($data->description)) {
            $data->descriptionformat = $data->description['format'];
            $data->description = $data->description['text'];
        }

        return $data;
    }
}
