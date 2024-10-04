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
 * Defines the editing form for the essay question type.
 *
 * @package    qtype
 * @subpackage essay
 * @copyright  2007 Jamie Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Essay question type editing form.
 *
 * @copyright  2007 Jamie Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_essay_edit_form extends question_edit_form {

    protected function definition_inner($mform) {
        $qtype = question_bank::get_qtype('essay');

        $mform->addElement('header', 'responseoptions', get_string('responseoptions', 'qtype_essay'));
        $mform->setExpanded('responseoptions');

        $mform->addElement('select', 'responseformat',
                get_string('responseformat', 'qtype_essay'), $qtype->response_formats());
        $mform->setDefault('responseformat', $this->get_default_value('responseformat', 'editor'));

        $mform->addElement('select', 'responserequired',
                get_string('responserequired', 'qtype_essay'), $qtype->response_required_options());
        $mform->setDefault('responserequired', $this->get_default_value('responserequired', 1));
        $mform->hideIf('responserequired', 'responseformat', 'eq', 'noinline');

        $mform->addElement('select', 'responsefieldlines',
                get_string('responsefieldlines', 'qtype_essay'), $qtype->response_sizes());
        $mform->setDefault('responsefieldlines', $this->get_default_value('responsefieldlines', 10));
        $mform->hideIf('responsefieldlines', 'responseformat', 'eq', 'noinline');

        // Create a text box that can be enabled/disabled for max/min word limits options.
        $wordlimitoptions = ['size' => '6', 'maxlength' => '6'];

        $strminwordlimit = get_string('minwordlimit', 'qtype_essay');
        $mingrp[] = $mform->createElement('checkbox', 'minwordenabled', '', get_string('enable'));
        $mform->setDefault('minwordenabled', 0);
        $mingrp[] = $mform->createElement('text', 'minwordlimit', '', $wordlimitoptions + ['aria-label' => $strminwordlimit]);
        $mform->setType('minwordlimit', PARAM_INT);
        $mform->addGroup($mingrp, 'mingroup', $strminwordlimit, ' ', false);
        $mform->addHelpButton('mingroup', 'minwordlimit', 'qtype_essay');
        $mform->disabledIf('minwordlimit', 'minwordenabled', 'notchecked');
        $mform->hideIf('mingroup', 'responserequired', 'eq', '0');
        $mform->hideIf('mingroup', 'responseformat', 'eq', 'noinline');

        $strmaxwordlimit = get_string('maxwordlimit', 'qtype_essay');
        $maxgrp[] = $mform->createElement('checkbox', 'maxwordenabled', '', get_string('enable'));
        $mform->setDefault('maxwordenabled', 0);
        $maxgrp[] = $mform->createElement('text', 'maxwordlimit', '', $wordlimitoptions + ['aria-label' => $strmaxwordlimit]);
        $mform->setType('maxwordlimit', PARAM_INT);
        $mform->addGroup($maxgrp, 'maxgroup', $strmaxwordlimit, ' ', false);
        $mform->addHelpButton('maxgroup', 'maxwordlimit', 'qtype_essay');
        $mform->disabledIf('maxwordlimit', 'maxwordenabled', 'notchecked');
        $mform->hideIf('maxgroup', 'responserequired', 'eq', '0');
        $mform->hideIf('maxgroup', 'responseformat', 'eq', 'noinline');

        $mform->addElement('select', 'attachments',
                get_string('allowattachments', 'qtype_essay'), $qtype->attachment_options());
        $mform->setDefault('attachments', $this->get_default_value('attachments', 0));

        $mform->addElement('select', 'attachmentsrequired',
                get_string('attachmentsrequired', 'qtype_essay'), $qtype->attachments_required_options());
        $mform->setDefault('attachmentsrequired', $this->get_default_value('attachmentsrequired', 0));
        $mform->addHelpButton('attachmentsrequired', 'attachmentsrequired', 'qtype_essay');
        $mform->hideIf('attachmentsrequired', 'attachments', 'eq', 0);

        $mform->addElement('filetypes', 'filetypeslist', get_string('acceptedfiletypes', 'qtype_essay'));
        $mform->addHelpButton('filetypeslist', 'acceptedfiletypes', 'qtype_essay');
        $mform->hideIf('filetypeslist', 'attachments', 'eq', 0);

        $mform->addElement('select', 'maxbytes', get_string('maxbytes', 'qtype_essay'), $qtype->max_file_size_options());
        $mform->setDefault('maxbytes', $this->get_default_value('maxbytes', 0));
        $mform->hideIf('maxbytes', 'attachments', 'eq', 0);

        $mform->addElement('header', 'responsetemplateheader', get_string('responsetemplateheader', 'qtype_essay'));
        $mform->addElement('editor', 'responsetemplate', get_string('responsetemplate', 'qtype_essay'),
                array('rows' => 10),  array_merge($this->editoroptions, array('maxfiles' => 0)));
        $mform->addHelpButton('responsetemplate', 'responsetemplate', 'qtype_essay');

        $mform->addElement('header', 'graderinfoheader', get_string('graderinfoheader', 'qtype_essay'));
        $mform->setExpanded('graderinfoheader');
        $mform->addElement('editor', 'graderinfo', get_string('graderinfo', 'qtype_essay'),
                array('rows' => 10), $this->editoroptions);
    }

    protected function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);

        if (empty($question->options)) {
            return $question;
        }

        $question->responseformat = $question->options->responseformat;
        $question->responserequired = $question->options->responserequired;
        $question->responsefieldlines = $question->options->responsefieldlines;
        $question->minwordenabled = $question->options->minwordlimit ? 1 : 0;
        $question->minwordlimit = $question->options->minwordlimit;
        $question->maxwordenabled = $question->options->maxwordlimit ? 1 : 0;
        $question->maxwordlimit = $question->options->maxwordlimit;
        $question->attachments = $question->options->attachments;
        $question->attachmentsrequired = $question->options->attachmentsrequired;
        $question->filetypeslist = $question->options->filetypeslist;
        $question->maxbytes = $question->options->maxbytes;

        $draftid = file_get_submitted_draft_itemid('graderinfo');
        $question->graderinfo = array();
        $question->graderinfo['text'] = file_prepare_draft_area(
            $draftid,           // Draftid
            $this->context->id, // context
            'qtype_essay',      // component
            'graderinfo',       // filarea
            !empty($question->id) ? (int) $question->id : null, // itemid
            $this->fileoptions, // options
            $question->options->graderinfo // text.
        );
        $question->graderinfo['format'] = $question->options->graderinfoformat;
        $question->graderinfo['itemid'] = $draftid;

        $question->responsetemplate = array(
            'text' => $question->options->responsetemplate,
            'format' => $question->options->responsetemplateformat,
        );

        return $question;
    }

    public function validation($fromform, $files) {
        $errors = parent::validation($fromform, $files);

        // Don't allow both 'no inline response' and 'no attachments' to be selected,
        // as these options would result in there being no input requested from the user.
        if ($fromform['responseformat'] == 'noinline' && !$fromform['attachments']) {
            $errors['attachments'] = get_string('mustattach', 'qtype_essay');
        }

        // If 'no inline response' is set, force the teacher to require attachments;
        // otherwise there will be nothing to grade.
        if ($fromform['responseformat'] == 'noinline' && !$fromform['attachmentsrequired']) {
            $errors['attachmentsrequired'] = get_string('mustrequire', 'qtype_essay');
        }

        // Don't allow the teacher to require more attachments than they allow; as this would
        // create a condition that it's impossible for the student to meet.
        if ($fromform['attachments'] > 0 && $fromform['attachments'] < $fromform['attachmentsrequired'] ) {
            $errors['attachmentsrequired']  = get_string('mustrequirefewer', 'qtype_essay');
        }

        if ($fromform['responserequired']) {
            if (isset($fromform['minwordenabled'])) {
                if (!is_numeric($fromform['minwordlimit'])) {
                    $errors['mingroup'] = get_string('err_numeric', 'form');
                }
                if ($fromform['minwordlimit'] < 0) {
                    $errors['mingroup'] = get_string('err_minwordlimitnegative', 'qtype_essay');
                }
                if (!$fromform['minwordlimit']) {
                    $errors['mingroup'] = get_string('err_minwordlimit', 'qtype_essay');
                }
            }
            if (isset($fromform['maxwordenabled'])) {
                if (!is_numeric($fromform['maxwordlimit'])) {
                    $errors['maxgroup'] = get_string('err_numeric', 'form');
                }
                if ($fromform['maxwordlimit'] < 0) {
                    $errors['maxgroup'] = get_string('err_maxwordlimitnegative', 'qtype_essay');
                }
                if (!$fromform['maxwordlimit']) {
                    $errors['maxgroup'] = get_string('err_maxwordlimit', 'qtype_essay');
                }
            }
            if (isset($fromform['maxwordenabled']) && isset($fromform['minwordenabled'])) {
                if ($fromform['maxwordlimit'] < $fromform['minwordlimit'] &&
                    $fromform['maxwordlimit'] > 0 && $fromform['minwordlimit'] > 0) {
                    $errors['maxgroup'] = get_string('err_maxminmismatch', 'qtype_essay');
                }
            }
        }
        return $errors;
    }

    public function qtype() {
        return 'essay';
    }
}
