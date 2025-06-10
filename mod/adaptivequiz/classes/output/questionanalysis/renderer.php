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
 * @copyright  2013 Middlebury College {@link http://www.middlebury.edu/}
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_adaptivequiz\output\questionanalysis;

use html_table;
use html_writer;
use mod_adaptivequiz\local\questionanalysis\question_analyser;
use moodle_url;
use plugin_renderer_base;
use question_display_options;
use question_engine;
use stdClass;

class renderer extends plugin_renderer_base {
    /** @var string $sortdir the sorting direction being used */
    protected $sortdir = '';
    /** @var moodle_url $sorturl the current base url used for keeping the table sorted */
    protected $sorturl = '';
    /** @var int $groupid variable used to reference the groupid that is currently being used to filter by */
    public $groupid = 0;
    /** @var array options that should be used for opening the secure popup. */
    protected static $popupoptions = array(
        'left' => 0,
        'top' => 0,
        'fullscreen' => true,
        'scrollbars' => false,
        'resizeable' => false,
        'directories' => false,
        'toolbar' => false,
        'titlebar' => false,
        'location' => false,
        'status' => false,
        'menubar' => false
    );

    /**
     * This function returns page header information to be printed to the page
     * @return string HTML markup for header inforation
     */
    public function print_header() {
        return $this->header();
    }

    /**
     * This function returns page footer information to be printed to the page
     * @return string HTML markup for footer inforation
     */
    public function print_footer() {
        return $this->footer();
    }

    /**
     * This function generates the HTML required to display the initial reports table
     * @param array $records attempt records from adaptivequiz_attempt table
     * @param stdClass $cm course module object set to the instance of the activity
     * @param string $sort the column the the table is to be sorted by
     * @param string $sortdir the direction of the sort
     * @return string HTML markup
     */
    public function get_report_table($headers, $records, $cm, $baseurl, $sort, $sortdir) {
        $table = new html_table();
        $table->attributes['class'] = 'generaltable quizsummaryofattempt boxaligncenter';
        $table->head = $this->format_report_table_headers($headers, $cm, $baseurl, $sort, $sortdir);
        $table->align = array('center', 'center', 'center');
        $table->size = array('', '', '');

        $table->data = $records;
        return html_writer::table($table);
    }

    /**
     * This function creates the table header links that will be used to allow instructor to sort the data
     * @param stdClass $cm a course module object set to the instance of the activity
     * @param string $sort the column the the table is to be sorted by
     * @param string $sortdir the direction of the sort
     * @return array an array of column headers (firstname / lastname, number of attempts, standard error)
     */
    public function format_report_table_headers($headers, $cm, $baseurl, $sort, $sortdir) {
        /* Create header links */
        $contents = array();
        foreach ($headers as $key => $name) {
            if ($sort == $key) {
                $seperator = ' ';
                if ($sortdir == 'DESC') {
                    $sortdir = 'ASC';
                    $imageparam = array('src' => $this->image_url('t/up'), 'alt' => '');
                    $icon = html_writer::empty_tag('img', $imageparam);
                } else {
                    $sortdir = 'DESC';
                    $imageparam = array('src' => $this->image_url('t/down'), 'alt' => '');
                    $icon = html_writer::empty_tag('img', $imageparam);
                }
            } else {
                $sortdir = 'ASC';
                $seperator = '';
                $icon = '';
            }

            $url = new moodle_url($baseurl, array('cmid' => $cm->id, 'sort' => $key, 'sortdir' => $sortdir));

            $contents[] = html_writer::link($url, $name.$seperator.$icon);
        }
        return $contents;
    }

    /**
     * This function prints paging information
     * @param int $totalrecords the total number of records returned
     * @param int $page the current page the user is on
     * @param int $perpage the number of records displayed on one page
     * @return string HTML markup
     */
    public function print_paging_bar($totalrecords, $page, $perpage, $cm, $baseurl, $sort, $sortdir) {
        $url = new moodle_url($baseurl, array('cmid' => $cm->id, 'sort' => $sort, 'sortdir' => $sortdir));

        $output = '';
        $output .= $this->paging_bar($totalrecords, $page, $perpage, $url);
        return $output;
    }

    /**
     * This function generates the HTML required to display the single-question report
     * @param array $headers The labels for the report
     * @param array $record An attempt record
     * @return string HTML markup
     */
    public function get_single_question_report($headers, $record) {
        $table = new html_table();
        $table->attributes['class'] = 'generaltable quizsummaryofattempt boxaligncenter';
        $table->head = array(get_string('statistic', 'adaptivequiz'), get_string('value', 'adaptivequiz'));
        $table->align = array('left', 'left');
        $table->size = array('200px', '');
        $table->width = '100%';

        while ($name = array_shift($headers)) {
            $value = array_shift($record);
            $table->data[] = array($name, $value);
        }

        return html_writer::table($table);
    }

    /**
     * Generate an HTML view of a single question.
     *
     * @param  $analyzer
     * @return string HTML markup
     */
    public function get_question_details(question_analyser $analyzer, $context) {
        // Setup display options.
        $options = new question_display_options();
        $options->readonly = true;
        $options->flags = question_display_options::HIDDEN;
        $options->marks = question_display_options::MAX_ONLY;
        $options->rightanswer = question_display_options::VISIBLE;
        $options->correctness = question_display_options::VISIBLE;
        $options->numpartscorrect = question_display_options::VISIBLE;

        // Init question usage and set default behaviour of usage.
        $quba = question_engine::make_questions_usage_by_activity('mod_adaptivequiz', $context);
        $quba->set_preferred_behaviour('deferredfeedback');
        $quba->add_question($analyzer->get_question_definition());
        $quba->start_question(1);
        $quba->process_action(1, $quba->get_correct_response(1));

        return $quba->render_question(1, $options);
    }
}
