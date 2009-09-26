<?php // $Id$
require_once ($CFG->dirroot.'/lib/formslib.php');

class mod_glossary_entry_form extends moodleform {

    function definition() {

        global $CFG, $COURSE;
        $mform    =& $this->_form;

        $glossary =& $this->_customdata['glossary'];
        $mode     =& $this->_customdata['mode'];
        $cm       =& $this->_customdata['cm'];
        $hook     =& $this->_customdata['hook'];
        $e        =& $this->_customdata['e'];

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'concept', get_string('concept', 'glossary'));
        $mform->setType('concept', PARAM_TEXT);
        $mform->addRule('concept', null, 'required', null, 'client');

        $mform->addElement('htmleditor', 'definition', get_string('definition', 'glossary'), array('rows'=>20));
        $mform->setType('definition', PARAM_RAW);
        $mform->addRule('definition', null, 'required', null, 'client');
        $mform->setHelpButton('definition', array('writing', 'richtext'), false, 'editorhelpbutton');

        $mform->addElement('format');

        $categories = array();
        if ($categories = get_records_menu('glossary_categories', 'glossaryid', $glossary->id, 'name ASC', 'id, name')){
            $categories = array(0 => get_string('notcategorised', 'glossary')) + $categories;
        } else {
            $categories = array(0 => get_string('notcategorised', 'glossary'));
        }

        $categoriesEl =& $mform->addElement('select', 'categories', get_string('categories', 'glossary'), $categories);
        $categoriesEl->setMultiple(true);
        $categoriesEl->setSize(5);

        $mform->addElement('textarea', 'aliases', get_string('aliases', 'glossary'), 'rows="2" cols="40"');
        $mform->setType('aliases', PARAM_TEXT);
        $mform->setHelpButton('aliases', array('aliases2', strip_tags(get_string('aliases', 'glossary')), 'glossary'));

        $this->set_upload_manager(new upload_manager('attachment', true, false, $COURSE, false, 0, true, true, false));
        $mform->addElement('file', 'attachment', get_string('attachment', 'forum'));
        $mform->setHelpButton('attachment', array('attachment', get_string('attachment', 'glossary'), 'glossary'));

        if (isset($CFG->glossary_linkentries)) {
            $usedynalink = $CFG->glossary_linkentries;
        } else {
            $usedynalink = 0;
        }
        if (isset($CFG->glossary_casesensitive)) {
            $casesensitive = $CFG->glossary_casesensitive;
        } else {
            $casesensitive = 0;
        }
        if (isset($CFG->glossary_fullmatch)) {
            $fullmatch = $CFG->glossary_fullmatch;
        } else {
            $fullmatch = 0;
        }
        if ( !$glossary->usedynalink ) {
            $mform->addElement('hidden', 'usedynalink', $usedynalink);
            $mform->setType('usedynalink', PARAM_INT);
            $mform->addElement('hidden', 'casesensitive', $casesensitive);
            $mform->setType('casesensitive', PARAM_INT);
            $mform->addElement('hidden', 'fullmatch', $fullmatch);
            $mform->setType('fullmatch', PARAM_INT);
        } else {
//-------------------------------------------------------------------------------
            $mform->addElement('header', 'linkinghdr', get_string('linking', 'glossary'));

            $mform->addElement('checkbox', 'usedynalink', get_string('entryusedynalink', 'glossary'));
            $mform->setHelpButton('usedynalink', array('usedynalinkentry', strip_tags(get_string('usedynalink', 'glossary')), 'glossary'));
            $mform->setDefault('usedynalink', $usedynalink);

            $mform->addElement('checkbox', 'casesensitive', get_string('casesensitive', 'glossary'));
            $mform->setHelpButton('casesensitive', array('casesensitive', strip_tags(get_string('casesensitive', 'glossary')), 'glossary'));
            $mform->disabledIf('casesensitive', 'usedynalink');
            $mform->setDefault('casesensitive', $casesensitive);

            $mform->addElement('checkbox', 'fullmatch', get_string('fullmatch', 'glossary'));
            $mform->setHelpButton('fullmatch', array('fullmatch', strip_tags(get_string('fullmatch', 'glossary')), 'glossary'));
            $mform->disabledIf('fullmatch', 'usedynalink');
            $mform->setDefault('fullmatch', $fullmatch);
        }

        $mform->addElement('hidden', 'e', $e);
        $mform->setType('e', PARAM_INT);
        $mform->addElement('hidden', 'id', $cm->id);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'mode', $mode);
        $mform->setType('mode', PARAM_ALPHA);
        $mform->addElement('hidden', 'hook', $hook);
        $mform->setType('hook', PARAM_ALPHANUM);

//-------------------------------------------------------------------------------
        $this->add_action_buttons();
    }

    function validation($data, $files) {
        global $CFG, $USER;
        $errors = parent::validation($data, $files);
        $e = $this->_customdata['e'];
        $glossary = $this->_customdata['glossary'];
        $context = $this->_customdata['context'];
        $data['concept'] = trim($data['concept']);
        if ($e) {
            //We are updating an entry, so we compare current session user with
            //existing entry user to avoid some potential problems if secureforms=off
            //Perhaps too much security? Anyway thanks to skodak (Bug 1823)
            $old = get_record('glossary_entries', 'id', $e);
            $ineditperiod = ((time() - $old->timecreated <  $CFG->maxeditingtime) || $glossary->editalways);
            if ( (!$ineditperiod  || $USER->id != $old->userid) and !has_capability('mod/glossary:manageentries', $context)) {
                if ( $USER->id != $old->userid ) {
                    $errors['concept'] = get_string('errcannoteditothers', 'glossary');
                } elseif (!$ineditperiod) {
                    $errors['concept'] = get_string('erredittimeexpired', 'glossary');
                }
            }
            if ( !$glossary->allowduplicatedentries ) {
                if ($dupentries = get_records('glossary_entries', 'lower(concept)', moodle_strtolower($data['concept']))) {
                    foreach ($dupentries as $curentry) {
                        if ( $glossary->id == $curentry->glossaryid ) {
                           if ( $curentry->id != $e ) {
                               $errors['concept'] = get_string('errconceptalreadyexists', 'glossary');
                               break;
                           }
                        }
                    }
                }
            }

        } else {
            if ( !$glossary->allowduplicatedentries ) {
                if ($dupentries = get_record('glossary_entries', 'lower(concept)', moodle_strtolower($data['concept']), 'glossaryid', $glossary->id)) {
                    $errors['concept'] = get_string('errconceptalreadyexists', 'glossary');
                }
            }
        }
        return $errors;
    }

}
?>
