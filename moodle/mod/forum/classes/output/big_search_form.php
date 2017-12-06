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
 * Big search form.
 *
 * @package    mod_forum
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\output;
defined('MOODLE_INTERNAL') || die();

use html_writer;
use moodle_url;
use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * Big search form class.
 *
 * @package    mod_forum
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class big_search_form implements renderable, templatable {

    public $course;
    public $datefrom;
    public $dateto;
    public $forumoptions;
    public $fullwords;
    public $notwords;
    public $phrase;
    public $showfullwords;
    public $subject;
    public $user;
    public $words;
    public $tags;
    /** @var string The URL of the search form. */
    public $actionurl;

    /**
     * Constructor.
     *
     * @param object $course The course.
     * @param object $user The user.
     */
    public function __construct($course) {
        global $DB;
        $this->course = $course;
        $this->tags = [];
        $this->showfullwords = $DB->get_dbfamily() == 'mysql' || $DB->get_dbfamily() == 'postgres';
        $this->actionurl = new moodle_url('/mod/forum/search.php');

        $forumoptions = ['' => get_string('allforums', 'forum')] + forum_menu_list($course);
        $this->forumoptions = array_map(function($option) use ($forumoptions) {
            return [
                'value' => $option,
                'name' => $forumoptions[$option]
            ];
        }, array_keys($forumoptions));
    }

    /**
     * Set date from.
     *
     * @param mixed $value Date from.
     */
    public function set_datefrom($value) {
        $this->datefrom = $value;
    }

    /**
     * Set date to.
     *
     * @param mixed $value Date to.
     */
    public function set_dateto($value) {
        $this->dateto = $value;
    }

    /**
     * Set full words.
     *
     * @param mixed $value Full words.
     */
    public function set_fullwords($value) {
        $this->fullwords = $value;
    }

    /**
     * Set not words.
     *
     * @param mixed $value Not words.
     */
    public function set_notwords($value) {
        $this->notwords = $value;
    }

    /**
     * Set phrase.
     *
     * @param mixed $value Phrase.
     */
    public function set_phrase($value) {
        $this->phrase = $value;
    }

    /**
     * Set subject.
     *
     * @param mixed $value Subject.
     */
    public function set_subject($value) {
        $this->subject = $value;
    }

    /**
     * Set user.
     *
     * @param mixed $value User.
     */
    public function set_user($value) {
        $this->user = $value;
    }

    /**
     * Set words.
     *
     * @param mixed $value Words.
     */
    public function set_words($value) {
        $this->words = $value;
    }

    /**
     * Set tags.
     *
     * @param mixed $value Tags.
     */
    public function set_tags($value) {
        $this->tags = $value;
    }

    /**
     * Forum ID setter search criteria.
     *
     * @param int $forumid The forum ID.
     */
    public function set_forumid($forumid) {
        $this->forumid = $forumid;
    }

    public function export_for_template(renderer_base $output) {
        global $DB, $CFG, $PAGE;
        $data = new stdClass();

        $data->courseid = $this->course->id;
        $data->words = $this->words;
        $data->phrase = $this->phrase;
        $data->notwords = $this->notwords;
        $data->fullwords = $this->fullwords;
        $data->datefromchecked = !empty($this->datefrom);
        $data->datetochecked = !empty($this->dateto);
        $data->subject = $this->subject;
        $data->user = $this->user;
        $data->showfullwords = $this->showfullwords;
        $data->actionurl = $this->actionurl->out(false);

        $tagtypestoshow = \core_tag_area::get_showstandard('mod_forum', 'forum_posts');
        $showstandard = ($tagtypestoshow != \core_tag_tag::HIDE_STANDARD);
        $typenewtags = ($tagtypestoshow != \core_tag_tag::STANDARD_ONLY);

        $PAGE->requires->js_call_amd('core/form-autocomplete', 'enhance', $params = array('#tags', $typenewtags, '',
                              get_string('entertags', 'tag'), false, $showstandard, get_string('noselection', 'form')));

        $data->tagsenabled = \core_tag_tag::is_enabled('mod_forum', 'forum_posts');
        $namefield = empty($CFG->keeptagnamecase) ? 'name' : 'rawname';
        $tags = $DB->get_records('tag',
            array('isstandard' => 1, 'tagcollid' => \core_tag_area::get_collection('mod_forum', 'forum_posts')),
            $namefield, 'rawname,' . $namefield . ' as fieldname');
        $data->tags = [];
        foreach ($tags as $tag) {
            $data->tagoptions[] = ['value'    => $tag->rawname,
                                   'text'     => $tag->fieldname,
                                   'selected' => in_array($tag->rawname, $this->tags)
            ];
        }

        $datefrom = $this->datefrom;
        if (empty($datefrom)) {
            $datefrom = make_timestamp(2000, 1, 1, 0, 0, 0);
        }

        $dateto = $this->dateto;
        if (empty($dateto)) {
            $dateto = time() + HOURSECS;
        }

        $data->datefromfields = html_writer::select_time('days', 'fromday', $datefrom)
                              . html_writer::select_time('months', 'frommonth', $datefrom)
                              . html_writer::select_time('years', 'fromyear', $datefrom)
                              . html_writer::select_time('hours', 'fromhour', $datefrom)
                              . html_writer::select_time('minutes', 'fromminute', $datefrom);

        $data->datetofields = html_writer::select_time('days', 'today', $dateto)
                            . html_writer::select_time('months', 'tomonth', $dateto)
                            . html_writer::select_time('years', 'toyear', $dateto)
                            . html_writer::select_time('hours', 'tohour', $dateto)
                            . html_writer::select_time('minutes', 'tominute', $dateto);

        if ($this->forumid && !empty($this->forumoptions)) {
            foreach ($this->forumoptions as $index => $option) {
                if ($option['value'] == $this->forumid) {
                    $this->forumoptions[$index]['selected'] = true;
                } else {
                    $this->forumoptions[$index]['selected'] = false;
                }
            }
        }
        $data->forumoptions = $this->forumoptions;

        return $data;
    }

}
