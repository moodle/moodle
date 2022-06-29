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
 * Version information
 *
 * @package    tool
 * @subpackage iomadmerge
 * @copyright  Derick Turner
 * @author     Derick Turner
 * @basedon    admin tool merge by:
 * @author     Nicolas Dunand <Nicolas.Dunand@unil.ch>
 * @author     Mike Holzer
 * @author     Forrest Gaston
 * @author     Juan Pablo Torres Herrera
 * @author     Jordi Pujol-Ahulló, SREd, Universitat Rovira i Virgili
 * @author     John Hoopes <hoopes@wisc.edu>, University of Wisconsin - Madison
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir . '/formslib.php'); /// forms library

/**
 * Define the form to confirm users' merge.
 */
class reviewuserform extends moodleform
{

    /** @var UserSelectTable Table to select users. */
    protected $urt;

    /** @var renderer_base renderer */
    protected $output;

    /** @var bool if user is in the merge process step. */
    protected $review_step;

    public function __construct(UserReviewTable $urt, $renderer, $review_step)
    {
        //just before parent's construct
        $this->urt = $urt;
        $this->output = $renderer;
        $this->review_step = $review_step;
        parent::__construct();
    }

    /**
     * Form definition
     *
     * @uses $CFG
     */
    public function definition()
    {
        // if there are no rows in the table, return.
        // (won't be rows if both olduser and newuser are NULL in session stdClass)
        if (empty($this->urt->data)) {
            return '';
        }

        $mform = & $this->_form;

        // header
        $mform->addElement('header', 'reviewusers', get_string('userreviewtable_legend', 'tool_iomadmerge'));

        // table content
        $mform->addElement('static', 'reviewuserslist', '', html_writer::table($this->urt));

        // buttons
        // set up url here so the same url can be used more than once
        $mergeurl = new moodle_url('/admin/tool/iomadmerge/index.php');
        $buttonarray = array();
        if ($this->review_step) {
            $mergeurl->param('option', 'iomadmerge');
            $iomadmergebutton = new single_button($mergeurl, get_string('iomadmerge', 'tool_iomadmerge'));
            $iomadmergebutton->add_confirm_action(get_string('iomadmerge_confirm', 'tool_iomadmerge'));
            $buttonarray[0][] = $this->output->render($iomadmergebutton);
        } else if (count($this->urt->data) === 2) {
            $mergeurl->param('option', 'continueselection');
            $iomadmergebutton = new single_button($mergeurl, get_string('saveselection_submit', 'tool_iomadmerge'));
            $buttonarray[0][] = $this->output->render($iomadmergebutton);
        }
        $mergeurl->param('option', 'clearselection');
        $iomadmergebutton = new single_button($mergeurl, get_string('clear_selection', 'tool_iomadmerge'));
        $buttonarray[0][] = $this->output->render($iomadmergebutton);

        if ($this->review_step) {
            $mergeurl->param('option', 'searchusers');
            $iomadmergebutton = new single_button($mergeurl, get_string('cancel'));
            $buttonarray[0][] = $this->output->render($iomadmergebutton);
        }
        $htmltable = new html_table();
        $htmltable->attributes['class'] = 'clearfix';
        $htmltable->data = $buttonarray;

        $mform->addElement('static', 'buttonar', '', html_writer::table($htmltable));
        $mform->closeHeaderBefore('buttonar');
    }
}
