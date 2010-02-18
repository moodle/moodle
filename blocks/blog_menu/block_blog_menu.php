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
 * Blog Menu Block page.
 *
 * @package    moodlecore
 * @subpackage blog
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once($CFG->dirroot .'/blog/lib.php');

class block_blog_menu extends block_base {

    function init() {
        $this->title = get_string('blockmenutitle', 'blog');
        $this->version = 2009071700;
    }

    function instance_allow_multiple() {
        return true;
    }

    function has_config() {
        return false;
    }

    function applicable_formats() {
        return array('all' => true, 'my' => false, 'tag' => false);
    }

    function instance_allow_config() {
        return true;
    }

    function get_content() {
        global $CFG, $USER, $PAGE, $OUTPUT;

        $context = $PAGE->context;

        // don't display menu block if block is set at site level, and user is not logged in
        if (empty($CFG->bloglevel) || ($CFG->bloglevel < BLOG_GLOBAL_LEVEL && !(isloggedin() && !isguestuser()))) {
            $this->content->text = '';
            if ($this->page->user_is_editing()) {
                $this->content->text = get_string('blogdisable', 'blog');
            }
            return $this->content;
        }

        $output = '';

        $this->content = new stdClass;

        $blogheaders = blog_get_headers();
        $canviewblogs = has_capability('moodle/blog:view', $context);

        /// Accessibility: markup as a list.

        $blogmodon = false;
        $menulist = array();

        if (!empty($blogheaders['strview']) && $CFG->useblogassociations) {
            if ($blogheaders['url']->compare($PAGE->url) == URL_MATCH_EXACT) {
                $menulist[] = html_writer::tag('span', $blogheaders['strview'], array('class'=>'current'));
            } else {
                $menulist[] = html_writer::link($blogheaders['url'], $blogheaders['strview']);
            }
        }

        // show View site entries link
        if ($CFG->bloglevel >= BLOG_SITE_LEVEL && $canviewblogs) {
            if (!$PAGE->url->param('search') && !$PAGE->url->param('tag') && !$PAGE->url->param('tagid') &&
                !$PAGE->url->param('modid') && !$PAGE->url->param('courseid') && !$PAGE->url->param('userid') && !$PAGE->url->param('entryid')) {
                // no
            } else {
                $menulist[] = html_writer::add($CFG->wwwroot .'/blog/index.php', get_string('viewsiteentries', 'blog'));
            }
        }

        $output .= '';

        // show View my entries link
        $pageuserid = $PAGE->url->param('userid');
        if (!empty($pageuserid) && $pageuserid == $USER->id) {
            // no
        } else {
            $murl = new moodle_url('/blog/index.php', array('userid' => $USER->id));
            $murl->params($blogheaders['url']->params());
            $murl->param('userid', $USER->id);
            $menulist[] = html_writer::link($murl, get_string('viewmyentries', 'blog'));
        }

        // show "Add entry" or "Blog about this" link
        $sitecontext = get_context_instance(CONTEXT_SYSTEM);
        if (has_capability('moodle/blog:create', $sitecontext)) {
            $aurl = new moodle_url('/blog/edit.php', array('action' => 'add'));
            $aurl->params($blogheaders['url']->params());
            if ($PAGE->url->compare($aurl) != URL_MATCH_EXACT) {
                $menulist[] =html_writer::link($aurl, $blogheaders['stradd']);
            }
        }

        // Full-text search field
        if (has_capability('moodle/blog:search', $sitecontext)) {
            $target = new moodle_url($blogheaders['url']);
            $form = '<form class="blogsearchform" method="get" action="'.$target.'">';
            $form .= '<div><label for="blogsearchquery" class="accesshide">'.s(get_string('search', 'admin')).'</label><input id="blogsearchquery" type="text" name="search" />';
            $form .= '<input type="submit" value="'.s(get_string('search')).'" />';
            $form .= '</div></form>';
            $this->content->footer = $form;
        } else {
            $this->content->footer = '';
        }

        $this->content->text = html_writer::alist($menulist, array('class'=>'list'));
    }
}
