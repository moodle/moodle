<?php
/*
 * @package TurnitinAPI
 * @subpackage TiiClass 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Integrations\PhpSdk;

/**
 * Defines the TiiSubmission data object which contains getters and setters for a Turnitin Class API object.
 * 
 * @package TurnitinSDK
 * @subpackage Data
 */
class TiiClass {
    private $title;
    private $classid;
    private $classids;
    private $enddate;
    private $datefrom;
    private $integrationid;
    private $userid;
    private $userrole;
    private $sharedrubrics;

    /**
     * Get any shared rubrics for the class
     *
     * Returns an array of Rubric objects
     *
     * @return array
     */
    public function getSharedRubrics()
    {
        $rubrics = array();
        $values = ( !empty( $this->sharedrubrics ) ) ? json_decode( $this->sharedrubrics ) : array();

        foreach ($values as $key => $shared_rubric) {
            $rubric = new TiiRubric();
            foreach ($shared_rubric as $k => $v ) {
                $method = 'set'.$k;
                if (is_callable(array($rubric, $method))) {
                    $rubric->$method( $v );
                }
            }
            $rubrics[] = $rubric;
        }

        return $rubrics;
    }

    /**
     * @ignore
     * Set the Shared Rubrics for this class
     * @param String $sharedrubrics
     */
    public function setSharedRubrics($sharedrubrics)
    {
        $this->sharedrubrics = $sharedrubrics;
    }

    /**
     * Set the Class ID for this Class
     * 
     * @param integer $classid
     */
    public function setClassId( $classid ) {
        $this->classid = $classid;
    }

    /**
     * Get the Class ID for this Class
     * 
     * @return integer
     */
    public function getClassId() {
        return $this->classid;
    }

    /**
     * Set an array of Class IDs for this Class data object
     * 
     * @param array $classids
     */
    public function setClassIds( $classids ) {
        $this->classids = $classids;
    }

    /**
     * Get an array of Class IDs for this Class data object
     * 
     * @return array
     */
    public function getClassIds() {
        return $this->classids;
    }

    /**
     * Set the Title for this Class
     * 
     * A string between 5 and 100 characters to use as the Class title.
     * 
     * @param string $title
     */
    public function setTitle( $title ) {
        $this->title = $title;
    }

    /**
     * Get the Title for this Class
     * 
     * A string between 5 and 100 characters to use as the Class title.
     * 
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Set the End Date for this Class
     * 
     * Class End Date must be in ISO8601 format and include a timezone e.g. 2012-09-23T02:30:00Z
     * 
     * @param string $enddate
     */
    public function setEndDate( $enddate ) {
        $this->enddate = $enddate;
    }

    /**
     * Get the End Date for this Class
     * 
     * Class End Date must be in ISO8601 format and include a timezone e.g. 2012-09-23T02:30:00Z
     * 
     * @return string
     */
    public function getEndDate() {
        return $this->enddate;
    }

    /**
     * Set the Date From for a Find Class call
     * 
     * Optional on a Find Class call to determine the date from which to return active classes,
     * the Date From must be in ISO8601 format and include a timezone e.g. 2012-09-23T02:30:00Z
     * 
     * @param string $datefrom
     */
    public function setDateFrom( $datefrom ) {
        $this->datefrom = $datefrom;
    }

    /**
     * Get the Date From for a Find Class call
     * 
     * Optional on a Find Class call to determine the date from which to return active classes,
     * the Date From must be in ISO8601 format and include a timezone e.g. 2012-09-23T02:30:00Z
     * 
     * @return string
     */
    public function getDateFrom() {
        return $this->datefrom;
    }

    /**
     * Set the Integration Id for a Find Class call
     * 
     * Optional on a Find Class call to determine the integration source id to return active classes for
     * 
     * @param integer $integrationid
     */
    public function setIntegrationId( $integrationid ) {
        $this->integrationid = $integrationid;
    }

    /**
     * Get the Integration Id for a Find Class call
     * 
     * Optional on a Find Class call to determine the integration source id to return active classes for
     * 
     * @return integer
     */
    public function getIntegrationId() {
        return $this->integrationid;
    }

    /**
     * Set the User Id for a Find Class call
     * 
     * Optional on a Find Class call to determine the user to return active classes for
     * 
     * @param integer $userid
     */
    public function setUserId( $userid ) {
        $this->userid = $userid;
    }

    /**
     * Get the User Id for a Find Class call
     * 
     * Optional on a Find Class call to determine the user to return active classes for
     * 
     * @return integer
     */
    public function getUserId() {
        return $this->userid;
    }

    /**
     * Set the User Role for a Find Class call
     * 
     * Optional on a Find Class call to determine the user role to return active classes for combined with userid, options are Instructor and Learner
     * 
     * @param integer $userrole
     */
    public function setUserRole( $userrole ) {
        $this->userrole = $userrole;
    }

    /**
     * Get the User Role for a Find Class call
     * 
     * Optional on a Find Class call to determine the user role to return active classes for combined with userid, options are Instructor and Learner
     * 
     * @return integer
     */
    public function getUserRole() {
        return $this->userrole;
    }

}

