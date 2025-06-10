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
 *
 * @package    block_ues_logs
 * @copyright  2014 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_ues_logs extends block_list {
    public function init() {
        $this->title = get_string('pluginname', 'block_ues_logs');
    }

    public function applicable_formats() {
        return array('site' => false, 'course' => true, 'my' => false);
    }

    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }

        global $CFG, $COURSE, $OUTPUT;

        $context = context_course::instance($COURSE->id);
        if (!has_capability('moodle/grade:edit', $context)) {
            return $this->content;
        }

        require_once($CFG->dirroot . '/blocks/ues_logs/classes/lib.php');
        require_once($CFG->dirroot . '/enrol/ues/publiclib.php');
        ues::require_daos();

        $sections = ues_section::from_course($COURSE);

        $byparams = function ($sections) {
            return array('sectionid' => current($sections)->id);
        };

        // No sections or ones with enrollment info.
        if (empty($sections) or !ues_log::count($byparams($sections))) {
            return $this->content;
        }

        $content = new stdClass;
        $content->icons = array();
        $content->footer = '';
        $this->content = $content;

        // Actually build the content and add it.
        $this->add_item_to_content([
            'lang_key' => $this->title,
            'icon_key' => 'i/users',
            'page' => 'view',
            'query_string' => ['id' => $COURSE->id]
        ]);

        return $this->content;
    }

    /**
     * Builds and adds an item to the content container for the given params
     *
     * @param  array $params  [lang_key, icon_key, page, query_string]
     * @return void
     */
    private function add_item_to_content($params) {
        if ( ! array_key_exists('query_string', $params)) {
            $params['query_string'] = [];
        }
        $item = $this->build_item($params);
        $this->content->items[] = $item;
    }

    /**
     * Builds a content item (link) for the given params
     *
     * @param  array $params  [lang_key, icon_key, page, query_string]
     * @return string
     */
    private function build_item($params) {
        global $OUTPUT;

        $label = $params['lang_key'];

        $icon = $OUTPUT->pix_icon($params['icon_key'], $label, 'moodle', ['class' => 'icon']);

        return html_writer::link(
            new moodle_url('/blocks/ues_logs/' . $params['page'] . '.php', $params['query_string']),
            $icon . $label
        );
    }
}