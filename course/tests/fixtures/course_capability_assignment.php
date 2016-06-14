<?php

/**
 * Aids in capability assignment and alteration of the assigned capability.
 *
 * @package    core_course
 * @copyright  2013 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_capability_assignment {
    /**
     * @var array The capability that has been assigned.
     */
    protected $capability;
    /**
     * @var int The role ID that the assignment was made for.
     */
    protected $roleid;
    /**
     * @var int The context ID against which the assignment was made.
     */
    protected $contextid;

    /**
     * Assigns a capability to a role at the given context giving it permission.
     *
     * @param string|array $capability The capability to assign.
     * @param int $roleid The roleID to assign to.
     * @param int $contextid The contextID for where to make the assignment.
     * @return course_capability_assignment
     */
    public static function allow($capability, $roleid, $contextid) {
        return new course_capability_assignment($capability, $roleid, $contextid, CAP_ALLOW);
    }

    /**
     * Assigns a capability to a role at the given context prohibiting it.
     *
     * @param string|array $capability The capability to assign.
     * @param int $roleid The roleID to assign to.
     * @param int $contextid The contextID for where to make the assignment.
     * @return course_capability_assignment
     */
    public static function prohibit($capability, $roleid, $contextid) {
        return new course_capability_assignment($capability, $roleid, $contextid, CAP_PROHIBIT);
    }

    /**
     * Assigns a capability to a role at the given context preventing it.
     *
     * @param string|array $capability The capability to assign.
     * @param int $roleid The roleID to assign to.
     * @param int $contextid The contextID for where to make the assignment.
     * @return course_capability_assignment
     */
    public static function prevent($capability, $roleid, $contextid) {
        return new course_capability_assignment($capability, $roleid, $contextid, CAP_PREVENT);
    }

    /**
     * Creates a new course_capability_assignment object
     *
     * @param string|array $capability The capability to assign.
     * @param int $roleid The roleID to assign to.
     * @param int $contextid The contextID for where to make the assignment.
     * @param int $permission The permission to apply. One of CAP_ALLOW, CAP_PROHIBIT, CAP_PREVENT.
     * @return course_capability_assignment
     */
    protected function __construct($capability, $roleid, $contextid, $permission) {
        if (is_string($capability)) {
            $capability = array($capability);
        }
        $this->capability = $capability;
        $this->roleid = $roleid;
        $this->contextid = $contextid;
        $this->assign($permission);
    }

    /**
     * Assign a new permission.
     * @param int $permission One of CAP_ALLOW, CAP_PROHIBIT, CAP_PREVENT
     */
    public function assign($permission) {
        foreach ($this->capability as $capability) {
            assign_capability($capability, $permission, $this->roleid, $this->contextid, true);
        }
        accesslib_clear_all_caches_for_unit_testing();
    }

    /**
     * Revokes the capability assignment.
     */
    public function revoke() {
        foreach ($this->capability as $capability) {
            unassign_capability($capability, $this->roleid, $this->contextid);
        }
        accesslib_clear_all_caches_for_unit_testing();
    }
}