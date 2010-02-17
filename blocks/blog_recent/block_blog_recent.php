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
 * Recent Blog Entries Block page.
 *
 * @package    moodlecore
 * @subpackage blog
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot .'/blog/lib.php');
require_once($CFG->dirroot .'/blog/locallib.php');

/**
 * This block simply outputs a list of links to recent blog entries, depending on
 * the context of the current page.
 */
class block_blog_recent extends block_base {

    function init() {
        $this->title = get_string('blockrecenttitle', 'blog');
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->version = 2009070900;
    }

    function applicable_formats() {
        return array('all' => true, 'my' => false, 'tag' => false);
    }

    function has_config() {
        return true;
    }

    function instance_allow_config() {
        return true;
    }

    function get_content() {
        global $CFG, $USER, $PAGE, $DB, $OUTPUT;

        if (empty($this->config->recentbloginterval)) {
            $this->config->recentbloginterval = 8400;
        }

        if (empty($this->config->numberofrecentblogentries)) {
            $this->config->numberofrecentblogentries = 4;
        }

        if (empty($CFG->bloglevel) || ($CFG->bloglevel < BLOG_GLOBAL_LEVEL && !(isloggedin() && !isguestuser()))) {
            $this->content->text = '';
            if ($this->page->user_is_editing()) {
                $this->content->text = get_string('blogdisable', 'blog');
            }
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->footer = '';

        $context = $PAGE->context;

        $blogheaders = blog_get_headers();

        // Remove entryid filter
        if (!empty($blogheaders['filters']['entry'])) {
            unset($blogheaders['filters']['entry']);
            $blogheaders['url']->remove_params(array('entryid'));
        }

        $blogheaders['filters']['since'] = $this->config->recentbloginterval;

        $bloglisting = new blog_listing($blogheaders['filters']);
        $entries = $bloglisting->get_entries(0, $this->config->numberofrecentblogentries, 4);

        if (!empty($entries)) {
            $entrieslist = array();
            $viewblogurl = new moodle_url('/blog/index.php');

            foreach ($entries as $entryid => $entry) {
                $viewblogurl->param('entryid', $entryid);
                $entrylink = html_writer::link($viewblogurl, shorten_text($entry->subject));
                $entrieslist[] = $entrylink;
            }

            $this->content->text .= html_writer::alist($entrieslist, array('class'=>'list'));
            $strview = get_string('viewsiteentries', 'blog');
            if (!empty($blogheaders['strview'])) {
                $strview = $blogheaders['strview'];
            }
            $viewallentrieslink = html_writer::link($blogheaders['url'], $strview);
            $this->content->text .= $viewallentrieslink;
        } else {
            $this->content->text .= get_string('norecentblogentries', 'block_blog_recent');
        }
    }
}
