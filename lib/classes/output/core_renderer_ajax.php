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

namespace core\output;

use core_useragent;
use moodle_url;
use stdClass;

/**
 * A renderer that generates output for ajax scripts.
 *
 * This renderer prevents accidental sends back only json
 * encoded error messages, all other output is ignored.
 *
 * @copyright 2010 Petr Skoda
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class core_renderer_ajax extends core_renderer {
    /**
     * Returns a template fragment representing a fatal error.
     *
     * @param string $message The message to output
     * @param string $moreinfourl URL where more info can be found about the error
     * @param string $link Link for the Continue button
     * @param array $backtrace The execution backtrace
     * @param null|string $debuginfo Debugging information
     * @param string $errorcode
     * @return string A template fragment for a fatal error
     */
    public function fatal_error($message, $moreinfourl, $link, $backtrace, $debuginfo = null, $errorcode = "") {
        global $CFG;

        // Ugly hack - make sure page context is set to something, we do not want bogus warnings here.
        $this->page->set_context(null);

        $e = new stdClass();
        $e->error      = $message;
        $e->errorcode  = $errorcode;
        $e->stacktrace = null;
        $e->debuginfo  = null;
        $e->reproductionlink = null;
        if (!empty($CFG->debug) && $CFG->debug >= DEBUG_DEVELOPER) {
            $link = (string) $link;
            if ($link) {
                $e->reproductionlink = $link;
            }
            if (!empty($debuginfo)) {
                $e->debuginfo = $debuginfo;
            }
            if (!empty($backtrace)) {
                $e->stacktrace = format_backtrace($backtrace, true);
            }
        }
        $this->header();
        return json_encode($e);
    }

    /**
     * Used to display a notification.
     * For the AJAX notifications are discarded.
     *
     * @param string $message The message to print out.
     * @param string $type    The type of notification. See constants on \core\output\notification.
     * @param bool $closebutton Whether to show a close icon to remove the notification (default true).
     * @param string|null $title The title of the notification.
     * @param ?string $titleicon if the title should have an icon you can give the icon name with the component
     *  (e.g. 'i/circleinfo, core' or 'i/circleinfo' if the icon is from core)
     */
    public function notification($message, $type = null, $closebutton = true, ?string $title = null, ?string $titleicon = null) {
    }

    /**
     * Used to display a redirection message.
     * AJAX redirections should not occur and as such redirection messages
     * are discarded.
     *
     * @param moodle_url|string $encodedurl
     * @param string $message
     * @param int $delay
     * @param bool $debugdisableredirect
     * @param string $messagetype The type of notification to show the message in.
     *         See constants on \core\output\notification.
     */
    public function redirect_message(
        $encodedurl,
        $message,
        $delay,
        $debugdisableredirect,
        $messagetype = notification::NOTIFY_INFO,
    ) {
    }

    /**
     * Prepares the start of an AJAX output.
     */
    public function header() {
        // Unfortunately YUI iframe upload does not support application/json.
        if (!empty($_FILES)) {
            @header('Content-type: text/plain; charset=utf-8');
            if (!core_useragent::supports_json_contenttype()) {
                @header('X-Content-Type-Options: nosniff');
            }
        } else if (!core_useragent::supports_json_contenttype()) {
            @header('Content-type: text/plain; charset=utf-8');
            @header('X-Content-Type-Options: nosniff');
        } else {
            @header('Content-type: application/json; charset=utf-8');
        }

        // Headers to make it not cacheable and json.
        @header('Cache-Control: no-store, no-cache, must-revalidate');
        @header('Cache-Control: post-check=0, pre-check=0', false);
        @header('Pragma: no-cache');
        @header('Expires: Mon, 20 Aug 1969 09:23:00 GMT');
        @header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        @header('Accept-Ranges: none');
    }

    /**
     * There is no footer for an AJAX request, however we must override the
     * footer method to prevent the default footer.
     */
    public function footer() {
    }

    /**
     * No need for headers in an AJAX request... this should never happen.
     * @param string $text
     * @param int $level
     * @param string $classes
     * @param string $id
     */
    public function heading($text, $level = 2, $classes = 'main', $id = null) {
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(core_renderer_ajax::class, \core_renderer_ajax::class);
