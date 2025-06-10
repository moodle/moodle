<?php

/*
 * @package TurnitinAPI
 * @subpackage TiiMembership
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Defines the TiiMembership data object which contains getters and setters for a Turnitin Membership object.
 *
 * @package TurnitinSDK
 * @subpackage Data
 */
class TiiMembership {
    private $membershipid;
    private $membershipids;
    private $classid;
    private $userid;
    private $role;

    /**
     * Set the Membership ID for this Membership
     *
     * The membership ID is the ID Turnitin uses to identify an enrollment on a class
     *
     * @param integer $membershipid
     */
    public function setMembershipId( $membershipid ) {
        $this->membershipid = $membershipid;
    }

    /**
     * Get the Membership ID for this Membership
     *
     * The membership ID is the ID Turnitin uses to identify an enrollment on a class
     *
     * @param integer $membershipid
     */
    public function getMembershipId() {
        return $this->membershipid;
    }

    /**
     * Set an array of Membership IDs for this Membership data object
     *
     * The membership ID is the ID Turnitin uses to identify an enrollment on a class
     *
     * @param array $membershipids
     */
    public function setMembershipIds( $membershipids ) {
        $this->membershipids = $membershipids;
    }

    /**
     * Get an array of Membership IDs for this Membership data object
     *
     * The membership ID is the ID Turnitin uses to identify an enrollment on a class
     *
     * @return array
     */
    public function getMembershipIds() {
        return $this->membershipids;
    }

    /**
     * Set the User ID for this Membership
     *
     * The User ID of the User enrolled on this Class
     *
     * @return integer
     */
    public function setUserId( $userid ) {
        $this->userid = $userid;
    }

    /**
     * Get the User ID for this Membership
     *
     * The User ID of the User enrolled on this Class
     *
     * @return integer
     */
    public function getUserId() {
        return $this->userid;
    }

    /**
     * Set the Class ID for this Membership
     *
     * The Class ID a User is enrolled on
     *
     * @param integer $classid
     */
    public function setClassId( $classid ) {
        $this->classid = $classid;
    }

    /**
     * Get the Class ID for this Membership
     *
     * The Class ID a User is enrolled on
     *
     * @return integer
     */
    public function getClassId() {
        return $this->classid;
    }

    /**
     * Set the Role for this Membership
     *
     * The Role the user is enrolled on the Class as, Learner or Instructor
     *
     * @param string $role
     */
    public function setRole( $role ) {
        switch ( strtolower( $role ) ) {
            case 'student':
                $role = 'Learner';
                break;
            case 'learner':
                $role = 'Learner';
                break;
            case 'instructor':
                $role = 'Instructor';
                break;
        }
        $this->role = $role;
    }

    /**
     * Get the Role for this Membership
     *
     * The Role the user is enrolled on the Class as, Learner or Instructor
     *
     * @return string
     */
    public function getRole() {
        return $this->role;
    }

}

//?>