<?php

/*
 * @package TurnitinAPI
 * @subpackage TiiPeermarkAssignment
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Defines the TiiPeermarkAssignment data object which contains getters and setters for a Turnitin Peermark Assignment object.
 *
 * @package TurnitinSDK
 * @subpackage Data
 */
class TiiPeermarkAssignment {

    private $assignmentid;
    private $title;
    private $startdate;
    private $duedate;
    private $feedbackreleasedate;
    private $maxgrade;
    private $distributedreviews;
    private $selectedreviews;
    private $selfreviewrequired;
    private $showreviewernames;
    private $nonsubmitterstoreview;
    private $submittersreadallpapers;
    private $studentsreadallpapers;
    private $fullcreditifcompleted;
    private $instructions;
    private $todelete;

    /**
     * Get the Id for this Peermark Assignment
     *
     * @return integer
     */
    public function getAssignmentId() {
        return $this->assignmentid;
    }

    /**
     * Set the Id for this Peermark Assignment
     *
     * @param integer $assignmentid
     */
    public function setAssignmentId($assignmentid) {
        $this->assignmentid = $assignmentid;
    }

    /**
     * Get the Title for this Peermark Assignment
     *
     * @return text
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Set the Title for this Peermark Assignment
     *
     * @param text $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * Set the Start Date for this Peermark Assignment
     *
     * Start Date must be in ISO8601 format and include a timezone e.g. 2012-09-23T02:30:00Z
     *
     * @param string $startdate
     */
    public function setStartDate( $startdate ) {
        $this->startdate = $startdate;
    }

    /**
     * Get the Start Date for this Peermark Assignment
     *
     * Start Date must be in ISO8601 format and include a timezone e.g. 2012-09-23T02:30:00Z
     *
     * @return string
     */
    public function getStartDate() {
        return $this->startdate;
    }

    /**
     * Set the Due Date for this Peermark Assignment
     *
     * Due Date must be in ISO8601 format and include a timezone e.g. 2012-09-23T02:30:00Z
     *
     * @param string $duedate
     */
    public function setDueDate( $duedate ) {
        $this->duedate = $duedate;
    }

    /**
     * Get the Due Date for this Peermark Assignment
     *
     * Due Date must be in ISO8601 format and include a timezone e.g. 2012-09-23T02:30:00Z
     *
     * @return string
     */
    public function getDueDate() {
        return $this->duedate;
    }

    /**
     * Set the Feedback Release Date for this Peermark Assignment
     *
     * Feedback Release Date must be in ISO8601 format and include a timezone e.g. 2012-09-23T02:30:00Z
     * The date that any GradeMark feedback is released to the authors of reviews made to this assignment.
     *
     * @param string $feedbackreleasedate
     */
    public function setFeedbackReleaseDate( $feedbackreleasedate ) {
        $this->feedbackreleasedate = $feedbackreleasedate;
    }

    /**
     * Get the Feedback ReleaseDate for this Peermark Assignment
     *
     * Feedback Release Date must be in ISO8601 format and include a timezone e.g. 2012-09-23T02:30:00Z
     * The date that any GradeMark feedback is released to the authors of reviews made to this Peermark assignment.
     *
     * @return string
     */
    public function getFeedbackReleaseDate() {
        return $this->feedbackreleasedate;
    }

    /**
     * Set the Max Grade awarded for this Peermark Assignment
     *
     * This will determine the maximum grade allowed as an overall grade for this Peermark Assignment.
     * Value must be between 0 and 100
     *
     * @param integer $maxgrade
     */
    public function setMaxGrade( $maxgrade ) {
        $this->maxgrade = $maxgrade;
    }

    /**
     * Get the Max Grade awarded for this Assignment
     *
     * This will determine the maximum grade allowed as an overall grade for this Peermark Assignment.
     * Value must be between 0 and 100
     *
     * @return integer
     */
    public function getMaxGrade() {
        return $this->maxgrade;
    }

    /**
     * Get the number of distributed reviews required for this Peermark Assignment
     *
     * @return integer
     */
    public function getDistributedReviews() {
        return $this->distributedreviews;
    }

    /**
     * Set the number of distributed reviews required for this Peermark Assignment
     *
     * @param integer $distributedreviews
     */
    public function setDistributedReviews($distributedreviews) {
        $this->distributedreviews = $distributedreviews;
    }

    /**
     * Get the number of user selected reviews required for this Peermark Assignment
     *
     * @return integer
     */
    public function getSelectedReviews() {
        return $this->selectedreviews;
    }

    /**
     * Set the number of user selected reviews required for this Peermark Assignment
     *
     * @param integer $selectedreviews
     */
    public function setSelectedReviews($selectedreviews) {
        $this->selectedreviews = $selectedreviews;
    }

    /**
     * Get the Self Review Required setting for this Peermark Assignment
     *
     * This will determine whether a student has to review their own paper
     *
     * @return boolean
     */
    public function getSelfReviewRequired() {
        return $this->selfreviewrequired;
    }

    /**
     * Set the Self Review Required setting for this Peermark Assignment
     *
     * This will determine whether a student has to review their own paper
     *
     * @param boolean $selfreviewrequired
     */
    public function setSelfReviewRequired($selfreviewrequired) {
        $this->selfreviewrequired = $selfreviewrequired;
    }

    /**
     * Get the Show Reviewer Names setting for this Peermark Assignment
     *
     * This will determine whether reviewers names are revealed to students
     *
     * @return boolean
     */
    public function getShowReviewerNames() {
        return $this->showreviewernames;
    }

    /**
     * Set the Show Reviewer Names setting for this Peermark Assignment
     *
     * This will determine whether reviewers names are revealed to students
     *
     * @param boolean $showreviewernames
     */
    public function setShowReviewerNames($showreviewernames) {
        $this->showreviewernames = $showreviewernames;
    }

    /**
     * Get the Non Submitters To Review setting for this Peermark Assignment
     *
     * This will determine whether students who have not yet submitted to the assignment can review papers
     *
     * @return boolean
     */
    public function getNonSubmittersToReview() {
        return $this->nonsubmitterstoreview;
    }

    /**
     * Set the Non Submitters To Review setting for this Peermark Assignment
     *
     * This will determine whether students who have not yet submitted to the assignment can review papers
     *
     * @param boolean $nonsubmitterstoreview
     */
    public function setNonSubmittersToReview($nonsubmitterstoreview) {
        $this->nonsubmitterstoreview = $nonsubmitterstoreview;
    }

    /**
     * Get the Submitters Read All Papers setting for this Peermark Assignment
     *
     * This will determine whether submitters can read all papers after the start date
     *
     * @return boolean
     */
    public function getSubmittersReadAllPapers() {
        return $this->submittersreadallpapers;
    }

    /**
     * Set the Submitters Read All Papers setting for this Peermark Assignment
     *
     * This will determine whether submitters can read all papers after the start date
     *
     * @param boolean $submittersreadallpapers
     */
    public function setSubmittersReadAllPapers($submittersreadallpapers) {
        $this->submittersreadallpapers = $submittersreadallpapers;
    }

    /**
     * Get the Students Read All Papers setting for this Peermark Assignment
     *
     * This will determine whether students can read all papers and all reviews after the feedback release date
     *
     * @return boolean
     */
    public function getStudentsReadAllPapers() {
        return $this->studentsreadallpapers;
    }

    /**
     * Set the Students Read All Papers setting for this Peermark Assignment
     *
     * This will determine whether students can read all papers and all reviews after the feedback release date
     *
     * @param boolean $studentsreadallpapers
     */
    public function setStudentsReadAllPapers($studentsreadallpapers) {
        $this->studentsreadallpapers = $studentsreadallpapers;
    }

    /**
     * Get the Full Credit If Completed setting for this Peermark Assignment
     *
     * This will determine whether students get full points if a review is written
     *
     * @return boolean
     */
    public function getFullCreditIfCompleted() {
        return $this->fullcreditifcompleted;
    }

    /**
     * Set the Full Credit If Completed setting for this Peermark Assignment
     *
     * This will determine whether students get full points if a review is written
     *
     * @param boolean $fullcreditifcompleted
     */
    public function setFullCreditIfCompleted($fullcreditifcompleted) {
        $this->fullcreditifcompleted = $fullcreditifcompleted;
    }

    /**
     * Set the Instructions message for this Peermark Assignment
     *
     * Instructions for the Peermark Assignment must be a string with a maximum of 1000 characters
     *
     * @param string $instructions
     */
    public function setInstructions( $instructions ) {
        $this->instructions = $instructions;
    }

    /**
     * Get the Instructors message for this Peermark Assignment
     *
     * Instructions for the Peermark Assignment must be a string with a maximum of 1000 characters
     *
     * @return string
     */
    public function getInstructions() {
        return $this->instructions;
    }

    /**
     * Get the To Delete flag for this Peermark Assignment
     *
     * This will determine whether peermark assignment is to be deleted
     *
     * @return boolean
     */
    public function getToDelete() {
        return $this->todelete;
    }

    /**
     * Set the To Delete flag for this Peermark Assignment
     *
     * This will determine whether peermark assignment is to be deleted
     *
     * @param boolean $todelete
     */
    public function setToDelete($todelete) {
        $this->todelete = $todelete;
    }

}

//?>