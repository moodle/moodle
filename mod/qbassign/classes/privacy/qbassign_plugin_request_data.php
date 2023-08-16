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
 * This file contains the mod_qbassign qbassign_plugin_request_data class
 *
 * For qbassign plugin privacy data to fulfill requests.
 *
 * @package mod_qbassign
 * @copyright 2018 Adrian Greeve <adrian@moodle.com>
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_qbassign\privacy;

defined('MOODLE_INTERNAL') || die();

/**
 * An object for fulfilling an qbassign plugin data request.
 *
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbassign_plugin_request_data {

    /** @var context The context that we are dealing with. */
    protected $context;

    /** @var object For submisisons the submission object, for feedback the grade object. */
    protected $pluginobject;

    /** @var array The path or location that we are exporting data to. */
    protected $subcontext;

    /** @var object If set then only export data related directly to this user. */
    protected $user;

    /** @var array The user IDs of the users that will be affected. */
    protected $userids;

    /** @var array The submissions related to the users added. */
    protected $submissions = [];

    /** @var array The grades related to the users added. */
    protected $grades = [];

    /** @var qbassign The qbassign object */
    protected $qbassign;

    /**
     * Object creator for qbassign plugin request data.
     *
     * @param \context $context Context object.
     * @param \stdClass $pluginobject The grade object.
     * @param array  $subcontext Directory / file location.
     * @param \stdClass $user The user object.
     * @param \qbassign $qbassign The qbassign object.
     */
    public function __construct(\context $context, \qbassign $qbassign, \stdClass $pluginobject = null, array $subcontext = [],
            \stdClass $user = null) {
        $this->context = $context;
        $this->pluginobject = $pluginobject;
        $this->subcontext = $subcontext;
        $this->user = $user;
        $this->qbassign = $qbassign;
    }

    /**
     * Method for adding an array of user IDs. This will do a query to populate the submissions and grades
     * for these users.
     *
     * @param array $userids User IDs to do something with.
     */
    public function set_userids(array $userids) {
        $this->userids = $userids;
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
     * @return object The qbassign plugin object
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
     * @return qbassign The qbassign object.
     */
    public function get_qbassign() {
        return $this->qbassign;
    }

    /**
     * A method to conveniently fetch the qbassign id.
     *
     * @return int The qbassign id.
     */
    public function get_qbassignid() {
        return $this->qbassign->get_instance()->id;
    }

    /**
     * Get all of the user IDs
     *
     * @return array User IDs
     */
    public function get_userids() {
        return $this->userids;
    }

    /**
     * Returns all of the submission IDs
     *
     * @return array submission IDs
     */
    public function get_submissionids() {
        return array_keys($this->submissions);
    }

    /**
     * Returns the submissions related to the user IDs
     *
     * @return array User submissions.
     */
    public function get_submissions() {
        return $this->submissions;
    }

    /**
     * Returns the grade IDs related to the user IDs
     *
     * @return array User grade IDs.
     */
    public function get_gradeids() {
        return array_keys($this->grades);
    }

    /**
     * Returns the grades related to the user IDs
     *
     * @return array User grades.
     */
    public function get_grades() {
        return $this->grades;
    }

    /**
     * Fetches all of the submissions and grades related to the User IDs provided. Use get_grades, get_submissions etc to
     * retrieve this information.
     */
    public function populate_submissions_and_grades() {
        global $DB;

        if (empty($this->get_userids())) {
            throw new \coding_exception('Please use set_userids() before calling this method.');
        }

        list($sql, $params) = $DB->get_in_or_equal($this->get_userids(), SQL_PARAMS_NAMED);
        $params['qbassign'] = $this->get_qbassign()->get_instance()->id;
        $this->submissions = $DB->get_records_select('qbassign_submission', "qbassignment = :qbassign AND userid $sql", $params);
        $this->grades = $DB->get_records_select('qbassign_grades', "qbassignment = :qbassign AND userid $sql", $params);
    }
}
