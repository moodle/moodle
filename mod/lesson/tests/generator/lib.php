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
 * mod_lesson data generator.
 *
 * @package    mod_lesson
 * @category   test
 * @copyright  2013 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/lesson/locallib.php');

/**
 * mod_lesson data generator class.
 *
 * @package    mod_lesson
 * @category   test
 * @copyright  2013 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_lesson_generator extends testing_module_generator {

    /**
     * @var int keep track of how many pages have been created.
     */
    protected $pagecount = 0;

    /**
     * @var array list of candidate pages to be created when all answers have been added.
     */
    protected $candidatepages = [];

    /**
     * @var array map of readable jumpto to integer value.
     */
    protected $jumptomap = [
        'This page' => LESSON_THISPAGE,
        'Next page' => LESSON_NEXTPAGE,
        'Previous page' => LESSON_PREVIOUSPAGE,
        'End of lesson' => LESSON_EOL,
        'Unseen question within a content page' => LESSON_UNSEENBRANCHPAGE,
        'Random question within a content page' => LESSON_RANDOMPAGE,
        'Random content page' => LESSON_RANDOMBRANCH,
        'Unseen question within a cluster' => LESSON_CLUSTERJUMP,
    ];

    /**
     * To be called from data reset code only,
     * do not use in tests.
     * @return void
     */
    public function reset() {
        $this->pagecount = 0;
        $this->candidatepages = [];
        parent::reset();
    }

    /**
     * Creates a lesson instance for testing purposes.
     *
     * @param null|array|stdClass $record data for module being generated.
     * @param null|array $options general options for course module.
     * @return stdClass record from module-defined table with additional field cmid (corresponding id in course_modules table)
     */
    public function create_instance($record = null, ?array $options = null) {
        global $CFG;

        // Add default values for lesson.
        $lessonconfig = get_config('mod_lesson');
        $record = (array)$record + array(
            'progressbar' => $lessonconfig->progressbar,
            'ongoing' => $lessonconfig->ongoing,
            'displayleft' => $lessonconfig->displayleftmenu,
            'displayleftif' => $lessonconfig->displayleftif,
            'slideshow' => $lessonconfig->slideshow,
            'maxanswers' => $lessonconfig->maxanswers,
            'feedback' => $lessonconfig->defaultfeedback,
            'activitylink' => 0,
            'available' => 0,
            'deadline' => 0,
            'usepassword' => 0,
            'password' => '',
            'dependency' => 0,
            'timespent' => 0,
            'completed' => 0,
            'gradebetterthan' => 0,
            'modattempts' => $lessonconfig->modattempts,
            'review' => $lessonconfig->displayreview,
            'maxattempts' => $lessonconfig->maximumnumberofattempts,
            'nextpagedefault' => $lessonconfig->defaultnextpage,
            'maxpages' => $lessonconfig->numberofpagestoshow,
            'practice' => $lessonconfig->practice,
            'custom' => $lessonconfig->customscoring,
            'retake' => $lessonconfig->retakesallowed,
            'usemaxgrade' => $lessonconfig->handlingofretakes,
            'minquestions' => $lessonconfig->minimumnumberofquestions,
            'grade' => 100,
        );
        if (!isset($record['mediafile'])) {
            require_once($CFG->libdir.'/filelib.php');
            $record['mediafile'] = file_get_unused_draft_itemid();
        }

        return parent::create_instance($record, (array)$options);
    }

    /**
     * Creates a page for testing purposes. The page will be created when answers are added.
     *
     * @param null|array|stdClass $record data for page being generated.
     * @param null|array $options general options.
     */
    public function create_page($record = null, ?array $options = null) {
        $record = (array) $record;

        // Pages require answers to work. Add it as a candidate page to be created once answers have been added.
        $record['answer_editor'] = [];
        $record['response_editor'] = [];
        $record['jumpto'] = [];
        $record['score'] = [];

        if (!isset($record['previouspage']) || $record['previouspage'] === '') {
            // Previous page not set, set it to the last candidate page (if any).
            $record['previouspage'] = empty($this->candidatepages) ? '0' : end($this->candidatepages)['title'];
        }

        $this->candidatepages[] = $record;
    }

    /**
     * Creates a page and its answers for testing purposes.
     *
     * @param array $record data for page being generated.
     * @return stdClass created page, null if couldn't be created because it has a jump to a page that doesn't exist.
     * @throws coding_exception
     */
    private function perform_create_page(array $record): ?stdClass {
        global $DB;

        $lesson = $DB->get_record('lesson', ['id' => $record['lessonid']], '*', MUST_EXIST);
        $cm = get_coursemodule_from_instance('lesson', $lesson->id);
        $lesson->cmid = $cm->id;
        $qtype = $record['qtype'];

        unset($record['qtype']);
        unset($record['lessonid']);

        if (isset($record['content'])) {
            $record['contents_editor'] = [
                'text' => $record['content'],
                'format' => FORMAT_MOODLE,
                'itemid' => 0,
            ];
            unset($record['content']);
        }

        $record['pageid'] = $this->get_previouspage_id($lesson->id, $record['previouspage']);
        unset($record['previouspage']);

        try {
            $record['jumpto'] = $this->convert_page_jumpto($lesson->id, $record['jumpto']);
        } catch (coding_exception $e) {
            // This page has a jump to a page that hasn't been created yet.
            return null;
        }

        switch ($qtype) {
            case 'content':
            case 'cluster':
            case 'endofcluster':
            case 'endofbranch':
                $funcname = "create_{$qtype}";
                break;
            default:
                $funcname = "create_question_{$qtype}";
        }

        if (!method_exists($this, $funcname)) {
            throw new coding_exception('The page '.$record['title']." has an invalid qtype: $qtype");
        }

        return $this->{$funcname}($lesson, $record);
    }

    /**
     * Creates a content page for testing purposes.
     *
     * @param stdClass $lesson instance where to create the page.
     * @param array|stdClass $record data for page being generated.
     * @return stdClass page record.
     */
    public function create_content($lesson, $record = array()) {
        global $DB, $CFG;
        $now = time();
        $this->pagecount++;
        $record = (array)$record + array(
            'lessonid' => $lesson->id,
            'title' => 'Lesson page '.$this->pagecount,
            'timecreated' => $now,
            'qtype' => 20, // LESSON_PAGE_BRANCHTABLE
            'pageid' => 0, // By default insert in the beginning.
        );
        if (!isset($record['contents_editor'])) {
            $record['contents_editor'] = array(
                'text' => 'Contents of lesson page '.$this->pagecount,
                'format' => FORMAT_MOODLE,
                'itemid' => 0,
            );
        }
        $context = context_module::instance($lesson->cmid);
        $page = lesson_page::create((object)$record, new lesson($lesson), $context, $CFG->maxbytes);
        return $DB->get_record('lesson_pages', array('id' => $page->id), '*', MUST_EXIST);
    }

    /**
     * Create True/false question pages.
     * @param object $lesson
     * @param array $record
     * @return stdClass page record.
     */
    public function create_question_truefalse($lesson, $record = array()) {
        global $DB, $CFG;
        $now = time();
        $this->pagecount++;
        $record = (array)$record + array(
            'lessonid' => $lesson->id,
            'title' => 'Lesson TF question '.$this->pagecount,
            'timecreated' => $now,
            'qtype' => 2,  // LESSON_PAGE_TRUEFALSE.
            'pageid' => 0, // By default insert in the beginning.
        );
        if (!isset($record['contents_editor'])) {
            $record['contents_editor'] = array(
                'text' => 'The answer is TRUE '.$this->pagecount,
                'format' => FORMAT_HTML,
                'itemid' => 0
            );
        }

        // First Answer (TRUE).
        if (!isset($record['answer_editor'][0])) {
            $record['answer_editor'][0] = array(
                'text' => 'TRUE answer for '.$this->pagecount,
                'format' => FORMAT_HTML
            );
        }
        if (!isset($record['jumpto'][0])) {
            $record['jumpto'][0] = LESSON_NEXTPAGE;
        }

        // Second Answer (FALSE).
        if (!isset($record['answer_editor'][1])) {
            $record['answer_editor'][1] = array(
                'text' => 'FALSE answer for '.$this->pagecount,
                'format' => FORMAT_HTML
            );
        }
        if (!isset($record['jumpto'][1])) {
            $record['jumpto'][1] = LESSON_THISPAGE;
        }

        $context = context_module::instance($lesson->cmid);
        $page = lesson_page::create((object)$record, new lesson($lesson), $context, $CFG->maxbytes);
        return $DB->get_record('lesson_pages', array('id' => $page->id), '*', MUST_EXIST);
    }

    /**
     * Create multichoice question pages.
     * @param object $lesson
     * @param array $record
     * @return stdClass page record.
     */
    public function create_question_multichoice($lesson, $record = array()) {
        global $DB, $CFG;
        $now = time();
        $this->pagecount++;
        $record = (array)$record + array(
            'lessonid' => $lesson->id,
            'title' => 'Lesson multichoice question '.$this->pagecount,
            'timecreated' => $now,
            'qtype' => 3,  // LESSON_PAGE_MULTICHOICE.
            'pageid' => 0, // By default insert in the beginning.
        );
        if (!isset($record['contents_editor'])) {
            $record['contents_editor'] = array(
                'text' => 'Pick the correct answer '.$this->pagecount,
                'format' => FORMAT_HTML,
                'itemid' => 0
            );
        }

        // First Answer (correct).
        if (!isset($record['answer_editor'][0])) {
            $record['answer_editor'][0] = array(
                'text' => 'correct answer for '.$this->pagecount,
                'format' => FORMAT_HTML
            );
        }
        if (!isset($record['jumpto'][0])) {
            $record['jumpto'][0] = LESSON_NEXTPAGE;
        }

        // Second Answer (incorrect).
        if (!isset($record['answer_editor'][1])) {
            $record['answer_editor'][1] = array(
                'text' => 'correct answer for '.$this->pagecount,
                'format' => FORMAT_HTML
            );
        }
        if (!isset($record['jumpto'][1])) {
            $record['jumpto'][1] = LESSON_THISPAGE;
        }

        $context = context_module::instance($lesson->cmid);
        $page = lesson_page::create((object)$record, new lesson($lesson), $context, $CFG->maxbytes);
        return $DB->get_record('lesson_pages', array('id' => $page->id), '*', MUST_EXIST);
    }

    /**
     * Create essay question pages.
     * @param object $lesson
     * @param array $record
     * @return stdClass page record.
     */
    public function create_question_essay($lesson, $record = array()) {
        global $DB, $CFG;
        $now = time();
        $this->pagecount++;
        $record = (array)$record + array(
            'lessonid' => $lesson->id,
            'title' => 'Lesson Essay question '.$this->pagecount,
            'timecreated' => $now,
            'qtype' => 10, // LESSON_PAGE_ESSAY.
            'pageid' => 0, // By default insert in the beginning.
        );
        if (!isset($record['contents_editor'])) {
            $record['contents_editor'] = array(
                'text' => 'Write an Essay '.$this->pagecount,
                'format' => FORMAT_HTML,
                'itemid' => 0
            );
        }

        // Essays have an answer of NULL.
        if (!isset($record['answer_editor'][0])) {
            $record['answer_editor'][0] = array(
                'text' => null,
                'format' => FORMAT_MOODLE
            );
        }
        if (!isset($record['jumpto'][0])) {
            $record['jumpto'][0] = LESSON_NEXTPAGE;
        }

        $context = context_module::instance($lesson->cmid);
        $page = lesson_page::create((object)$record, new lesson($lesson), $context, $CFG->maxbytes);
        return $DB->get_record('lesson_pages', array('id' => $page->id), '*', MUST_EXIST);
    }

    /**
     * Create matching question pages.
     * @param object $lesson
     * @param array $record
     * @return stdClass page record.
     */
    public function create_question_matching($lesson, $record = array()) {
        global $DB, $CFG;
        $now = time();
        $this->pagecount++;
        $record = (array)$record + array(
            'lessonid' => $lesson->id,
            'title' => 'Lesson Matching question '.$this->pagecount,
            'timecreated' => $now,
            'qtype' => 5,  // LESSON_PAGE_MATCHING.
            'pageid' => 0, // By default insert in the beginning.
        );
        if (!isset($record['contents_editor'])) {
            $record['contents_editor'] = array(
                'text' => 'Match the values '.$this->pagecount,
                'format' => FORMAT_HTML,
                'itemid' => 0
            );
        }
        // Feedback for correct result.
        if (!isset($record['answer_editor'][0])) {
            $record['answer_editor'][0] = array(
                'text' => '',
                'format' => FORMAT_HTML
            );
        }
        // Feedback for wrong result.
        if (!isset($record['answer_editor'][1])) {
            $record['answer_editor'][1] = array(
                'text' => '',
                'format' => FORMAT_HTML
            );
        }
        // First answer value.
        if (!isset($record['answer_editor'][2])) {
            $record['answer_editor'][2] = array(
                'text' => 'Match value 1',
                'format' => FORMAT_HTML
            );
        }
        // First response value.
        if (!isset($record['response_editor'][2])) {
            $record['response_editor'][2] = 'Match answer 1';
        }
        // Second Matching value.
        if (!isset($record['answer_editor'][3])) {
            $record['answer_editor'][3] = array(
                'text' => 'Match value 2',
                'format' => FORMAT_HTML
            );
        }
        // Second Matching answer.
        if (!isset($record['response_editor'][3])) {
            $record['response_editor'][3] = 'Match answer 2';
        }

        // Jump Values.
        if (!isset($record['jumpto'][0])) {
            $record['jumpto'][0] = LESSON_NEXTPAGE;
        }
        if (!isset($record['jumpto'][1])) {
            $record['jumpto'][1] = LESSON_THISPAGE;
        }

        // Mark the correct values.
        if (!isset($record['score'][0])) {
            $record['score'][0] = 1;
        }
        $context = context_module::instance($lesson->cmid);
        $page = lesson_page::create((object)$record, new lesson($lesson), $context, $CFG->maxbytes);
        return $DB->get_record('lesson_pages', array('id' => $page->id), '*', MUST_EXIST);
    }

    /**
     * Create shortanswer question pages.
     * @param object $lesson
     * @param array $record
     * @return stdClass page record.
     */
    public function create_question_shortanswer($lesson, $record = array()) {
        global $DB, $CFG;
        $now = time();
        $this->pagecount++;
        $record = (array)$record + array(
            'lessonid' => $lesson->id,
            'title' => 'Lesson Shortanswer question '.$this->pagecount,
            'timecreated' => $now,
            'qtype' => 1,  // LESSON_PAGE_SHORTANSWER.
            'pageid' => 0, // By default insert in the beginning.
        );
        if (!isset($record['contents_editor'])) {
            $record['contents_editor'] = array(
                'text' => 'Fill in the blank '.$this->pagecount,
                'format' => FORMAT_HTML,
                'itemid' => 0
            );
        }

        // First Answer (correct).
        if (!isset($record['answer_editor'][0])) {
            $record['answer_editor'][0] = array(
                'text' => 'answer'.$this->pagecount,
                'format' => FORMAT_MOODLE
            );
        }
        if (!isset($record['jumpto'][0])) {
            $record['jumpto'][0] = LESSON_NEXTPAGE;
        }

        $context = context_module::instance($lesson->cmid);
        $page = lesson_page::create((object)$record, new lesson($lesson), $context, $CFG->maxbytes);
        return $DB->get_record('lesson_pages', array('id' => $page->id), '*', MUST_EXIST);
    }

    /**
     * Create shortanswer question pages.
     * @param object $lesson
     * @param array $record
     * @return stdClass page record.
     */
    public function create_question_numeric($lesson, $record = array()) {
        global $DB, $CFG;
        $now = time();
        $this->pagecount++;
        $record = (array)$record + array(
            'lessonid' => $lesson->id,
            'title' => 'Lesson numerical question '.$this->pagecount,
            'timecreated' => $now,
            'qtype' => 8,  // LESSON_PAGE_NUMERICAL.
            'pageid' => 0, // By default insert in the beginning.
        );
        if (!isset($record['contents_editor'])) {
            $record['contents_editor'] = array(
                'text' => 'Numerical question '.$this->pagecount,
                'format' => FORMAT_HTML,
                'itemid' => 0
            );
        }

        // First Answer (correct).
        if (!isset($record['answer_editor'][0])) {
            $record['answer_editor'][0] = array(
                'text' => $this->pagecount,
                'format' => FORMAT_MOODLE
            );
        }
        if (!isset($record['jumpto'][0])) {
            $record['jumpto'][0] = LESSON_NEXTPAGE;
        }

        $context = context_module::instance($lesson->cmid);
        $page = lesson_page::create((object)$record, new lesson($lesson), $context, $CFG->maxbytes);
        return $DB->get_record('lesson_pages', array('id' => $page->id), '*', MUST_EXIST);
    }

    /**
     * Creates a cluster page for testing purposes.
     *
     * @param stdClass $lesson instance where to create the page.
     * @param array $record data for page being generated.
     * @return stdClass page record.
     */
    public function create_cluster(stdClass $lesson, array $record = []): stdClass {
        global $DB, $CFG;
        $now = time();
        $this->pagecount++;
        $record = $record + [
            'lessonid' => $lesson->id,
            'title' => 'Cluster '.$this->pagecount,
            'timecreated' => $now,
            'qtype' => 30, // LESSON_PAGE_CLUSTER.
            'pageid' => 0, // By default insert in the beginning.
        ];
        if (!isset($record['contents_editor'])) {
            $record['contents_editor'] = [
                'text' => 'Cluster '.$this->pagecount,
                'format' => FORMAT_MOODLE,
                'itemid' => 0,
            ];
        }
        $context = context_module::instance($lesson->cmid);
        $page = lesson_page::create((object)$record, new lesson($lesson), $context, $CFG->maxbytes);
        return $DB->get_record('lesson_pages', ['id' => $page->id], '*', MUST_EXIST);
    }

    /**
     * Creates a end of cluster page for testing purposes.
     *
     * @param stdClass $lesson instance where to create the page.
     * @param array $record data for page being generated.
     * @return stdClass page record.
     */
    public function create_endofcluster(stdClass $lesson, array $record = []): stdClass {
        global $DB, $CFG;
        $now = time();
        $this->pagecount++;
        $record = $record + [
            'lessonid' => $lesson->id,
            'title' => 'End of cluster '.$this->pagecount,
            'timecreated' => $now,
            'qtype' => 31, // LESSON_PAGE_ENDOFCLUSTER.
            'pageid' => 0, // By default insert in the beginning.
        ];
        if (!isset($record['contents_editor'])) {
            $record['contents_editor'] = [
                'text' => 'End of cluster '.$this->pagecount,
                'format' => FORMAT_MOODLE,
                'itemid' => 0,
            ];
        }
        $context = context_module::instance($lesson->cmid);
        $page = lesson_page::create((object)$record, new lesson($lesson), $context, $CFG->maxbytes);
        return $DB->get_record('lesson_pages', ['id' => $page->id], '*', MUST_EXIST);
    }

    /**
     * Creates a end of branch page for testing purposes.
     *
     * @param stdClass $lesson instance where to create the page.
     * @param array $record data for page being generated.
     * @return stdClass page record.
     */
    public function create_endofbranch(stdClass $lesson, array $record = []): stdClass {
        global $DB, $CFG;
        $now = time();
        $this->pagecount++;
        $record = $record + [
            'lessonid' => $lesson->id,
            'title' => 'End of branch '.$this->pagecount,
            'timecreated' => $now,
            'qtype' => 21, // LESSON_PAGE_ENDOFBRANCH.
            'pageid' => 0, // By default insert in the beginning.
        ];
        if (!isset($record['contents_editor'])) {
            $record['contents_editor'] = [
                'text' => 'End of branch '.$this->pagecount,
                'format' => FORMAT_MOODLE,
                'itemid' => 0,
            ];
        }
        $context = context_module::instance($lesson->cmid);
        $page = lesson_page::create((object)$record, new lesson($lesson), $context, $CFG->maxbytes);
        return $DB->get_record('lesson_pages', ['id' => $page->id], '*', MUST_EXIST);
    }

    /**
     * Create a lesson override (either user or group).
     *
     * @param array $data must specify lessonid, and one of userid or groupid.
     * @throws coding_exception
     */
    public function create_override(array $data): void {
        global $DB;

        if (!isset($data['lessonid'])) {
            throw new coding_exception('Must specify lessonid when creating a lesson override.');
        }

        if (!isset($data['userid']) && !isset($data['groupid'])) {
            throw new coding_exception('Must specify one of userid or groupid when creating a lesson override.');
        }

        if (isset($data['userid']) && isset($data['groupid'])) {
            throw new coding_exception('Cannot specify both userid and groupid when creating a lesson override.');
        }

        $DB->insert_record('lesson_overrides', (object) $data);
    }

    /**
     * Creates an answer in a page for testing purposes.
     *
     * @param null|array|stdClass $record data for module being generated.
     * @param null|array $options general options.
     * @throws coding_exception
     */
    public function create_answer($record = null, ?array $options = null) {
        $record = (array) $record;

        $candidatepage = null;
        $pagetitle = $record['page'];
        $found = false;
        foreach ($this->candidatepages as &$candidatepage) {
            if ($candidatepage['title'] === $pagetitle) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            throw new coding_exception("Page '$pagetitle' not found in candidate pages. Please make sure the page exists "
                . 'and all answers are in the same table.');
        }

        if (isset($record['answer'])) {
            $candidatepage['answer_editor'][] = [
                'text' => $record['answer'],
                'format' => FORMAT_HTML,
            ];
        } else {
            $candidatepage['answer_editor'][] = null;
        }

        if (isset($record['response'])) {
            $candidatepage['response_editor'][] = [
                'text' => $record['response'],
                'format' => FORMAT_HTML,
            ];
        } else {
            $candidatepage['response_editor'][] = null;
        }

        $candidatepage['jumpto'][] = $record['jumpto'] ?? LESSON_THISPAGE;
        $candidatepage['score'][] = $record['score'] ?? 0;
    }

    /**
     * All answers in a table have been generated, create the pages.
     */
    public function finish_generate_answer() {
        $this->create_candidate_pages();
    }

    /**
     * Create candidate pages.
     *
     * @throws coding_exception
     */
    protected function create_candidate_pages(): void {
        // For performance reasons it would be better to use a topological sort algorithm. But since test cases shouldn't have
        // a lot of paged and complex jumps it was implemented using a simpler approach.
        $consecutiveblocked = 0;

        while (count($this->candidatepages) > 0) {
            $page = array_shift($this->candidatepages);
            $id = $this->perform_create_page($page);

            if ($id === null) {
                // Page cannot be created yet because of jumpto. Move it to the end of list.
                $consecutiveblocked++;
                $this->candidatepages[] = $page;

                if ($consecutiveblocked === count($this->candidatepages)) {
                    throw new coding_exception('There is a circular dependency in pages jumps.');
                }
            } else {
                $consecutiveblocked = 0;
            }
        }
    }

    /**
     * Calculate the previous page id.
     * If no page title is supplied, use the last page created in the lesson (0 if no pages).
     * If page title is supplied, search it in DB and the list of candidate pages.
     *
     * @param int $lessonid the lesson id.
     * @param string $pagetitle the page title, for example 'Test page'. '0' if no previous page.
     * @return int corresponding id. 0 if no previous page.
     * @throws coding_exception
     */
    protected function get_previouspage_id(int $lessonid, string $pagetitle): int {
        global $DB;

        if (is_numeric($pagetitle) && intval($pagetitle) === 0) {
            return 0;
        }

        $pages = $DB->get_records('lesson_pages', ['lessonid' => $lessonid, 'title' => $pagetitle], 'id ASC', 'id, title');

        if (count($pages) > 1) {
            throw new coding_exception("More than one page with '$pagetitle' found");
        } else if (!empty($pages)) {
            return current($pages)->id;
        }

        // Page doesn't exist, search if it's a candidate page. If it is, use its previous page instead.
        foreach ($this->candidatepages as $candidatepage) {
            if ($candidatepage['title'] === $pagetitle) {
                return $this->get_previouspage_id($lessonid, $candidatepage['previouspage']);
            }
        }

        throw new coding_exception("Page '$pagetitle' not found");
    }

    /**
     * Convert the jumpto using a string to an integer value.
     * The jumpto can contain a page name or one of our predefined values.
     *
     * @param int $lessonid the lesson id.
     * @param array|null $jumptolist list of jumpto to treat.
     * @return array|null list of jumpto already treated.
     * @throws coding_exception
     */
    protected function convert_page_jumpto(int $lessonid, ?array $jumptolist): ?array {
        global $DB;

        if (empty($jumptolist)) {
            return $jumptolist;
        }

        foreach ($jumptolist as $i => $jumpto) {
            if (empty($jumpto) || is_numeric($jumpto)) {
                continue;
            }

            if (isset($this->jumptomap[$jumpto])) {
                $jumptolist[$i] = $this->jumptomap[$jumpto];

                continue;
            }

            $page = $DB->get_record('lesson_pages', ['lessonid' => $lessonid, 'title' => $jumpto], 'id');
            if ($page === false) {
                throw new coding_exception("Jump '$jumpto' not found in pages.");
            }

            $jumptolist[$i] = $page->id;
        }

        return $jumptolist;
    }
}
