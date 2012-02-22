<?php
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once ($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/scorm/locallib.php');

class mod_scorm_mod_form extends moodleform_mod {

    function definition() {
        global $CFG, $COURSE, $OUTPUT;
        $cfg_scorm = get_config('scorm');

        $mform = $this->_form;

        if (!$CFG->slasharguments) {
            $mform->addElement('static', '', '',$OUTPUT->notification(get_string('slashargs', 'scorm'), 'notifyproblem'));
        }
//-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

// Name
        $mform->addElement('text', 'name', get_string('name'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');

// Summary
        $this->add_intro_editor(true);

        // Scorm types
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

        // Reference
        if (count($scormtypes) > 1) {
            $mform->addElement('select', 'scormtype', get_string('scormtype', 'scorm'), $scormtypes);
            $mform->addHelpButton('scormtype', 'scormtype', 'scorm');
            $mform->addElement('text', 'packageurl', get_string('packageurl', 'scorm'), array('size'=>60));
            $mform->setType('packageurl', PARAM_RAW);
            $mform->addHelpButton('packageurl', 'packageurl', 'scorm');
            $mform->disabledIf('packageurl', 'scormtype', 'eq', SCORM_TYPE_LOCAL);
        } else {
            $mform->addElement('hidden', 'scormtype', SCORM_TYPE_LOCAL);
        }

// New local package upload
        $maxbytes = get_max_upload_file_size($CFG->maxbytes, $COURSE->maxbytes);
        $mform->setMaxFileSize($maxbytes);
        $mform->addElement('filepicker', 'packagefile', get_string('package','scorm'));
        $mform->addHelpButton('packagefile', 'package', 'scorm');
        $mform->disabledIf('packagefile', 'scormtype', 'noteq', SCORM_TYPE_LOCAL);

//-------------------------------------------------------------------------------
// Time restrictions
        $mform->addElement('header', 'timerestricthdr', get_string('timerestrict', 'scorm'));

        $mform->addElement('date_time_selector', 'timeopen', get_string("scormopen", "scorm"),array('optional' => true));
        $mform->addElement('date_time_selector', 'timeclose', get_string("scormclose", "scorm"),array('optional' => true));
//-------------------------------------------------------------------------------
// Other Settings
        $mform->addElement('header', 'advanced', get_string('othersettings', 'form'));

// Grade Method
        $mform->addElement('select', 'grademethod', get_string('grademethod', 'scorm'), scorm_get_grade_method_array());
        $mform->addHelpButton('grademethod', 'grademethod', 'scorm');
        $mform->setDefault('grademethod', $cfg_scorm->grademethod);

// Maximum Grade
        for ($i=0; $i<=100; $i++) {
          $grades[$i] = "$i";
        }
        $mform->addElement('select', 'maxgrade', get_string('maximumgrade'), $grades);
        $mform->setDefault('maxgrade', $cfg_scorm->maxgrade);
        $mform->disabledIf('maxgrade', 'grademethod','eq', GRADESCOES);

// Attempts
        $mform->addElement('static', '', '' ,'<hr />');

// Max Attempts
        $mform->addElement('select', 'maxattempt', get_string('maximumattempts', 'scorm'), scorm_get_attempts_array());
        $mform->addHelpButton('maxattempt', 'maximumattempts', 'scorm');
        $mform->setDefault('maxattempt', $cfg_scorm->maxattempts);

// What Grade
        $mform->addElement('select', 'whatgrade', get_string('whatgrade', 'scorm'),  scorm_get_what_grade_array());
        $mform->disabledIf('whatgrade', 'maxattempt','eq',1);
        $mform->addHelpButton('whatgrade', 'whatgrade', 'scorm');
        $mform->setDefault('whatgrade', $cfg_scorm->whatgrade);
        $mform->setAdvanced('whatgrade');

// Display attempt status
        $mform->addElement('selectyesno', 'displayattemptstatus', get_string('displayattemptstatus', 'scorm'));
        $mform->addHelpButton('displayattemptstatus', 'displayattemptstatus', 'scorm');
        $mform->setDefault('displayattemptstatus', $cfg_scorm->displayattemptstatus);

// Force completed
        $mform->addElement('selectyesno', 'forcecompleted', get_string('forcecompleted', 'scorm'));
        $mform->addHelpButton('forcecompleted', 'forcecompleted', 'scorm');
        $mform->setDefault('forcecompleted', $cfg_scorm->forcecompleted);
        $mform->setAdvanced('forcecompleted');

// Force new attempt
        $mform->addElement('selectyesno', 'forcenewattempt', get_string('forcenewattempt', 'scorm'));
        $mform->addHelpButton('forcenewattempt', 'forcenewattempt', 'scorm');
        $mform->setDefault('forcenewattempt', $cfg_scorm->forcenewattempt);
        $mform->setAdvanced('forcenewattempt');

// Last attempt lock - lock the enter button after the last available attempt has been made
        $mform->addElement('selectyesno', 'lastattemptlock', get_string('lastattemptlock', 'scorm'));
        $mform->addHelpButton('lastattemptlock', 'lastattemptlock', 'scorm');
        $mform->setDefault('lastattemptlock', $cfg_scorm->lastattemptlock);
        $mform->setAdvanced('lastattemptlock');

// Activation period
/*        $mform->addElement('static', '', '' ,'<hr />');
        $mform->addElement('static', 'activation', get_string('activation','scorm'));
        $datestartgrp = array();
        $datestartgrp[] = &$mform->createElement('date_time_selector', 'startdate');
        $datestartgrp[] = &$mform->createElement('checkbox', 'startdisabled', null, get_string('disable'));
        $mform->addGroup($datestartgrp, 'startdategrp', get_string('from'), ' ', false);
        $mform->setDefault('startdate', 0);
        $mform->setDefault('startdisabled', 1);
        $mform->disabledIf('startdategrp', 'startdisabled', 'checked');

        $dateendgrp = array();
        $dateendgrp[] = &$mform->createElement('date_time_selector', 'enddate');
        $dateendgrp[] = &$mform->createElement('checkbox', 'enddisabled', null, get_string('disable'));
        $mform->addGroup($dateendgrp, 'dateendgrp', get_string('to'), ' ', false);
        $mform->setDefault('enddate', 0);
        $mform->setDefault('enddisabled', 1);
        $mform->disabledIf('dateendgrp', 'enddisabled', 'checked');
*/

// Framed / Popup Window
        $mform->addElement('select', 'popup', get_string('display', 'scorm'), scorm_get_popup_display_array());
        $mform->setDefault('popup', $cfg_scorm->popup);
        $mform->setAdvanced('popup');

// Width
        $mform->addElement('text', 'width', get_string('width','scorm'), 'maxlength="5" size="5"');
        $mform->setDefault('width', $cfg_scorm->framewidth);
        $mform->setType('width', PARAM_INT);
        $mform->setAdvanced('width');
        $mform->disabledIf('width', 'popup', 'eq', 0);

// Height
        $mform->addElement('text', 'height', get_string('height','scorm'), 'maxlength="5" size="5"');
        $mform->setDefault('height', $cfg_scorm->frameheight);
        $mform->setType('height', PARAM_INT);
        $mform->setAdvanced('height');
        $mform->disabledIf('height', 'popup', 'eq', 0);

// Window Options
        $winoptgrp = array();
        foreach(scorm_get_popup_options_array() as $key => $value){
            $winoptgrp[] = &$mform->createElement('checkbox', $key, '', get_string($key, 'scorm'));
            $mform->setDefault($key, $value);
        }
        $mform->addGroup($winoptgrp, 'winoptgrp', get_string('options','scorm'), '<br />', false);
        $mform->setAdvanced('winoptgrp');
        $mform->disabledIf('winoptgrp', 'popup', 'eq', 0);

// Skip view page
        $mform->addElement('select', 'skipview', get_string('skipview', 'scorm'),scorm_get_skip_view_array());
        $mform->addHelpButton('skipview', 'skipview', 'scorm');
        $mform->setDefault('skipview', $cfg_scorm->skipview);
        $mform->setAdvanced('skipview');

// Hide Browse
        $mform->addElement('selectyesno', 'hidebrowse', get_string('hidebrowse', 'scorm'));
        $mform->addHelpButton('hidebrowse', 'hidebrowse', 'scorm');
        $mform->setDefault('hidebrowse', $cfg_scorm->hidebrowse);
        $mform->setAdvanced('hidebrowse');

// Display course structure
        $mform->addElement('selectyesno', 'displaycoursestructure', get_string('displaycoursestructure', 'scorm'));
        $mform->addHelpButton('displaycoursestructure', 'displaycoursestructure', 'scorm');
        $mform->setDefault('displaycoursestructure', $cfg_scorm->displaycoursestructure);
        $mform->setAdvanced('displaycoursestructure');

// Toc display
        $mform->addElement('select', 'hidetoc', get_string('hidetoc', 'scorm'), scorm_get_hidetoc_array());
        $mform->addHelpButton('hidetoc', 'hidetoc', 'scorm');
        $mform->setDefault('hidetoc', $cfg_scorm->hidetoc);
        $mform->setAdvanced('hidetoc');

// Hide Navigation panel
        $mform->addElement('selectyesno', 'hidenav', get_string('hidenav', 'scorm'));
        $mform->setDefault('hidenav', $cfg_scorm->hidenav);
        $mform->setAdvanced('hidenav');
        $mform->disabledIf('hidenav', 'hidetoc', 'noteq', 0);

// Autocontinue
        $mform->addElement('selectyesno', 'auto', get_string('autocontinue', 'scorm'));
        $mform->addHelpButton('auto', 'autocontinue', 'scorm');
        $mform->setDefault('auto', $cfg_scorm->auto);
        $mform->setAdvanced('auto');

        if (count($scormtypes) > 1) {
            // Update packages timing
            $mform->addElement('select', 'updatefreq', get_string('updatefreq', 'scorm'), scorm_get_updatefreq_array());
            $mform->setDefault('updatefreq', $cfg_scorm->updatefreq);
            $mform->setAdvanced('updatefreq');
            $mform->addHelpButton('updatefreq', 'updatefreq', 'scorm');
            $mform->disabledIf('updatefreq', 'scormtype', 'eq', SCORM_TYPE_LOCAL);
        } else {
            $mform->addElement('hidden', 'updatefreq', 0);
        }
//-------------------------------------------------------------------------------
// Hidden Settings
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
        // buttons
        $this->add_action_buttons();

    }

    function data_preprocessing(&$default_values) {
        global $COURSE;

        if (isset($default_values['popup']) && ($default_values['popup'] == 1) && isset($default_values['options'])) {
            if (!empty($default_values['options'])) {
                $options = explode(',',$default_values['options']);
                foreach ($options as $option) {
                    list($element,$value) = explode('=',$option);
                    $element = trim($element);
                    $default_values[$element] = trim($value);
                }
            }
        }
        if (isset($default_values['grademethod'])) {
            $default_values['grademethod'] = intval($default_values['grademethod']);
        }
        if (isset($default_values['width']) && (strpos($default_values['width'],'%') === false) && ($default_values['width'] <= 100)) {
            $default_values['width'] .= '%';
        }
        if (isset($default_values['width']) && (strpos($default_values['height'],'%') === false) && ($default_values['height'] <= 100)) {
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
            $default_values['pkgtype'] = (substr($default_values['version'],0,5) == 'SCORM') ? 'scorm':'aicc';
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
    }

    function validation($data, $files) {
        global $CFG;
        $errors = parent::validation($data, $files);

        $type = $data['scormtype'];

        if ($type === SCORM_TYPE_LOCAL) {
            if (!empty($data['update'])) {
                //ok, not required

            } else if (empty($data['packagefile'])) {
                $errors['packagefile'] = get_string('required');

            } else {
                $files = $this->get_draft_files('packagefile');
                if (count($files)<1) {
                    $errors['packagefile'] = get_string('required');
                    return $errors;
                }
                $file = reset($files);
                $filename = $CFG->dataroot.'/temp/scormimport/scrom_'.time();
                make_upload_directory('temp/scormimport');
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
            if (!preg_match('/(http:\/\/|https:\/\/|www).*\/imsmanifest.xml$/i', $reference)) {
                $errors['packageurl'] = get_string('required'); // TODO: improve help
            }

        } else if ($type === 'packageurl') {
            $reference = $data['reference'];
            if (!preg_match('/(http:\/\/|https:\/\/|www).*(\.zip|\.pif)$/i', $reference)) {
                $errors['packageurl'] = get_string('required'); // TODO: improve help
            }

        } else if ($type === SCORM_TYPE_IMSREPOSITORY) {
            $reference = $data['packageurl'];
            if (stripos($reference, '#') !== 0) {
                $errors['packageurl'] = get_string('required');
            }
        }

        return $errors;
    }

    //need to translate the "options" and "reference" field.
    function set_data($default_values) {
        $default_values = (array)$default_values;

        if (isset($default_values['scormtype']) and isset($default_values['reference'])) {
            switch ($default_values['scormtype']) {
                case SCORM_TYPE_LOCALSYNC :
                case SCORM_TYPE_EXTERNAL:
                case SCORM_TYPE_IMSREPOSITORY:
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
}

