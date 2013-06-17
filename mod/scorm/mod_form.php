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

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/scorm/locallib.php');

class mod_scorm_mod_form extends moodleform_mod {

    function definition() {
        global $CFG, $COURSE, $OUTPUT;
        $cfg_scorm = get_config('scorm');

        $mform = $this->_form;

        if (!$CFG->slasharguments) {
            $mform->addElement('static', '', '', $OUTPUT->notification(get_string('slashargs', 'scorm'), 'notifyproblem'));
        }
        //-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Name.
        $mform->addElement('text', 'name', get_string('name'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        // Summary.
        $this->add_intro_editor(true);

        // Package.
        $mform->addElement('header', 'packagehdr', get_string('packagehdr', 'scorm'));
        $mform->setExpanded('packagehdr', true);

        // Scorm types.
        $scormtypes = array(SCORM_TYPE_LOCAL => get_string('typelocal', 'scorm'));

        if ($cfg_scorm->allowtypeexternal) {
            $scormtypes[SCORM_TYPE_EXTERNAL] = get_string('typeexternal', 'scorm');
        }

        if ($cfg_scorm->allowtypelocalsync) {
            $scormtypes[SCORM_TYPE_LOCALSYNC] = get_string('typelocalsync', 'scorm');
        }

        if (!empty($CFG->repositoryactivate) and $cfg_scorm->allowtypeimsrepository) {
            $scormtypes[SCORM_TYPE_IMSREPOSITORY] = get_string('typeimsrepository', 'scorm');
        }

        if ($cfg_scorm->allowtypeexternalaicc) {
            $scormtypes[SCORM_TYPE_AICCURL] = get_string('typeaiccurl', 'scorm');
        }

        // Reference.
        if (count($scormtypes) > 1) {
            $mform->addElement('select', 'scormtype', get_string('scormtype', 'scorm'), $scormtypes);
            $mform->setType('scormtype', PARAM_ALPHA);
            $mform->addHelpButton('scormtype', 'scormtype', 'scorm');
            $mform->addElement('text', 'packageurl', get_string('packageurl', 'scorm'), array('size'=>60));
            $mform->setType('packageurl', PARAM_RAW);
            $mform->addHelpButton('packageurl', 'packageurl', 'scorm');
            $mform->disabledIf('packageurl', 'scormtype', 'eq', SCORM_TYPE_LOCAL);
        } else {
            $mform->addElement('hidden', 'scormtype', SCORM_TYPE_LOCAL);
            $mform->setType('scormtype', PARAM_ALPHA);
        }

        if (count($scormtypes) > 1) {
            // Update packages timing.
            $mform->addElement('select', 'updatefreq', get_string('updatefreq', 'scorm'), scorm_get_updatefreq_array());
            $mform->setType('updatefreq', PARAM_INT);
            $mform->setDefault('updatefreq', $cfg_scorm->updatefreq);
            $mform->addHelpButton('updatefreq', 'updatefreq', 'scorm');
            $mform->disabledIf('updatefreq', 'scormtype', 'eq', SCORM_TYPE_LOCAL);
        } else {
            $mform->addElement('hidden', 'updatefreq', 0);
            $mform->setType('updatefreq', PARAM_INT);
        }

        // New local package upload.
        $mform->addElement('filepicker', 'packagefile', get_string('package', 'scorm'));
        $mform->addHelpButton('packagefile', 'package', 'scorm');
        $mform->disabledIf('packagefile', 'scormtype', 'noteq', SCORM_TYPE_LOCAL);

        // Display Settings.
        $mform->addElement('header', 'displaysettings', get_string('appearance'));

        // Framed / Popup Window.
        $mform->addElement('select', 'popup', get_string('display', 'scorm'), scorm_get_popup_display_array());
        $mform->setDefault('popup', $cfg_scorm->popup);
        $mform->setAdvanced('popup', $cfg_scorm->popup_adv);

        // Width.
        $mform->addElement('text', 'width', get_string('width', 'scorm'), 'maxlength="5" size="5"');
        $mform->setDefault('width', $cfg_scorm->framewidth);
        $mform->setType('width', PARAM_INT);
        $mform->setAdvanced('width', $cfg_scorm->framewidth_adv);
        $mform->disabledIf('width', 'popup', 'eq', 0);

        // Height.
        $mform->addElement('text', 'height', get_string('height', 'scorm'), 'maxlength="5" size="5"');
        $mform->setDefault('height', $cfg_scorm->frameheight);
        $mform->setType('height', PARAM_INT);
        $mform->setAdvanced('height', $cfg_scorm->frameheight_adv);
        $mform->disabledIf('height', 'popup', 'eq', 0);

        // Window Options.
        $winoptgrp = array();
        foreach (scorm_get_popup_options_array() as $key => $value) {
            $winoptgrp[] = &$mform->createElement('checkbox', $key, '', get_string($key, 'scorm'));
            $mform->setDefault($key, $value);
        }
        $mform->addGroup($winoptgrp, 'winoptgrp', get_string('options', 'scorm'), '<br />', false);
        $mform->disabledIf('winoptgrp', 'popup', 'eq', 0);
        $mform->setAdvanced('winoptgrp', $cfg_scorm->winoptgrp_adv);

        // Skip view page.
        $skipviewoptions = scorm_get_skip_view_array();
        if ($COURSE->format == 'scorm') { // Remove option that would cause a constant redirect.
            unset($skipviewoptions[SCORM_SKIPVIEW_ALWAYS]);
            if ($cfg_scorm->skipview == SCORM_SKIPVIEW_ALWAYS) {
                $cfg_scorm->skipview = SCORM_SKIPVIEW_FIRST;
            }
        }
        $mform->addElement('select', 'skipview', get_string('skipview', 'scorm'), $skipviewoptions);
        $mform->addHelpButton('skipview', 'skipview', 'scorm');
        $mform->setDefault('skipview', $cfg_scorm->skipview);
        $mform->setAdvanced('skipview', $cfg_scorm->skipview_adv);

        // Hide Browse.
        $mform->addElement('selectyesno', 'hidebrowse', get_string('hidebrowse', 'scorm'));
        $mform->addHelpButton('hidebrowse', 'hidebrowse', 'scorm');
        $mform->setDefault('hidebrowse', $cfg_scorm->hidebrowse);
        $mform->setAdvanced('hidebrowse', $cfg_scorm->hidebrowse_adv);

        // Display course structure.
        $mform->addElement('selectyesno', 'displaycoursestructure', get_string('displaycoursestructure', 'scorm'));
        $mform->addHelpButton('displaycoursestructure', 'displaycoursestructure', 'scorm');
        $mform->setDefault('displaycoursestructure', $cfg_scorm->displaycoursestructure);
        $mform->setAdvanced('displaycoursestructure', $cfg_scorm->displaycoursestructure_adv);

        // Toc display.
        $mform->addElement('select', 'hidetoc', get_string('hidetoc', 'scorm'), scorm_get_hidetoc_array());
        $mform->addHelpButton('hidetoc', 'hidetoc', 'scorm');
        $mform->setDefault('hidetoc', $cfg_scorm->hidetoc);
        $mform->setAdvanced('hidetoc', $cfg_scorm->hidetoc_adv);

        // Hide Navigation panel.
        $mform->addElement('selectyesno', 'hidenav', get_string('hidenav', 'scorm'));
        $mform->setDefault('hidenav', $cfg_scorm->hidenav);
        $mform->setAdvanced('hidenav', $cfg_scorm->hidenav_adv);
        $mform->disabledIf('hidenav', 'hidetoc', 'noteq', 0);

        // Display attempt status.
        $mform->addElement('select', 'displayattemptstatus', get_string('displayattemptstatus', 'scorm'), scorm_get_attemptstatus_array());
        $mform->addHelpButton('displayattemptstatus', 'displayattemptstatus', 'scorm');
        $mform->setDefault('displayattemptstatus', $cfg_scorm->displayattemptstatus);
        $mform->setAdvanced('displayattemptstatus', $cfg_scorm->displayattemptstatus_adv);

        // Availability.
        $mform->addElement('header', 'availability', get_string('availability'));

        $mform->addElement('date_time_selector', 'timeopen', get_string("scormopen", "scorm"), array('optional' => true));
        $mform->addElement('date_time_selector', 'timeclose', get_string("scormclose", "scorm"), array('optional' => true));

        //-------------------------------------------------------------------------------
        // Grade Settings.
        $mform->addElement('header', 'gradesettings', get_string('grade'));

        // Grade Method.
        $mform->addElement('select', 'grademethod', get_string('grademethod', 'scorm'), scorm_get_grade_method_array());
        $mform->addHelpButton('grademethod', 'grademethod', 'scorm');
        $mform->setDefault('grademethod', $cfg_scorm->grademethod);

        // Maximum Grade.
        for ($i=0; $i<=100; $i++) {
            $grades[$i] = "$i";
        }
        $mform->addElement('select', 'maxgrade', get_string('maximumgrade'), $grades);
        $mform->setDefault('maxgrade', $cfg_scorm->maxgrade);
        $mform->disabledIf('maxgrade', 'grademethod', 'eq', GRADESCOES);

        // Attempts management.
        $mform->addElement('header', 'attemptsmanagementhdr', get_string('attemptsmanagement', 'scorm'));

        // Max Attempts.
        $mform->addElement('select', 'maxattempt', get_string('maximumattempts', 'scorm'), scorm_get_attempts_array());
        $mform->addHelpButton('maxattempt', 'maximumattempts', 'scorm');
        $mform->setDefault('maxattempt', $cfg_scorm->maxattempt);

        // What Grade.
        $mform->addElement('select', 'whatgrade', get_string('whatgrade', 'scorm'),  scorm_get_what_grade_array());
        $mform->disabledIf('whatgrade', 'maxattempt', 'eq', 1);
        $mform->addHelpButton('whatgrade', 'whatgrade', 'scorm');
        $mform->setDefault('whatgrade', $cfg_scorm->whatgrade);

        // Force new attempt.
        $mform->addElement('selectyesno', 'forcenewattempt', get_string('forcenewattempt', 'scorm'));
        $mform->addHelpButton('forcenewattempt', 'forcenewattempt', 'scorm');
        $mform->setDefault('forcenewattempt', $cfg_scorm->forcenewattempt);

        // Last attempt lock - lock the enter button after the last available attempt has been made.
        $mform->addElement('selectyesno', 'lastattemptlock', get_string('lastattemptlock', 'scorm'));
        $mform->addHelpButton('lastattemptlock', 'lastattemptlock', 'scorm');
        $mform->setDefault('lastattemptlock', $cfg_scorm->lastattemptlock);

        // Compatibility settings.
        $mform->addElement('header', 'compatibilitysettingshdr', get_string('compatibilitysettings', 'scorm'));

        // Force completed.
        $mform->addElement('selectyesno', 'forcecompleted', get_string('forcecompleted', 'scorm'));
        $mform->addHelpButton('forcecompleted', 'forcecompleted', 'scorm');
        $mform->setDefault('forcecompleted', $cfg_scorm->forcecompleted);

        // Autocontinue.
        $mform->addElement('selectyesno', 'auto', get_string('autocontinue', 'scorm'));
        $mform->addHelpButton('auto', 'autocontinue', 'scorm');
        $mform->setDefault('auto', $cfg_scorm->auto);

        //-------------------------------------------------------------------------------
        // Hidden Settings.
        $mform->addElement('hidden', 'datadir', null);
        $mform->setType('datadir', PARAM_RAW);
        $mform->addElement('hidden', 'pkgtype', null);
        $mform->setType('pkgtype', PARAM_RAW);
        $mform->addElement('hidden', 'launch', null);
        $mform->setType('launch', PARAM_RAW);
        $mform->addElement('hidden', 'redirect', null);
        $mform->setType('redirect', PARAM_RAW);
        $mform->addElement('hidden', 'redirecturl', null);
        $mform->setType('redirecturl', PARAM_RAW);

        //-------------------------------------------------------------------------------
        $this->standard_coursemodule_elements();
        //-------------------------------------------------------------------------------
        // Buttons.
        $this->add_action_buttons();
    }

    function data_preprocessing(&$default_values) {
        global $COURSE;

        if (isset($default_values['popup']) && ($default_values['popup'] == 1) && isset($default_values['options'])) {
            if (!empty($default_values['options'])) {
                $options = explode(',', $default_values['options']);
                foreach ($options as $option) {
                    list($element, $value) = explode('=', $option);
                    $element = trim($element);
                    $default_values[$element] = trim($value);
                }
            }
        }
        if (isset($default_values['grademethod'])) {
            $default_values['grademethod'] = intval($default_values['grademethod']);
        }
        if (isset($default_values['width']) && (strpos($default_values['width'], '%') === false) && ($default_values['width'] <= 100)) {
            $default_values['width'] .= '%';
        }
        if (isset($default_values['width']) && (strpos($default_values['height'], '%') === false) && ($default_values['height'] <= 100)) {
            $default_values['height'] .= '%';
        }
        $scorms = get_all_instances_in_course('scorm', $COURSE);
        $coursescorm = current($scorms);

        $draftitemid = file_get_submitted_draft_itemid('packagefile');
        file_prepare_draft_area($draftitemid, $this->context->id, 'mod_scorm', 'package', 0);
        $default_values['packagefile'] = $draftitemid;

        if (($COURSE->format == 'scorm') && ((count($scorms) == 0) || ($default_values['instance'] == $coursescorm->id))) {
            $default_values['redirect'] = 'yes';
            $default_values['redirecturl'] = '../course/view.php?id='.$default_values['course'];
        } else {
            $default_values['redirect'] = 'no';
            $default_values['redirecturl'] = '../mod/scorm/view.php?id='.$default_values['coursemodule'];
        }
        if (isset($default_values['version'])) {
            $default_values['pkgtype'] = (substr($default_values['version'], 0, 5) == 'SCORM') ? 'scorm':'aicc';
        }
        if (isset($default_values['instance'])) {
            $default_values['datadir'] = $default_values['instance'];
        }
        if (empty($default_values['timeopen'])) {
            $default_values['timeopen'] = 0;
        }
        if (empty($default_values['timeclose'])) {
            $default_values['timeclose'] = 0;
        }

        // Set some completion default data.
        if (!empty($default_values['completionstatusrequired']) && !is_array($default_values['completionstatusrequired'])) {
            // Unpack values.
            $cvalues = array();
            foreach (scorm_status_options() as $key => $value) {
                if (($default_values['completionstatusrequired'] & $key) == $key) {
                    $cvalues[$key] = 1;
                }
            }

            $default_values['completionstatusrequired'] = $cvalues;
        }

        if (!isset($default_values['completionscorerequired']) || !strlen($default_values['completionscorerequired'])) {
            $default_values['completionscoredisabled'] = 1;
        }

    }

    function validation($data, $files) {
        global $CFG;
        $errors = parent::validation($data, $files);

        $type = $data['scormtype'];

        if ($type === SCORM_TYPE_LOCAL) {
            if (!empty($data['update'])) {
                // OK, not required.

            } else if (empty($data['packagefile'])) {
                $errors['packagefile'] = get_string('required');

            } else {
                $files = $this->get_draft_files('packagefile');
                if (count($files)<1) {
                    $errors['packagefile'] = get_string('required');
                    return $errors;
                }
                $file = reset($files);
                $filename = $CFG->tempdir.'/scormimport/scrom_'.time();
                make_temp_directory('scormimport');
                $file->copy_content_to($filename);

                $packer = get_file_packer('application/zip');

                $filelist = $packer->list_files($filename);
                if (!is_array($filelist)) {
                    $errors['packagefile'] = 'Incorrect file package - not an archive'; //TODO: localise
                } else {
                    $manifestpresent = false;
                    $aiccfound       = false;
                    foreach ($filelist as $info) {
                        if ($info->pathname == 'imsmanifest.xml') {
                            $manifestpresent = true;
                            break;
                        }
                        if (preg_match('/\.cst$/', $info->pathname)) {
                            $aiccfound = true;
                            break;
                        }
                    }
                    if (!$manifestpresent and !$aiccfound) {
                        $errors['packagefile'] = 'Incorrect file package - missing imsmanifest.xml or AICC structure'; //TODO: localise
                    }
                }
                unlink($filename);
            }

        } else if ($type === SCORM_TYPE_EXTERNAL) {
            $reference = $data['packageurl'];
            // Syntax check.
            if (!preg_match('/(http:\/\/|https:\/\/|www).*\/imsmanifest.xml$/i', $reference)) {
                $errors['packageurl'] = get_string('invalidurl', 'scorm');
            } else {
                // Availability check.
                $result = scorm_check_url($reference);
                if (is_string($result)) {
                    $errors['packageurl'] = $result;
                }
            }

        } else if ($type === 'packageurl') {
            $reference = $data['reference'];
            // Syntax check.
            if (!preg_match('/(http:\/\/|https:\/\/|www).*(\.zip|\.pif)$/i', $reference)) {
                $errors['packageurl'] = get_string('invalidurl', 'scorm');
            } else {
                // Availability check.
                $result = scorm_check_url($reference);
                if (is_string($result)) {
                    $errors['packageurl'] = $result;
                }
            }

        } else if ($type === SCORM_TYPE_IMSREPOSITORY) {
            $reference = $data['packageurl'];
            if (stripos($reference, '#') !== 0) {
                $errors['packageurl'] = get_string('invalidurl', 'scorm');
            }

        } else if ($type === SCORM_TYPE_AICCURL) {
            $reference = $data['packageurl'];
            // Syntax check.
            if (!preg_match('/(http:\/\/|https:\/\/|www).*/', $reference)) {
                $errors['packageurl'] = get_string('invalidurl', 'scorm');
            } else {
                // Availability check.
                $result = scorm_check_url($reference);
                if (is_string($result)) {
                    $errors['packageurl'] = $result;
                }
            }

        }

        return $errors;
    }

    // Need to translate the "options" and "reference" field.
    function set_data($default_values) {
        $default_values = (array)$default_values;

        if (isset($default_values['scormtype']) and isset($default_values['reference'])) {
            switch ($default_values['scormtype']) {
                case SCORM_TYPE_LOCALSYNC :
                case SCORM_TYPE_EXTERNAL:
                case SCORM_TYPE_IMSREPOSITORY:
                case SCORM_TYPE_AICCURL:
                    $default_values['packageurl'] = $default_values['reference'];
            }
        }
        unset($default_values['reference']);

        if (!empty($default_values['options'])) {
            $options = explode(',', $default_values['options']);
            foreach ($options as $option) {
                $opt = explode('=', $option);
                if (isset($opt[1])) {
                    $default_values[$opt[0]] = $opt[1];
                }
            }
        }

        $this->data_preprocessing($default_values);
        parent::set_data($default_values);
    }

    function add_completion_rules() {
        $mform =& $this->_form;
        $items = array();

        // Require score.
        $group = array();
        $group[] =& $mform->createElement('text', 'completionscorerequired', '', array('size' => 5));
        $group[] =& $mform->createElement('checkbox', 'completionscoredisabled', null, get_string('disable'));
        $mform->setType('completionscorerequired', PARAM_INT);
        $mform->addGroup($group, 'completionscoregroup', get_string('completionscorerequired', 'scorm'), '', false);
        $mform->addHelpButton('completionscoregroup', 'completionscorerequired', 'scorm');
        $mform->disabledIf('completionscorerequired', 'completionscoredisabled', 'checked');
        $mform->setDefault('completionscorerequired', 0);

        $items[] = 'completionscoregroup';


        // Require status.
        $first = true;
        $firstkey = null;
        foreach (scorm_status_options(true) as $key => $value) {
            $name = null;
            $key = 'completionstatusrequired['.$key.']';
            if ($first) {
                $name = get_string('completionstatusrequired', 'scorm');
                $first = false;
                $firstkey = $key;
            }
            $mform->addElement('checkbox', $key, $name, $value);
            $mform->setType($key, PARAM_BOOL);
            $items[] = $key;
        }
        $mform->addHelpButton($firstkey, 'completionstatusrequired', 'scorm');

        return $items;
    }

    function completion_rule_enabled($data) {
        $status = !empty($data['completionstatusrequired']);
        $score = empty($data['completionscoredisabled']) && strlen($data['completionscorerequired']);

        return $status || $score;
    }

    function get_data($slashed = true) {
        $data = parent::get_data($slashed);

        if (!$data) {
            return false;
        }

        // Convert completionstatusrequired to a proper integer, if any.
        $total = 0;
        if (isset($data->completionstatusrequired) && is_array($data->completionstatusrequired)) {
            foreach (array_keys($data->completionstatusrequired) as $state) {
                $total |= $state;
            }
            $data->completionstatusrequired = $total;
        }

        if (!empty($data->completionunlocked)) {
            // Turn off completion settings if the checkboxes aren't ticked.
            $autocompletion = isset($data->completion) && $data->completion == COMPLETION_TRACKING_AUTOMATIC;

            if (isset($data->completionstatusrequired) && $autocompletion) {
                // Do nothing: completionstatusrequired has been already converted
                //             into a correct integer representation.
            } else {
                $data->completionstatusrequired = null;
            }

            if (!empty($data->completionscoredisabled) || !$autocompletion) {
                $data->completionscorerequired = null;
            }
        }

        return $data;
    }
}
