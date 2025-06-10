<?php
/* 
 * @package TurnitinAPI
 * @subpackage TiiAssignment 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Integrations\PhpSdk;

/**
 * Defines the TiiAssignment data object which contains getters and setters for a Turnitin Assignment API object.
 * 
 * @package TurnitinSDK
 * @subpackage Data
 */
class TiiAssignment {
    private $classid;
    private $title;
    private $assignmentid;
    private $assignmentids;
    private $startdate;
    private $duedate;
    private $feedbackreleasedate;
    private $instructions;
    private $authororiginalityaccess;
    private $rubricid;
    private $submitteddocumentscheck;
    private $internetcheck;
    private $publicationscheck;
    private $institutioncheck;
    private $maxgrade;
    private $latesubmissionsallowed;
    private $submitpapersto;
    private $resubmissionrule;
    private $bibliographyexcluded;
    private $quotedexcluded;
    private $smallmatchexclusiontype;
    private $smallmatchexclusionthreshold;
    private $anonymousmarking;
    private $erater;
    private $eraterspelling;
    private $eratergrammar;
    private $eraterusage;
    private $eratermechanics;
    private $eraterstyle;
    private $eraterspellingdictionary;
    private $eraterhandbook;
    private $translatedmatching;
    private $instructordefaults;
    private $instructordefaultssave;
    private $peermarkassignments;
    private $allownonorsubmissions;
    private $eraterclientid;
    private $eraterpromptid;
    private $eraterusername;
    private $eraterpassword;

    /**
     * Set the Class ID
     * 
     * @param integer $classid
     */
    public function setClassId( $classid ) {
        $this->classid = $classid;
    }

    /**
     * Get the Class ID
     * 
     * @return integer
     */
    public function getClassId() {
        return $this->classid;
    }

    /**
     * Set the Title for this Assignment
     * 
     * Assignment titles may be between 3 and 99 characters
     * 
     * @param string $title
     */
    public function setTitle( $title ) {
        $this->title = $title;
    }

    /**
     * Get the Title for this Assignment
     * 
     * Assignment titles may be between 3 and 99 characters
     * 
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Set the Assignment ID
     * 
     * @param integer $assignmentid
     */
    public function setAssignmentId( $assignmentid ) {
        $this->assignmentid = $assignmentid;
    }

    /**
     * Get the Assignment ID
     * 
     * @return integer
     */
    public function getAssignmentId() {
        return $this->assignmentid;
    }

    /**
     * Set an array of Assignment IDs
     * 
     * @param array $assignmentids
     */
    public function setAssignmentIds( $assignmentids ) {
        $this->assignmentids = $assignmentids;
    }

    /**
     * Get an array of Assignment IDs
     * 
     * @return array
     */
    public function getAssignmentIds() {
        return $this->assignmentids;
    }

    /**
     * Set the Start Date for this Assignment
     * 
     * Start Date must be in ISO8601 format and include a timezone e.g. 2012-09-23T02:30:00Z
     * 
     * @param string $startdate
     */
    public function setStartDate( $startdate ) {
        $this->startdate = $startdate;
    }

    /**
     * Get the Start Date for this Assignment
     * 
     * Start Date must be in ISO8601 format and include a timezone e.g. 2012-09-23T02:30:00Z
     * 
     * @return string
     */
    public function getStartDate() {
        return $this->startdate;
    }

    /**
     * Set the Due Date for this Assignment
     * 
     * Due Date must be in ISO8601 format and include a timezone e.g. 2012-09-23T02:30:00Z
     * 
     * @param string $duedate
     */
    public function setDueDate( $duedate ) {
        $this->duedate = $duedate;
    }

    /**
     * Get the Due Date for this Assignment
     * 
     * Due Date must be in ISO8601 format and include a timezone e.g. 2012-09-23T02:30:00Z
     * 
     * @return string
     */
    public function getDueDate() {
        return $this->duedate;
    }
    
    /**
     * Set the Feedback Release Date for this Assignment
     * 
     * Feedback Release Date must be in ISO8601 format and include a timezone e.g. 2012-09-23T02:30:00Z
     * The date that any GradeMark feedback is released to the authors of submissions made to this assignment.
     * 
     * @param string $feedbackreleasedate
     */
    public function setFeedbackReleaseDate( $feedbackreleasedate ) {
        $this->feedbackreleasedate = $feedbackreleasedate;
    }

    /**
     * Get the Feedback ReleaseDate for this Assignment
     * 
     * Feedback Release Date must be in ISO8601 format and include a timezone e.g. 2012-09-23T02:30:00Z
     * The date that any GradeMark feedback is released to the authors of submissions made to this assignment.
     * 
     * @return string
     */
    public function getFeedbackReleaseDate() {
        return $this->feedbackreleasedate;
    }

    /**
     * Set the Instructions message for this Assignment
     * 
     * Instructions for the Assignment must be a string with a maximum of 1000 characters
     * 
     * @param string $instructions
     */
    public function setInstructions( $instructions ) {
        $this->instructions = $instructions;
    }

    /**
     * Get the Instructors message for this Assignment
     * 
     * Instructions for the Assignment must be a string with a maximum of 1000 characters
     * 
     * @return string
     */
    public function getInstructions() {
        return $this->instructions;
    }

    /**
     * Set the Author Originality Access setting for this Assignment
     *
     * This will determine if submission authors should be able to view originality reports
     *
     * @param $authororiginalityaccess
     * @return null
     */
    public function setAuthorOriginalityAccess($authororiginalityaccess ) {
        if ( !is_null($authororiginalityaccess) ) {
            $authororiginalityaccess = (boolean)(integer)$authororiginalityaccess;
        }
        $this->authororiginalityaccess = $authororiginalityaccess;
    }

    /**
     * Get the Author Originality Access setting for this Assignment
     * 
     * This will determine if submission authors should be able to view originality reports
     * 
     * @return boolean
     */
    public function getAuthorOriginalityAccess() {
        if ( isset( $this->authororiginalityaccess ) ) {
            return (boolean)(integer)$this->authororiginalityaccess;
        } else {
            return null;
        }
    }

    /**
     * Set the Rubric ID attached to this assignment
     * 
     * @param integer $rubricid
     */
    public function setRubricId( $rubricid ) {
        $this->rubricid = $rubricid;
    }

    /**
     * Get the Rubric ID
     * 
     * @return integer
     */
    public function getRubricId() {
        return $this->rubricid;
    }

    /**
     * Set the Submitted Documents Check setting for this Assignment
     * 
     * This will determine if submissions are checked for similarity with the Submitted Documents sources database
     * 
     * @param boolean $submitteddocumentscheck
     */
    public function setSubmittedDocumentsCheck( $submitteddocumentscheck ) {
        if ( !is_null($submitteddocumentscheck) ) {
            $submitteddocumentscheck = (boolean)(integer)$submitteddocumentscheck;
        }
        $this->submitteddocumentscheck = $submitteddocumentscheck;
    }

    /**
     * Get the Submitted Documents Check setting for this Assignment
     * 
     * This will determine if submissions are checked for similarity with the Submitted Documents sources database
     * 
     * @return boolean
     */
    public function getSubmittedDocumentsCheck() {
        if ( isset( $this->submitteddocumentscheck ) ) {
            return (boolean)(integer)$this->submitteddocumentscheck;
        } else {
            return null;
        }
    }

    /**
     * Set the Internet Check setting for this Assignment
     * 
     * This will determine if submissions are checked for similarity with the Internet sources database
     * 
     * @param boolean $internetcheck
     */
    public function setInternetCheck(  $internetcheck ) {
        if ( !is_null($internetcheck) ) {
            $internetcheck = (boolean)(integer)$internetcheck;
        }
        $this->internetcheck = $internetcheck;
    }

    /**
     * Get the Internet Check setting for this Assignment
     * 
     * This will determine if submissions are checked for similarity with the Internet sources database
     * 
     * @return boolean
     */
    public function getInternetCheck() {
        if ( isset( $this->internetcheck ) ) {
            return (boolean)(integer)$this->internetcheck;
        } else {
            return null;
        }
    }

    /**
     * Set the Publications Check setting for this Assignment
     * 
     * This will determine if submissions are checked for similarity with the Publication sources database
     * 
     * @param boolean $publicationscheck
     */
    public function setPublicationsCheck(  $publicationscheck ) {
        if ( !is_null($publicationscheck) ) {
            $publicationscheck = (boolean)(integer)$publicationscheck;
        }
        $this->publicationscheck = $publicationscheck;
    }

    /**
     * Get the Publications Check setting for this Assignment
     * 
     * This will determine if submissions are checked for similarity with the Publication sources database
     * 
     * @return boolean
     */
    public function getPublicationsCheck() {
        if ( isset( $this->publicationscheck ) ) {
            return (boolean)(integer)$this->publicationscheck;
        } else {
            return null;
        }
    }

    /**
     * Set the Institution Check setting for this Assignment
     * 
     * This will determine if submissions are checked for similarity with the Institution paper repository.
     * Only applicable if the account has an institutional repository.
     * 
     * @param boolean $institutioncheck
     */
    public function setInstitutionCheck(  $institutioncheck ) {
        if ( !is_null($institutioncheck) ) {
            $institutioncheck = (boolean)(integer)$institutioncheck;
        }
        $this->institutioncheck = $institutioncheck;
    }

    /**
     * Get the Institution Check setting for this Assignment
     * 
     * This will determine if submissions are checked for similarity with the Institution paper repository.
     * Only applicable if the account has an institutional repository.
     * 
     * @return boolean
     */
    public function getInstitutionCheck() {
        if ( isset( $this->institutioncheck ) ) {
            return (boolean)(integer)$this->institutioncheck;
        } else {
            return null;
        }
    }
    
    /**
     * Set the Max Grade awarded for this Assignment
     * 
     * This will determine the maximum grade allowed as an overall grade for this Assignment.
     * Value must be between 0 and 999
     * 
     * @param integer $maxgrade
     */
    public function setMaxGrade( $maxgrade ) {
        $this->maxgrade = $maxgrade;
    }

    /**
     * Get the Max Grade awarded for this Assignment
     * 
     * This will determine the maximum grade allowed as an overall grade for this Assignment.
     * Value must be between 0 and 999
     * 
     * @return integer
     */
    public function getMaxGrade() {
        return $this->maxgrade;
    }
    
    /**
     * Set the Late Submissions Allowed setting for this Assignment
     * 
     * This will determine whether a submission can be made after the Due Date for this Assignment
     * 
     * @param boolean $latesubmissionsallowed
     */
    public function setLateSubmissionsAllowed(  $latesubmissionsallowed ) {
        if ( !is_null($latesubmissionsallowed) ) {
            $latesubmissionsallowed = (boolean)(integer)$latesubmissionsallowed;
        }
        $this->latesubmissionsallowed = $latesubmissionsallowed;
    }

    /**
     * Get the Late Submissions Allowed setting for this Assignment
     * 
     * This will determine whether a submission can be made after the Due Date for this Assignment
     * 
     * @return boolean
     */
    public function getLateSubmissionsAllowed() {
        if ( isset( $this->latesubmissionsallowed ) ) {
            return (boolean)(integer)$this->latesubmissionsallowed;
        } else {
            return null;
        }
    }
    
    /**
     * Set the setting to determine where the submission is submitted to
     * 
     * Options are:<br />
     * <ul>
     * <li>No Repository (0)</li>
     * <li>Standard Repository (1) (Default)</li>
     * <li>Institution Repository (2)</li>
     * </ul>  
     * 
     * @param integer $submitpapersto
     */
    public function setSubmitPapersTo(  $submitpapersto ) {
        $this->submitpapersto = $submitpapersto;
    }

    /**
     * Get the setting to determine where the submission is submitted to
     * 
     * Options are:<br />
     * <ul>
     * <li>No Repository (0)</li>
     * <li>Standard Repository (1) (Default)</li>
     * <li>Institution Repository (2)</li>
     * </ul>
     * 
     * @return integer
     */
    public function getSubmitPapersTo() {
        return $this->submitpapersto;
    }
    
    /**
     * Set the setting to determine the Resubmission and Report Generation rules
     * 
     * Options are:<br />
     * <ul>
     * <li>Generate Report Immediately, firt report is final (0) (Default)</li>
     * <li>Generate Report Immediately, can resubmit until due date (1)</li>
     * <li>Generate Report on Due Date, (2)</li>
     * </ul>
     * 
     * @param integer $resubmissionrule
     */
    public function setResubmissionRule( $resubmissionrule ) {
        $this->resubmissionrule = $resubmissionrule;
    }

    /**
     * Get the setting to determine the Resubmission and Report Generation rules
     * 
     * Options are:<br />
     * <ul>
     * <li>Generate Report Immediately, firt report is final (0) (Default)</li>
     * <li>Generate Report Immediately, can resubmit until due date (1)</li>
     * <li>Generate Report on Due Date, (2)</li>
     * </ul>
     * 
     * @return integer
     */
    public function getResubmissionRule() {
        return $this->resubmissionrule;
    }
    
    /**
     * Set the Bibliography Excluded setting
     * 
     * This setting determines whether bibliographies should be excluded when calculating similarities
     * 
     * @param boolean $bibliographyexcluded
     */
    public function setBibliographyExcluded(  $bibliographyexcluded ) {
        if ( !is_null($bibliographyexcluded) ) {
            $bibliographyexcluded = (boolean)(integer)$bibliographyexcluded;
        }
        $this->bibliographyexcluded = $bibliographyexcluded;
    }

    /**
     * Get the Bibliography Excluded setting
     * 
     * This setting determines whether bibliographies should be excluded when calculating similarities
     * 
     * @return boolean
     */
    public function getBibliographyExcluded() {
        if ( isset( $this->bibliographyexcluded ) ) {
            return (boolean)(integer)$this->bibliographyexcluded;
        } else {
            return null;
        }
    }
    
    /**
     * Set the Quoted Excluded setting for this Assignment
     * 
     * This setting determines whether quoted text should be excluded when calculating similarities
     * 
     * @param boolean $quotedexcluded
     */
    public function setQuotedExcluded(  $quotedexcluded ) {
        if ( !is_null($quotedexcluded) ) {
            $quotedexcluded = (boolean)(integer)$quotedexcluded;
        }
        $this->quotedexcluded = $quotedexcluded;
    }

    /**
     * Get the Quoted Excluded setting for this Assignment
     * 
     * This setting determines whether quoted text should be excluded when calculating similarities
     * 
     * @return boolean
     */
    public function getQuotedExcluded() {
        if ( isset( $this->quotedexcluded ) ) {
            return (boolean)(integer)$this->quotedexcluded;
        } else {
            return null;
        }
    }

    /**
     * Set the Exclude Small Matches type setting on this Assignment
     * 
     * This setting determines whether small matches should be excluded when calculating similarities.
     * It also defines what type of small match exclusion to use, options are:
     * <ul>
     * <li>Do Not Exclude Small Matches (0)</li>
     * <li>Exclude Based on Word Count (1)</li>
     * <li>Exclude Based on Percentage (2)</li>
     * </ul>
     * 
     * @param integer $smallmatchexclusiontype
     */
    public function setSmallMatchExclusionType( $smallmatchexclusiontype ) {
        $this->smallmatchexclusiontype = $smallmatchexclusiontype;
    }

    /**
     * Get the Exclude Small Matches type setting on this Assignment
     * 
     * This setting determines whether small matches should be excluded when calculating similarities.
     * It also defines what type of small match exclusion to use, options are:
     * <ul>
     * <li>Do Not Exclude Small Matches (0)</li>
     * <li>Exclude Based on Word Count (1)</li>
     * <li>Exclude Based on Percentage (2)</li>
     * </ul>
     * 
     * @return integer
     */
    public function getSmallMatchExclusionType() {
        return $this->smallmatchexclusiontype;
    }
    
    /**
     * Set the Exclude Small Matches Threshold on this Assignment
     * 
     * This setting determines the threshold at which to exclude small matches.
     * Takes an integer which is used to determine the threshold depending on the Small Match Type
     * set by {@link TiiAssignment.html#getSmallMatchExclusionType TiiAssignment->getSmallMatchExclusionType()}
     * 
     * @param integer $smallmatchexclusionthreshold
     */
    public function setSmallMatchExclusionThreshold( $smallmatchexclusionthreshold ) {
        $this->smallmatchexclusionthreshold = $smallmatchexclusionthreshold;
    }

    /**
     * Get the Exclude Small Matches Threshold on this Assignment
     * 
     * This setting determines the threshold at which to exclude small matches.
     * Takes an integer which is used to determine the threshold depending on the Small Match Type
     * set by {@link TiiAssignment.html#getSmallMatchExclusionType TiiAssignment->getSmallMatchExclusionType()}
     * 
     * @return integer
     */
    public function getSmallMatchExclusionThreshold() {
        return $this->smallmatchexclusionthreshold;
    }
    
    /**
     * Set the Anonymous Marking setting for this Assignment
     * 
     * This setting determines whether student names associated with the submissions made to
     * this Assignment should remain anonymous until the Post Date for this Assignment.
     * 
     * @param boolean $anonymousmarking
     */
    public function setAnonymousMarking(  $anonymousmarking ) {
        if ( !is_null($anonymousmarking) ) {
            $anonymousmarking = (boolean)(integer)$anonymousmarking;
        }
        $this->anonymousmarking = $anonymousmarking;
    }

    /**
     * Get the Anonymous Marking setting for this Assignment
     * 
     * This setting determines whether student names associated with the submissions made to
     * this Assignment should remain anonymous until the Post Date for this Assignment.
     * 
     * @return boolean
     */
    public function getAnonymousMarking() {
        if ( isset( $this->anonymousmarking ) ) {
            return (boolean)(integer)$this->anonymousmarking;
        } else {
            return null;
        }
    }
    
    /**
     * Set the Erater setting for this Assignment
     * 
     * This setting determines whether the submissions made to this Assignment should be assessed by the ETS e-rater service.
     * This setting is only applicable for accounts that have the erater service enabled.
     * 
     * @param boolean $erater
     */
    public function setErater(  $erater ) {
        if ( !is_null($erater) ) {
            $erater = (boolean)(integer)$erater;
        }
        $this->erater = $erater;
    }

    /**
     * Get the Erater setting for this Assignment
     * 
     * This setting determines whether the submissions made to this Assignment should be assessed by the ETS e-rater service.
     * This setting is only applicable for accounts that have the erater service enabled.
     * 
     * @return boolean
     */
    public function getErater() {
        if ( isset( $this->erater ) ) {
            return (boolean)(integer)$this->erater;
        } else {
            return null;
        }
    }
    
    /**
     * Set the Erater Spelling setting for this Assignment
     * 
     * This setting determines whether the ETS e-rater service should check spelling.
     * 
     * @param boolean $eraterspelling
     */
    public function setEraterSpelling(  $eraterspelling ) {
        if ( !is_null($eraterspelling) ) {
            $eraterspelling = (boolean)(integer)$eraterspelling;
        }
        $this->eraterspelling = $eraterspelling;
    }

    /**
     * Get the Erater Spelling setting for this Assignment
     * 
     * This setting determines whether the ETS e-rater service should check spelling.
     * 
     * @return boolean
     */
    public function getEraterSpelling() {
        if ( isset( $this->eraterspelling ) ) {
            return (boolean)(integer)$this->eraterspelling;
        } else {
            return null;
        }
    }
    
    /**
     * Set the Erater Grammar setting for this Assignment
     * 
     * This setting determines whether the ETS e-rater service should check grammar.
     * 
     * @param boolean $eratergrammar
     */
    public function setEraterGrammar(  $eratergrammar ) {
        if ( !is_null($eratergrammar) ) {
            $eratergrammar = (boolean)(integer)$eratergrammar;
        }
        $this->eratergrammar = $eratergrammar;
    }

    /**
     * Get the Erater Grammar setting for this Assignment
     * 
     * This setting determines whether the ETS e-rater service should check grammar.
     * 
     * @return boolean
     */
    public function getEraterGrammar() {
        if ( isset( $this->eratergrammar ) ) {
            return (boolean)(integer)$this->eratergrammar;
        } else {
            return null;
        }
    }
    
    /**
     * Set the Erater Usage setting for this Assignment
     * 
     * This setting determines whether the ETS e-rater service should check word and phrase usage.
     * 
     * @param boolean $eraterusage
     */
    public function setEraterUsage(  $eraterusage ) {
        if ( !is_null($eraterusage) ) {
            $eraterusage = (boolean)(integer)$eraterusage;
        }
        $this->eraterusage = $eraterusage;
    }

    /**
     * Get the Erater Usage setting for this Assignment
     * 
     * This setting determines whether the ETS e-rater service should check word and phrase usage.
     * 
     * @return boolean
     */
    public function getEraterUsage() {
        if ( isset( $this->eraterusage ) ) {
            return (boolean)(integer)$this->eraterusage;
        } else {
            return null;
        }
    }
    
    /**
     * Set the Erater Mechanics setting for this Assignment
     * 
     * This setting determines whether the ETS e-rater service should check word and phrase mechanics.
     * 
     * @param boolean $eratermechanics
     */
    public function setEraterMechanics(  $eratermechanics ) {
        if ( !is_null($eratermechanics) ) {
            $eratermechanics = (boolean)(integer)$eratermechanics;
        }
        $this->eratermechanics = $eratermechanics;
    }

    /**
     * Get the Erater Mechanics setting for this Assignment
     * 
     * This setting determines whether the ETS e-rater service should check word and phrase mechanics.
     * 
     * @return boolean
     */
    public function getEraterMechanics() {
        if ( isset( $this->eratermechanics ) ) {
            return (boolean)(integer)$this->eratermechanics;
        } else {
            return null;
        }
    }
    
    /**
     * Set the Erater Style setting for this Assignment
     * 
     * This setting determines whether the ETS e-rater service should check writing style.
     * 
     * @param boolean $eraterstyle
     */
    public function setEraterStyle( $eraterstyle ) {
        if ( !is_null($eraterstyle) ) {
            $eraterstyle = (boolean)(integer)$eraterstyle;
        }
        $this->eraterstyle = $eraterstyle;
    }

    /**
     * Get the Erater Style setting for this Assignment
     * 
     * This setting determines whether the ETS e-rater service should check writing style.
     * 
     * @return boolean
     */
    public function getEraterStyle() {
        if ( isset( $this->eraterstyle ) ) {
            return (boolean)(integer)$this->eraterstyle;
        } else {
            return null;
        }
    }
    
    /**
     * Set the Erater Spelling Dictionary setting for this Assignment
     * 
     * This setting determines which dictionary to use with the ETS e-rater service.
     * 
     * Options are:
     * <ul>
     * <li>UK and US English Dictionaries (en)</li>
     * <li>UK English Dictionaries (en_GB)</li>
     * <li>US English Dictionaries (en_US)</li>
     * </ul>
     * 
     * @param string $eraterspellingdictionary
     */
    public function setEraterSpellingDictionary( $eraterspellingdictionary ) {
        $this->eraterspellingdictionary = $eraterspellingdictionary;
    }

    /**
     * Get the Erater Spelling Dictionary setting for this Assignment
     * 
     * This setting determines which dictionary to use with the ETS e-rater service.
     * 
     * Options are:
     * <ul>
     * <li>UK and US English Dictionaries (en)</li>
     * <li>UK English Dictionaries (en_GB)</li>
     * <li>US English Dictionaries (en_US)</li>
     * </ul>
     * 
     * @return string
     */
    public function getEraterSpellingDictionary() {
        return $this->eraterspellingdictionary;
    }
    
    /**
     * Set the Erater Handbook setting for this Assignment
     * 
     * This setting determines which Student Handbook to use with the ETS e-rater service.
     * 
     * Options are:
     * <ul>
     * <li>Advanced (1)</li>
     * <li>High School (2)</li>
     * <li>Middle School (3)</li>
     * <li>Elementary (4)</li>
     * <li>English Learners (5)</li>
     * </ul>
     * 
     * @return string $eraterhandbook
     */
    public function setEraterHandbook( $eraterhandbook ) {
        $this->eraterhandbook = $eraterhandbook;
    }

    /**
     * Get the Erater Handbook setting for this Assignment
     * 
     * This setting determines which Student Handbook to use with the ETS e-rater service.
     * 
     * Options are:
     * <ul>
     * <li>Advanced (1)</li>
     * <li>High School (2)</li>
     * <li>Middle School (3)</li>
     * <li>Elementary (4)</li>
     * <li>English Learners (5)</li>
     * </ul>
     * 
     * @return string
     */
    public function getEraterHandbook() {
        return $this->eraterhandbook;
    }

    /**
     * Set the Translated Matching setting for this Assignment
     * 
     * This setting determines whether or not to use Translated Matching on submissions to this Assignment when calculating the similarity.
     * 
     * @param boolean $translatedmatching
     */
    public function setTranslatedMatching( $translatedmatching ) {
        if ( !is_null($translatedmatching) ) {
            $translatedmatching = (boolean)(integer)$translatedmatching;
        }
        $this->translatedmatching = $translatedmatching;
    }

    /**
     * Get the Translated Matching setting for this Assignment
     * 
     * This setting determines whether or not to use Translated Matching on submissions to this Assignment when calculating the similarity.
     * 
     * @return boolean
     */
    public function getTranslatedMatching() {
        if ( isset( $this->translatedmatching ) ) {
            return (boolean)(integer)$this->translatedmatching;
        } else {
            return null;
        }
    }
    
    /**
     * Get the Instructor Defaults setting for this Assignment
     * 
     * This setting determines whether or not to use the Instructor Saved Defaults as default settings for this Assignment
     * Takes the Turnitin User ID of the Instructor that the default settings are saved against
     * 
     * @return integer
     */
    public function getInstructorDefaults() {
        return $this->instructordefaults;
    }

    /**
     * Set the Instructor Defaults setting for this Assignment
     * 
     * This setting determines whether or not to use the Instructor Saved Defaults as default settings for this Assignment
     * Takes the Turnitin User ID of the Instructor that the default settings are saved against
     * 
     * @param integer $instructordefaults
     */
    public function setInstructorDefaults( $instructordefaults ) {
        $this->instructordefaults = $instructordefaults;
    }

    /**
     * Get the Instructor Defaults Save setting for this Assignment
     * 
     * This setting determines whether or not to use the Assignment settings in this Assignment object as Instructor Defaults
     * Takes the Turnitin User ID of the Instructor that the default settings are to be saved against
     * 
     * @return integer
     */
    public function getInstructorDefaultsSave() {
        return $this->instructordefaultssave;
    }

    /**
     * Set the Instructor Defaults Save setting for this Assignment
     * 
     * This setting determines whether or not to use the Assignment settings in this Assignment object as Instructor Defaults
     * Takes the Turnitin User ID of the Instructor that the default settings are to be saved against
     * 
     * @param integer $instructordefaultssave
     */
    public function setInstructorDefaultsSave($instructordefaultssave) {
        $this->instructordefaultssave = $instructordefaultssave;
    }

    /**
     * Get the Peermark Assignments for this Assignment
     * 
     * Returns an array of TiiPeermarkAssignment Objects
     * 
     * @return array
     */
    public function getPeermarkAssignments() {
        return $this->peermarkassignments;
    }

    /**
     * Set the Peermark Assignments for this Assignment
     * 
     * Takes an array of TiiPeermarkAssignment Objects
     * 
     * @param array $peermarkassignments
     */
    public function setPeermarkAssignments( $peermarkassignments ) {
        $this->peermarkassignments = $peermarkassignments;
    }

    /**
     * Get the AllowNonOrSubmissions boolean
     *
     * Returns the boolean that determines if this assignment should accept files that will not generate an Originality Report
     *
     * @return boolean
     */
    public function getAllowNonOrSubmissions() {
        if ( isset( $this->allownonorsubmissions ) ) {
            return (boolean)(integer)$this->allownonorsubmissions;
        } else {
            return null;
        }
    }

    /**
     * Get the AllowNonOrSubmissions boolean
     *
     * Sets the boolean that determines if this assignment should accept files that will not generate an Originality Report
     *
     * @param boolean $allownonorsubmissions
     */
    public function setAllowNonOrSubmissions( $allownonorsubmissions ) {
        if ( !is_null($allownonorsubmissions) ) {
            $allownonorsubmissions = (boolean)(integer)$allownonorsubmissions;
        }
        $this->allownonorsubmissions = $allownonorsubmissions;
    }

    /**
     * Get the Erater Prompt ID for this Assignment
     *
     * Returns the e-rater prompt id string
     *
     * @return string
     */
    public function getEraterPromptId() {
        return $this->eraterpromptid;
    }

    /**
     * Set the Erater Prompt ID for this Assignment
     *
     * Sets the e-rater prompt id string
     *
     * @param string $eraterpromptid
     */
    public function setEraterPromptId( $eraterpromptid ) {
        $this->eraterpromptid = $eraterpromptid;
    }

    /**
     * Get the Erater Client ID for this Assignment
     *
     * Returns the e-rater client id string
     *
     * @return string
     */
    public function getEraterClientId() {
        return $this->eraterclientid;
    }

    /**
     * Set the Erater Client ID for this Assignment
     *
     * Sets the e-rater client id string
     *
     * @param string $eraterclientid
     */
    public function setEraterClientId( $eraterclientid ) {
        $this->eraterclientid = $eraterclientid;
    }

    /**
     * Get the Erater Username for this Assignment
     *
     * Returns the e-rater username
     *
     * @return string
     */
    public function getEraterUsername() {
        return $this->eraterusername;
    }

    /**
     * Set the Erater Username for this Assignment
     *
     * Sets the e-rater username
     *
     * @param string $eraterusername
     */
    public function setEraterUsername( $eraterusername ) {
        $this->eraterusername = $eraterusername;
    }

    /**
     * Get the Erater Password for this Assignment
     *
     * Returns the e-rater Password
     *
     * @return string
     */
    public function getEraterPassword() {
        return $this->eraterpassword;
    }

    /**
     * Set the Erater Password for this Assignment
     *
     * Sets the e-rater Password
     *
     * @param string $eraterpassword
     */
    public function setEraterPassword( $eraterpassword ) {
        $this->eraterpassword = $eraterpassword;
    }

}

