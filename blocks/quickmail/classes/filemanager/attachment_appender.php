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

namespace block_quickmail\filemanager;

defined('MOODLE_INTERNAL') || die();

use block_quickmail\persistents\message;
use block_quickmail\persistents\message_attachment;
use block_quickmail_string;
use moodle_url;
use html_writer;
use context_course;

class attachment_appender {

    public static $pluginname = 'block_quickmail';

    public $message;
    public $body;
    public $course_context;
    public $message_attachments;

    public function __construct(message $message, $body) {
        $this->message = $message;
        $this->body = $body;
        $this->set_course_context();
        $this->set_message_attachments();
    }

    /**
     * Appends download links to the given message and body, if any
     *
     * @param message  $message
     * @param string  $body
     */
    public static function add_download_links($message, $body) {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');

        $appender = new self($message, $body);

        // If there are no attachments for this message, return body as is.
        if ( ! count($appender->message_attachments)) {
            return $appender->body;
        }

        $appender->add_download_all_links();

        // Append a download link for each individual file.
        $appender->add_individual_files();

        // Run through the moodle cleanser thing.
        $appender->body = file_rewrite_pluginfile_urls(
            $appender->body,
            'pluginfile.php',
            $appender->course_context->id,
            'block_quickmail',
            'attachments',
            $appender->message->get('id')
        );

        return $appender->body;
    }

    /**
     * Appends individual download links to the view message page, if any
     *
     * @param message  $message
     * @param string  $body
     */
    public static function add_individual_links($message, $links='') {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');

        $appender = new self($message, $links);

        // If there are no attachments for this message, return body as is.
        if ( ! count($appender->message_attachments)) {
            return;
        }

        // Append a download link for each individual file.
        $appender->add_individual_link();

        // Append a download link for all files.
        if (count($appender->message_attachments) > 1) {
            $appender->add_download_all_link();
        }
        // Run through the moodle cleanser thing.
        $appender->links = file_rewrite_pluginfile_urls(
            $appender->links,
            'pluginfile.php',
            $appender->course_context->id,
            'block_quickmail',
            'attachments',
            $appender->message->get('id')
        );

        return $appender->links;
    }

    /**
     * Appends "download all" links (both short and full) to the body
     */
    private function add_download_all_links() {
        if ($this->has_attachments()) {
            $this->body .= $this->hr();
            $this->body .= block_quickmail_string::get('attached_files', $this->get_download_all_link());
            $this->body .= $this->br();
            $this->body .= $this->get_download_all_link(true);
        }
    }

    /**
     * Appends "download all" link
     */
    private function add_download_all_link() {
        if ($this->has_attachments()) {
            $this->links ? $this->links .= $this->br() : $this->links = '';
            $this->links .= $this->get_download_all_link(false);
        }
    }

    /**
     * Returns an HTML download link for a zip file containing all attachments
     *
     * @param  bool  $asurl  whether or not to return with lang string text, or just plain
     * @return string
     */
    private function get_download_all_link($asurl = false) {
        $createdat = $this->message->get('timecreated');

        $filename = $createdat . '_attachments.zip';

        $url = $this->generate_url('/', $filename);

        return ! $asurl
            ? html_writer::link($url, get_string('downloadall'))
            : html_writer::link($url, $url);
    }

    /**
     * Appends download links for each attachment to the body
     */
    private function add_individual_files() {
        if ($this->has_attachments()) {
            $this->body .= $this->hr();
            $this->body .= block_quickmail_string::get('download_file_content');
            $this->body .= $this->br();

            // Iterate through each attachment, adding a link and line break.
            foreach ($this->message_attachments as $attachment) {
                $this->body .= html_writer::link($this->generate_url($attachment->get('path'),
                                                                     $attachment->get('filename')),
                                                                     $attachment->get_full_filepath());
                $this->body .= $this->br();
            }
        }
    }

    private function add_individual_link() {
        if ($this->has_attachments()) {
            isset($this->links) ? $this->links .= $this->br() : $this->links = '';
            // Iterate through each attachment, adding a link and line break.
            foreach ($this->message_attachments as $attachment) {
                $this->links .= html_writer::link($this->generate_url($attachment->get('path'),
                                                                      $attachment->get('filename')),
                                                                      $attachment->get_full_filepath());
                $this->links .= $this->br();
            }
        }
    }
    /**
     * Returns a URL pointing to a file with the given path and filename
     *
     * @param  string  $path
     * @param  string  $filename
     * @return string            [description]
     */
    private function generate_url($path, $filename) {
        $url = moodle_url::make_pluginfile_url(
            $this->course_context->id,
            'block_quickmail',
            'attachments',
            $this->message->get('id'),
            $path,
            $filename,
            true
        );

        return $url->out(false);
    }

    /**
     * Reports whether or not this appender's message has any attachments
     *
     * @return bool
     */
    private function has_attachments() {
        return (bool) count($this->message_attachments);
    }

    private function hr() {
        return "\n<br/>-------\n<br/>";
    }

    private function br() {
        return "\n<br/>";
    }

    private function set_course_context() {
        $course = $this->message->get_course();

        $this->course_context = context_course::instance($course->id);
    }

    private function set_message_attachments() {
        $this->message_attachments = $this->message->get_message_attachments();
    }

}
