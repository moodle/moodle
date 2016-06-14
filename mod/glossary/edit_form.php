<?php
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once ($CFG->dirroot.'/lib/formslib.php');

class mod_glossary_entry_form extends moodleform {

    function definition() {
        global $CFG, $DB;

        $mform = $this->_form;

        $currententry      = $this->_customdata['current'];
        $glossary          = $this->_customdata['glossary'];
        $cm                = $this->_customdata['cm'];
        $definitionoptions = $this->_customdata['definitionoptions'];
        $attachmentoptions = $this->_customdata['attachmentoptions'];

        $context  = context_module::instance($cm->id);
        // Prepare format_string/text options
        $fmtoptions = array(
            'context' => $context);

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'concept', get_string('concept', 'glossary'));
        $mform->setType('concept', PARAM_TEXT);
        $mform->addRule('concept', null, 'required', null, 'client');

        $mform->addElement('editor', 'definition_editor', get_string('definition', 'glossary'), null, $definitionoptions);
        $mform->setType('definition_editor', PARAM_RAW);
        $mform->addRule('definition_editor', get_string('required'), 'required', null, 'client');

        if ($categories = $DB->get_records_menu('glossary_categories', array('glossaryid'=>$glossary->id), 'name ASC', 'id, name')){
            foreach ($categories as $id => $name) {
                $categories[$id] = format_string($name, true, $fmtoptions);
            }
            $categories = array(0 => get_string('notcategorised', 'glossary')) + $categories;
            $categoriesEl = $mform->addElement('select', 'categories', get_string('categories', 'glossary'), $categories);
            $categoriesEl->setMultiple(true);
            $categoriesEl->setSize(5);
        }

        $mform->addElement('textarea', 'aliases', get_string('aliases', 'glossary'), 'rows="2" cols="40"');
        $mform->setType('aliases', PARAM_TEXT);
        $mform->addHelpButton('aliases', 'aliases', 'glossary');

        $mform->addElement('filemanager', 'attachment_filemanager', get_string('attachment', 'glossary'), null, $attachmentoptions);
        $mform->addHelpButton('attachment_filemanager', 'attachment', 'glossary');

        if (!$glossary->usedynalink) {
            $mform->addElement('hidden', 'usedynalink',   $CFG->glossary_linkentries);
            $mform->setType('usedynalink', PARAM_INT);
            $mform->addElement('hidden', 'casesensitive', $CFG->glossary_casesensitive);
            $mform->setType('casesensitive', PARAM_INT);
            $mform->addElement('hidden', 'fullmatch',     $CFG->glossary_fullmatch);
            $mform->setType('fullmatch', PARAM_INT);

        } else {
//-------------------------------------------------------------------------------
            $mform->addElement('header', 'linkinghdr', get_string('linking', 'glossary'));

            $mform->addElement('checkbox', 'usedynalink', get_string('entryusedynalink', 'glossary'));
            $mform->addHelpButton('usedynalink', 'entryusedynalink', 'glossary');
            $mform->setDefault('usedynalink', $CFG->glossary_linkentries);

            $mform->addElement('checkbox', 'casesensitive', get_string('casesensitive', 'glossary'));
            $mform->addHelpButton('casesensitive', 'casesensitive', 'glossary');
            $mform->disabledIf('casesensitive', 'usedynalink');
            $mform->setDefault('casesensitive', $CFG->glossary_casesensitive);

            $mform->addElement('checkbox', 'fullmatch', get_string('fullmatch', 'glossary'));
            $mform->addHelpButton('fullmatch', 'fullmatch', 'glossary');
            $mform->disabledIf('fullmatch', 'usedynalink');
            $mform->setDefault('fullmatch', $CFG->glossary_fullmatch);
        }

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'cmid');
        $mform->setType('cmid', PARAM_INT);

//-------------------------------------------------------------------------------
        $this->add_action_buttons();

//-------------------------------------------------------------------------------
        $this->set_data($currententry);
    }

    function validation($data, $files) {
        global $CFG, $USER, $DB;
        $errors = parent::validation($data, $files);

        $glossary = $this->_customdata['glossary'];
        $cm       = $this->_customdata['cm'];
        $context  = context_module::instance($cm->id);

        $id = (int)$data['id'];
        $data['concept'] = trim($data['concept']);

        if ($id) {
            //We are updating an entry, so we compare current session user with
            //existing entry user to avoid some potential problems if secureforms=off
            //Perhaps too much security? Anyway thanks to skodak (Bug 1823)
            $old = $DB->get_record('glossary_entries', array('id'=>$id));
            $ineditperiod = ((time() - $old->timecreated <  $CFG->maxeditingtime) || $glossary->editalways);
            if ((!$ineditperiod || $USER->id != $old->userid) and !has_capability('mod/glossary:manageentries', $context)) {
                if ($USER->id != $old->userid) {
                    $errors['concept'] = get_string('errcannoteditothers', 'glossary');
                } elseif (!$ineditperiod) {
                    $errors['concept'] = get_string('erredittimeexpired', 'glossary');
                }
            }
            if (!$glossary->allowduplicatedentries) {
                if ($DB->record_exists_select('glossary_entries',
                        'glossaryid = :glossaryid AND LOWER(concept) = :concept AND id != :id', array(
                            'glossaryid' => $glossary->id,
                            'concept'    => core_text::strtolower($data['concept']),
                            'id'         => $id))) {
                    $errors['concept'] = get_string('errconceptalreadyexists', 'glossary');
                }
            }

        } else {
            if (!$glossary->allowduplicatedentries) {
                if ($DB->record_exists_select('glossary_entries',
                        'glossaryid = :glossaryid AND LOWER(concept) = :concept', array(
                            'glossaryid' => $glossary->id,
                            'concept'    => core_text::strtolower($data['concept'])))) {
                    $errors['concept'] = get_string('errconceptalreadyexists', 'glossary');
                }
            }
        }

        return $errors;
    }
}

