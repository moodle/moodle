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

namespace block_quickmail\controllers\support;

defined('MOODLE_INTERNAL') || die();

class controller_request {

    public $view_name;
    public $view_form_name;
    public $post;
    public $input;

    public function __construct() {
        $this->set_view_props();
        $this->set_input();
    }

    /**
     * Instantiate and return a new request instance
     *
     * @return controller_request
     */
    public static function make() {
        $request = new self();

        return $request;
    }

    /**
     * Sets view name and form name properties on the request
     */
    private function set_view_props() {
        $this->view_form_name = ! empty($_POST) && array_key_exists('view_form_name', $_POST)
            ? $_POST['view_form_name']
            : '';

        $this->view_name = $this->view_form_name
            ? substr($this->view_form_name, 0, -5)
            : '';
    }

    /**
     * Sets any relevant submitted input on the request
     */
    private function set_input() {
        $this->input = ! empty($_POST)
            ? $this->filter_input($_POST)
            : (object)[];
    }

    /**
     * Reports whether or not this request includes form input of a given key
     *
     * @param  string  $key  the input's key
     * @return bool
     */
    public function has_input($key) {
        return property_exists($this->input, $key);
    }

    /**
     * Returns filtered post input given an array of posted data
     *
     * @param  array  $post
     * @return \stdClass
     */
    private function filter_input($post) {
        $input = (object)[];

        // Strip out a few keys from the post...
        foreach ($post as $key => $value) {
            if (in_array($key, ['view_form_name', 'sesskey'])) {
                continue;
            } else if (strpos($key, '_qf__') === 0) {
                continue;
            }

            $input->$key = $value;
        }

        return $input;
    }

    // Redirects.
    /**
     * Convenience wrapper for redirecting to moodle URLs
     *
     * @param  string  $url
     * @param  array   $urlparams   array of parameters for the given URL
     * @param  int     $delay        delay, in seconds, before redirecting
     * @return (http redirect header)
     */
    public function redirect_to_url($url, $urlparams = [], $delay = 2) {
        $moodleurl = new \moodle_url($url, $urlparams);

        redirect($moodleurl, '', $delay);
    }

    /**
     * Helper for redirecting to a course, or defaulting to the "my" page
     *
     * @param  int  $courseid
     * @return (http redirect header)
     */
    public function redirect_to_course_or_my($courseid = 0) {
        if ($courseid) {
            $this->redirect_to_url('/course/view.php', ['id' => $courseid]);
        } else {
            $this->redirect_to_url('/my');
        }
    }

    /**
     * Convenience wrapper for redirecting to moodle URLs while including a status type and message
     *
     * @param  string  $type         success|info|warning|error
     * @param  string  $message      a pre-rendered string message
     * @param  string  $url
     * @param  array   $urlparams   array of parameters for the given URL
     * @param  int     $delay        delay, in seconds, before redirecting
     * @return (http redirect header)
     */
    public function redirect_as_type($type, $message, $url, $urlparams = [], $delay = 2) {
        $types = [
            'success' => \core\output\notification::NOTIFY_SUCCESS,
            'info' => \core\output\notification::NOTIFY_INFO,
            'warning' => \core\output\notification::NOTIFY_WARNING,
            'error' => \core\output\notification::NOTIFY_ERROR,
        ];

        $moodleurl = new \moodle_url($url, $urlparams);

        redirect($moodleurl, $message, $delay, $types[$type]);
    }

    /**
     * Helper function to redirect as type success
     */
    public function redirect_as_success($message, $url, $urlparams = [], $delay = 2) {
        $this->redirect_as_type('success', $message, $url, $urlparams, $delay);
    }

    /**
     * Helper function to redirect as type info
     */
    public function redirect_as_info($message, $url, $urlparams = [], $delay = 2) {
        $this->redirect_as_type('info', $message, $url, $urlparams, $delay);
    }

    /**
     * Helper function to redirect as type warning
     */
    public function redirect_as_warning($message, $url, $urlparams = [], $delay = 2) {
        $this->redirect_as_type('warning', $message, $url, $urlparams, $delay);
    }

    /**
     * Helper function to redirect as type error
     */
    public function redirect_as_error($message, $url, $urlparams = [], $delay = 2) {
        $this->redirect_as_type('error', $message, $url, $urlparams, $delay);
    }

}
