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

defined('MOODLE_INTERNAL') || die();

class block_post_grades extends block_list {
    public function init() {
        $this->title = get_string('pluginname', 'block_post_grades');
    }

    public function applicable_formats() {
        return array('site' => false, 'my' => false, 'course' => true);
    }

    /**
     * @return bool true if this block is configurable
     */
    public function has_config() {
        return true;
    }

    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }

        global $DB, $OUTPUT, $COURSE, $CFG;

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        $context = context_course::instance($COURSE->id);

        if (!has_capability('block/post_grades:canpost', $context)) {
            return $this->content;
        }

        require_once($CFG->dirroot . '/blocks/post_grades/lib.php');

        $sections = ues_section::from_course($COURSE, true);

        $periods = post_grades::active_periods_for_sections($sections);

        if (empty($periods)) {
            return $this->content;
        }

        $params = array('courseid' => $COURSE->id);

        $s = ues::gen_str('block_post_grades');

        $notpostedicon = $OUTPUT->pix_icon('i/completion-manual-n',
            $s('not_posted'), 'moodle', array('class' => 'icon'));

        $postedicon = $OUTPUT->pix_icon('i/completion-manual-y',
            $s('posted'), 'moodle', array('class' => 'icon'));

        foreach ($periods as $period) {
            $found = false;

            $bold = html_writer::tag('strong', $s($period->post_type));
            $screenclass = post_grades::screenclass($period->post_type);

            $filterable = method_exists($screenclass, 'can_post');

            $params['period'] = $period->id;
            foreach ($sections as $sec) {
                $sc = new $screenclass($period, $COURSE, $sec->group());
                if ($filterable and !$sc->can_post($sec)) {
                    continue;
                }

                // Hide label if none present.
                if (empty($found)) {
                    $found = true;

                    $this->content->items[] = $bold;
                    $this->content->icons[] = '';
                }

                $group = $sec->group();
                $params['group'] = $group->id;

                $url = new moodle_url('/blocks/post_grades/confirm.php', $params);
                $link = html_writer::link($url, $group->name);

                $this->content->items[] = (post_grades::already_posted_section($sec, $period) ? $postedicon : $notpostedicon) . $link;
            }
        }

        return $this->content;
    }
}
