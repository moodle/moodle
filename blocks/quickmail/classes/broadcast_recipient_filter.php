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

require_once($CFG->dirroot . '/user/filters/lib.php');

class block_quickmail_broadcast_recipient_filter {

    public $filterparams;
    public $extraparams;
    public $draftmessage;
    public $ufilter;
    public $filterresultsql;
    public $filterresultparams;
    public $resultusers;
    public $displayusers;
    public $resultusercount = null;

    public static $sessionkey = 'user_filtering';

    /*
     * Necessary filter navigation key and their default values
     */
    public static $defaultfilterparams = [
        'page' => 1,
        'per_page' => 20,
        'sort_by' => 'lastname',
        'sort_dir' => 'asc'
    ];

    public $supportedfields = [
        'realname' => 1,
        'lastname' => 1,
        'firstname' => 1,
        'email' => 1,
        'city' => 1,
        'country' => 1,
        'confirmed' => 1,
        'suspended' => 1,
        'profile' => 1,
        'courserole' => 0,
        'systemrole' => 0,
        'username' => 0,
        'cohort' => 1,
        'firstaccess' => 1,
        'lastaccess' => 0,
        'neveraccessed' => 1,
        'timemodified' => 1,
        'nevermodified' => 1,
        'auth' => 1,
        'mnethostid' => 1,
        'language' => 1,
        'firstnamephonetic' => 1,
        'lastnamephonetic' => 1,
        'middlename' => 1,
        'alternatename' => 1
    ];

    /**
     * Construct a wrapper instance for moodle's user_filtering class
     *
     * @param array  $filterparams
     * @param array  $extraparams
     * @param mixed  $draftmessage  optional draft message, defaults to null
     */
    public function __construct($filterparams, $extraparams, $draftmessage = null) {
        global $SESSION;

        $this->filter_params = $filterparams;
        $this->extra_params = $extraparams;
        $this->draft_message = $draftmessage;

        // In user/filters/lib.php  this variable $SESSION->user_filtering
        // sometimes is set to '' which causes an error. Instead of changing core from
        // if (!isset($SESSION->user_filtering) to the what's below I'll do that 
        // check here right before the call. 
        if (!isset($SESSION->user_filtering) || $SESSION->user_filtering == '') {
            $SESSION->user_filtering = array();
        }

        $this->ufilter = new user_filtering($this->supportedfields, null, $this->extra_params);

        // If there is a valid draft message passed, attempt to set the pre-set the filter but only if none already exist.
        if (!empty($this->draft_message) && ! $this->has_set_filter()) {
            $this->set_filter_value($this->draft_message->get_broadcast_draft_recipient_filter());
        }

        $this->set_filter_sql_results();
        $this->set_result_users();
        $this->set_display_users();
    }

    /**
     *
     * Instantiates a new recipient filter object based on page params and optional draft message
     * If a draft message is passed, this will set the current filter selections to anything saved for that draft
     *
     * @param  array    $pageparams
     * @param  message  $draftmessage  optional
     * @return broadcast_recipient_filter
     */
    public static function make($pageparams, $draftmessage = null) {
        $filterparams = self::get_filter_params($pageparams);
        $extraparams = self::get_extra_params($draftmessage);
        return new self($filterparams, $extraparams, $draftmessage);
    }

    /**
     * Gets normalized filter params necessary for navigation of this filter instance from a given array of params
     *
     * @param  array  $params
     * @return array
     */
    public static function get_filter_params($params) {
        $filterparams = [];
        foreach (array_keys(self::$defaultfilterparams) as $key) {
            $filterparams[$key] = array_key_exists($key, $params)
                ? $params[$key]
                : self::$defaultfilterparams[$key];
        }
        return $filterparams;
    }

    /**
     * Gets additional query string params needed for external use outside of this filter instance
     *
     * @param mixed  $draftmessage  optional draft message, defaults to null
     * @return array
     */
    public static function get_extra_params($draftmessage = null) {
        return !empty($draftmessage)
            ? ['draftid' => $draftmessage->get('id')]
            : [];
    }

    /**
     * Sets ufilter sql results and params
     */
    private function set_filter_sql_results() {
        list($sql, $params) = $this->ufilter->get_sql_filter();
        $this->filter_result_sql = $sql;
        $this->filter_result_params = $params;
    }

    /**
     * Sets the filtered "result" users
     */
    public function set_result_users() {
        $this->result_users = empty($this->filter_result_sql) ? []
            : get_users_listing($this->filter_params['sort_by'],
            $this->filter_params['sort_dir'], 0, 0, '', '', '', $this->filter_result_sql, $this->filter_result_params);
    }

    /**
     * Sets the filtered "result" users to display as per "page" and "per_page" settings
     */
    public function set_display_users() {
        if (empty($this->result_users)) {
            $this->display_users = [];
        } else {
            $offset = ($this->filter_params['page'] * $this->filter_params['per_page']) - $this->filter_params['per_page'];
            $this->display_users = array_slice($this->result_users, $offset, $this->filter_params['per_page'], true);
        }
    }

    /**
     * Returns the count of users in the current results, caching the result for later calls
     *
     * @return int
     */
    public function get_result_user_count() {
        if (is_null($this->resultusercount)) {
            $this->result_user_count = count($this->result_users);
        }
        return $this->result_user_count;
    }

    /**
     * Returns the user ids for the result users
     *
     * @return array
     */
    public function get_result_user_ids() {
        return array_keys($this->result_users);
    }

    /**
     * Returns the current draft id, if any, defaulting to 0
     *
     * @return int
     */
    public function get_draft_id() {
        return !empty($this->draft_message) ? $this->draft_message->get('id') : 0;
    }

     // Output Rendering.
    /**
     * Renders the user_filtering "add" magic
     *
     * @return string
     */
    public function render_add() {
        return $this->ufilter->display_add();
    }

    /**
     * Renders the user_filtering "active" magic
     *
     * @return string
     */
    public function render_active() {
        return $this->ufilter->display_active();
    }

    /**
     * Renders a pagination bar for the result users
     *
     * @return string
     */
    public function render_paging_bar() {
        global $OUTPUT;
        echo $OUTPUT->paging_bar($this->get_result_user_count(),
            $this->filter_params['page'], $this->filter_params['per_page'],
            new moodle_url('/blocks/quickmail/broadcast.php', [
                'draftid' => $this->get_draft_id(),
                'sort_by' => $this->filter_params['sort_by'],
                'sort_dir' => $this->filter_params['sort_dir'],
                'per_page' => $this->filter_params['per_page'],
            ]
        ));
    }

    // Session.
    /**
     * Unsets any session data for this filter
     *
     * @return void
     */
    public function clear_session() {
        global $SESSION;
        $key = self::$sessionkey;
        unset($SESSION->$key);
    }

    /**
     * Reports whether or not there are filters set
     *
     * @return bool
     */
    public function has_set_filter() {
        global $SESSION;
        $key = self::$sessionkey;
        return !empty($SESSION->$key);
    }

    /**
     * Returns the set user filter value, optionally as serialized string (by default)
     *
     * @param  bool    $serialize   if true, will return as serialized string
     * @param  mixed   $default     default value to return if no filter in session
     * @return mixed
     */
    public function get_filter_value($serialize = true, $default = '') {
        if (!$this->has_set_filter()) {
            return $default;
        }

        global $SESSION;
        $key = self::$sessionkey;
        $value = $SESSION->$key;
        return $serialize ? serialize($value) : $value;
    }

    /**
     * Sets the current filter state to the given value
     *
     * @param  array
     * @return void
     */
    public function set_filter_value($value) {
        global $SESSION;
        $key = self::$sessionkey;
        $SESSION->$key = $value;
    }
}
