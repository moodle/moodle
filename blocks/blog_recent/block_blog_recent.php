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
 * @package    block
 * @subpackage blog_recent
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * This block simply outputs a list of links to recent blog entries, depending on
 * the context of the current page.
 */
class block_blog_recent extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_blog_recent');
        $this->content_type = BLOCK_TYPE_TEXT;
    }

    function applicable_formats() {
        return array('all' => true, 'my' => false, 'tag' => false);
    }

    function instance_allow_config() {
        return true;
    }

    function get_content() {
        global $CFG;

        if ($this->content !== NULL) {
            return $this->content;
        }

        // verify blog is enabled
        if (empty($CFG->bloglevel)) {
            $this->content = new stdClass();
            $this->content->text = '';
            if ($this->page->user_is_editing()) {
                $this->content->text = get_string('blogdisable', 'blog');
            }
            return $this->content;

        } else if ($CFG->bloglevel < BLOG_GLOBAL_LEVEL and (!isloggedin() or isguestuser())) {
            $this->content = new stdClass();
            $this->content->text = '';
            return $this->content;
        }

        require_once($CFG->dirroot .'/blog/lib.php');
        require_once($CFG->dirroot .'/blog/locallib.php');

        if (empty($this->config->recentbloginterval)) {
            $this->config->recentbloginterval = 8400;
        }

        if (empty($this->config->numberofrecentblogentries)) {
            $this->config->numberofrecentblogentries = 4;
        }

        $this->content = new stdClass();
        $this->content->footer = '';

        $context = $this->page->context;

        $filter = array();
        if ($context->contextlevel == CONTEXT_MODULE) {
            $filter['module'] = $context->instanceid;
            $a = new stdClass;
            $a->type = get_string('modulename', $this->page->cm->modname);
            $strview = get_string('viewallmodentries', 'blog', $a);
        } else if ($context->contextlevel == CONTEXT_COURSE) {
            $filter['course'] = $context->instanceid;
            $a = new stdClass;
            $a->type = get_string('course');
            $strview = get_string('viewblogentries', 'blog', $a);
        } else {
            $strview = get_string('viewsiteentries', 'blog');
        }
        $filter['since'] = $this->config->recentbloginterval;

        $bloglisting = new blog_listing($filter);
        $entries = $bloglisting->get_entries(0, $this->config->numberofrecentblogentries, 4);
        $url = new moodle_url('/blog/index.php', $filter);

        if (!empty($entries)) {
            $entrieslist = array();
            $viewblogurl = new moodle_url('/blog/index.php');

            foreach ($entries as $entryid => $entry) {
                $viewblogurl->param('entryid', $entryid);
                $entrylink = html_writer::link($viewblogurl, shorten_text($entry->subject));
                $entrieslist[] = $entrylink;
            }

            $this->content->text .= html_writer::alist($entrieslist, array('class'=>'list'));
            $viewallentrieslink = html_writer::link($url, $strview);
            $this->content->text .= $viewallentrieslink;
        } else {
            $this->content->text .= get_string('norecentblogentries', 'block_blog_recent');
        }
    }
}
