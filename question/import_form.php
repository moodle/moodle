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
 * Defines the import questions form.
 *
 * @package    moodlecore
 * @subpackage questionbank
 * @copyright  2007 Jamie Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');


/**
 * Form to import questions into the question bank.
 *
 * @copyright  2007 Jamie Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_import_form extends moodleform {

    protected function definition() {
        global $OUTPUT;

        $mform = $this->_form;

        $defaultcategory = $this->_customdata['defaultcategory'];
        $contexts = $this->_customdata['contexts'];

        // Choice of import format, with help icons.
        $mform->addElement('header', 'fileformat', get_string('fileformat', 'question'));

        $fileformatnames = get_import_export_formats('import');
        $radioarray = array();
        $separators = array();
        foreach ($fileformatnames as $shortname => $fileformatname) {
            $radioarray[] = $mform->createElement('radio', 'format', '', $fileformatname, $shortname);

            $separator = '';
            if (get_string_manager()->string_exists('pluginname_help', 'qformat_' . $shortname)) {
                $separator .= $OUTPUT->help_icon('pluginname', 'qformat_' . $shortname);
            }
            $separator .= '<br>';
            $separators[] = $separator;
        }

        $radioarray[] = $mform->createElement('static', 'makelasthelpiconshowup', '');
        $mform->addGroup($radioarray, "formatchoices", '', $separators, false);
        $mform->addRule("formatchoices", null, 'required', null, 'client');

        // Import options.
        $mform->addElement('header','general', get_string('general', 'form'));

        $mform->addElement('questioncategory', 'category', get_string('importcategory', 'question'), compact('contexts'));
        $mform->setDefault('category', $defaultcategory);
        $mform->addHelpButton('category', 'importcategory', 'question');

        $categorygroup = array();
        $categorygroup[] = $mform->createElement('checkbox', 'catfromfile', '', get_string('getcategoryfromfile', 'question'));
        $categorygroup[] = $mform->createElement('checkbox', 'contextfromfile', '', get_string('getcontextfromfile', 'question'));
        $mform->addGroup($categorygroup, 'categorygroup', '', '', false);
        $mform->disabledIf('categorygroup', 'catfromfile', 'notchecked');
        $mform->setDefault('catfromfile', 1);
        $mform->setDefault('contextfromfile', 1);

        $matchgrades = array();
        $matchgrades['error'] = get_string('matchgradeserror', 'question');
        $matchgrades['nearest'] = get_string('matchgradesnearest', 'question');
        $mform->addElement('select', 'matchgrades', get_string('matchgrades', 'question'), $matchgrades);
        $mform->addHelpButton('matchgrades', 'matchgrades', 'question');
        $mform->setDefault('matchgrades', 'error');

        $mform->addElement('selectyesno', 'stoponerror', get_string('stoponerror', 'question'));
        $mform->setDefault('stoponerror', 1);
        $mform->addHelpButton('stoponerror', 'stoponerror', 'question');

        // The file to import
        $mform->addElement('header', 'importfileupload', get_string('importquestions', 'question'));

        $mform->addElement('filepicker', 'newfile', get_string('import'));
        $mform->addRule('newfile', null, 'required', null, 'client');

        // Submit button.
        $mform->addElement('submit', 'submitbutton', get_string('import'));

        // Set a template for the format select elements
        $renderer = $mform->defaultRenderer();
        $template = "{help} {element}\n";
        $renderer->setGroupElementTemplate($template, 'format');
    }

    /**
     * Checks that a file has been uploaded, and that it is of a plausible type.
     * @param array $data the submitted data.
     * @param array $errors the errors so far.
     * @return array the updated errors.
     */
    protected function validate_uploaded_file($data, $errors) {
        if (empty($data['newfile'])) {
            $errors['newfile'] = get_string('required');
            return $errors;
        }

        $files = $this->get_draft_files('newfile');
        if (count($files) < 1) {
            $errors['newfile'] = get_string('required');
            return $errors;
        }

        if (empty($data['format'])) {
            $errors['format'] = get_string('required');
            return $errors;
        }

        $formatfile = 'format/' . $data['format'] . '/format.php';
        if (!is_readable($formatfile)) {
            throw new moodle_exception('formatnotfound', 'question', '', $data['format']);
        }

        require_once($formatfile);

        $classname = 'qformat_' . $data['format'];
        $qformat = new $classname();

        $file = reset($files);
        if (!$qformat->can_import_file($file)) {
            $a = new stdClass();
            $a->actualtype = $file->get_mimetype();
            $a->expectedtype = $qformat->mime_type();
            $errors['newfile'] = get_string('importwrongfiletype', 'question', $a);
        }

        return $errors;
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $errors = $this->validate_uploaded_file($data, $errors);
        return $errors;
    }
}
