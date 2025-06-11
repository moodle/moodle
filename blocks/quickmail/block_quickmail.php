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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_quickmail extends block_list {

    public $course;
    public $user;
    public $content;
    public $systemcontext;
    public $coursecontext;

    public function init() {
        $this->title = $this->get_title();
        $this->set_course();
        $this->set_user();
        $this->set_system_context();
        $this->set_course_context();
    }

    public function get_title() {
        return block_quickmail_string::get('pluginname');
    }

    public function set_course() {
        global $COURSE;

        $this->course = $COURSE;
    }

    public function set_user() {
        global $USER;

        $this->user = $USER;
    }

    /**
     * Returns the system context
     *
     * @return context
     */
    private function set_system_context() {
        $this->systemcontext = context_system::instance();
    }

    /**
     * Returns this course's context
     *
     * @return context
     */
    private function set_course_context() {
        $this->coursecontext = context_course::instance($this->course->id);
    }

    /**
     * Indicates which pages types this block may be added to
     *
     * @return array
     */
    public function applicable_formats() {
        $formats = [
            'course-view' => true,
            'mod-scorm-view' => true
        ];

        if (block_quickmail_plugin::user_can_send('broadcast', $this->user, $this->systemcontext)) {
            return array_merge($formats, [
                'site' => true,
                'my' => true,
            ]);
        } else {
            return array_merge($formats, [
                'site' => false,
                'my' => false,
            ]);
        }
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
        if (!empty($this->content)) {
            return $this->content;
        }

        // Create a fresh content container.
        $this->content = $this->get_new_content_container();

        $coursecontext = context_course::instance($this->course->id);
        $systemcontext = context_system::instance();

        // Course-level Features (compose).
        if (!$this->is_site_course() && block_quickmail_plugin::user_can_send('compose', $this->user, $this->coursecontext)) {
            // COMPOSE (course-scoped message).
            $this->add_item_to_content([
                'lang_key' => block_quickmail_string::get('compose'),
                'icon_key' => 't/email',
                'page' => 'compose',
                'query_string' => ['courseid' => $this->course->id]
            ]);

            // DRAFTS (manage your personal message drafts).
            $this->add_item_to_content([
                'lang_key' => block_quickmail_string::get('manage_drafts'),
                'icon_key' => 'e/template',
                'page' => 'drafts',
                'query_string' => ['courseid' => $this->course->id]
            ]);

            // QUEUED (view/manage your queued messages).
            $this->add_item_to_content([
                'lang_key' => block_quickmail_string::get('view_queued'),
                'icon_key' => 'i/scheduled',
                'page' => 'queued',
                'query_string' => ['courseid' => $this->course->id]
            ]);

            // SENT (view your sent messages).
            $this->add_item_to_content([
                'lang_key' => block_quickmail_string::get('view_sent'),
                'icon_key' => 't/message',
                'page' => 'sent',
                'query_string' => ['courseid' => $this->course->id]
            ]);

            // SIGNATURES (manage your personal signatures).
            $this->add_item_to_content([
                'lang_key' => block_quickmail_string::get('manage_signatures'),
                'icon_key' => 'i/edit',
                'page' => 'signatures',
                'query_string' => ['courseid' => $this->course->id]
            ]);

            // ALTERNATES (manage your alternate send-from emails).
            if (block_quickmail_plugin::user_has_capability('allowalternate', $this->user, $this->coursecontext)) {
                $this->add_item_to_content([
                    'lang_key' => block_quickmail_string::get('manage_alternates'),
                    'icon_key' => 't/add',
                    'page' => 'alternate',
                    'query_string' => ['courseid' => $this->course->id]
                ]);
            }

            // CONFIGURATION (settings for course-level messaging preferences).
            if (block_quickmail_plugin::user_has_capability('canconfig', $this->user, $this->coursecontext)) {
                $this->add_item_to_content([
                    'lang_key' => get_string('configuration'),
                    'icon_key' => 'i/settings',
                    'page' => 'configuration',
                    'query_string' => ['courseid' => $this->course->id]
                ]);
            }

            // NOTIFICATIONS.
            if (block_quickmail_plugin::user_can_create_notifications($this->user, $this->coursecontext)) {
                $this->add_item_to_content([
                    'lang_key' => block_quickmail_string::get('notifications'),
                    'icon_key' => 'e/insert_time',
                    'page' => 'notifications',
                    'query_string' => ['courseid' => $this->course->id]
                ]);

                $this->add_item_to_content([
                    'lang_key' => block_quickmail_string::get('create_notification'),
                    'icon_key' => 'i/calendar',
                    'page' => 'create_notification',
                    'query_string' => ['courseid' => $this->course->id]
                ]);
            }

            // Site-level Features (broadcast).
        } else if ($this->is_site_course() && block_quickmail_plugin::user_can_send(
                      'broadcast',
                      $this->user,
                      $this->systemcontext)) {
            // BROADCAST (site-scoped admin message).
            $this->add_item_to_content([
                'lang_key' => block_quickmail_string::get('open_broadcast'),
                'icon_key' => 't/email',
                'page' => 'broadcast',
            ]);

            // DRAFTS (manage your personal message drafts).
            $this->add_item_to_content([
                'lang_key' => block_quickmail_string::get('manage_drafts'),
                'icon_key' => 'e/template',
                'page' => 'drafts',
                'query_string' => ['courseid' => $this->course->id]
            ]);

            // QUEUED (view/manage your queued messages).
            $this->add_item_to_content([
                'lang_key' => block_quickmail_string::get('view_queued'),
                'icon_key' => 'i/calendar',
                'page' => 'queued',
                'query_string' => ['courseid' => $this->course->id]
            ]);

            // SENT (view your sent messages).
            $this->add_item_to_content([
                'lang_key' => block_quickmail_string::get('view_sent'),
                'icon_key' => 't/message',
                'page' => 'sent',
                'query_string' => ['courseid' => $this->course->id]
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
        global $OUTPUT;

        $label = $params['lang_key'];
        $icon = $OUTPUT->pix_icon($params['icon_key'], $label, 'moodle', ['class' => 'icon']);

        return html_writer::link(
            new moodle_url('/blocks/quickmail/' . $params['page'] . '.php', $params['query_string']),
            $icon . $label
        );
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
     * Reports whether or not this is a site-level course
     *
     * @return boolean
     */
    private function is_site_course() {
        return $this->course->id == SITEID;
    }
}
