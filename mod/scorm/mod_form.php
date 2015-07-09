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
    die('Direct access to this script is forbidden.');    //  It must be included from a Moodle page.
}

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/scorm/locallib.php');

class mod_scorm_mod_form extends moodleform_mod {

    public function definition() {
        global $CFG, $COURSE, $OUTPUT;
        $cfgscorm = get_config('scorm');

        $mform = $this->_form;

        if (!$CFG->slasharguments) {
            $mform->addElement('static', '', '', $OUTPUT->notification(get_string('slashargs', 'scorm'), 'notifyproblem'));
        }

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
        $this->standard_intro_elements();

        // Package.
        $mform->addElement('header', 'packagehdr', get_string('packagehdr', 'scorm'));
        $mform->setExpanded('packagehdr', true);

        // Scorm types.
        $scormtypes = array(SCORM_TYPE_LOCAL => get_string('typelocal', 'scorm'));

        if ($cfgscorm->allowtypeexternal) {
            $scormtypes[SCORM_TYPE_EXTERNAL] = get_string('typeexternal', 'scorm');
        }

        if ($cfgscorm->allowtypelocalsync) {
            $scormtypes[SCORM_TYPE_LOCALSYNC] = get_string('typelocalsync', 'scorm');
        }

        if ($cfgscorm->allowtypeexternalaicc) {
            $scormtypes[SCORM_TYPE_AICCURL] = get_string('typeaiccurl', 'scorm');
        }

        // Reference.
        if (count($scormtypes) > 1) {
            $mform->addElement('select', 'scormtype', get_string('scormtype', 'scorm'), $scormtypes);
            $mform->setType('scormtype', PARAM_ALPHA);
            $mform->addHelpButton('scormtype', 'scormtype', 'scorm');
            $mform->addElement('text', 'packageurl', get_string('packageurl', 'scorm'), array('size' => 60));
            $mform->setType('packageurl', PARAM_RAW);
            $mform->addHelpButton('packageurl', 'packageurl', 'scorm');
            $mform->disabledIf('packageurl', 'scormtype', 'eq', SCORM_TYPE_LOCAL);
        } else {
            $mform->addElement('hidden', 'scormtype', SCORM_TYPE_LOCAL);
            $mform->setType('scormtype', PARAM_ALPHA);
        }

        // New local package upload.
        $filemanageroptions = array();
        $filemanageroptions['accepted_types'] = array('.zip', '.xml');
        $filemanageroptions['maxbytes'] = 0;
        $filemanageroptions['maxfiles'] = 1;
        $filemanageroptions['subdirs'] = 0;

        $mform->addElement('filemanager', 'packagefile', get_string('package', 'scorm'), null, $filemanageroptions);
        $mform->addHelpButton('packagefile', 'package', 'scorm');
        $mform->disabledIf('packagefile', 'scormtype', 'noteq', SCORM_TYPE_LOCAL);

        // Update packages timing.
        $mform->addElement('select', 'updatefreq', get_string('updatefreq', 'scorm'), scorm_get_updatefreq_array());
        $mform->setType('updatefreq', PARAM_INT);
        $mform->setDefault('updatefreq', $cfgscorm->updatefreq);
        $mform->addHelpButton('updatefreq', 'updatefreq', 'scorm');

        // Display Settings.
        $mform->addElement('header', 'displaysettings', get_string('appearance'));

        // Framed / Popup Window.
        $mform->addElement('select', 'popup', get_string('display', 'scorm'), scorm_get_popup_display_array());
        $mform->setDefault('popup', $cfgscorm->popup);
        $mform->setAdvanced('popup', $cfgscorm->popup_adv);

        // Width.
        $mform->addElement('text', 'width', get_string('width', 'scorm'), 'maxlength="5" size="5"');
        $mform->setDefault('width', $cfgscorm->framewidth);
        $mform->setType('width', PARAM_INT);
        $mform->setAdvanced('width', $cfgscorm->framewidth_adv);
        $mform->disabledIf('width', 'popup', 'eq', 0);

        // Height.
        $mform->addElement('text', 'height', get_string('height', 'scorm'), 'maxlength="5" size="5"');
        $mform->setDefault('height', $cfgscorm->frameheight);
        $mform->setType('height', PARAM_INT);
        $mform->setAdvanced('height', $cfgscorm->frameheight_adv);
        $mform->disabledIf('height', 'popup', 'eq', 0);

        // Window Options.
        $winoptgrp = array();
        foreach (scorm_get_popup_options_array() as $key => $value) {
            $winoptgrp[] = &$mform->createElement('checkbox', $key, '', get_string($key, 'scorm'));
            $mform->setDefault($key, $value);
        }
        $mform->addGroup($winoptgrp, 'winoptgrp', get_string('options', 'scorm'), '<br />', false);
        $mform->disabledIf('winoptgrp', 'popup', 'eq', 0);
        $mform->setAdvanced('winoptgrp', $cfgscorm->winoptgrp_adv);

        // Display activity name.
        $mform->addElement('advcheckbox', 'displayactivityname', get_string('displayactivityname', 'scorm'));
        $mform->addHelpButton('displayactivityname', 'displayactivityname', 'scorm');
        $mform->setDefault('displayactivityname', $cfgscorm->displayactivityname);

        // Skip view page.
        $skipviewoptions = scorm_get_skip_view_array();
        if ($COURSE->format == 'singleactivity') { // Remove option that would cause a constant redirect.
            unset($skipviewoptions[SCORM_SKIPVIEW_ALWAYS]);
            if ($cfgscorm->skipview == SCORM_SKIPVIEW_ALWAYS) {
                $cfgscorm->skipview = SCORM_SKIPVIEW_FIRST;
            }
        }
        $mform->addElement('select', 'skipview', get_string('skipview', 'scorm'), $skipviewoptions);
        $mform->addHelpButton('skipview', 'skipview', 'scorm');
        $mform->setDefault('skipview', $cfgscorm->skipview);
        $mform->setAdvanced('skipview', $cfgscorm->skipview_adv);

        // Hide Browse.
        $mform->addElement('selectyesno', 'hidebrowse', get_string('hidebrowse', 'scorm'));
        $mform->addHelpButton('hidebrowse', 'hidebrowse', 'scorm');
        $mform->setDefault('hidebrowse', $cfgscorm->hidebrowse);
        $mform->setAdvanced('hidebrowse', $cfgscorm->hidebrowse_adv);

        // Display course structure.
        $mform->addElement('selectyesno', 'displaycoursestructure', get_string('displaycoursestructure', 'scorm'));
        $mform->addHelpButton('displaycoursestructure', 'displaycoursestructure', 'scorm');
        $mform->setDefault('displaycoursestructure', $cfgscorm->displaycoursestructure);
        $mform->setAdvanced('displaycoursestructure', $cfgscorm->displaycoursestructure_adv);

        // Toc display.
        $mform->addElement('select', 'hidetoc', get_string('hidetoc', 'scorm'), scorm_get_hidetoc_array());
        $mform->addHelpButton('hidetoc', 'hidetoc', 'scorm');
        $mform->setDefault('hidetoc', $cfgscorm->hidetoc);
        $mform->setAdvanced('hidetoc', $cfgscorm->hidetoc_adv);
        $mform->disabledIf('hidetoc', 'scormtype', 'eq', SCORM_TYPE_AICCURL);

        // Navigation panel display.
        $mform->addElement('select', 'nav', get_string('nav', 'scorm'), scorm_get_navigation_display_array());
        $mform->addHelpButton('nav', 'nav', 'scorm');
        $mform->setDefault('nav', $cfgscorm->nav);
        $mform->setAdvanced('nav', $cfgscorm->nav_adv);
        $mform->disabledIf('nav', 'hidetoc', 'noteq', SCORM_TOC_SIDE);

        // Navigation panel position from left.
        $mform->addElement('text', 'navpositionleft', get_string('fromleft', 'scorm'), 'maxlength="5" size="5"');
        $mform->setDefault('navpositionleft', $cfgscorm->navpositionleft);
        $mform->setType('navpositionleft', PARAM_INT);
        $mform->setAdvanced('navpositionleft', $cfgscorm->navpositionleft_adv);
        $mform->disabledIf('navpositionleft', 'hidetoc', 'noteq', SCORM_TOC_SIDE);
        $mform->disabledIf('navpositionleft', 'nav', 'noteq', SCORM_NAV_FLOATING);

        // Navigation panel position from top.
        $mform->addElement('text', 'navpositiontop', get_string('fromtop', 'scorm'), 'maxlength="5" size="5"');
        $mform->setDefault('navpositiontop', $cfgscorm->navpositiontop);
        $mform->setType('navpositiontop', PARAM_INT);
        $mform->setAdvanced('navpositiontop', $cfgscorm->navpositiontop_adv);
        $mform->disabledIf('navpositiontop', 'hidetoc', 'noteq', SCORM_TOC_SIDE);
        $mform->disabledIf('navpositiontop', 'nav', 'noteq', SCORM_NAV_FLOATING);

        // Display attempt status.
        $mform->addElement('select', 'displayattemptstatus', get_string('displayattemptstatus', 'scorm'),
                           scorm_get_attemptstatus_array());
        $mform->addHelpButton('displayattemptstatus', 'displayattemptstatus', 'scorm');
        $mform->setDefault('displayattemptstatus', $cfgscorm->displayattemptstatus);
        $mform->setAdvanced('displayattemptstatus', $cfgscorm->displayattemptstatus_adv);

        // Availability.
        $mform->addElement('header', 'availability', get_string('availability'));

        $mform->addElement('date_time_selector', 'timeopen', get_string("scormopen", "scorm"), array('optional' => true));
        $mform->addElement('date_time_selector', 'timeclose', get_string("scormclose", "scorm"), array('optional' => true));

        // Grade Settings.
        $mform->addElement('header', 'gradesettings', get_string('grade'));

        // Grade Method.
        $mform->addElement('select', 'grademethod', get_string('grademethod', 'scorm'), scorm_get_grade_method_array());
        $mform->addHelpButton('grademethod', 'grademethod', 'scorm');
        $mform->setDefault('grademethod', $cfgscorm->grademethod);

        // Maximum Grade.
        for ($i = 0; $i <= 100; $i++) {
            $grades[$i] = "$i";
        }
        $mform->addElement('select', 'maxgrade', get_string('maximumgrade'), $grades);
        $mform->setDefault('maxgrade', $cfgscorm->maxgrade);
        $mform->disabledIf('maxgrade', 'grademethod', 'eq', GRADESCOES);

        // Attempts management.
        $mform->addElement('header', 'attemptsmanagementhdr', get_string('attemptsmanagement', 'scorm'));

        // Max Attempts.
        $mform->addElement('select', 'maxattempt', get_string('maximumattempts', 'scorm'), scorm_get_attempts_array());
        $mform->addHelpButton('maxattempt', 'maximumattempts', 'scorm');
        $mform->setDefault('maxattempt', $cfgscorm->maxattempt);

        // What Grade.
        $mform->addElement('select', 'whatgrade', get_string('whatgrade', 'scorm'),  scorm_get_what_grade_array());
        $mform->disabledIf('whatgrade', 'maxattempt', 'eq', 1);
        $mform->addHelpButton('whatgrade', 'whatgrade', 'scorm');
        $mform->setDefault('whatgrade', $cfgscorm->whatgrade);

        // Force new attempt.
        $mform->addElement('selectyesno', 'forcenewattempt', get_string('forcenewattempt', 'scorm'));
        $mform->addHelpButton('forcenewattempt', 'forcenewattempt', 'scorm');
        $mform->setDefault('forcenewattempt', $cfgscorm->forcenewattempt);

        // Last attempt lock - lock the enter button after the last available attempt has been made.
        $mform->addElement('selectyesno', 'lastattemptlock', get_string('lastattemptlock', 'scorm'));
        $mform->addHelpButton('lastattemptlock', 'lastattemptlock', 'scorm');
        $mform->setDefault('lastattemptlock', $cfgscorm->lastattemptlock);

        // Compatibility settings.
        $mform->addElement('header', 'compatibilitysettingshdr', get_string('compatibilitysettings', 'scorm'));

        // Force completed.
        $mform->addElement('selectyesno', 'forcecompleted', get_string('forcecompleted', 'scorm'));
        $mform->addHelpButton('forcecompleted', 'forcecompleted', 'scorm');
        $mform->setDefault('forcecompleted', $cfgscorm->forcecompleted);

        // Autocontinue.
        $mform->addElement('selectyesno', 'auto', get_string('autocontinue', 'scorm'));
        $mform->addHelpButton('auto', 'autocontinue', 'scorm');
        $mform->setDefault('auto', $cfgscorm->auto);

        // Autocommit.
        $mform->addElement('selectyesno', 'autocommit', get_string('autocommit', 'scorm'));
        $mform->addHelpButton('autocommit', 'autocommit', 'scorm');
        $mform->setDefault('autocommit', $cfgscorm->autocommit);

        // Mastery score overrides status.
        $mform->addElement('selectyesno', 'masteryoverride', get_string('masteryoverride', 'scorm'));
        $mform->addHelpButton('masteryoverride', 'masteryoverride', 'scorm');
        $mform->setDefault('masteryoverride', $cfgscorm->masteryoverride);

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

        $this->standard_coursemodule_elements();

        // Buttons.
        $this->add_action_buttons();
    }

    public function data_preprocessing(&$defaultvalues) {
        global $COURSE;

        if (isset($defaultvalues['popup']) && ($defaultvalues['popup'] == 1) && isset($defaultvalues['options'])) {
            if (!empty($defaultvalues['options'])) {
                $options = explode(',', $defaultvalues['options']);
                foreach ($options as $option) {
                    list($element, $value) = explode('=', $option);
                    $element = trim($element);
                    $defaultvalues[$element] = trim($value);
                }
            }
        }
        if (isset($defaultvalues['grademethod'])) {
            $defaultvalues['grademethod'] = intval($defaultvalues['grademethod']);
        }
        if (isset($defaultvalues['width']) && (strpos($defaultvalues['width'], '%') === false)
                                           && ($defaultvalues['width'] <= 100)) {
            $defaultvalues['width'] .= '%';
        }
        if (isset($defaultvalues['width']) && (strpos($defaultvalues['height'], '%') === false)
                                           && ($defaultvalues['height'] <= 100)) {
            $defaultvalues['height'] .= '%';
        }
        $scorms = get_all_instances_in_course('scorm', $COURSE);
        $coursescorm = current($scorms);

        $draftitemid = file_get_submitted_draft_itemid('packagefile');
        file_prepare_draft_area($draftitemid, $this->context->id, 'mod_scorm', 'package', 0,
            array('subdirs' => 0, 'maxfiles' => 1));
        $defaultvalues['packagefile'] = $draftitemid;

        if (($COURSE->format == 'singleactivity') && ((count($scorms) == 0) || ($defaultvalues['instance'] == $coursescorm->id))) {
            $defaultvalues['redirect'] = 'yes';
            $defaultvalues['redirecturl'] = '../course/view.php?id='.$defaultvalues['course'];
        } else {
            $defaultvalues['redirect'] = 'no';
            $defaultvalues['redirecturl'] = '../mod/scorm/view.php?id='.$defaultvalues['coursemodule'];
        }
        if (isset($defaultvalues['version'])) {
            $defaultvalues['pkgtype'] = (substr($defaultvalues['version'], 0, 5) == 'SCORM') ? 'scorm' : 'aicc';
        }
        if (isset($defaultvalues['instance'])) {
            $defaultvalues['datadir'] = $defaultvalues['instance'];
        }
        if (empty($defaultvalues['timeopen'])) {
            $defaultvalues['timeopen'] = 0;
        }
        if (empty($defaultvalues['timeclose'])) {
            $defaultvalues['timeclose'] = 0;
        }

        // Set some completion default data.
        if (!empty($defaultvalues['completionstatusrequired']) && !is_array($defaultvalues['completionstatusrequired'])) {
            // Unpack values.
            $cvalues = array();
            foreach (scorm_status_options() as $key => $value) {
                if (($defaultvalues['completionstatusrequired'] & $key) == $key) {
                    $cvalues[$key] = 1;
                }
            }

            $defaultvalues['completionstatusrequired'] = $cvalues;
        }

        if (!isset($defaultvalues['completionscorerequired']) || !strlen($defaultvalues['completionscorerequired'])) {
            $defaultvalues['completionscoredisabled'] = 1;
        }
    }

    public function validation($data, $files) {
        global $CFG, $USER;
        $errors = parent::validation($data, $files);

        $type = $data['scormtype'];

        if ($type === SCORM_TYPE_LOCAL) {
            if (empty($data['packagefile'])) {
                $errors['packagefile'] = get_string('required');

            } else {
                $draftitemid = file_get_submitted_draft_itemid('packagefile');

                file_prepare_draft_area($draftitemid, $this->context->id, 'mod_scorm', 'packagefilecheck', null,
                    array('subdirs' => 0, 'maxfiles' => 1));

                // Get file from users draft area.
                $usercontext = context_user::instance($USER->id);
                $fs = get_file_storage();
                $files = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid, 'id', false);

                if (count($files) < 1) {
                    $errors['packagefile'] = get_string('required');
                    return $errors;
                }
                $file = reset($files);
                if (!$file->is_external_file() && !empty($data['updatefreq'])) {
                    // Make sure updatefreq is not set if using normal local file.
                    $errors['updatefreq'] = get_string('updatefreq_error', 'mod_scorm');
                }
                if (strtolower($file->get_filename()) == 'imsmanifest.xml') {
                    if (!$file->is_external_file()) {
                        $errors['packagefile'] = get_string('aliasonly', 'mod_scorm');
                    } else {
                        $repository = repository::get_repository_by_id($file->get_repository_id(), context_system::instance());
                        if (!$repository->supports_relative_file()) {
                            $errors['packagefile'] = get_string('repositorynotsupported', 'mod_scorm');
                        }
                    }
                } else if (strtolower(substr($file->get_filename(), -3)) == 'xml') {
                    $errors['packagefile'] = get_string('invalidmanifestname', 'mod_scorm');
                } else {
                    // Validate this SCORM package.
                    $errors = array_merge($errors, scorm_validate_package($file));
                }
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
    public function set_data($defaultvalues) {
        $defaultvalues = (array)$defaultvalues;

        if (isset($defaultvalues['scormtype']) and isset($defaultvalues['reference'])) {
            switch ($defaultvalues['scormtype']) {
                case SCORM_TYPE_LOCALSYNC :
                case SCORM_TYPE_EXTERNAL:
                case SCORM_TYPE_AICCURL:
                    $defaultvalues['packageurl'] = $defaultvalues['reference'];
            }
        }
        unset($defaultvalues['reference']);

        if (!empty($defaultvalues['options'])) {
            $options = explode(',', $defaultvalues['options']);
            foreach ($options as $option) {
                $opt = explode('=', $option);
                if (isset($opt[1])) {
                    $defaultvalues[$opt[0]] = $opt[1];
                }
            }
        }

        $this->data_preprocessing($defaultvalues);
        parent::set_data($defaultvalues);
    }

    public function add_completion_rules() {
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

    public function completion_rule_enabled($data) {
        $status = !empty($data['completionstatusrequired']);
        $score = empty($data['completionscoredisabled']) && strlen($data['completionscorerequired']);

        return $status || $score;
    }

    public function get_data($slashed = true) {
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

            if (!(isset($data->completionstatusrequired) && $autocompletion)) {
                $data->completionstatusrequired = null;
            }
            // Else do nothing: completionstatusrequired has been already converted
            //             into a correct integer representation.

            if (!empty($data->completionscoredisabled) || !$autocompletion) {
                $data->completionscorerequired = null;
            }
        }

        return $data;
    }
}
