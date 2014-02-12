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
 * The comments block
 *
 * @package    block_comments
 * @copyright 2009 Dongsheng Cai <dongsheng@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Obviously required
require_once($CFG->dirroot . '/comment/lib.php');

class block_comments extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_comments');
    }

    function specialization() {
        // require js for commenting
        comment::init();
    }
    function applicable_formats() {
        return array('all' => true);
    }

    function instance_allow_multiple() {
        return false;
    }

    function get_content() {
        global $CFG, $PAGE;
        if ($this->content !== NULL) {
            return $this->content;
        }
        if (!$CFG->usecomments) {
            $this->content = new stdClass();
            $this->content->text = '';
            if ($this->page->user_is_editing()) {
                $this->content->text = get_string('disabledcomments');
            }
            return $this->content;
        }
        $this->content = new stdClass();
        $this->content->footer = '';
        $this->content->text = '';
        if (empty($this->instance)) {
            return $this->content;
        }
        list($context, $course, $cm) = get_context_info_array($PAGE->context->id);

        $args = new stdClass;
        $args->context   = $PAGE->context;
        $args->course    = $course;
        $args->area      = 'page_comments';
        $args->itemid    = 0;
        $args->component = 'block_comments';
        $args->linktext  = get_string('showcomments');
        $args->notoggle  = true;
        $args->autostart = true;
        $args->displaycancel = false;
        $comment = new comment($args);
        $comment->set_view_permission(true);

        $this->content = new stdClass();
        $this->content->text = $comment->output(true);
        $this->content->footer = '';
        return $this->content;
    }
}
