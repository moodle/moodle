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

    public function has_config() {
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
            $this->title = $this->config->title;
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

        if (empty($this->config->tagtype)) {
            $this->config->tagtype = '';
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

        require_once($CFG->dirroot.'/tag/locallib.php');

        if (empty($CFG->block_tags_showcoursetags) or !$CFG->block_tags_showcoursetags) {

            $this->content->text = tag_print_cloud(null, $this->config->numberoftags, true);

        } else {
            // Start of show course tags section.
            require_once($CFG->dirroot.'/tag/coursetagslib.php');

            // Page awareness.
            $tagtype = 'all';
            if ($SCRIPT == '/my/index.php') {
                $tagtype = 'my';
            } else if (isset($this->page->course->id)) {
                if ($this->page->course->id != SITEID) {
                    $tagtype = 'course';
                }
            }

            // DB hits to get groups of marked up tags (if available).
            // TODO check whether time limited personal tags are required.
            $content = '';
            $moretags = new moodle_url('/tag/coursetags_more.php', array('show'=>$tagtype));
            if ($tagtype == 'all') {
                $tags = coursetag_get_tags(0, 0, $this->config->tagtype, $this->config->numberoftags);
            } else if ($tagtype == 'course') {
                $tags = coursetag_get_tags($this->page->course->id, 0, $this->config->tagtype, $this->config->numberoftags);
                $moretags->param('courseid', $this->page->course->id);
            } else if ($tagtype == 'my') {
                $tags = coursetag_get_tags(0, $USER->id, $this->config->tagtype, $this->config->numberoftags);
            }
            $tagcloud = tag_print_cloud($tags, 150, true);
            if (!$tagcloud) {
                $tagcloud = get_string('notagsyet', 'block_tags');
            }

            // Prepare the divs that display the groups of tags.
            $content = get_string($tagtype."tags", 'block_tags').
                    '<div class="coursetag_list">'.$tagcloud.'</div>
                    <div class="coursetag_morelink">
                        <a href="'.$moretags->out().'" title="'.get_string('moretags', 'block_tags').'">'
                        .get_string('more', 'block_tags').'</a>
                    </div>';
            // Add javascript.
            coursetag_get_jscript();

            // Add the divs (containing the tags) to the block's content.
            $this->content->text .= $content;

            // Add the input form section (allowing a user to tag the current course) and navigation, or login message.
            if (isloggedin() && !isguestuser()) {
                // Only show the input form on course pages for those allowed (or not barred).
                if ($tagtype == 'course' &&
                                has_capability('moodle/tag:create', context_course::instance($this->page->course->id))) {
                    $buttonadd = get_string('add', 'block_tags');
                    $arrowtitle = get_string('arrowtitle', 'block_tags');
                    $edittags = get_string('edittags', 'block_tags');
                    $sesskey = sesskey();
                    $arrowright = $OUTPUT->pix_url('t/arrow_left');
                    $redirect = $this->page->url->out();
                    $this->content->footer .= <<<EOT
                        <hr />
                        <form action="{$CFG->wwwroot}/tag/coursetags_add.php" method="post" id="coursetag"
                                onsubmit="return ctags_checkinput(this.coursetag_new_tag.value)">
                            <div style="display: none;">
                                <input type="hidden" name="entryid" value="$COURSE->id" />
                                <input type="hidden" name="userid" value="$USER->id" />
                                <input type="hidden" name="sesskey" value="$sesskey" />
                                <input type="hidden" name="returnurl" value="$redirect" />
                                </div>
                            <div class="coursetag_form_wrapper">
                                <div class="coursetag_form_positioner">
                                    <div class="coursetag_form_input1">
                                        <input type="text" name="coursetag_sug_keyword" class="coursetag_form_input1a" disabled="disabled" />
                                    </div>
                                    <div class="coursetag_form_input2">
                                        <input type="text" name="coursetag_new_tag" id="coursetag_new_tag"
                                        class="coursetag_form_input2a" onfocus="ctags_getKeywords()" onkeyup="ctags_getKeywords()" maxlength="50" />
                                    </div>
                                    <div class="coursetag_form_input3" id="coursetag_sug_btn">
                                        <a title="$arrowtitle">
                                            <img src="$arrowright" width="10" height="10" alt="enter" onclick="ctags_setKeywords()" />
                                        </a>
                                    </div>
                                </div>
                                <div style="display: inline;">
                                    <button type="submit">$buttonadd</button>
                                    <a href="$CFG->wwwroot/tag/coursetags_edit.php?courseid=$COURSE->id" title="$edittags">$edittags</a>
                                </div>
                            </div>
                        </form>
EOT;
                }
            } else {
                // If not logged in.
                $this->content->footer = '<hr />'.get_string('please', 'block_tags').'
                    <a href="'.get_login_url().'">'.get_string('login', 'block_tags').'
                        </a> '.get_string('tagunits', 'block_tags');
            }
        }
        // End of show course tags section.

        return $this->content;
    }
}
