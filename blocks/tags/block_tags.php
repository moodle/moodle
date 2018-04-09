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
 * Tags block.
 *
 * @package   block_tags
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_tags extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_tags');
    }

    public function instance_allow_multiple() {
        return true;
    }

    public function applicable_formats() {
        return array('all' => true);
    }

    public function instance_allow_config() {
        return true;
    }

    public function specialization() {

        // Load userdefined title and make sure it's never empty.
        if (empty($this->config->title)) {
            $this->title = get_string('pluginname', 'block_tags');
        } else {
            $this->title = format_string($this->config->title, true, ['context' => $this->context]);
        }
    }

    public function get_content() {

        global $CFG, $COURSE, $USER, $SCRIPT, $OUTPUT;

        if (empty($CFG->usetags)) {
            $this->content = new stdClass();
            $this->content->text = '';
            if ($this->page->user_is_editing()) {
                $this->content->text = get_string('disabledtags', 'block_tags');
            }
            return $this->content;
        }

        if (!isset($this->config)) {
            $this->config = new stdClass();
        }

        if (empty($this->config->numberoftags)) {
            $this->config->numberoftags = 80;
        }

        if (empty($this->config->showstandard)) {
            $this->config->showstandard = core_tag_tag::BOTH_STANDARD_AND_NOT;
        }

        if (empty($this->config->ctx)) {
            $this->config->ctx = 0;
        }

        if (empty($this->config->rec)) {
            $this->config->rec = 1;
        }

        if (empty($this->config->tagcoll)) {
            $this->config->tagcoll = 0;
        }

        if ($this->content !== NULL) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        // Get a list of tags.

        $tagcloud = core_tag_collection::get_tag_cloud($this->config->tagcoll,
                $this->config->showstandard == core_tag_tag::STANDARD_ONLY,
                $this->config->numberoftags,
                'name', '', $this->page->context->id, $this->config->ctx, $this->config->rec);
        $this->content->text = $OUTPUT->render_from_template('core_tag/tagcloud', $tagcloud->export_for_template($OUTPUT));

        return $this->content;
    }
}
