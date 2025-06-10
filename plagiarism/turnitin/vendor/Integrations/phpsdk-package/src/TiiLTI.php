<?php

/*
 * @package TurnitinAPI
 * @subpackage TiiLTI
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Integrations\PhpSdk;

/**
 * Defines the TiiLTI data object which contains getters and setters for a Turnitin LTI Launch object.
 *
 * @package TurnitinSDK
 * @subpackage Data
 */
class TiiLTI extends TiiForm {

    protected $submissionid;
    protected $submissionids;
    protected $classid;
    protected $assignmentid;
    protected $userid;
    protected $role;
    protected $xmlresponse;
    protected $customcss;
    protected $asjson;
    protected $peermarkid;
    protected $skipsetup;
    protected $studentlist;
    protected $returnUrl;

    /**
     * @ignore
     */
    const SUBMITENDPOINT               = '/api/lti/1p0/upload/submit';
    /**
     * @ignore
     */
    const RESUBMITENDPOINT             = '/api/lti/1p0/upload/resubmit';
    /**
     * @ignore
     */
    const DVGRADEMARKENDPOINT          = '/api/lti/1p0/dv/grademark';
    /**
     * @ignore
     */
    const DVREPORTENDPOINT             = '/api/lti/1p0/dv/report';
    /**
     * @ignore
     */
    const DVPEERMARKENDPOINT           = '/api/lti/1p0/dv/peermark';
    /**
     * @ignore
     */
    const DVDEFAULTENDPOINT            = '/api/lti/1p0/dv/default';
    /**
     * @ignore
     */
    const MESSAGESENDPOINT             = '/api/lti/1p0/user/messages';
    /**
     * @ignore
     */
    const EULAENDPOINT                 = '/api/lti/1p0/user/eula';
    /**
     * @ignore
     */
    const DOWNLOADZIPENDPOINT          = '/api/lti/1p0/download/origzip';
    /**
     * @ignore
     */
    const DOWNLOADGRADEMARKZIPENDPOINT = '/api/lti/1p0/download/gradezip';
    /**
     * @ignore
     */
    const DOWNLOADREPORTZIPENDPOINT    = '/api/lti/1p0/download/reportzip';
    /**
     * @ignore
     */
    const DOWNLOADPDFZIPENDPOINT       = '/api/lti/1p0/download/pdfzip';
    /**
     * @ignore
     */
    const DOWNLOADORIGENDPOINT         = '/api/lti/1p0/download/orig';
    /**
     * @ignore
     */
    const DOWNLOADDEFAULTPDFENDPOINT   = '/api/lti/1p0/download/pdf';
    /**
     * @ignore
     */
    const DOWNLOADGRADEMARKPDFENDPOINT = '/api/lti/1p0/download/grademark';
    /**
     * @ignore
     */
    const DOWNLOADREPORTPDFENDPOINT    = '/api/lti/1p0/download/report';
    /**
     * @ignore
     */
    const DOWNLOADXLSENDPOINT          = '/api/lti/1p0/download/xls';
    /**
     * @ignore
     */
    const DOWNLOADVOICEENDPOINT        = '/api/lti/1p0/download/voice';
    /**
     * @ignore
     */
    const PEERMARKSETUPENDPOINT        = '/api/lti/1p0/peermark/setup';
    /**
     * @ignore
     */
    const PEERMARKREVIEWENDPOINT       = '/api/lti/1p0/peermark/review';
    /**
     * @ignore
     */
    const RUBRICENDPOINT               = '/api/lti/1p0/user/rubric';
    /**
     * @ignore
     */
    const QUICKMARKENDPOINT            = '/api/lti/1p0/user/quickmark';
    /**
     * @ignore
     */
    const CREATEASSIGNMENTENDPOINT     = '/api/lti/1p0/assignment/create';
    /**
     * @ignore
     */
    const EDITASSIGNMENTENDPOINT       = '/api/lti/1p0/assignment/edit';
    /**
     * @ignore
     */
    const ASSIGNMENTINBOXENDPOINT      = '/api/lti/1p0/assignment/inbox';
    
    /**
     * @ignore
     * Get the Submission ID for this LTI Launch
     *
     * @return integer
     */
    public function getSubmissionId() {
        return $this->submissionid;
    }

    /**
     * Set the Submission ID for this LTI Launch
     *
     * @param integer $submissionid
     */
    public function setSubmissionId($submissionid) {
        $this->submissionid = $submissionid;
    }

    /**
     * @ignore
     * Get an array of Submissions IDs for this LTI Launch data object
     *
     * @return array
     */
    public function getSubmissionIds() {
        return $this->submissionids;
    }

    /**
     * Set an array of Submissions IDs for this LTI Launch data object
     *
     * @param array $submissionids
     */
    public function setSubmissionIds($submissionids) {
        $this->submissionids = $submissionids;
    }

    /**
     * @ignore
     * Get the Assignment ID for this LTI Launch
     *
     * @return integer
     */
    public function getAssignmentId() {
        return $this->assignmentid;
    }

    /**
     * Set the Assignment ID for this LTI Launch
     *
     * @param integer $assignmentid
     */
    public function setAssignmentId($assignmentid) {
        $this->assignmentid = $assignmentid;
    }

    /**
     * @ignore
     * Get the Class ID for this LTI Launch
     *
     * @return integer
     */
    public function getClassId() {
        return $this->classid;
    }

    /**
     * Set the Class ID for this LTI Launch
     *
     * @param integer $classid
     */
    public function setClassId($classid) {
        $this->classid = $classid;
    }

    /**
     * @ignore
     * Get the User ID for this LTI Launch
     *
     * @return integer
     */
    public function getUserId() {
        return $this->userid;
    }

    /**
     * Set the User ID for this LTI Launch
     *
     * @param integer $userid
     */
    public function setUserId($userid) {
        $this->userid = $userid;
    }

    /**
     * @ignore
     * Get the User Role for this LTI Launch
     *
     * A string that determines the role in which to launch the LTI request, Learner or Instructor
     *
     * @return string
     */
    public function getRole() {
        return $this->role;
    }

    /**
     * Set the User Role for this LTI Launch
     *
     * A string that determines the role in which to launch the LTI request, Learner or Instructor
     *
     * @param string $role
     */
    public function setRole($role) {
        $this->role = $role;
    }

    /**
     * @ignore
     * Get the XML Response option for this LTI Launch
     *
     * @return boolean
     */
    public function getXmlResponse() {
        return (boolean)(integer)$this->xmlresponse;
    }

    /**
     * Set the XML Response option for this LTI Launch
     *
     * A boolean to determine if error messages should be returned as XML rather than HTML
     *
     * @param boolean $xmlresponse
     */
    public function setXmlResponse(  $xmlresponse ) {
        $this->xmlresponse = $xmlresponse;
    }

    /**
     * @ignore
     * Get the CSS URL for the LTI launch screen presentation
     *
     * The URL of the CSS to be used to style the Turnitin screen presented by the LTI launch
     *
     * @return string
     */
    public function getCustomCSS() {
        return $this->customcss;
    }

    /**
     * Set the CSS URL for the LTI launch screen presentation
     *
     * The URL of the CSS to be used to style the Turnitin screen presented by the LTI launch
     *
     * @param string $customcss
     */
    public function setCustomCSS($customcss) {
        $this->customcss = $customcss;
    }

    /**
     * Gets a boolean flag to indicate that the outputForm call from TurnitinAPI should return json rather than a form string
     *
     * @return boolean
     */
    public function getAsJson() {
        return (boolean)(integer)$this->asjson;
    }

    /**
     * Sets the boolean flag which indicate that the outputForm call from TurnitinAPI should return json rather than a form string
     *
     * @param boolean $asjson
     */
    public function setAsJson( $asjson ) {
        $this->asjson = $asjson;
    }

    /**
     * Gets a the PeerMark Assignment ID, if set on a PeerMark LTI Launch the launch UI will be ring-fenced to this PeerMark assignment
     *
     * @return integer
     */
    public function getPeermarkId() {
        return $this->peermarkid;
    }

    /**
     * Sets a the PeerMark Assignment ID, if set on a PeerMark LTI Launch the launch UI will be ring-fenced to this PeerMark assignment
     *
     * @param integer $peermarkid
     */
    public function setPeermarkId( $peermarkid ) {
        $this->peermarkid = $peermarkid;
    }

    /**
     * Gets a the Boolean SkipSetup, this boolean is only usable on PeerMark setup launched and if set to true the initial set up screens in the UI are skipped
     *
     * @return boolean
     */
    public function getSkipSetup() {
        return (boolean)(integer)$this->skipsetup;
    }

    /**
     * Sets a the Boolean SkipSetup, this boolean is only usable on PeerMark setup launched and if set to true the initial set up screens in the UI are skipped
     *
     * @param boolean $skipsetup
     */
    public function setSkipSetup( $skipsetup ) {
        $this->skipsetup = $skipsetup;
    }


    /**
     * Gets a list of students to limit the view of the inbox to just that subset
     *
     * @return string
     */
    public function getStudentList() {
        return $this->studentlist;
    }

    /**
     * Sets a list of students to limit the view of the inbox to just that subset
     *
     * @param string $studentlist
     */
    public function setStudentList($studentlist) {
        $this->studentlist = $studentlist;
    }

    /**
     * Gets the String returnUrl. This is going to be used in EULA launches, as a callback to the consumer, to notify of user EULA decision.
     * @return mixed
     */
    public function getReturnUrl()
    {
        return $this->returnUrl;
    }

    /**
     * Sets the returnUrl String parameter passed in by the request, to the returnUrl field.
     * @param mixed $returnUrl
     */
    public function setReturnUrl($returnUrl)
    {
        $this->returnUrl = $returnUrl;
    }


}

