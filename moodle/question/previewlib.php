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
 * Library functions used by question/preview.php.
 *
 * @package    moodlecore
 * @subpackage questionengine
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');


/**
 * Settings form for the preview options.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class preview_options_form extends moodleform {
    public function definition() {
        $mform = $this->_form;

        $hiddenofvisible = array(
            question_display_options::HIDDEN => get_string('notshown', 'question'),
            question_display_options::VISIBLE => get_string('shown', 'question'),
        );

        $mform->addElement('header', 'attemptoptionsheader', get_string('attemptoptions', 'question'));

        $behaviours = question_engine::get_behaviour_options(
                $this->_customdata['quba']->get_preferred_behaviour());
        $mform->addElement('select', 'behaviour',
                get_string('howquestionsbehave', 'question'), $behaviours);
        $mform->addHelpButton('behaviour', 'howquestionsbehave', 'question');

        $mform->addElement('text', 'maxmark', get_string('markedoutof', 'question'),
                array('size' => '5'));
        $mform->setType('maxmark', PARAM_FLOAT);

        if ($this->_customdata['maxvariant'] > 1) {
            $variants = range(1, $this->_customdata['maxvariant']);
            $mform->addElement('select', 'variant', get_string('questionvariant', 'question'),
                    array_combine($variants, $variants));
        }
        $mform->setType('variant', PARAM_INT);

        $mform->addElement('submit', 'saverestart',
                get_string('restartwiththeseoptions', 'question'));

        $mform->addElement('header', 'displayoptionsheader', get_string('displayoptions', 'question'));

        $mform->addElement('select', 'correctness', get_string('whethercorrect', 'question'),
                $hiddenofvisible);

        $marksoptions = array(
            question_display_options::HIDDEN => get_string('notshown', 'question'),
            question_display_options::MAX_ONLY => get_string('showmaxmarkonly', 'question'),
            question_display_options::MARK_AND_MAX => get_string('showmarkandmax', 'question'),
        );
        $mform->addElement('select', 'marks', get_string('marks', 'question'), $marksoptions);

        $mform->addElement('select', 'markdp', get_string('decimalplacesingrades', 'question'),
                question_engine::get_dp_options());

        $mform->addElement('select', 'feedback',
                get_string('specificfeedback', 'question'), $hiddenofvisible);

        $mform->addElement('select', 'generalfeedback',
                get_string('generalfeedback', 'question'), $hiddenofvisible);

        $mform->addElement('select', 'rightanswer',
                get_string('rightanswer', 'question'), $hiddenofvisible);

        $mform->addElement('select', 'history',
                get_string('responsehistory', 'question'), $hiddenofvisible);

        $mform->addElement('submit', 'saveupdate',
                get_string('updatedisplayoptions', 'question'));
    }
}


/**
 * Displays question preview options as default and set the options
 * Setting default, getting and setting user preferences in question preview options.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_preview_options extends question_display_options {
    /** @var string the behaviour to use for this preview. */
    public $behaviour;

    /** @var number the maximum mark to use for this preview. */
    public $maxmark;

    /** @var int the variant of the question to preview. */
    public $variant;

    /** @var string prefix to append to field names to get user_preference names. */
    const OPTIONPREFIX = 'question_preview_options_';

    /**
     * Constructor.
     */
    public function __construct($question) {
        $this->behaviour = 'deferredfeedback';
        $this->maxmark = $question->defaultmark;
        $this->variant = null;
        $this->correctness = self::VISIBLE;
        $this->marks = self::MARK_AND_MAX;
        $this->markdp = get_config('quiz', 'decimalpoints');
        $this->feedback = self::VISIBLE;
        $this->numpartscorrect = $this->feedback;
        $this->generalfeedback = self::VISIBLE;
        $this->rightanswer = self::VISIBLE;
        $this->history = self::HIDDEN;
        $this->flags = self::HIDDEN;
        $this->manualcomment = self::HIDDEN;
    }

    /**
     * @return array names of the options we store in the user preferences table.
     */
    protected function get_user_pref_fields() {
        return array('behaviour', 'correctness', 'marks', 'markdp', 'feedback',
                'generalfeedback', 'rightanswer', 'history');
    }

    /**
     * @return array names and param types of the options we read from the request.
     */
    protected function get_field_types() {
        return array(
            'behaviour' => PARAM_ALPHA,
            'maxmark' => PARAM_FLOAT,
            'variant' => PARAM_INT,
            'correctness' => PARAM_BOOL,
            'marks' => PARAM_INT,
            'markdp' => PARAM_INT,
            'feedback' => PARAM_BOOL,
            'generalfeedback' => PARAM_BOOL,
            'rightanswer' => PARAM_BOOL,
            'history' => PARAM_BOOL,
        );
    }

    /**
     * Load the value of the options from the user_preferences table.
     */
    public function load_user_defaults() {
        $defaults = get_config('question_preview');
        foreach ($this->get_user_pref_fields() as $field) {
            $this->$field = get_user_preferences(
                    self::OPTIONPREFIX . $field, $defaults->$field);
        }
        $this->numpartscorrect = $this->feedback;
    }

    /**
     * Save a change to the user's preview options to the database.
     * @param object $newoptions
     */
    public function save_user_preview_options($newoptions) {
        foreach ($this->get_user_pref_fields() as $field) {
            if (isset($newoptions->$field)) {
                set_user_preference(self::OPTIONPREFIX . $field, $newoptions->$field);
            }
        }
    }

    /**
     * Set the value of any fields included in the request.
     */
    public function set_from_request() {
        foreach ($this->get_field_types() as $field => $type) {
            $this->$field = optional_param($field, $this->$field, $type);
        }
        $this->numpartscorrect = $this->feedback;
    }

    /**
     * @return string URL fragment. Parameters needed in the URL when continuing
     * this preview.
     */
    public function get_url_params() {
        $params = array();
        foreach ($this->get_field_types() as $field => $notused) {
            if ($field == 'behaviour' || $field == 'maxmark' || is_null($this->$field)) {
                continue;
            }
            $params[$field] = $this->$field;
        }
        return $params;
    }
}


/**
 * Called via pluginfile.php -> question_pluginfile to serve files belonging to
 * a question in a question_attempt when that attempt is a preview.
 *
 * @package  core_question
 * @category files
 * @param stdClass $course course settings object
 * @param stdClass $context context object
 * @param string $component the name of the component we are serving files for.
 * @param string $filearea the name of the file area.
 * @param int $qubaid the question_usage this image belongs to.
 * @param int $slot the relevant slot within the usage.
 * @param array $args the remaining bits of the file path.
 * @param bool $forcedownload whether the user must be forced to download the file.
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - justsend the file
 */
function question_preview_question_pluginfile($course, $context, $component,
        $filearea, $qubaid, $slot, $args, $forcedownload, $fileoptions) {
    global $USER, $DB, $CFG;

    list($context, $course, $cm) = get_context_info_array($context->id);
    require_login($course, false, $cm);

    $quba = question_engine::load_questions_usage_by_activity($qubaid);

    if (!question_has_capability_on($quba->get_question($slot), 'use')) {
        send_file_not_found();
    }

    $options = new question_display_options();
    $options->feedback = question_display_options::VISIBLE;
    $options->numpartscorrect = question_display_options::VISIBLE;
    $options->generalfeedback = question_display_options::VISIBLE;
    $options->rightanswer = question_display_options::VISIBLE;
    $options->manualcomment = question_display_options::VISIBLE;
    $options->history = question_display_options::VISIBLE;
    if (!$quba->check_file_access($slot, $options, $component,
            $filearea, $args, $forcedownload)) {
        send_file_not_found();
    }

    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = "/{$context->id}/{$component}/{$filearea}/{$relativepath}";
    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        send_file_not_found();
    }

    send_stored_file($file, 0, 0, $forcedownload, $fileoptions);
}

/**
 * The the URL to use for actions relating to this preview.
 * @param int $questionid the question being previewed.
 * @param int $qubaid the id of the question usage for this preview.
 * @param question_preview_options $options the options in use.
 */
function question_preview_action_url($questionid, $qubaid,
        question_preview_options $options, $context) {
    $params = array(
        'id' => $questionid,
        'previewid' => $qubaid,
    );
    if ($context->contextlevel == CONTEXT_MODULE) {
        $params['cmid'] = $context->instanceid;
    } else if ($context->contextlevel == CONTEXT_COURSE) {
        $params['courseid'] = $context->instanceid;
    }
    $params = array_merge($params, $options->get_url_params());
    return new moodle_url('/question/preview.php', $params);
}

/**
 * The the URL to use for actions relating to this preview.
 * @param int $questionid the question being previewed.
 * @param context $context the current moodle context.
 * @param int $previewid optional previewid to sign post saved previewed answers.
 */
function question_preview_form_url($questionid, $context, $previewid = null) {
    $params = array(
        'id' => $questionid,
    );
    if ($context->contextlevel == CONTEXT_MODULE) {
        $params['cmid'] = $context->instanceid;
    } else if ($context->contextlevel == CONTEXT_COURSE) {
        $params['courseid'] = $context->instanceid;
    }
    if ($previewid) {
        $params['previewid'] = $previewid;
    }
    return new moodle_url('/question/preview.php', $params);
}

/**
 * Delete the current preview, if any, and redirect to start a new preview.
 * @param int $previewid
 * @param int $questionid
 * @param object $displayoptions
 * @param object $context
 */
function restart_preview($previewid, $questionid, $displayoptions, $context) {
    global $DB;

    if ($previewid) {
        $transaction = $DB->start_delegated_transaction();
        question_engine::delete_questions_usage_by_activity($previewid);
        $transaction->allow_commit();
    }
    redirect(question_preview_url($questionid, $displayoptions->behaviour,
            $displayoptions->maxmark, $displayoptions, $displayoptions->variant, $context));
}

/**
 * Scheduled tasks relating to question preview. Specifically, delete any old
 * previews that are left over in the database.
 */
function question_preview_cron() {
    $maxage = 24*60*60; // We delete previews that have not been touched for 24 hours.
    $lastmodifiedcutoff = time() - $maxage;

    mtrace("\n  Cleaning up old question previews...", '');
    $oldpreviews = new qubaid_join('{question_usages} quba', 'quba.id',
            'quba.component = :qubacomponent
                    AND NOT EXISTS (
                        SELECT 1
                          FROM {question_attempts}      subq_qa
                          JOIN {question_attempt_steps} subq_qas ON subq_qas.questionattemptid = subq_qa.id
                          JOIN {question_usages}        subq_qu  ON subq_qu.id = subq_qa.questionusageid
                         WHERE subq_qa.questionusageid = quba.id
                           AND subq_qu.component = :qubacomponent2
                           AND (subq_qa.timemodified > :qamodifiedcutoff
                                    OR subq_qas.timecreated > :stepcreatedcutoff)
                    )
            ',
            array('qubacomponent' => 'core_question_preview', 'qubacomponent2' => 'core_question_preview',
                'qamodifiedcutoff' => $lastmodifiedcutoff, 'stepcreatedcutoff' => $lastmodifiedcutoff));

    question_engine::delete_questions_usage_by_activities($oldpreviews);
    mtrace('done.');
}
