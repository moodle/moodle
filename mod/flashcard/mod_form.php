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
 * This view allows checking deck states
 *
 * @package mod_flashcard
 * @category mod
 * @author Gustav Delius
 * @author Valery Fremaux for Moodle 2
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @version Moodle 2.0
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->libdir.'/questionlib.php');
require_once($CFG->dirroot.'/mod/flashcard/locallib.php');

/**
 * overrides moodleform for flashcard setup
 */
class mod_flashcard_mod_form extends moodleform_mod {

    public $currentfiles = array();

    public function definition() {
        global $CFG, $COURSE, $DB;

        $mform =& $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('name'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $this->standard_intro_elements();

        $startdatearray[] = &$mform->createElement('date_time_selector', 'starttime', '');
        $startdatearray[] = &$mform->createElement('checkbox', 'starttimeenable', '');
        $mform->addGroup($startdatearray, 'startfrom', get_string('starttime', 'flashcard'), ' ', false);
        $mform->disabledIf('startfrom', 'starttimeenable');

        $enddatearray[] = &$mform->createElement('date_time_selector', 'endtime', '');
        $enddatearray[] = &$mform->createElement('checkbox', 'endtimeenable', '');
        $mform->addGroup($enddatearray, 'endfrom', get_string('endtime', 'flashcard'), ' ', false);
        $mform->disabledIf('endfrom', 'endtimeenable');

        $modeloptions[FLASHCARD_MODEL_BOTH] = get_string('bothmodels', 'flashcard');
        $modeloptions[FLASHCARD_MODEL_LEITNER] = get_string('leitner', 'flashcard');
        $modeloptions[FLASHCARD_MODEL_FREEUSE] = get_string('freeuse', 'flashcard');
        $mform->addElement('select', 'models', get_string('models', 'flashcard'), $modeloptions);
        $mform->addHelpButton('models', 'models', 'flashcard');

        $mediaoptions[FLASHCARD_MEDIA_TEXT] = get_string('text', 'flashcard');
        $mediaoptions[FLASHCARD_MEDIA_IMAGE] = get_string('image', 'flashcard');
        $mediaoptions[FLASHCARD_MEDIA_SOUND] = get_string('sound', 'flashcard');
        $mediaoptions[FLASHCARD_MEDIA_IMAGE_AND_SOUND] = get_string('imageplussound', 'flashcard');
        $mediaoptions[FLASHCARD_MEDIA_VIDEO] = get_string('video', 'flashcard').' (Experimental)';
        $mform->addElement('select', 'questionsmediatype', get_string('questionsmediatype', 'flashcard'), $mediaoptions);
        $mform->addHelpButton('questionsmediatype', 'mediatypes', 'flashcard');

        $mform->addElement('select', 'answersmediatype', get_string('answersmediatype', 'flashcard'), $mediaoptions);
        $mform->addHelpButton('answersmediatype', 'mediatypes', 'flashcard');

        $yesnooptions['0'] = get_string('no');
        $yesnooptions['1'] = get_string('yes');
        $mform->addElement('select', 'audiostart', get_string('audiostart', 'flashcard'), $yesnooptions);
        $mform->setType('audiostart', PARAM_BOOL);

        $mform->addElement('selectyesno', 'flipdeck', get_string('flipdeck', 'flashcard'));
        $mform->setAdvanced('flipdeck');
        $mform->setType('flipdeck', PARAM_BOOL);
        $mform->addHelpButton('flipdeck', 'flipdeck', 'flashcard');

        $options['2'] = 2;
        $options['3'] = 3;
        $options['4'] = 4;
        $mform->addElement('select', 'decks', get_string('decks', 'flashcard'), $options);
        $mform->setType('decks', PARAM_INT);
        $mform->setDefault('decks', 2);
        $mform->addHelpButton('decks', 'decks', 'flashcard');

        $mform->addElement('selectyesno', 'autodowngrade', get_string('autodowngrade', 'flashcard'));
        $mform->addHelpButton('autodowngrade', 'autodowngrade', 'flashcard');
        $mform->setAdvanced('autodowngrade');

        $mform->addElement('text', 'deck2_release', get_string('deck2_release', 'flashcard'), array('size' => '5'));
        $mform->addHelpButton('deck2_release', 'deck_release', 'flashcard');
        $mform->setType('deck2_release', PARAM_INT);
        $mform->setDefault('deck2_release', 96);
        $mform->addRule('deck2_release', get_string('numericrequired', 'flashcard'), 'numeric', null, 'client');
        $mform->setAdvanced('deck2_release');

        $mform->addElement('text', 'deck3_release', get_string('deck3_release', 'flashcard'), array('size' => '5'));
        $mform->setType('deck3_release', PARAM_INT);
        $mform->setDefault('deck3_release', 96);
        $mform->addRule('deck3_release', get_string('numericrequired', 'flashcard'), 'numeric', null, 'client');
        $mform->disabledIf('deck3_release', 'decks', 'eq', 2);
        $mform->setAdvanced('deck3_release');

        $mform->addElement('text', 'deck4_release', get_string('deck4_release', 'flashcard'), array('size' => '5'));
        $mform->setType('deck4_release', PARAM_INT);
        $mform->setDefault('deck4_release', 96);
        $mform->addRule('deck4_release', get_string('numericrequired', 'flashcard'), 'numeric', null, 'client');
        $mform->disabledIf('deck4_release', 'decks', 'neq', 4);
        $mform->setAdvanced('deck4_release');

        $mform->addElement('text', 'deck1_delay', get_string('deck1_delay', 'flashcard'), array('size' => '5'));
        $mform->addHelpButton('deck1_delay', 'deck_delay', 'flashcard');
        $mform->setType('deck1_delay', PARAM_INT);
        $mform->setDefault('deck1_delay', 48);
        $mform->addRule('deck1_delay', get_string('numericrequired', 'flashcard'), 'numeric', null, 'client');

        $mform->addElement('text', 'deck2_delay', get_string('deck2_delay', 'flashcard'), array('size' => '5'));
        $mform->setType('deck2_delay', PARAM_INT);
        $mform->setDefault('deck2_delay', 96);
        $mform->addRule('deck2_delay', get_string('numericrequired', 'flashcard'), 'numeric', null, 'client');

        $mform->addElement('text', 'deck3_delay', get_string('deck3_delay', 'flashcard'), array('size' => '5'));
        $mform->setType('deck3_delay', PARAM_INT);
        $mform->setDefault('deck3_delay', 168);
        $mform->addRule('deck3_delay', get_string('numericrequired', 'flashcard'), 'numeric', null, 'client');
        $mform->disabledIf('deck3_delay', 'decks', 'eq', 2);

        $mform->addElement('text', 'deck4_delay', get_string('deck4_delay', 'flashcard'), array('size' => '5'));
        $mform->setType('deck4_delay', PARAM_INT);
        $mform->setDefault('deck4_delay', 336);
        $mform->addRule('deck4_delay', get_string('numericrequired', 'flashcard'), 'numeric', null, 'client');
        $mform->disabledIf('deck4_delay', 'decks', 'neq', 4);

        $mform->addElement('header', 'notifications_head', get_string('notifications', 'flashcard'));

        $mform->addElement('select', 'remindusers', get_string('remindusers', 'flashcard'), $yesnooptions);
        $mform->setType('remindusers', PARAM_BOOL);

        $mform->addElement('header', 'customfiles_head', get_string('customisationfiles', 'flashcard'));
        $mform->setAdvanced('customfiles_head');

        $customcardoptions = array('maxfiles' => 1,
                                   'maxbytes' => $COURSE->maxbytes,
                                   'accepted_types' => array('.jpg', '.png', '.gif'));

        $maxbytes = 100000;
        $label = get_string('cardfront', 'flashcard');
        $mform->addElement('filepicker', 'custombackfileid', $label, null, $customcardoptions);
        $mform->setAdvanced('custombackfileid');
        $label = get_string('cardback', 'flashcard');
        $mform->addElement('filepicker', 'customfrontfileid', $label, null, $customcardoptions);
        $mform->setAdvanced('customfrontfileid');
        $label = get_string('emptydeck', 'flashcard');
        $mform->addElement('filepicker', 'customemptyfileid', $label, null, $customcardoptions);
        $mform->setAdvanced('customemptyfileid');
        $label = get_string('reviewback', 'flashcard');
        $mform->addElement('filepicker', 'customreviewfileid', $label, null, $customcardoptions);
        $mform->setAdvanced('customreviewfileid');
        $label = get_string('reviewedback', 'flashcard');
        $mform->addElement('filepicker', 'customreviewedfileid', $label, null, $customcardoptions);
        $mform->setAdvanced('customreviewedfileid');
        $label = get_string('reviewedempty', 'flashcard');
        $mform->addElement('filepicker', 'customreviewemptyfileid', $label, null, $customcardoptions);
        $mform->setAdvanced('customreviewemptyfileid');

        $label = get_string('extracss', 'flashcard');
        $mform->addElement('textarea', 'extracss', $label, array('cols' => '60', 'rows' => 15));
        $mform->setAdvanced('extracss');

        $this->standard_coursemodule_elements();

        $this->add_action_buttons();
    }

    public function add_completion_rules() {
        $mform =& $this->_form;

        $group = array();
        $label = get_string('completionallviewed', 'flashcard');
        $group[] =& $mform->createElement('checkbox', 'completionallviewedenabled', '', $label);
        $group[] =& $mform->createElement('text', 'completionallviewed', 999, array('size' => 3));
        $mform->setType('completionallviewed', PARAM_INT);
        $label = get_string('completionallviewedgroup', 'flashcard');
        $mform->addGroup($group, 'completionallviewedgroup', $label, array(' '), false);
        $mform->disabledIf('completionallviewedgroup', 'completionallgoodenabled', 'checked');

        // $group = array();
        $label = get_string('completionallgoodenabled', 'flashcard');
        $mform->addElement('checkbox', 'completionallgoodenabled', '', $label);
        /*
        $group[] =& $mform->createElement('checkbox', 'completionallgoodenabled', '', $label);
        $label = get_string('completionallgoodgroup', 'flashcard');
        $mform->addGroup($group, 'completionallgoodgroup', $label, array(' '), false);
        */

        // return array('completionallviewedgroup', 'completionallgoodgroup');
        return array('completionallviewedgroup', 'completionallgoodenabled');
    }

    public function completion_rule_enabled($data) {
        return (!empty($data['completionallviewedenabled']) && $data['completionallviewed'] != 0) ||
            (!empty($data['completionallgoodenabled']));
    }

    public function data_preprocessing(&$defaultvalues) {
        parent::data_preprocessing($defaultvalues);

        /*
         * Set up the completion checkboxes which aren't part of standard data.
         * We also make the default value (if you turn on the checkbox) for those
         * numbers to be 1, this will not apply unless checkbox is ticked.
         */
        $defaultvalues['completionallviewedenabled'] = !empty($defaultvalues['completionallviewed']) ? 1 : 0;
        // $defaultvalues['completionallgoodenabled'] = !empty($defaultvalues['completionallgoodenabled']) ? 1 : 0;
    }

    public function set_data($data) {
        global $CFG;

        if (!empty($data->coursemodule)) {
            $context = context_module::instance($data->coursemodule);

            $maxbytes = $CFG->maxbytes;
            $options = array('subdirs' => 0, 'maxbytes' => $maxbytes, 'maxfiles' => 1);

            $draftitemid = file_get_submitted_draft_itemid('customfront');
            $maxbytes = 100000;
            file_prepare_draft_area($draftitemid, $context->id, 'mod_flashcard', 'customfront', 0, $options);
            $data->customfrontfileid = $draftitemid;

            $draftitemid = file_get_submitted_draft_itemid('customback');
            $maxbytes = 100000;
            file_prepare_draft_area($draftitemid, $context->id, 'mod_flashcard', 'customback', 0, $options);
            $data->custombackfileid = $draftitemid;

            $draftitemid = file_get_submitted_draft_itemid('customempty');
            $maxbytes = 100000;
            file_prepare_draft_area($draftitemid, $context->id, 'mod_flashcard', 'customempty', 0, $options);
            $data->customemptyfileid = $draftitemid;

            $draftitemid = file_get_submitted_draft_itemid('customreview');
            $maxbytes = 100000;
            file_prepare_draft_area($draftitemid, $context->id, 'mod_flashcard', 'customreview', 0, $options);
            $data->customreviewfileid = $draftitemid;

            $draftitemid = file_get_submitted_draft_itemid('customreview');
            $maxbytes = 100000;
            file_prepare_draft_area($draftitemid, $context->id, 'mod_flashcard', 'customreviewed', 0, $options);
            $data->customreviewedfileid = $draftitemid;

            $draftitemid = file_get_submitted_draft_itemid('customreviewempty');
            $maxbytes = 100000;
            file_prepare_draft_area($draftitemid, $context->id, 'mod_flashcard', 'customreviewempty', 0, $options);
            $data->customreviewemptyfileid = $draftitemid;
        }

        if (empty($data->extracss)) {
            $data->extracss = '
/* panel div for question */
.flashcard-question{
}
/* panel div for answer */
.flashcard-answer{
}
';
        }

        parent::set_data($data);
    }

    public function get_data() {
        $data = parent::get_data();
        if (!$data) {
            return false;
        }

        // Turn off completion settings if the checkboxes aren't ticked.
        if (!empty($data->completionunlocked)) {
            // Weird effect of form resubmission. nothing in submited values. but why ?
            $data->completionallviewedenabled = false;
            if (!empty($_POST['completionallviewedenabled'])) {
                $data->completionallviewedenabled = clean_param($_POST['completionallviewedenabled'], PARAM_BOOL);
            }
            $data->completionallviewed = false;
            if (!empty($_POST['completionallviewed'])) {
                $data->completionallviewed = clean_param($_POST['completionallviewed'], PARAM_BOOL);
            }
            $data->completionallgoodenabled = false;
            if (!empty($_POST['completionallgoodenabled'])) {
                $data->completionallgoodenabled = clean_param($_POST['completionallgoodenabled'], PARAM_BOOL);
            }

            $autocompletion = !empty($data->completion) && $data->completion == COMPLETION_TRACKING_AUTOMATIC;

            if (empty($data->completionallviewedenabled) || !$autocompletion) {
                $data->completionallviewed = 0;
            } else {
                $data->completionallviewed = 999;
            }

            if (empty($data->completionallgoodenabled) || !$autocompletion) {
                $data->completionallgood = 0;
            } else {
                $data->completionallgood = 1;
            }
        }
        return $data;
    }

    public function validation($data, $files = array()) {
        $errors = parent::validation($data, $files);

        if ($data['starttime'] > $data['endtime']) {
            $errors['endfrom'] = get_string('mustbehigherthanstart', 'flashcard');
        }

        if ($data['decks'] >= 2) {
            if ($data['deck1_delay'] > $data['deck2_delay']) {
                $errors['deck2_delay'] = get_string('mustbegreaterthanabove');
            }
        }
        if ($data['decks'] >= 3) {
            if ($data['deck2_delay'] > $data['deck3_delay']) {
                $errors['deck3_delay'] = get_string('mustbegreaterthanabove');
            }
        }
        if ($data['decks'] >= 4) {
            if ($data['deck3_delay'] > $data['deck4_delay']) {
                $errors['deck4_delay'] = get_string('mustbegreaterthanabove');
            }
        }
        return $errors;
    }
}
