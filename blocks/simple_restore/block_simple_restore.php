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
 * @package    block_simple_restore
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Be sure no one accesses the page directly.
defined('MOODLE_INTERNAL') || die();

// Import requisite php.
require_once($CFG->dirroot . '/blocks/simple_restore/lib.php');

/**
 * Class providing required functionality.
 *
 * @uses block_list
 * @package block_simple_restore
 */
class block_simple_restore extends block_list {
    public $archivemode;

    /**
     * Setsthe pluginname and archive mode boolean.
     *
     * @global $COURSE
     * @return title
     * @return archive_mode
     */
    public function init() {
        global $COURSE;
        $this->title        = simple_restore_utils::_s('pluginname');
        $this->archivemode = $this->get_archive_mode($COURSE->id);
    }

    /**
     * Returns archive mode boolean.
     *
     * @param $courseid
     * @return archivemode
     */
    public function get_archive_mode($courseid) {
        $archive      = get_config('simple_restore', 'is_archive_server');
        $sitecontext = $courseid == SITEID;
        $archivemode = null != $archive && $archive == 1 && $sitecontext;
        return $archivemode;
    }

    /**
     * Indicates which pages types this block may be added to.
     *
     * @return array
     */
    public function applicable_formats() {
        $site   = array('site' => true, 'course' => false, 'my' => true);
        $course = array('site' => false, 'course' => true, 'my' => false);
        return $this->archivemode ? $site : $course;
    }

    /**
     * Indicates that this block has its own configuration settings
     *
     * @return bool
     */
    public function has_config() {
        return true;
    }

    /**
     * Sets the content to be rendered when displaying this block
     *
     * @return object
     */
    public function get_content() {
        global $CFG, $COURSE, $OUTPUT;
        if (!empty($this->content)) {
            return $this->content;
        }

        // Create a fresh content container.
        $this->content = $this->get_new_content_container();

        // Are we in archive mode or course context?
        if ($this->archivemode) {
            // We are in archive mode.
            $content = $this->get_site_content($COURSE);
        } else {
            // We are in course context.
            $context = context_course::instance($COURSE->id);
            if (!simple_restore_utils::permission('canrestore', $context)) {
                return $this->content;
            }
        $content = $this->get_course_content($COURSE);
        }

        $content->footer = '';
        $this->content = $content;
        return $this->content;
    }

    /**
     * Returns an empty "block list" content container to be filled with content
     *
     * @return object
     */
    private function get_new_content_container() {
        $content = new stdClass;
        $content->items = [];
        $content->icons = [];
        $content->footer = '';

        return $content;
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

        $icon = $OUTPUT->pix_icon($params['icon_key'], $label, 'block_simple_restore', ['class' => 'icon']);

        return html_writer::link(
            new moodle_url('/blocks/simple_restore/' . $params['page'] . '.php', $params['query_string']),
            $icon . $label
        );
    }

    /**
     * Builds the content object apropriate to course contexts.
     *
     * @global type $COURSE
     * @return \stdclass
     */
    private function get_course_content($course) {
        $importstr = simple_restore_utils::_s('restore_course');
        $deletestr = simple_restore_utils::_s('delete_restore');

        $this->add_item_to_content([
            'lang_key' => $importstr,
            'icon_key' => 'import',
            'page' => 'list',
            'query_string' => ['id' => $course->id, 'restore_to' => 1]
        ]);

        $this->add_item_to_content([
            'lang_key' => $deletestr,
            'icon_key' => 'overwrite',
            'page' => 'list',
            'query_string' => ['id' => $course->id, 'restore_to' => 0]
        ]);

        return $this->content;
    }

    /**
     * Build the content object appropriate to the SITE context.
     *
     * @global type $COURSE
     * @global type $OUTPUT
     * @return \stdclass
     */
    private function get_site_content($course) {
        $archivestr = simple_restore_utils::_s('archive_restore');

        $this->add_item_to_content([
            'lang_key' => $archivestr,
            'icon_key' => 'import',
            'page' => 'list',
            'query_string' => ['id' => $course->id, 'restore_to' => 1]
        ]);

        return $this->content;
    }

    /**
     * helper fn for generating the block links.
     */
    private function gen_link($restoreto, $text) {
        global $COURSE;
            return html_writer::link(
                new moodle_url('/blocks/simple_restore/list.php', array(
                    'id' => $COURSE->id,
                    'restore_to' => $restoreto
                )), $text
            );
    }
}
