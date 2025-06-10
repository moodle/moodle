<?php
/*
 * @package TurnitinAPI
 * @subpackage TiiSubmission
 */

namespace Integrations\PhpSdk;

/**
 * Defines the TiiSubmission data object which contains getters and setters for a Turnitin Submission API object.
 *
 * @package TurnitinSDK
 * @subpackage Data
 */
class TiiSubmission extends TiiForm {
    private $submissionid;
    private $submissionids;
    private $datefrom;
    private $authoruserid;
    private $assignmentid;
    private $title;
    private $date;
    private $submitteruserid;
    private $grade;
    private $overallsimilarity;
    private $internetsimilarity;
    private $publicationssimilarity;
    private $submitteddocumentssimilarity;
    private $translatedoverallsimilarity;
    private $translatedinternetsimilarity;
    private $translatedpublicationssimilarity;
    private $translatedsubmitteddocumentssimilarity;
    private $authorlastviewedfeedback;
    private $voicecomment;
    private $feedbackexists;
    private $role;
    private $submissiondatatext;
    private $submissiondatapath;
    private $submissiondataurl;
    private $submissiondatafilename;
    private $textextract;
    private $customcss;
    private $xmlresponse;
    private $anonymous;
    private $anonymousrevealreason;
    private $anonymousrevealdatetime;
    private $anonymousrevealuser;
    private $originalityreportcapable;
    private $acceptnothingsubmission;

    /**
     * @ignore
     */
    const SUBMITENDPOINT      = '/api/lti/1p0/upload/submit';
    /**
     * @ignore
     */
    const RESUBMITENDPOINT    = '/api/lti/1p0/upload/resubmit';

    /**
     * Get the Submission ID for this Submission
     *
     * @return integer
     */
    public function getSubmissionId() {
        return $this->submissionid;
    }
    /**
     * Set the Submission ID for this Submission
     *
     * @param integer $submissionid
     */
    public function setSubmissionId($submissionid) {
        $this->submissionid = $submissionid;
    }
    /**
     * Get an array of Submissions IDs for this Submission data object
     *
     * @return array
     */
    public function getSubmissionIds() {
        return $this->submissionids;
    }
    /**
     * Set an array of Submissions IDs for this Submission data object
     *
     * @param array $submissionids
     */
    public function setSubmissionIds($submissionids) {
        $this->submissionids = $submissionids;
    }
    /**
     * Get the Date From datestring for this Submission data object
     *
     * Date From must be in ISO8601 format and include a timezone e.g. 2012-09-23T02:30:00Z
     * and is used to determine the date and time range that the results from findSubmissions will include.
     *
     * The date and time range used to findSubmissions will be from Date From until now.
     *
     * @return string
     */
    public function getDateFrom() {
        return $this->datefrom;
    }
    /**
     * Set the Date From datestring for this Submission data object
     *
     * Date From must be in ISO8601 format and include a timezone e.g. 2012-09-23T02:30:00Z
     * and is used to determine the date and time range that the results from findSubmissions will include.
     *
     * The date and time range used to findSubmissions will be from Date From until now.
     *
     * @param string $datefrom
     */
    public function setDateFrom($datefrom) {
        $this->datefrom = $datefrom;
    }
    /**
     * Get the User ID for student associated with this Submission
     *
     * @return integer
     */
    public function getAuthorUserId() {
        return $this->authoruserid;
    }
    /**
     * Set the User ID for student associated with this Submission
     *
     * @param integer $authoruserid
     */
    public function setAuthorUserId($authoruserid) {
        $this->authoruserid = $authoruserid;
    }
    /**
     * Get the Assignment ID for the Assignment associated with this Submission
     *
     * @return integer
     */
    public function getAssignmentId() {
        return $this->assignmentid;
    }
    /**
     * Set the Assignment ID for the Assignment associated with this Submission
     *
     * @param integer $assignmentid
     */
    public function setAssignmentId($assignmentid) {
        $this->assignmentid = $assignmentid;
    }
    /**
     * Get the Date that this submission was received by Turnitin
     *
     * Date must be in ISO8601 format and include a timezone e.g. 2012-09-23T02:30:00Z
     *
     * @return string
     */
    public function getDate() {
        return $this->date;
    }
    /**
     * @ignore
     * Set the Date that this submission was received by Turnitin
     *
     * @param string $date
     */
    public function setDate($date) {
        $this->date = $date;
    }
    /**
     * Get the Title for this Submission
     *
     * Submission title must be between 1 and 200 characters
     *
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }
    /**
     * Set the Title for this Submission
     *
     * Submission title must be between 1 and 200 characters
     *
     * @param string $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }
    /**
     * Get the User Id for the user that uploaded this Submission
     *
     * @return integer
     */
    public function getSubmitterUserId() {
        return $this->submitteruserid;
    }
    /**
     * Set the User Id for the user that uploaded this Submission
     *
     * @param integer $submitteruserid
     */
    public function setSubmitterUserId($submitteruserid) {
        $this->submitteruserid = $submitteruserid;
    }
    /**
     * @ignore
     * Set the User Id for the user that uploaded this Submission (alias for setSubmitterUserId)
     *
     * @param integer $submitteruserid
     */
    public function setSubmitter($submitteruserid) {
        $this->submitteruserid = $submitteruserid;
    }
    /**
     * Get the overall Grade for this Submission
     *
     * @return integer
     */
    public function getGrade() {
        return $this->grade;
    }
    /**
     * @ignore
     * Set the overall Grade for this Submission
     *
     * @param integer $grade
     */
    public function setGrade($grade) {
        $this->grade = $grade;
    }
    /**
     * Get the Overall Similarity percentage for this Submission
     *
     * The overall similarity percentage which is calculated by comparing the
     * submission with Internet, Student Paper and Journal sources.
     *
     * @return integer
     */
    public function getOverallSimilarity() {
        return $this->overallsimilarity;
    }
    /**
     * @ignore
     * Set the Overall Similarity percentage for this Submission
     *
     * The overall similarity percentage which is calculated by comparing the
     * submission with Internet, Student Paper and Publication / Journal sources.
     *
     * @param integer $overallsimilarity
     */
    public function setOverallSimilarity($overallsimilarity) {
        $this->overallsimilarity = $overallsimilarity;
    }
    /**
     * Get the Internet Similarity percentage for this Submission
     *
     * The similarity percentage which is calculated by comparing the submission with Internet sources.
     *
     * @return integer
     */
    public function getInternetSimilarity() {
        return $this->internetsimilarity;
    }
    /**
     * Set the Internet Similarity percentage for this Submission
     *
     * The similarity percentage which is calculated by comparing the submission with Internet sources.
     *
     * @param integer $internetsimilarity
     */
    public function setInternetSimilarity($internetsimilarity) {
        $this->internetsimilarity = $internetsimilarity;
    }
    /**
     * Get the Publications Similarity percentage for this Submission
     *
     * The similarity percentage which is calculated by comparing the submission with Publication and Journal sources.
     *
     * @return integer
     */
    public function getPublicationsSimilarity() {
        return $this->publicationssimilarity;
    }
    /**
     * @ignore
     * Set the Publications Similarity percentage for this Submission
     *
     * The similarity percentage which is calculated by comparing the submission with Publication and Journal sources.
     *
     * @param integer $publicationssimilarity
     */
    public function setPublicationsSimilarity($publicationssimilarity) {
        $this->publicationssimilarity = $publicationssimilarity;
    }
    /**
     * Get the Submitted Documents Similarity percentage for this Submission
     *
     * The similarity percentage which is calculated by comparing the submission with Student Paper sources.
     *
     * @return integer
     */
    public function getSubmittedDocumentsSimilarity() {
        return $this->submitteddocumentssimilarity;
    }
    /**
     * @ignore
     * Set the Submitted Documents Similarity percentage for this Submission
     *
     * The similarity percentage which is calculated by comparing the submission with Student Paper sources.
     *
     * @param integer $submitteddocumentssimilarity
     */
    public function setSubmittedDocumentsSimilarity($submitteddocumentssimilarity) {
        $this->submitteddocumentssimilarity = $submitteddocumentssimilarity;
    }
    /**
     * Get the Translated Overall Similarity percentage for this Submission
     *
     * The translated overall similarity percentage which is calculated by translating the submission content and comparing the
     * submission with Internet, Student Paper and Journal sources.
     *
     * @return integer
     */
    public function getTranslatedOverallSimilarity() {
        return $this->translatedoverallsimilarity;
    }
    /**
     * @ignore
     * Get the Translated Overall Similarity percentage for this Submission
     *
     * The translated overall similarity percentage which is calculated by translating the submission content and comparing the
     * submission with Internet, Student Paper and Journal sources.
     *
     * @param integer $translatedoverallsimilarity
     */
    public function setTranslatedOverallSimilarity($translatedoverallsimilarity) {
        $this->translatedoverallsimilarity = $translatedoverallsimilarity;
    }
    /**
     * Get the Translated Internet Similarity percentage for this Submission
     *
     * The translated internet similarity percentage which is calculated by translating the submission content and comparing the
     * submission with Internet sources.
     *
     * @return integer
     */
    public function getTranslatedInternetSimilarity() {
        return $this->translatedinternetsimilarity;
    }
    /**
     * @ignore
     * Set the Translated Internet Similarity percentage for this Submission
     *
     * The translated internet similarity percentage which is calculated by translating the submission content and comparing the
     * submission with Internet sources.
     *
     * @param integer $translatedinternetsimilarity
     */
    public function setTranslatedInternetSimilarity($translatedinternetsimilarity) {
        $this->translatedinternetsimilarity = $translatedinternetsimilarity;
    }
    /**
     * Get the Translated Publications Similarity percentage for this Submission
     *
     * The translated publication and journal similarity percentage which is calculated by translating the submission content
     * and comparing the submission with Publications and Journal sources.
     *
     * @return integer
     */
    public function getTranslatedPublicationsSimilarity() {
        return $this->translatedpublicationssimilarity;
    }
    /**
     * @ignore
     * Set the Translated Publications Similarity percentage for this Submission
     *
     * The translated publication and journal similarity percentage which is calculated by translating the submission content
     * and comparing the submission with Publications and Journal sources.
     *
     * @param integer $translatedpublicationssimilarity
     */
    public function setTranslatedPublicationsSimilarity($translatedpublicationssimilarity) {
        $this->translatedpublicationssimilarity = $translatedpublicationssimilarity;
    }
    /**
     * Get the Translated Submitted Documents Similarity percentage for this Submission
     *
     * The translated Submitted Documents Similarity percentage which is calculated by translating the submission content
     * and comparing the submission with Student Paper sources.
     *
     * @return integer
     */
    public function getTranslatedSubmittedDocumentsSimilarity () {
        return $this->translatedsubmitteddocumentssimilarity;
    }
    /**
     * @ignore
     * Set the Translated Submitted Documents Similarity percentage for this Submission
     *
     * The translated Submitted Documents Similarity percentage which is calculated by translating the submission content
     * and comparing the submission with Student Paper sources.
     *
     * @param integer $translatedsubmitteddocumentssimilarity
     */
    public function setTranslatedSubmittedDocumentsSimilarity($translatedsubmitteddocumentssimilarity) {
        $this->translatedsubmitteddocumentssimilarity = $translatedsubmitteddocumentssimilarity;
    }
    /**
     * Get the Date the Student last viewed the GradeMark feedback for this submission
     *
     * The date when the student last accessed the Document Viewer with the GradeMark service active for 10 seconds or more.
     *
     * @return string
     */
    public function getAuthorLastViewedFeedback() {
        return $this->authorlastviewedfeedback;
    }
    /**
     * @ignore
     * Set the Date the Student last viewed the GradeMark feedback for this submission
     *
     * The date when the student last accessed the Document Viewer with the GradeMark service active for 10 seconds or more.
     *
     * @param string $authorlastviewedfeedback
     */
    public function setAuthorLastViewedFeedback($authorlastviewedfeedback) {
        $this->authorlastviewedfeedback = $authorlastviewedfeedback;
    }
    /**
     * Get the Voice Comment indicator for this Submission
     *
     * Indicates whether a voice comment audio file is available for this Submission
     *
     * @return boolean
     */
    public function getVoiceComment() {
        return (boolean)(integer)$this->voicecomment;
    }
    /**
     * @ignore
     * Set the Voice Comment indicator for this Submission
     *
     * Indicates whether a voice comment audio file is available for this Submission
     *
     * @param boolean $voicecomment
     */
    public function setVoiceComment(  $voicecomment) {
        $this->voicecomment = $voicecomment;
    }
    /**
     * Get the Feedback Available indicator for this Submission
     *
     * Indicates whether any GradeMark feedback exists on a Submission
     *
     * @return boolean
     */
    public function getFeedbackExists() {
        return (boolean)(integer)$this->feedbackexists;
    }
    /**
     * @ignore
     * Set the Feedback Available indicator for this Submission
     *
     * Indicates whether any GradeMark feedback exists on a Submission
     *
     * @param boolean $feedbackexists
     */
    public function setFeedbackExists(  $feedbackexists) {
        $this->feedbackexists = $feedbackexists;
    }
    /**
     * Get the Role of the User making a request for this Submission
     *
     * Gets the Role of the User requesting this Submission, Learner or Instructor
     *
     * @return string
     */
    public function getRole() {
        return $this->role;
    }

    /**
     * Set the Role of the User making a request for this Submission
     *
     * Gets the Role of the User requesting this Submission, Learner or Instructor
     *
     * @param string $role
     */
    public function setRole($role) {
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
     * @ignore
     * Get the Text Data for this Submission
     *
     * Gets the text data for the submission to be made to Turnitin
     *
     * @return string
     */
    public function getSubmissionDataText() {
        return $this->submissiondatatext;
    }

    /**
     * Set the Text Data for this Submission
     *
     * Sets the text data for the submission to be made to Turnitin
     *
     * @param string $submissiondatatext
     */
    public function setSubmissionDataText($submissiondatatext) {
        $this->submissiondatatext = $submissiondatatext;
    }

    /**
     * @ignore
     * Get the File Path for the Submission file
     *
     * Gets the File Path for the file to be submitted to Turnitin
     *
     * @return string
     */
    public function getSubmissionDataPath() {
        return $this->submissiondatapath;
    }

    /**
     * Set the File Path for the Submission file
     *
     * Sets the local file path for the file to be submitted to Turnitin
     *
     * @param string $submissiondatapath
     */
    public function setSubmissionDataPath($submissiondatapath) {
        $this->submissiondatapath = $submissiondatapath;
    }

    /**
     * @ignore
     * Get the URL for the Submission file
     *
     * Gets the URL for the file to be submitted to Turnitin
     *
     * @return string
     */
    public function getSubmissionDataUrl() {
        return $this->submissiondataurl;
    }

    /**
     * Set the URL for the Submission file
     *
     * Sets the URL for the file to be submitted to Turnitin
     *
     * @param string $submissiondataurl
     */
    private function setSubmissionDataUrl($submissiondataurl) {
        $this->submissiondataurl = $submissiondataurl;
    }

    /**
     * @ignore
     * Get the Filename for the Submission file
     *
     * Gets the Filename for the file to be submitted to Turnitin
     *
     * @return string
     */
    public function getSubmissionDataFilename() {
        return $this->submissiondatafilename;
    }

    /**
     * Set the Filename for the Submission file
     *
     * Sets the Filename for the file to be submitted to Turnitin
     *
     * @param string $submissiondatafilename
     */
    public function setSubmissionDataFilename($submissiondatafilename) {
        $this->submissiondatafilename = $submissiondatafilename;
    }

    /**
     * Set the Web URL and Filename for the Submission file
     *
     * Sets the Web URL and Filename for the file to be submitted to Turnitin
     * The URL must be accessible from the Turnitin servers, generally it is
     * best to add a time limited access token to the URL
     *
     * @param string $url
     * @param string $filename
     */
    public function setSubmissionDataWeb( $url, $filename ) {
        $this->setSubmissionDataUrl( $url );
        $this->setSubmissionDataFilename( $filename );
    }

    /**
     * Get the Text Extract for the Submission
     *
     * Gets the text that was extracted from the file submitted to Turnitin
     *
     * @return string
     */
    public function getTextExtract() {
        return $this->textextract;
    }

    /**
     * @ignore
     * Set the Text Extract for the Submission
     *
     * Sets the text that was extracted from the file submitted to Turnitin
     *
     * @param string $textextract
     */
    public function setTextExtract($textextract) {
        $this->textextract = $textextract;
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
     * @ignore
     * Get the XML Response parameter for this Submission
     *
     * @return boolean
     */
    public function getXmlResponse() {
        return (boolean)(integer)$this->xmlresponse;
    }

    /**
     * Set the XML Response parameter for this Submission
     *
     * A boolean to determine if error messages should be returned as XML rather than HTML
     *
     * @param boolean $xmlresponse
     */
    public function setXmlResponse(  $xmlresponse) {
        $this->xmlresponse = $xmlresponse;
    }

    /**
     * Get the Boolean that determines if Anonymous Marking is in effect for this submission
     *
     * @return boolean
     */
    public function getAnonymous() {
        return (boolean)(integer)$this->anonymous;
    }

    /**
     * Set the Boolean that determines if Anonymous Marking is in effect for this submission
     *
     * @param boolean $anonymous
     */
    public function setAnonymous(  $anonymous ) {
        $this->anonymous = $anonymous;
    }

    /**
     * Get the reason for revealing an anonymous submission authors name
     *
     * @return string
     */
    public function getAnonymousRevealReason() {
        return $this->anonymousrevealreason;
    }

    /**
     * Set the reason for revealing an anonymous submission authors name
     *
     * @param string
     */
    public function setAnonymousRevealReason( $anonymousrevealreason ) {
        $this->anonymousrevealreason = $anonymousrevealreason;
    }
    /**
     * Get the User ID of the instructor that revealed the anonymized student's identity
     *
     * @return string
     */
    public function getAnonymousRevealUser() {
        return $this->anonymousrevealuser;
    }

    /**
     * Set the User ID of the instructor that is requesting to reveal the student's identity
     *
     * @param string $anonymousrevealuser
     */
    public function setAnonymousRevealUser( $anonymousrevealuser ) {
        $this->anonymousrevealuser = $anonymousrevealuser;
    }

    /**
     * Get the Date that this student's data was revealed by AnonymousRevealUser
     *
     * Date is in ISO8601 format and include a timezone e.g. 2012-09-23T02:30:00Z
     *
     * @return string
     */
    public function getAnonymousRevealDateTime() {
        return $this->anonymousrevealdatetime;
    }

    /**
     * @ignore
     *
     * Date must be in ISO8601 format and include a timezone e.g. 2012-09-23T02:30:00Z
     *
     * @param string $anonymousrevealdatetime
     */
    public function setAnonymousRevealDateTime($anonymousrevealdatetime) {
        $this->anonymousrevealdatetime = $anonymousrevealdatetime;
    }

    /**
     * Get the Boolean value for OriginalityReportCapable
     *
     * Determines if a submission is capable of generating originality reports
     *
     * @return string
     */
    public function getOriginalityReportCapable() {
        return $this->originalityreportcapable;
    }

    /**
     * @ignore
     *
     * Determines if a submission is capable of generating originality reports
     *
     * @param string $originalityreportcapable
     */
    public function setOriginalityReportCapable($originalityreportcapable) {
        $this->originalityreportcapable = $originalityreportcapable;
    }

    /**
     * Get the Boolean value for AcceptNothingSubmission
     *
     * Determines if a submission is an accept nothing submission (Grading Template)
     *
     * @return string
     */
    public function getAcceptNothingSubmission() {
        return $this->acceptnothingsubmission;
    }

    /**
     * @ignore
     *
     * Determines if a submission is an accept nothing submission (Grading Template)
     *
     * @param string $acceptnothingsubmission
     */
    public function setAcceptNothingSubmission($acceptnothingsubmission) {
        $this->acceptnothingsubmission = $acceptnothingsubmission;
    }

}

