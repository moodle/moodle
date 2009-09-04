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
 * Block for managing external blogs. This block will appear only on a user's blog page, not
 * on any other blog listing page (site, course, module etc). It may be filtered by tag.
 *
 * @package    moodlecore
 * @subpackage blog
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot .'/blog/lib.php');

/**
 * External Blog Block class
 */
class block_blog_externals extends block_base {

    function init() {
        global $USER, $DB;

        $this->title = get_string('blockexternalstitle', 'blog');
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->version = 2009101509;

        // See if a deletion has been requested
        $delete = optional_param('delete_blog_external', false, PARAM_INT);
        if ($delete && ($external_blog = $DB->get_record('blog_external', array('id' => $delete)))) {
            // Check caps and userid matching $USER->id
            if ($external_blog->userid == $USER->id) {
                $DB->delete_records('blog_external', array('id' => $delete));
            }
        }
    }

    function get_content() {
        global $CFG, $USER, $DB, $PAGE, $OUTPUT;

        // This block should not appear if $CFG->useexternalblogs is off
        if (empty($CFG->bloglevel)) {
            $this->content->text = '';
            return $this->content;
        }

        // don't display menu block if block is set at site level, and user is not logged in
        if ($CFG->bloglevel < BLOG_GLOBAL_LEVEL && !(isloggedin() && !isguest())) {
            $this->content->text = '';
            return $this->content;
        }

        $output = '';

        $this->content = new stdClass;
        $this->content->footer = '';

        $external_blogs = $DB->get_records('blog_external', array('userid' => $USER->id));

        $external_blog_url = $CFG->wwwroot.'/blog/external.php?returnurl='.urlencode($PAGE->url->out());

        foreach ($external_blogs as $id => $eb) {
            $strdelete = get_string('delete') . " $eb->name";

            $delete_url = new moodle_url();
            $delete_url->param('delete_blog_external', $id);
            $deleteicon = '<a href="'.$delete_url->out().'" class="delete">' .
                              '<img src="'.$OUTPUT->old_icon_url('t/delete').'" alt="'.$strdelete.'" title="'.$strdelete.'" />' .
                          "</a>\n";
            $output .= '<li><a href="'.$external_blog_url.'&amp;id='.$id.'" title="'.$eb->name.'">'.shorten_text($eb->name, 20)."</a>$deleteicon</li>\n";
        }

        $this->content->text = '<ul class="list">'. $output ."</ul>\n";
        $this->content->text .= '<div class="newlink"><a href="'.$external_blog_url.'">'.get_string('addnewexternalblog', 'blog').'</a></div>';
        return $this->content;
    }
}
