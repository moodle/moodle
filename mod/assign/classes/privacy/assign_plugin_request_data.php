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
 * This file contains the mod_assign assign_plugin_request_data class
 *
 * For assign plugin privacy data to fulfill requests.
 *
 * @package mod_assign
 * @copyright 2018 Adrian Greeve <adrian@moodle.com>
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_assign\privacy;

defined('MOODLE_INTERNAL') || die();

/**
 * An object for fulfilling an assign plugin data request.
 *
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_plugin_request_data {

    /** @var context The context that we are dealing with. */
    protected $context;

    /** @var object For submisisons the submission object, for feedback the grade object. */
    protected $pluginobject;

    /** @var array The path or location that we are exporting data to. */
    protected $subcontext;

    /** @var object If set then only export data related directly to this user. */
    protected $user;

    /** @var assign The assign object */
    protected $assign;

    /**
     * Object creator for assign plugin request data.
     *
     * @param \context $context Context object.
     * @param \stdClass $pluginobject The grade object.
     * @param array  $subcontext Directory / file location.
     * @param \stdClass $user The user object.
     * @param \assign $assign The assign object.
     */
    public function __construct(\context $context, \assign $assign, \stdClass $pluginobject = null, array $subcontext = [],
            \stdClass $user = null) {
        $this->context = $context;
        $this->pluginobject = $pluginobject;
        $this->subcontext = $subcontext;
        $this->user = $user;
        $this->assign = $assign;
    }

    /**
     * Getter for this attribute.
     *
     * @return context Context
     */
    public function get_context() {
        return $this->context;
    }

    /**
     * Getter for this attribute.
     *
     * @return object The assign plugin object
     */
    public function get_pluginobject() {
        return $this->pluginobject;
    }

    /**
     * Getter for this attribute.
     *
     * @return array The location (path) that this data is being writter to.
     */
    public function get_subcontext() {
        return $this->subcontext;
    }

    /**
     * Getter for this attribute.
     *
     * @return object The user id. If set then only information directly related to this user ID will be returned.
     */
    public function get_user() {
        return $this->user;
    }

    /**
     * Getter for this attribute.
     *
     * @return assign The assign object.
     */
    public function get_assign() {
        return $this->assign;
    }
}
