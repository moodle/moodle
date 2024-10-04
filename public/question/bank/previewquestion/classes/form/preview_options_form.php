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

namespace qbank_previewquestion\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use moodleform;
use question_display_options;
use question_engine;
use qbank_previewquestion\question_preview_options;

/**
 * Settings form for the preview options.
 *
 * @package    qbank_previewquestion
 * @copyright  2009 The Open University
 * @author     2021 Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class preview_options_form extends moodleform {

    public function definition() {
        $mform = $this->_form;

        $hiddenorvisible = [
                question_display_options::HIDDEN => get_string('notshown', 'question'),
                question_display_options::VISIBLE => get_string('shown', 'question'),
        ];

        $mform->addElement('header', 'attemptoptionsheader', get_string('previewoptions', 'qbank_previewquestion'));
        $mform->setExpanded('attemptoptionsheader', false);
        // Add html element with class to display long text in single line.
        $mform->addElement('html', \html_writer::div(get_string('theoptionsyouselectonlyaffectthepreview',
            'qbank_previewquestion'), "col-md-12 row d-flex col-form-label mb-3"));
        $versions = $this->_customdata['versions'];
        $currentversion = $this->_customdata['restartversion'];
        $select = $mform->addElement('select', 'restartversion', get_string('questionversion', 'qbank_previewquestion'), $versions);
        $select->setSelected($currentversion);
        $behaviours = question_engine::get_behaviour_options(
                $this->_customdata['quba']->get_preferred_behaviour());
        $mform->addElement('select', 'behaviour',
                get_string('howquestionsbehave', 'question'), $behaviours);
        $mform->addHelpButton('behaviour', 'howquestionsbehave', 'question');

        $mform->addElement('float', 'maxmark', get_string('markedoutof', 'question'), ['size' => '5']);

        if ($this->_customdata['maxvariant'] > 1) {
            $variants = range(1, $this->_customdata['maxvariant']);
            $mform->addElement('select', 'variant', get_string('questionvariant', 'question'),
                    array_combine($variants, $variants));
        }
        $mform->setType('variant', PARAM_INT);

        $mform->addElement('submit', 'saverestart',
                get_string('restartwiththeseoptions', 'question'));

        $mform->addElement('header', 'displayoptionsheader', get_string('displayoptions', 'question'));
        $mform->setExpanded('displayoptionsheader', false);

        $mform->addElement('select', 'correctness', get_string('whethercorrect', 'question'),
                $hiddenorvisible);

        $marksoptions = [
                question_display_options::HIDDEN => get_string('notshown', 'question'),
                question_display_options::MAX_ONLY => get_string('showmaxmarkonly', 'question'),
                question_display_options::MARK_AND_MAX => get_string('showmarkandmax', 'question'),
        ];
        $mform->addElement('select', 'marks', get_string('marks', 'question'), $marksoptions);

        $mform->addElement('select', 'markdp', get_string('decimalplacesingrades', 'question'),
                question_engine::get_dp_options());

        $mform->addElement('select', 'feedback',
                get_string('specificfeedback', 'question'), $hiddenorvisible);

        $mform->addElement('select', 'generalfeedback',
                get_string('generalfeedback', 'question'), $hiddenorvisible);

        $mform->addElement('select', 'rightanswer',
                get_string('rightanswer', 'question'), $hiddenorvisible);

        $mform->addElement('select', 'history',
                get_string('responsehistory', 'question'), $hiddenorvisible);

        $mform->addElement('submit', 'saveupdate',
                get_string('updatedisplayoptions', 'question'));
    }

}
