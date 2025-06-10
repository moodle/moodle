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
 * Cross Enrollment Tool
 *
 * @package   block_lsuxe
 * @copyright 2008 onwards Louisiana State University
 * @copyright 2008 onwards David Lowe, Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_lsuxe extends block_list {

    public $course;
    public $user;
    public $content;
    public $coursecontext;

    public function init() {
        $this->title = get_string('pluginname', 'block_lsuxe');
    }

    /**
     * Indicates that this block has its own configuration settings
     *
     * @return @bool
     */
    public function has_config() {
        return true;
    }

    public function get_content() {
        global $COURSE, $OUTPUT;

        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = $this->get_new_content_container();

        if (is_siteadmin() && $COURSE->id == 1) {

            $this->add_item_to_content([
                'lang_key' => get_string('mappings_view', 'block_lsuxe'),
                'icon_key' => 'i/mnethost',
                'page' => '/blocks/lsuxe/mappings.php'
            ]);

            $this->add_item_to_content([
                'lang_key' => get_string('mappings_create', 'block_lsuxe'),
                'icon_key' => 'i/mnethost',
                'page' => '/blocks/lsuxe/mappings.php',
                'query_string' => ['vform' => 1]
            ]);

            $this->add_item_to_content([
                'lang_key' => get_string('tokens_view', 'block_lsuxe'),
                'icon_key' => 't/unlock',
                'page' => '/admin/settings.php?section=webservicetokens'
            ]);

            $this->add_item_to_content([
                'lang_key' => get_string('moodles_view', 'block_lsuxe'),
                'icon_key' => 't/calc',
                'page' => '/blocks/lsuxe/moodles.php'
            ]);

            $this->add_item_to_content([
                'lang_key' => get_string('moodles_create', 'block_lsuxe'),
                'icon_key' => 't/calc',
                'page' => '/blocks/lsuxe/moodles.php',
                'query_string' => ['vform' => 1]
            ]);
        }

        return $this->content;
    }

    /**
     * Builds and adds an item to the content container for the given params
     *
     * @param  array $params  [lang_key, icon_key, page, query_string]
     * @return void
     */
    private function add_item_to_content($params) {
        if (!array_key_exists('query_string', $params)) {
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
        global $CFG, $OUTPUT;

        $label = $params['lang_key'];
        $icon = $OUTPUT->pix_icon($params['icon_key'], $label, 'moodle', ['class' => 'icon']);

        return html_writer::link(
            new moodle_url($CFG->wwwroot . $params['page'] , $params['query_string']),
            $icon . $label
        );
    }

    // My moodle can only have SITEID and it's redundant here, so take it away.
    public function applicable_formats() {
        return array(
            'site' => true,
            'course-view' => false
        );
    }

    public function instance_allow_multiple() {
        return false;
    }

    public function cron() {
        return true;
    }

    /**
     * Returns an empty "block list" content container to be filled with content.
     *
     * @return @object
     */
    private function get_new_content_container() {
        $content = new stdClass;
        $content->items = array();
        $content->icons = array();
        $content->footer = '';

        return $content;
    }
}
