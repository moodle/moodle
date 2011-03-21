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
 * @package    block
 * @subpackage blog_menu
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * The blog menu block class
 */
class block_blog_menu extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_blog_menu');
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
        global $CFG;

        // detect if blog enabled
        if ($this->content !== NULL) {
            return $this->content;
        }

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

        // require necessary libs and get content
        require_once($CFG->dirroot .'/blog/lib.php');

        // Prep the content
        $this->content = new stdClass();

        $options = blog_get_all_options($this->page);
        if (count($options) == 0) {
            $this->content->text = '';
            return $this->content;
        }

        // Iterate the option types
        $menulist = array();
        foreach ($options as $types) {
            foreach ($types as $link) {
                $menulist[] = html_writer::link($link['link'], $link['string']);
            }
            $menulist[] = '<hr />';
        }
        // Remove the last element (will be an HR)
        array_pop($menulist);
        // Display the content as a list
        $this->content->text = html_writer::alist($menulist, array('class'=>'list'));

        // Prepare the footer for this block
        if (has_capability('moodle/blog:search', get_context_instance(CONTEXT_SYSTEM))) {
            // Full-text search field
            $form  = html_writer::tag('label', get_string('search', 'admin'), array('for'=>'blogsearchquery', 'class'=>'accesshide'));
            $form .= html_writer::empty_tag('input', array('id'=>'blogsearchquery', 'type'=>'text', 'name'=>'search'));
            $form .= html_writer::empty_tag('input', array('type'=>'submit', 'value'=>get_string('search')));
            $this->content->footer = html_writer::tag('form', html_writer::tag('div', $form), array('class'=>'blogsearchform', 'method'=>'get', 'action'=>new moodle_url('/blog/index.php')));
        } else {
            // No footer to display
            $this->content->footer = '';
        }

        // Return the content object
        return $this->content;
    }
}
