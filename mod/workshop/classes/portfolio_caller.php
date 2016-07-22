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
 * Workshop portfolio caller class to integrate with portfolio API.
 *
 * @package   mod_workshop
 * @copyright Loc Nguyen <ndloc1905@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/portfolio/caller.php');


/**
 * Workshop portfolio caller subclass to handle export submission and assessment to portfolio.
 *
 * @package   mod_workshop
 * @copyright Loc Nguyen <ndloc1905@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_workshop_portfolio_caller extends portfolio_module_caller_base {
    /**
     * @var int ID of submission.
     */
    protected $submissionid;
    /**
     * @var int ID of assessment.
     */
    protected $assessmentid;
    /**
     * @var stdClass Course module.
     */
    protected $cm;
    /**
     * @var array Submission files list.
     */
    protected $submissionfiles = array();
    /**
     * @var array Assessment files list.
     */
    protected $assessmentfiles = array();

    /**
     * @var stdClass Submission object load from DB.
     */
    private $submission;
    /**
     * @var stdClass Assessment object.
     */
    private $assessment;
    /**
     * @var stdClass User object.
     */
    private $author;
    /**
     * @var boolean Show author permission.
     */
    private $showauthor;

    /**
     * Return array of expected call back arguments
     *
     * and whether they are required or not.
     *
     * @return array
     */
    public static function expected_callbackargs() {
        return array(
            'submissionid' => false,
            'assessmentid' => false,
        );
    }

    /**
     * Load data required for the export.
     *
     * Load submission and assessment by submissionid, assessmentid.
     *
     * @return void
     */
    public function load_data() {
        global $DB;

        $this->submission = $DB->get_record('workshop_submissions', array('id' => $this->submissionid), '*', MUST_EXIST);
        $this->cm = get_coursemodule_from_instance('workshop', $this->submission->workshopid, 0, false, MUST_EXIST);
        $this->author = $DB->get_record('user', array('id' => $this->submission->authorid));

        $workshoprecord = $DB->get_record('workshop', array('id' => $this->cm->instance), '*', MUST_EXIST);
        $course = $DB->get_record('course', array('id' => $this->cm->course), '*', MUST_EXIST);
        $this->workshop = new workshop($workshoprecord, $this->cm, $course);

        // Load data for export assessment.
        if ($this->submissionid && $this->assessmentid) {
            $assessments    = $this->workshop->get_assessments_of_submission($this->submission->id);
            $this->assessment = new stdClass();
            $this->assessment = $assessments[$this->assessmentid];
            $fs = get_file_storage();
            $this->assessmentfiles = $fs->get_area_files($this->workshop->context->id, 'mod_workshop',
                'overallfeedback_attachment', $this->assessment->id);
        }
        // Load data for export submission.
        if ($this->submissionid && !$this->assessmentid) {
            $fs = get_file_storage();
            $submissioncontentfiles = $fs->get_area_files($this->workshop->context->id,
                'mod_workshop', 'submission_content', $this->submissionid);
            $submissionattachmentfiles = $fs->get_area_files($this->workshop->context->id,
                'mod_workshop', 'submission_attachment', $this->submissionid);
            $submissioninstructionfiles = $fs->get_area_files($this->workshop->context->id,
                'mod_workshop', 'instructauthors');
            $this->submissionfiles = array_merge($submissioncontentfiles, $submissionattachmentfiles, $submissioninstructionfiles);

            // Check anonymity of exported, show author or not.
            $ispublished    = ($this->workshop->phase == workshop::PHASE_CLOSED
                and $this->submission->published == 1
                and has_capability('mod/workshop:viewpublishedsubmissions', $this->workshop->context));
            $seenaspublished = false; // Is the submission seen as a published submission?

            if ($ispublished) {
                $seenaspublished = true;
            }
            if ($seenaspublished) {
                $this->showauthor = has_capability('mod/workshop:viewauthorpublished', $this->workshop->context);
            } else {
                $this->showauthor = has_capability('mod/workshop:viewauthornames', $this->workshop->context);
            }
        }
    }

    /**
     * Prepare the package ready to be passed off to the portfolio plugin.
     *
     * @return void
     */
    public function prepare_package() {
        // Set up the leap2a writer if we need it.
        $writingleap = false;
        if ($this->exporter->get('formatclass') == PORTFOLIO_FORMAT_LEAP2A) {
            $leapwriter = $this->exporter->get('format')->leap2a_writer();
            $writingleap = true;
        }

        if ($this->submissionid && $this->assessmentid) {
            $assessmenthtml = $this->prepare_assessment($this->assessment);
            $content = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $assessmenthtml);
            $name = 'assessment.html';
            if ($writingleap) {
                $this->prepare_assessment_leap2a($leapwriter, $this->assessment, $content);
                $content = $leapwriter->to_xml();
                $name = $this->exporter->get('format')->manifest_name();
            }
        }
        if ($this->submissionid && !$this->assessmentid) {
            $submissionhtml = $this->prepare_submission($this->submission);
            $content = $submissionhtml;
            $name = 'submission.html';
            if ($writingleap) {
                $this->prepare_submission_leap2a($leapwriter, $this->submission, $content);
                $content = $leapwriter->to_xml();
                $name = $this->exporter->get('format')->manifest_name();
            }
        }
        $manifest = ($this->exporter->get('format') instanceof PORTFOLIO_FORMAT_RICH);
        $this->get('exporter')->write_new_file($content, $name, $manifest);
    }

    /**
     * Helper function to output submission for export.
     *
     * This is cut down version of workshop submission renderer function.
     *
     * @param object $submission
     * @return string
     */
    private function prepare_submission($submission) {
        global $CFG;
        $output = '';
        $output .= '<meta charset="UTF-8">';
        $output .= html_writer::tag("h2", $this->workshop->name);

        // Write submission content.
        $output .= html_writer::start_tag('div', array('class' => 'submissionheader', 'style' => 'background-color: #ddd;'));
        $output .= html_writer::tag("h3", format_string($submission->title));
        if ($this->showauthor) {
            $output .= html_writer::tag('div', get_string('byfullnamewithoutlink', 'workshop', fullname($this->author)));
        }
        $created = get_string('userdatecreated', 'workshop', userdate($submission->timecreated));
        $output .= html_writer::tag("div", $created, array('style' => 'font-size:x-small'));

        if ($submission->timemodified > $submission->timecreated) {
            $modified = get_string('userdatemodified', 'workshop', userdate($submission->timemodified));
            $output .= html_writer::tag("div", $modified, array('style' => 'font-size:x-small'));
        }
        $output .= html_writer::end_tag('div');
        $format = $this->get('exporter')->get('format');
        $content = portfolio_rewrite_pluginfile_urls($submission->content, $this->workshop->context->id, 'mod_workshop',
            'submission_content', $submission->id, $format);
        $content = format_text($content, $submission->contentformat);
        $output .= html_writer::tag("div", $content);
        if ($this->submissionfiles) {
            $outputfiles = '';
            foreach ($this->submissionfiles as $file) {
                if ($file->is_directory()) {
                    continue;
                }

                $filename = $file->get_filename();
                $filearea = $file->get_filearea();
                $type = $file->get_mimetype();
                $linkhtml = $format->file_output($file);

                if ($filearea == "submission_attachment") {
                    $outputfiles .= html_writer::tag('li', $linkhtml, array('class' => $type));
                }

                if ($filename) {
                    $this->get('exporter')->copy_existing_file($file);
                }

                if (!empty($CFG->enableplagiarism)) {
                    require_once($CFG->libdir . '/plagiarismlib.php');
                    $outputfiles .= plagiarism_get_links(array('userid' => $file->get_userid(),
                        'file' => $file,
                        'cmid' => $this->cm->id,
                        'course' => $this->cm->course));
                }
            }
            $output .= $outputfiles;
        }

        // Write submission instruction.
        if (trim($this->workshop->instructauthors)) {
            $output .= html_writer::tag("h3", get_string('instructauthors', 'workshop'));
            $format = $this->get('exporter')->get('format');
            $instructauthorscontent = portfolio_rewrite_pluginfile_urls($this->workshop->instructauthors,
                $this->workshop->context->id, 'mod_workshop', 'instructauthors', 0, $format);
            $output .= $instructauthorscontent;
        }

        return $output;
    }

    /**
     * Helper function to add a leap2a entry element that corresponds to a submission,
     * including any attachments.
     *
     * The entry/ies are added directly to the leapwriter, which is passed by ref.
     *
     * @param portfolio_format_leap2a_writer $leapwriter writer object to add entries to
     * @param object $submission the stdclass object representing the database record
     * @param string $submissionhtml the content of the submission
     *
     * @return int id of new entry
     */
    private function prepare_submission_leap2a(portfolio_format_leap2a_writer $leapwriter, $submission, $submissionhtml) {
        global $DB;
        $entry = new portfolio_format_leap2a_entry('workshopsubmission' . $submission->id,  $submission->title,
            'resource', $submissionhtml);
        $entry->published = $submission->timecreated;
        $entry->updated = $submission->timemodified;
        $entry->author = $DB->get_record('user', array('id' => $submission->authorid), 'id,firstname,lastname,email');

        if (is_array($this->submissionfiles) && array_key_exists($submission->id, $this->submissionfiles)
            && is_array($this->submissionfiles[$submission->id])) {
            $leapwriter->link_files($entry, $this->submissionfiles[$submission->id], 'workshopsubmission'
                . $submission->id . 'attachment');
        }
        $entry->add_category('web', 'resource_type');
        $leapwriter->add_entry($entry);
        return $entry->id;
    }

    /**
     * Helper function to output assessment for export.
     *
     * This is a cut down function of workshop assessment renderer function.
     *
     * @param object $assessment the stdclass object representing the database record
     * @return string
     */
    private function prepare_assessment($assessment) {
        global $PAGE;
        global $CFG;
        global $USER;
        $strategy       = $this->workshop->grading_strategy_instance();
        $mform      = $strategy->get_assessment_form($PAGE->url, 'assessment', $assessment, false);
        $userassessment = $this->workshop->get_assessment_of_submission_by_user($assessment->submissionid, $USER->id);
        $isreviewer     = !empty($userassessment);
        if ($isreviewer) {
            $showreviewer = true;
        } else {
            $showreviewer   = has_capability('mod/workshop:viewreviewernames', $this->workshop->context);
        }

        $fs = get_file_storage();
        $options    = array(
            'showreviewer'  => $showreviewer,
            'showauthor'    => $this->showauthor,
            'showform'      => !is_null($assessment->grade),
            'showweight'    => true,
        );
        $displayassessment = $this->workshop->prepare_assessment($assessment, $mform, $options);

        $output = '';
        $output .= '<meta charset="UTF-8">';

        // Start write assessment content.
        $anonymous = is_null($displayassessment->reviewer);

        $output .= html_writer::start_tag('div', array('class' => 'assessmentheader', 'style' => 'background-color: #ddd;'));

        if (!empty($displayassessment->title)) {
            $title = s($displayassessment->title);
        } else {
            $title = get_string('assessmentbyyourself', 'workshop');
        }

        $output .= html_writer::tag("div", $title);

        if (!$anonymous) {
            $reviewer   = $displayassessment->reviewer;
            $output .= html_writer::tag("div", get_string('byfullnamewithoutlink', 'workshop', fullname($reviewer)));
        }

        if (is_null($displayassessment->realgrade)) {
            $output .= html_writer::tag("div", get_string('notassessed', 'workshop'));
        } else {
            $a              = new stdClass();
            $a->max         = $displayassessment->maxgrade;
            $a->received    = $displayassessment->realgrade;
            $output .= html_writer::tag("div", get_string('gradeinfo', 'workshop', $a));

            if (!is_null($displayassessment->weight) and $displayassessment->weight != 1) {
                $output .= html_writer::tag("div", get_string('weightinfo', 'workshop', $displayassessment->weight));
            }
        }
        $output .= html_writer::end_tag('div');

        $dir = $this->get('exporter')->get('format')->get_file_directory();

        if (!is_null($displayassessment->form)) {
            $output .= html_writer::tag("div", get_string('assessmentform', 'workshop'));
            $contentform = self::moodleform($displayassessment->form);
            $contentform = $this->portfolio_caller_rewrite_pluginfile_urls($contentform, $dir);
            $output .= html_writer::tag("div", $contentform);
            if (!$displayassessment->form->is_editable()) {
                $content = $displayassessment->get_overall_feedback_content();
                $content = $this->portfolio_caller_rewrite_pluginfile_urls($content, $dir);
                if ($content != '') {
                    $output .= html_writer::tag("div", get_string('overallfeedback', 'workshop'));
                    $output .= html_writer::tag("div", $content);
                }
                // Export overall feedback files.
                $overallfeedbackfiles = $fs->get_area_files($this->workshop->context->id,
                    'mod_workshop', 'overallfeedback_content', $assessment->id);
                foreach ($overallfeedbackfiles as $file) {
                    $this->get('exporter')->copy_existing_file($file);
                }
            }

            // Export description files.
            $assessmentdescriptionfiles = $fs->get_area_files($this->workshop->context->id,
                'workshopform_' . $this->workshop->strategy, 'description');
            foreach ($assessmentdescriptionfiles as $file) {
                $this->get('exporter')->copy_existing_file($file);
            }
        }

        if ($this->assessmentfiles) {
            $outputfiles = '';
            $format = $this->get('exporter')->get('format');
            foreach ($this->assessmentfiles as $file) {
                if ($file->is_directory()) {
                    continue;
                }
                $type       = $file->get_mimetype();
                $linkhtml   = $format->file_output($file);
                $outputfiles .= html_writer::tag('li', $linkhtml, array('class' => $type));

                $this->get('exporter')->copy_existing_file($file);

                if (!empty($CFG->enableplagiarism)) {
                    require_once($CFG->libdir.'/plagiarismlib.php');
                    $outputfiles .= plagiarism_get_links(array('userid' => $file->get_userid(),
                        'file' => $file,
                        'cmid' => $this->cm->id,
                        'course' => $this->cm->course));
                }
            }
            $output .= $outputfiles;
        }

        return $output;
    }


    /**
     * Helper function to add a leap2a entry element that corresponds to a assessment,
     * including any attachments.
     *
     * The entry/ies are added directly to the leapwriter, which is passed by ref.
     *
     * @param portfolio_format_leap2a_writer $leapwriter writer object to add entries to
     * @param object $assessment the stdclass object representing the database record
     * @param string $assessmenthtml the content of the assessment
     *
     * @return int id of new entry
     */
    private function prepare_assessment_leap2a(portfolio_format_leap2a_writer $leapwriter, $assessment, $assessmenthtml) {
        global $DB;
        $entry = new portfolio_format_leap2a_entry('workshopassessment' . $assessment->id,
            $assessment->title, 'resource', $assessmenthtml);
        $entry->published = $assessment->timecreated;
        $entry->updated = $assessment->timemodified;
        $entry->author = $DB->get_record('user', array('id' => $assessment->reviewerid), 'id,firstname,lastname,email');

        if (is_array($this->assessmentfiles) && array_key_exists($assessment->id, $this->assessmentfiles)
            && is_array($this->assessmentfiles[$assessment->id])) {
            $leapwriter->link_files($entry, $this->assessmentfiles[$assessment->id], 'workshopassessment'
                . $assessment->id . 'attachment');
        }
        $entry->add_category('web', 'resource_type');
        $leapwriter->add_entry($entry);
        return $entry->id;
    }

    /**
     * Return url for redirecting user when cancel or go back.
     *
     * @return string
     */
    public function get_return_url() {
        $returnurl = new moodle_url('/mod/workshop/submission.php',
            array('cmid' => $this->cm->id));
        return $returnurl->out();
    }

    /**
     * Get navigation that logically follows from the place the user was before.
     *
     * @return array
     */
    public function get_navigation() {
        $link = new moodle_url('/mod/workshop/submission.php',
            array('cmid' => $this->cm->id));
        $navlinks = array();
        $navlinks[] = array(
            'name' => format_string($this->submission->title),
            'link' => $link->out(),
            'type' => 'title'
        );
        return array($navlinks, $this->cm);
    }

    /**
     * How long might we expect this export to take.
     *
     * @return constant one of PORTFOLIO_TIME_XX
     */
    public function expected_time() {
        return PORTFOLIO_TIME_LOW;
    }

    /**
     * Make sure that the current user is allowed to do the export.
     *
     * @uses CONTEXT_MODULE
     * @return boolean
     */
    public function check_permissions() {
        $context = context_module::instance($this->cm->id);
        if ($this->submissionid && $this->assessmentid) {
            return has_capability('mod/workshop:exportownsubmission', $context)
                && has_capability('mod/workshop:exportownsubmissionassessment', $context);
        }
        return has_capability('mod/workshop:exportownsubmission', $context);
    }

    /**
     * Return the sha1 of this content.
     *
     * @return string
     */
    public function get_sha1() {
        if ($this->submissionid && $this->assessmentid) {
            return sha1($this->assessment->id . ',' . $this->assessment->title . ',' . $this->assessment->timecreated);
        }
        if ($this->submissionid && !$this->assessmentid) {
            return sha1($this->submission->id . ',' . $this->submission->title . ',' . $this->submission->timecreated);
        }
    }

    /**
     * Return a nice name to be displayed about this export location.
     *
     * @return string
     */
    public static function display_name() {
        return get_string('modulename', 'workshop');
    }

    /**
     * What formats this function *generally* supports.
     *
     * @return array
     */
    public static function base_supported_formats() {
        return array(PORTFOLIO_FORMAT_FILE, PORTFOLIO_FORMAT_RICHHTML, PORTFOLIO_FORMAT_PLAINHTML, PORTFOLIO_FORMAT_LEAP2A);
    }

    /**
     * Helper method dealing with the fact we can not just fetch the output of moodleforms.
     *
     * @param moodleform $mform
     * @return string HTML
     */
    protected static function moodleform(moodleform $mform) {
        ob_start();
        $mform->display();
        $o = ob_get_contents();
        ob_end_clean();
        return $o;
    }

    /**
     * Helper method dealing with the fact we can not just fetch the output of moodleforms.
     *
     * @param string $text HTML string to rewrite url
     * @param string $dir rewrite file directory
     * @return string HTML
     */
    public static function portfolio_caller_rewrite_pluginfile_urls($text, $dir) {
        $doc = new DOMDocument();
        $doc->loadHTML($text);

        $imagetags = $doc->getElementsByTagName('img');
        foreach ($imagetags as $tag) {
            $src = $tag->getAttribute('src');
            if (strpos($src, 'pluginfile.php') !== false) {
                $rewriteurl = $dir . basename($src);
                $text = str_replace($src, $rewriteurl, $text);
            }
        }

        $atags = $doc->getElementsByTagName('a');
        foreach ($atags as $atag) {
            $href = $atag->getAttribute('href');
            if (strpos($href, 'pluginfile.php') !== false) {
                $rewriteurl = $dir . basename($href);
                $text = str_replace($href, $rewriteurl, $text);
            }
        }

        return $text;
    }
}

