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
 * Moodle user analysable
 *
 * @package   core_analytics
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics;

defined('MOODLE_INTERNAL') || die();

/**
 * Moodle user analysable
 *
 * @package   core_analytics
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user implements \core_analytics\analysable {

    /**
     * @var bool Has this user data been already loaded.
     */
    protected $loaded = false;

    /**
     * @var int $cachedid self::$cachedinstance analysable id.
     */
    protected static $cachedid = 0;

    /**
     * @var \core_analytics\user $cachedinstance
     */
    protected static $cachedinstance = null;

    /**
     * User object
     *
     * @var \stdClass
     */
    protected $user = null;

    /**
     * The user context.
     *
     * @var \context_user
     */
    protected $usercontext = null;

    /** @var int Store current Unix timestamp. */
    protected int $now = 0;

    /**
     * Constructor.
     *
     * Use self::instance() instead to get cached copies of the class. Instances obtained
     * through this constructor will not be cached.
     *
     * @param int|\stdClass $user User id
     * @param \context|null $context
     * @return void
     */
    public function __construct($user, ?\context $context = null) {

        if (is_scalar($user)) {
            $this->user = new \stdClass();
            $this->user->id = $user;
        } else {
            $this->user = $user;
        }

        if (!is_null($context)) {
            $this->usercontext = $context;
        }
    }

    /**
     * Returns an analytics user instance.
     *
     * Lazy load of analysable data.
     *
     * @param int|\stdClass $user User object or user id
     * @param \context|null $context
     * @return \core_analytics\user
     */
    public static function instance($user, ?\context $context = null) {

        $userid = $user;
        if (!is_scalar($userid)) {
            $userid = $user->id;
        }

        if (self::$cachedid === $userid) {
            return self::$cachedinstance;
        }

        $cachedinstance = new \core_analytics\user($user, $context);
        self::$cachedinstance = $cachedinstance;
        self::$cachedid = (int)$userid;
        return self::$cachedinstance;
    }

    /**
     * get_id
     *
     * @return int
     */
    public function get_id() {
        return $this->user->id;
    }

    /**
     * Loads the analytics user object.
     *
     * @return void
     */
    protected function load() {

        // The instance constructor could be already loaded with the full user object. Using email
        // because it is a required user field.
        if (empty($this->user->email)) {
            $this->user = \core_user::get_user($this->user->id);
        }

        $this->usercontext = $this->get_context();

        $this->now = time();

        // Flag the instance as loaded.
        $this->loaded = true;
    }

    /**
     * The user full name.
     *
     * @return string
     */
    public function get_name() {

        if (!$this->loaded) {
            $this->load();
        }
        return fullname($this->user);
    }

    /**
     * get_context
     *
     * @return \context
     */
    public function get_context() {
        if ($this->usercontext === null) {
            $this->usercontext = \context_user::instance($this->user->id);
        }
        return $this->usercontext;
    }

    /**
     * Get the start timestamp.
     *
     * @return int
     */
    public function get_start() {

        if (!$this->loaded) {
            $this->load();
        }
        return $this->user->timecreated;
    }

    /**
     * Get the end timestamp.
     *
     * @return int
     */
    public function get_end() {
        return self::MAX_TIME;
    }

    /**
     * Returns a user plain object.
     *
     * @return \stdClass
     */
    public function get_user_data() {

        if (!$this->loaded) {
            $this->load();
        }

        return $this->user;
    }
}
