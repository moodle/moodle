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
        global $CFG, $USER, $PAGE;

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

        $viewblogentries_url = blog_get_context_url();
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

        if (!empty($strlevel)) {
            $output = '<li><a href="'.$viewblogentries_url.'">'.get_string('viewblogentries', 'blog', $strlevel).'</a></li>';
        }

        $parts = array();
        $query = parse_url($viewblogentries_url);

        if (!empty($query['query'])) {
            parse_str($query['query'], $parts);
        }

        // show View site entries link
        if ($CFG->bloglevel >= BLOG_SITE_LEVEL && $canviewblogs) {
            $output .= '<li><a href="'. $CFG->wwwroot .'/blog/index.php">';
            $output .= get_string('viewsiteentries', 'blog')."</a></li>\n";
        }

        $output .= '';

        // show View my entries link
        if ($context->contextlevel != CONTEXT_USER) {
            $output .= '<li><a href="'. $CFG->wwwroot .'/blog/index.php?userid='. $USER->id;

            foreach ($parts as $var => $val) {
                $output .= "&amp;$var=$val";
            }
            $output .= '">'.get_string('viewmyentries', 'blog').  "</a></li>\n";
        }

        // show link to manage blog prefs
        $output .= '<li><a href="'. $CFG->wwwroot. '/blog/preferences.php?userid='.
                         $USER->id .'">'.
                         get_string('blogpreferences', 'blog')."</a></li>\n";

        // show Add entry link
        $sitecontext = get_context_instance(CONTEXT_SYSTEM);
        if (has_capability('moodle/blog:create', $sitecontext)) {
            $output .= '<li><a href="'. $CFG->wwwroot. '/blog/edit.php?action=add';
            foreach ($parts as $var => $val) {
                $output .= "&amp;$var=$val";
            }
            $output .= '">'.get_string('addnewentry', 'blog') ."</a></li>\n";
        }

        // Full-text search field

        $output .= '<li><form method="get" action="'.$viewblogentries_url.'">';
        $output .= '<div><input type="text" name="search" /><input type="submit" value="'.get_string('search').'" />';

        if (!empty($parts)) {
            foreach ($parts as $var => $val) {
                $output .= '<input type="hidden" name="'.$var.'" value="'.$val.'" />';
            }
        }

        $output .= '</div></form></li>';
        $this->content->text = '<ul class="list">'. $output ."</ul>\n";
    }
}
