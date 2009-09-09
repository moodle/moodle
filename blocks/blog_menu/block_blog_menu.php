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

        $context = $PAGE->get_context();

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

        $viewblogentriesurl = blog_get_context_url();
        $strlevel = '';

        switch ($context->contextlevel) {
            case CONTEXT_COURSE:
                $strlevel = ($context->instanceid == SITEID) ? '' : get_string('course');
                break;
            case CONTEXT_MODULE:
                $strlevel = print_context_name($context);
                break;
            case CONTEXT_USER:
                $strlevel = get_string('user');
                break;
        }

        $canviewblogs = has_capability('moodle/blog:view', $context);

        /// Accessibility: markup as a list.

        $blogmodon = false;
        $menulist = new html_list();
        $menulist->add_class('list');

        if (!empty($strlevel)) {
            $menulist->add_item($OUTPUT->link(html_link::make($viewblogentriesurl, get_string('viewblogentries', 'blog', $strlevel))));
        }

        // show View site entries link
        if ($CFG->bloglevel >= BLOG_SITE_LEVEL && $canviewblogs) {
            $menulist->add_item($OUTPUT->link(html_link::make($CFG->wwwroot .'/blog/index.php', get_string('viewsiteentries', 'blog'))));
        }

        $output .= '';

        // show View my entries link
        if ($context->contextlevel != CONTEXT_USER) {
            $myentrieslink = html_link::make(new moodle_url($CFG->wwwroot .'/blog/index.php', array('userid' => $USER->id)), get_string('viewmyentries', 'blog'));
            $myentrieslink->url->params($viewblogentriesurl->params());
            $menulist->add_item($OUTPUT->link($myentrieslink));
        }

        // show link to manage blog prefs
        $blogpreflink = html_link::make(new moodle_url($CFG->wwwroot .'/blog/preferences.php', array('userid' => $USER->id)), get_string('blogpreferences', 'blog'));

        $menulist->add_item($OUTPUT->link($blogpreflink));

        // show Add entry link
        $sitecontext = get_context_instance(CONTEXT_SYSTEM);
        if (has_capability('moodle/blog:create', $sitecontext)) {
            $addentrylink = html_link::make(new moodle_url($CFG->wwwroot .'/blog/edit.php', array('action' => 'add')), get_string('addnewentry', 'blog'));
            $addentrylink->url->params($viewblogentriesurl->params());
            $menulist->add_item($OUTPUT->link($addentrylink));
        }

        // Full-text search field
        $searchform = new html_form();
        $searchform->method = 'get';
        $searchform->url = new moodle_url($viewblogentriesurl);
        $searchform->button->text = get_string('search');
        $formcontents = $OUTPUT->field(html_field::make_text('search'));

        $menulist->add_item($OUTPUT->form($searchform, $formcontents));
        $this->content->text = $OUTPUT->htmllist($menulist);
    }
}
