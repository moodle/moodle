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
 * @package   mod_pdfannotator
 * @copyright 2019 RWTH Aachen (see README.md)
 * @author    Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/tablelib.php');
/**
 * A base class for several table classes that are to be displayed on the overview page.
 */
class overviewtable extends flexible_table {

    public function __construct($id) {
        parent::__construct($id);
    }

    public function setup() {
        ($this->set_control_variables(array(
            TABLE_VAR_SORT    => 'sort',
            TABLE_VAR_HIDE    => 'hide',
            TABLE_VAR_SHOW    => 'show',
            TABLE_VAR_PAGE    => 'page',  // This is used for pagination in the tables.
            TABLE_VAR_RESET   => 'treset'
            )));
        parent::setup();
    }
    /**
     * Function wraps text elements with a text class for identification by media queries /
     * selective display/hiding.
     *
     * @param type $string
     * @return type
     */
    public static function wrap($string) {
        return "<span class='text'>$string</span>";
    }

}
/**
 * Table with all questions that are yet (marked as) unsolved.
 */
class questionstable extends overviewtable {

    private $id = 'mod-pdfannotator-questions';

    public function __construct($url, $showdropdown) {
        parent::__construct($this->id);
        global $OUTPUT;
//         $this->collapsible(true); // Concerns the tables columns.
        $this->define_baseurl($url);
        $columns = array('col0', 'col1', 'col2', 'col3', 'col4', 'col5');
        if ($showdropdown) {
            $columns[] = 'col6'; // Action dropdown menu.
            $this->no_sorting('col6');
        }
        $this->define_columns($columns);
        $this->column_style('col0', 'width', '30% !important'); // Question.
        $this->column_style('col1', 'width', '19%'); // Who asked the question when.
        $this->column_style('col2', 'width', '6%'); // How many people voted for the question.
        $this->column_style('col3', 'width', '6%'); // How many answers were given to it.
        $this->column_style('col4', 'width', '19%'); // When was the last answer given.
        $this->column_style('col5', 'width', '20%'); // In which annotator is the question located.

        $this->attributes['id'] = $this->id;
        $question = get_string('question', 'pdfannotator'); // $OUTPUT->pix_icon('i/unlock', '') . self::wrap(get_string('question', 'pdfannotator'));
        $whoasked = get_string('by', 'pdfannotator') . ' ' . get_string('on', 'pdfannotator'); // $OUTPUT->pix_icon('i/user', '') . self::wrap(get_string('by', 'pdfannotator')) . ' ' . $OUTPUT->pix_icon('e/insert_time', '') . self::wrap(get_string('on', 'pdfannotator'));
        $votes = "<i class='icon fa fa-thumbs-up fa-fw' style='float:left'></i>" . ' ' . $OUTPUT->help_icon('voteshelpicon', 'pdfannotator'); // "<i class='icon fa fa-chevron-up fa-lg' style='float:left'></i>" . self::wrap(get_string('votes', 'pdfannotator')) . ' ' . $OUTPUT->help_icon('voteshelpicon', 'pdfannotator');
        $answers = $OUTPUT->pix_icon('t/message', '') . ' ' . $OUTPUT->help_icon('answercounthelpicon', 'pdfannotator');; // $OUTPUT->pix_icon('t/message', '') . ' ' . self::wrap(get_string('answers', 'pdfannotator'));
        $lastanswered = get_string('lastanswered', 'pdfannotator'); // $OUTPUT->pix_icon('e/insert_time', '') . self::wrap(get_string('lastanswered', 'pdfannotator'));
        $document = get_string('pdfannotatorcolumn', 'pdfannotator'); // "<i class='icon fa fa-book fa-fw'></i>" . self::wrap(get_string('pdfannotatorcolumn', 'pdfannotator'));

        $headers = array($question, $whoasked, $votes, $answers, $lastanswered, $document);
        if ($showdropdown) {
            $this->column_style('col6', 'width', '5%'); // Action dropdown menu.
            $actionmenu = get_string('overviewactioncolumn', 'pdfannotator');
            $headers[] = $actionmenu;
        }

        $this->define_headers($headers);
        $this->no_sorting('col0');
        $this->sortable(true, 'col4', SORT_ASC);
        $this->sortable(true, 'col5', SORT_ASC);
        $this->sortable(true, 'col3', SORT_ASC);
        $this->sortable(true, 'col2', SORT_DESC);
        $this->sortable(true, 'col1', SORT_DESC);
    }
}
/**
 * Table with all answers to questions that the current user subscribed to.
 * Note: Users are automatically subscribed to their own questions on posting them.
 * They can, however, unsubscribe from any question including their own.
 */
class answerstable extends overviewtable {

    private $id = 'mod-pdfannotator-answers';

    public function __construct($url) {
        parent::__construct($this->id);
        global $OUTPUT;
        // $this->collapsible(true); // Concerns the tables columns.
        $this->define_baseurl($url);
        $this->define_columns(array('col0', 'col1', 'col2', 'col3', 'col4', 'col5'));
        $this->column_style('col0', 'width', '30%'); // Answer.
        $this->column_style('col1', 'width', '5%'); // Marked as correct?
        $this->column_style('col2', 'width', '20%'); // Who gave the answer and when.
        $this->column_style('col3', 'width', '30%'); // Anwered question.
        $this->column_style('col4', 'width', '10%'); // Annotator in which the question was asked.
        $this->column_style('col5', 'width', '10%'); // Action dropdown menu.
        $this->attributes['id'] = $this->id;
        $answer = get_string('answer', 'pdfannotator'); // $OUTPUT->pix_icon('t/message', '') . self::wrap(get_string('answer', 'pdfannotator'));
        $iscorrect = $OUTPUT->pix_icon('t/check', '') . ' ' . $OUTPUT->help_icon('iscorrecthelpicon', 'pdfannotator'); // . get_string('correct', 'pdfannotator');
        $whoanswered = get_string('by', 'pdfannotator') . ' ' . get_string('on', 'pdfannotator'); // $OUTPUT->pix_icon('i/user', '') . self::wrap(get_string('by', 'pdfannotator')) . ' ' . $OUTPUT->pix_icon('e/insert_time', '') . self::wrap(get_string('on', 'pdfannotator'));
        $question = get_string('myquestion', 'pdfannotator'); // $OUTPUT->pix_icon('i/email', '') . self::wrap(get_string('myquestion', 'pdfannotator'));
        $document = get_string('pdfannotatorcolumn', 'pdfannotator'); // "<i class='icon fa fa-book fa-fw'></i>" . self::wrap(get_string('pdfannotatorcolumn', 'pdfannotator'));
        $actionmenu = get_string('overviewactioncolumn', 'pdfannotator'); // $OUTPUT->pix_icon('i/settings', '') . self::wrap(get_string('overviewactioncolumn', 'pdfannotator'));
        $this->define_headers(array($answer, $iscorrect, $whoanswered, $question, $document, $actionmenu));
        $this->no_sorting('col1');
        $this->no_sorting('col0');
        $this->no_sorting('col5');
        $this->sortable(true, 'col3', SORT_ASC);
        $this->sortable(true, 'col2', SORT_ASC);
        $this->sortable(true, 'col4', SORT_DESC);
    }
}
/**
 * Table with all posts of the current user.
 */
class userspoststable extends overviewtable {

    private $id = 'mod-pdfannotator-ownposts';

    public function __construct($url) {
        parent::__construct($this->id);
        global $OUTPUT;
        // $this->collapsible(true); // Concerns the tables columns.
        $this->define_baseurl($url);
        $this->define_columns(array('col0', 'col1', 'col2', 'col3'));
        $this->column_style('col0', 'width', '60%'); // The user's post.
        $this->column_style('col1', 'width', '18%'); // Time of last modification.
        $this->column_style('col2', 'width', '7%'); // Number of votes for this post.
        $this->column_style('col3', 'width', '15%'); // Annotator in which they posted it.
        $this->attributes['id'] = $this->id;
        $mypost = get_string('mypost', 'pdfannotator'); // $OUTPUT->pix_icon('t/message', '') . self::wrap(get_string('mypost', 'pdfannotator'));
        $lastedited = get_string('lastedited', 'pdfannotator'); // $OUTPUT->pix_icon('e/insert_time', '') . self::wrap(get_string('lastedited', 'pdfannotator'));
        $votes = "<i class='icon fa fa-thumbs-up fa-fw' style='float:left'></i>" . ' ' . $OUTPUT->help_icon('voteshelpicontwo', 'pdfannotator');; // "<i class='icon fa fa-chevron-up fa-lg' style='float:left'></i>" . self::wrap(get_string('votes', 'pdfannotator')). ' ' . $OUTPUT->help_icon('voteshelpicon', 'pdfannotator');
        $document = get_string('pdfannotatorcolumn', 'pdfannotator'); // "<i class='icon fa fa-book fa-fw'></i>" . self::wrap(get_string('pdfannotatorcolumn', 'pdfannotator'));
        $this->define_headers(array($mypost, $lastedited, $votes, $document));
        $this->no_sorting('col0');
        $this->sortable(true, 'col2', SORT_ASC);
        $this->sortable(true, 'col3', SORT_DESC);
        $this->sortable(true, 'col1', SORT_DESC);
    }
}
/**
 * Table with reported comments.
 */
class reportstable extends overviewtable {

    private $id = 'mod-pdfannotator-reports';

    public function __construct($url) {
        parent::__construct($this->id);
        global $OUTPUT;
        $this->define_baseurl($url);
        $this->define_columns(array('col0', 'col1', 'col2', 'col3', 'col4'));
        $this->column_style('col0', 'width', '25%'); // Reported comment.
        $this->column_style('col1', 'width', '20%'); // Who wrote it when.
        $this->column_style('col2', 'width', '25%'); // Report.
        $this->column_style('col3', 'width', '20%'); // Who reported the comment and when.
        $this->column_style('col4', 'width', '10%'); // Action dropdown menu.
        $this->attributes['id'] = $this->id;
        $report = get_string('report', 'pdfannotator'); // $OUTPUT->pix_icon('i/email', '') . self::wrap(get_string('report', 'pdfannotator'));
        $reportedby = get_string('by', 'pdfannotator'). ' '. get_string('on', 'pdfannotator'); // $OUTPUT->pix_icon('i/user', '') . self::wrap(get_string('by', 'pdfannotator')) . ' ' . $OUTPUT->pix_icon('e/insert_time', '') . self::wrap(get_string('on', 'pdfannotator'));
        $reportedcomment = get_string('reportedcomment', 'pdfannotator'); // $OUTPUT->pix_icon('i/flagged', '') . self::wrap(get_string('reportedcomment', 'pdfannotator'));
        $writtenby = get_string('by', 'pdfannotator') . ' ' . get_string('on', 'pdfannotator'); // $OUTPUT->pix_icon('i/user', '') . self::wrap(get_string('by', 'pdfannotator')) . ' ' . $OUTPUT->pix_icon('e/insert_time', '') . self::wrap(get_string('on', 'pdfannotator'));
        $actionmenu = get_string('overviewactioncolumn', 'pdfannotator'); // $OUTPUT->pix_icon('i/settings', '') . self::wrap(get_string('overviewactioncolumn', 'pdfannotator'));
        $this->define_headers(array($report, $reportedby, $reportedcomment, $writtenby, $actionmenu));
        $this->no_sorting('col0');
        $this->no_sorting('col2');
        $this->no_sorting('col4');
        $this->sortable(true, 'col3', SORT_ASC);
        $this->sortable(true, 'col1', SORT_DESC);
    }
}