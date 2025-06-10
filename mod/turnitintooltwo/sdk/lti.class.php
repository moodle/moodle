<?php

/*
 * @ignore
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once( __DIR__.'/sdkexception.class.php' );
require_once( __DIR__.'/tiilti.class.php' );
require_once( __DIR__.'/response.class.php' );

/**
 * @ignore
 */
class LTI extends OAuthSimple {

    protected $integrationid;
    protected $accountid;
    protected $sharedkey;
    protected $logpath;
    protected $debug;
    protected $endpoint;
    protected $ltiparams;
    protected $xmlresponse;
    protected $lastrequest;
    protected $lastresponse;
    protected $apibaseurl;
    protected $language;

    protected $integrationversion;
    protected $pluginversion;

    protected $proxyhost;
    protected $proxyport;
    protected $proxytype;
    protected $proxyuser;
    protected $proxypassword;
    protected $proxybypass;
    protected $sslcertificate;

    protected $testingconnection;
    protected $performancelog;

    public function __construct( $apibaseurl ) {
        $this->setApiBaseUrl( $apibaseurl );
        $this->ltiparams = array(
            'lti_version'      => 'LTI-1p0',
            'resource_link_id' => $this->genUuid()
        );
        $this->testingconnection = false;
        $this->performancelog = null;
        $this->integrationversion = '';
        $this->pluginversion = '';
    }

    /**
     *
     * @param TiiLTI $lti
     * @return array
     */
    public function getDVGradeMarkFormHash( $lti ) {
        return $this->getDVFormHash( $lti::DVGRADEMARKENDPOINT, $lti );
    }

    /**
     *
     * @param TiiLTI $lti
     * @return array
     */
    public function getDVReportFormHash( $lti ) {
        return $this->getDVFormHash( $lti::DVREPORTENDPOINT, $lti );
    }

    /**
     *
     * @param TiiLTI $lti
     * @return array
     */
    public function getDVDefaultFormHash( $lti ) {
        return $this->getDVFormHash( $lti::DVDEFAULTENDPOINT, $lti );
    }

    /**
     *
     * @param TiiLTI $lti
     * @return array
     */
    public function getDVPeerMarkFormHash( $lti ) {
        return $this->getDVFormHash( $lti::DVPEERMARKENDPOINT, $lti );
    }

    /**
     *
     * @param string $endpoint
     * @param TiiLTI $lti
     * @return array
     */
    public function getDVFormHash( $endpoint, $lti ) {
        $params = array(
            'lis_person_sourcedid'        => $lti->getUserId(),
            'lis_result_sourcedid'        => $lti->getSubmissionId(),
            'roles'                       => $lti->getRole(),
            'custom_source'               => $this->getIntegrationId()
        );
        if ( !is_null( $lti->getCustomCSS() ) ) $params['launch_presentation_css_url'] =  $lti->getCustomCSS();
        $this->setLtiParams( $params );
        parent::__construct( $this->accountid, $this->sharedkey );
        $this->setEndPoint( $this->getApiBaseUrl() . $endpoint );
        $this->setParameters( $this->getLtiParams() );
        return array_merge( $this->getLtiParams(), $this->getParamArray( $params ) );
    }

    /**
     *
     * @param TiiLTI $lti
     * @return array
     */
    public function getMessagesFormHash( $lti ) {
        $params = array(
            'lis_person_sourcedid'        => $lti->getUserId(),
            'roles'                       => $lti->getRole(),
            'custom_source'               => $this->getintegrationid()
        );
        if ( !is_null( $lti->getCustomCSS() ) ) $params['launch_presentation_css_url'] =  $lti->getCustomCSS();
        $this->setLtiParams( $params );
        parent::__construct( $this->accountid, $this->sharedkey );
        $this->setEndPoint( $this->getApiBaseUrl() . $lti::MESSAGESENDPOINT );
        $this->setParameters( $this->getLtiParams() );
        return array_merge( $this->getLtiParams(), $this->getParamArray( $params ) );
    }

    /**
     *
     * @param TiiLTI $lti
     * @return array
     */
    public function getRubricManagerFormHash( $lti ) {
        $params = array(
            'lis_person_sourcedid'        => $lti->getUserId(),
            'roles'                       => $lti->getRole(),
            'custom_source'               => $this->getintegrationid()
        );
        if ( !is_null( $lti->getClassId() ) ) $params['lis_coursesection_sourcedid'] = $lti->getClassId();
        if ( !is_null( $lti->getCustomCSS() ) ) $params['launch_presentation_css_url'] =  $lti->getCustomCSS();
        $this->setLtiParams( $params );
        parent::__construct( $this->accountid, $this->sharedkey );
        $this->setEndPoint( $this->getApiBaseUrl() . $lti::RUBRICENDPOINT );
        $this->setParameters( $this->getLtiParams() );
        return array_merge( $this->getLtiParams(), $this->getParamArray( $params ) );
    }

    /**
     *
     * @param TiiLTI $lti
     * @return array
     */
    public function getRubricViewFormHash( $lti ) {
        $params = array(
            'lis_person_sourcedid'        => $lti->getUserId(),
            'roles'                       => $lti->getRole(),
            'lis_lineitem_sourcedid'      => $lti->getAssignmentId(),
            'custom_source'               => $this->getintegrationid()
        );
        if ( !is_null( $lti->getCustomCSS() ) ) $params['launch_presentation_css_url'] =  $lti->getCustomCSS();
        $this->setLtiParams( $params );
        parent::__construct( $this->accountid, $this->sharedkey );
        $this->setEndPoint( $this->getApiBaseUrl() . $lti::RUBRICENDPOINT );
        $this->setParameters( $this->getLtiParams() );
        return array_merge( $this->getLtiParams(), $this->getParamArray( $params ) );
    }

    /**
     *
     * @param TiiLTI $lti
     * @return array
     */
    public function getQuickmarkFormHash( $lti ) {
        $params = array(
            'lis_person_sourcedid'        => $lti->getUserId(),
            'roles'                       => $lti->getRole(),
            'custom_source'               => $this->getintegrationid()
        );
        if ( !is_null( $lti->getCustomCSS() ) ) $params['launch_presentation_css_url'] =  $lti->getCustomCSS();
        $this->setLtiParams( $params );
        parent::__construct( $this->accountid, $this->sharedkey );
        $this->setEndPoint( $this->getApiBaseUrl() . $lti::QUICKMARKENDPOINT );
        $this->setParameters( $this->getLtiParams() );
        return array_merge( $this->getLtiParams(), $this->getParamArray( $params ) );
    }

    /**
     *
     * @param TiiLTI $lti
     * @return array
     */
    public function getUserAgreementFormHash( $lti ) {
        $params = array(
            'lis_person_sourcedid'        => $lti->getUserId(),
            'custom_source'               => $this->getIntegrationId()
        );
        if ( !is_null( $lti->getCustomCSS() ) ) $params['launch_presentation_css_url'] =  $lti->getCustomCSS();
        $this->setLtiParams( $params );
        parent::__construct( $this->accountid, $this->sharedkey );
        $this->setEndPoint( $this->getApiBaseUrl() . $lti::EULAENDPOINT );
        $this->setParameters( $this->getLtiParams() );
        return array_merge( $this->getLtiParams(), $this->getParamArray( $params ) );
    }

    /**
     *
     * @param TiiLTI $lti
     * @return array
     */
    public function getDownloadXLSFormHash( $lti ) {
        $params = array(
            'lis_person_sourcedid'        => $lti->getUserId(),
            'lis_lineitem_sourcedid'      => $lti->getAssignmentId(),
            'custom_source'               => $this->getintegrationid(),
            'roles'                       => $lti->getRole()
        );
        if ( !is_null( $lti->getCustomCSS() ) ) $params['launch_presentation_css_url'] =  $lti->getCustomCSS();
        $this->setLtiParams( $params );
        parent::__construct( $this->accountid, $this->sharedkey );
        $this->setEndPoint( $this->getApiBaseUrl() . $lti::DOWNLOADXLSENDPOINT );
        $this->setParameters( $this->getLtiParams() );
        return array_merge( $this->getLtiParams(), $this->getParamArray( $params ) );
    }

    /**
     *
     * @param TiiLTI $lti
     * @return array
     */
    public function getPeerMarkSetupFormHash( $lti ) {
        $params = array(
            'lis_person_sourcedid'        => $lti->getUserId(),
            'lis_lineitem_sourcedid'      => $lti->getAssignmentId(),
            'custom_source'               => $this->getintegrationid(),
            'roles'                       => $lti->getRole()
        );
        if ( !is_null( $lti->getSkipSetup() ) AND $lti->getSkipSetup() == true ) $params['custom_skipsetup'] = 1;
        if ( !is_null( $lti->getPeermarkId() ) ) $params['custom_peermarkid'] = $lti->getPeermarkId();
        if ( !is_null( $lti->getCustomCSS() ) ) $params['launch_presentation_css_url'] = $lti->getCustomCSS();
        if ( !is_null( $lti->getWideMode() ) ) $params['custom_widemode'] = (integer)$lti->getWideMode();
        $this->setLtiParams( $params );
        parent::__construct( $this->accountid, $this->sharedkey );
        $this->setEndPoint( $this->getApiBaseUrl() . $lti::PEERMARKSETUPENDPOINT );
        $this->setParameters( $this->getLtiParams() );
        return array_merge( $this->getLtiParams(), $this->getParamArray( $params ) );
    }

    /**
     *
     * @param TiiLTI $lti
     * @return array
     */
    public function getPeerMarkReviewFormHash( $lti ) {
        $params = array(
            'lis_person_sourcedid'        => $lti->getUserId(),
            'lis_lineitem_sourcedid'      => $lti->getAssignmentId(),
            'custom_source'               => $this->getintegrationid(),
            'roles'                       => $lti->getRole()
        );
        if ( !is_null( $lti->getPeermarkId() ) ) $params['custom_peermarkid'] = $lti->getPeermarkId();
        if ( !is_null( $lti->getCustomCSS() ) ) $params['launch_presentation_css_url'] =  $lti->getCustomCSS();
        if ( !is_null( $lti->getWideMode() ) ) $params['custom_widemode'] = (integer)$lti->getWideMode();
        $this->setLtiParams( $params );
        parent::__construct( $this->accountid, $this->sharedkey );
        $this->setEndPoint( $this->getApiBaseUrl() . $lti::PEERMARKREVIEWENDPOINT );
        $this->setParameters( $this->getLtiParams() );
        return array_merge( $this->getLtiParams(), $this->getParamArray( $params ) );
    }

    /**
     *
     * @param TiiLTI $lti
     * @return array
     */
    public function getDownloadZipFormHash( $lti ) {
        return $this->getDownloadBulkFormHash( $lti::DOWNLOADZIPENDPOINT, $lti );
    }

    /**
     *
     * @param TiiLTI $lti
     * @return array
     */
    public function getDownloadPDFZipFormHash( $lti ) {
        return $this->getDownloadBulkFormHash( $lti::DOWNLOADPDFZIPENDPOINT, $lti );
    }

    /**
     *
     * @param TiiLTI $lti
     * @return array
     */
    public function getDownloadGradeMarkZipFormHash( $lti ) {
        return $this->getDownloadBulkFormHash( $lti::DOWNLOADGRADEMARKZIPENDPOINT, $lti );
    }

    /**
     *
     * @param string $endpoint
     * @param TiiLTI $lti
     * @return array
     */
    private function getDownloadBulkFormHash( $endpoint, $lti ) {
        $params = array(
            'lis_person_sourcedid'        => $lti->getUserId(),
            'lis_lineitem_sourcedid'      => $lti->getAssignmentId(),
            'roles'                       => $lti->getRole(),
            'custom_source'               => $this->getintegrationid()
        );
        if ( !is_null( $lti->getCustomCSS() ) ) $params['launch_presentation_css_url'] =  $lti->getCustomCSS();
        if ( !is_null( $lti->getSubmissionIds() ) ) $params['custom_submission_ids'] = join( ',', $lti->getSubmissionIds() );
        $this->setLtiParams( $params );
        parent::__construct( $this->accountid, $this->sharedkey );
        $this->setEndPoint( $this->getApiBaseUrl() . $endpoint );
        $this->setParameters( $this->getLtiParams() );
        return array_merge( $this->getLtiParams(), $this->getParamArray( $params ) );
    }

    /**
     *
     * @param TiiLTI $lti
     * @return array
     */
    public function getDownloadOriginalFileFormHash( $lti ) {
        return $this->getDownloadSubmissionFormHash( $lti::DOWNLOADORIGENDPOINT, $lti );
    }

    /**
     *
     * @param TiiLTI $lti
     * @return array
     */
    public function getDownloadDefaultPDFFormHash( $lti ) {
        return $this->getDownloadSubmissionFormHash( $lti::DOWNLOADDEFAULTPDFENDPOINT, $lti );
    }

    /**
     *
     * @param TiiLTI $lti
     * @return array
     */
    public function getDownloadGradeMarkPDFFormHash( $lti ) {
        return $this->getDownloadSubmissionFormHash( $lti::DOWNLOADGRADEMARKPDFENDPOINT, $lti );
    }

    /**
     *
     * @param TiiLTI $lti
     * @return array
     */
    private function getDownloadSubmissionFormHash( $endpoint, $lti ) {
        $params = array(
            'lis_person_sourcedid'        => $lti->getUserId(),
            'lis_result_sourcedid'        => $lti->getSubmissionId(),
            'roles'                       => $lti->getRole(),
            'custom_source'               => $this->getIntegrationId()
        );
        if ( !is_null( $lti->getCustomCSS() ) ) $params['launch_presentation_css_url'] =  $lti->getCustomCSS();
        if ( !is_null( $lti->getWideMode() ) ) $params['custom_widemode'] = (integer)$lti->getWideMode();
        $this->setLtiParams( $params );
        parent::__construct( $this->accountid, $this->sharedkey );
        $this->setEndPoint( $this->getApiBaseUrl() . $endpoint );
        $this->setParameters( $this->getLtiParams() );
        return array_merge( $this->getLtiParams(), $this->getParamArray( $params ) );
    }

    /**
     *
     * @param TiiSubmission $submission
     * @return array
     */
    public function getSubmissionFormHash( $submission ) {
        $params = array(
            'lis_person_sourcedid'        => $submission->getSubmitterUserId(),
            'lis_lineitem_sourcedid'      => $submission->getAssignmentId(),
            'roles'                       => $submission->getRole(),
            'custom_source'               => $this->getintegrationid(),
            'custom_submission_title'     => $submission->getTitle(),
            'custom_submission_author'    => $submission->getAuthorUserId(),
            'custom_xmlresponse'          => (integer)$this->getXmlResponse()
        );
        if ( !is_null( $submission->getSubmissionDataUrl() ) ) {
            $params['custom_submission_url'] =  $submission->getSubmissionDataUrl();
            $params['custom_submission_filename'] =  $submission->getSubmissionDataFilename();
        }
        if ( !is_null( $submission->getCustomCSS() ) ) $params['launch_presentation_css_url'] =  $submission->getCustomCSS();
        if ( !is_null( $submission->getWideMode() ) ) $params['custom_widemode'] = (integer)$submission->getWideMode();
        $this->setLtiParams( $params );
        parent::__construct( $this->accountid, $this->sharedkey );
        $this->setEndPoint( $this->getApiBaseUrl() . $submission::SUBMITENDPOINT );
        $this->setParameters( $this->getLtiParams() );
        return array_merge( $this->getLtiParams(), $this->getParamArray( $params ) );
    }

    /**
     *
     * @param TiiSubmission $submission
     * @return array
     */
    public function getResubmissionFormHash( $submission ) {
        $params = array(
            'lis_person_sourcedid'        => $submission->getSubmitterUserId(),
            'lis_result_sourcedid'        => $submission->getSubmissionId(),
            'roles'                       => $submission->getRole(),
            'custom_source'               => $this->getIntegrationId(),
            'custom_submission_title'     => $submission->getTitle(),
            'custom_submission_author'   => $submission->getAuthorUserId(),
            'custom_xmlresponse'          => (integer)$this->getXmlResponse()
        );
        if ( !is_null( $submission->getSubmissionDataUrl() ) ) {
            $params['custom_submission_url'] =  $submission->getSubmissionDataUrl();
            $params['custom_submission_filename'] =  $submission->getSubmissionDataFilename();
        }
        if ( !is_null( $submission->getCustomCSS() ) ) $params['launch_presentation_css_url'] =  $submission->getCustomCSS();
        if ( !is_null( $submission->getWideMode() ) ) $params['custom_widemode'] = (integer)$submission->getWideMode();
        $this->setLtiParams( $params );
        parent::__construct( $this->accountid, $this->sharedkey );
        $this->setEndPoint( $this->getApiBaseUrl() . $submission::RESUBMITENDPOINT );
        $this->setParameters( $this->getLtiParams() );
        return array_merge( $this->getLtiParams(), $this->getParamArray( $params ) );
    }

    /**
     *
     * @param TiiLTI $lti
     * @return array
     */
    public function getCreateAssignmentFormHash( $lti ) {
        $params = array(
            'lis_person_sourcedid'        => $lti->getUserId(),
            'lis_coursesection_sourcedid' => $lti->getClassId(),
            'custom_source'               => $this->getintegrationid(),
            'roles'                       => $lti->getRole()
        );
        if ( !is_null( $lti->getCustomCSS() ) ) $params['launch_presentation_css_url'] =  $lti->getCustomCSS();
        if ( !is_null( $lti->getWideMode() ) ) $params['custom_widemode'] = (integer)$lti->getWideMode();
        $this->setLtiParams( $params );
        parent::__construct( $this->accountid, $this->sharedkey );
        $this->setEndPoint( $this->getApiBaseUrl() . $lti::CREATEASSIGNMENTENDPOINT );
        $this->setParameters( $this->getLtiParams() );
        return array_merge( $this->getLtiParams(), $this->getParamArray( $params ) );
    }

    /**
     *
     * @param TiiLTI $lti
     * @return array
     */
    public function getEditAssignmentFormHash( $lti ) {
        $params = array(
            'lis_person_sourcedid'        => $lti->getUserId(),
            'lis_lineitem_sourcedid'      => $lti->getAssignmentId(),
            'custom_source'               => $this->getintegrationid(),
            'roles'                       => $lti->getRole()
        );
        if ( !is_null( $lti->getCustomCSS() ) ) $params['launch_presentation_css_url'] =  $lti->getCustomCSS();
        if ( !is_null( $lti->getWideMode() ) ) $params['custom_widemode'] = (integer)$lti->getWideMode();
        $this->setLtiParams( $params );
        parent::__construct( $this->accountid, $this->sharedkey );
        $this->setEndPoint( $this->getApiBaseUrl() . $lti::EDITASSIGNMENTENDPOINT );
        $this->setParameters( $this->getLtiParams() );
        return array_merge( $this->getLtiParams(), $this->getParamArray( $params ) );
    }

    /**
     *
     * @param TiiLTI $lti
     * @return array
     */
    public function getAssignmentInboxFormHash( $lti ) {
        $params = array(
            'lis_person_sourcedid'        => $lti->getUserId(),
            'lis_lineitem_sourcedid'      => $lti->getAssignmentId(),
            'custom_source'               => $this->getintegrationid(),
            'roles'                       => $lti->getRole()
        );
        if ( !is_null( $lti->getCustomCSS() ) ) $params['launch_presentation_css_url'] =  $lti->getCustomCSS();
        if ( !is_null( $lti->getWideMode() ) ) $params['custom_widemode'] = (integer)$lti->getWideMode();
        if ( !is_null( $lti->getStudentList() ) ) $params['custom_studentlist'] = $lti->getStudentList();
        $this->setLtiParams( $params );
        parent::__construct( $this->accountid, $this->sharedkey );
        $this->setEndPoint( $this->getApiBaseUrl() . $lti::ASSIGNMENTINBOXENDPOINT );
        $this->setParameters( $this->getLtiParams() );
        return array_merge( $this->getLtiParams(), $this->getParamArray( $params ) );
    }

    /**
     * @param TiiLTI $object
     * @param array $params
     * @param string $endpoint
     * @param string $target
     * @param string $buttonimg
     * @param string $buttontext
     * @param boolean $uploadfile
     * @param boolean $uploadtext
     * @return string
     */
    public function getFormHtml( $object, $params, $uploadfile, $uploadtext ) {
        if ( $uploadfile OR $uploadtext ) {
            $enctype = 'multipart/form-data';
        } else {
            $enctype = 'application/x-www-form-urlencoded';
        }
        $output = '<form action="' . $this->getEndPoint() . '" method="POST" target="' . $object->getFormTarget() . '" enctype="' . $enctype . '">'.PHP_EOL;
        foreach ( $params as $name => $value ) {
            $output .= '<input name="' . htmlentities($name, ENT_QUOTES) . '" value="' . htmlentities($value, ENT_QUOTES) . '" type="hidden" />'.PHP_EOL;
        }
        if ( $uploadtext ) {
            $output .= '<textarea name="custom_submission_data"></textarea>'.PHP_EOL;
        } else if ( $uploadfile ) {
            $output .= '<input name="custom_submission_data" type="file" />'.PHP_EOL;
        }
        if ( !is_null( $object->getHasButton() ) ) {
            $output .= '<input type="submit" value="' . $object->getButtonText() . '" style="' . $object->getButtonStyle() . '"  />'.PHP_EOL;
        }
        $output .= '</form>'.PHP_EOL;
        return $output;
    }

    /**
     *
     * @param TiiSubmission $submission
     */
    public function createSubmission( $submission ) {
        $params_merge = $this->getSubmissionFormHash( $submission );
        if ( is_null( $submission->getSubmissionDataPath() ) && is_null( $submission->getSubmissionDataUrl() ) ) {
            $params_merge['custom_submission_data'] = $submission->getSubmissionDataText();
        } else if ( is_null( $submission->getSubmissionDataUrl() ) ) {
            if ( !file_exists( $submission->getSubmissionDataPath() ) ) {
                throw new TurnitinSDKException( 'invaliddata', 'Submission Paper Data not found.' );
            }

            // CURL uploading with @ has been deprecated in PHP 5.5
            if (class_exists('CURLFile')) {
                $finfo = new finfo( FILEINFO_MIME );
                $mimetype = $finfo->file( $submission->getSubmissionDataPath() );
                $params_merge['custom_submission_data'] = new CurlFile($submission->getSubmissionDataPath(), $mimetype);
            } else {
                $params_merge['custom_submission_data'] = '@'.$submission->getSubmissionDataPath();
            }
        }
        $transport = $this->transportData( $params_merge );
        $response = new Response( $this );
        if ( $response->getStatusCode() == 'failure' ) {
            throw new TurnitinSDKException( $response->getStatusCode(), $response->getDescription() );
        } else {
            $dom = $response->getDomObject();
            $tiiSubmission = new TiiSubmission();
            $tiiSubmission->setSubmissionId( $dom->getElementsByTagName( 'lis_result_sourcedid' )->item(0)->nodeValue );
            $tiiSubmission->setTextExtract( @$dom->getElementsByTagName( 'submission_data_extract' )->item(0)->nodeValue );
            $response->setSubmission( $tiiSubmission );
        }
        return $response;
    }

    /**
     *
     * @param TiiSubmission $submission
     */
    public function replaceSubmission( $submission ) {
        $params_merge = $this->getResubmissionFormHash( $submission );
        if ( is_null( $submission->getSubmissionDataPath() ) && is_null( $submission->getSubmissionDataUrl() ) ) {
            $params_merge['custom_submission_data'] = $submission->getSubmissionDataText();
        } else if ( is_null( $submission->getSubmissionDataUrl() ) ) {
            // CURL uploading with @ has been deprecated in PHP 5.5
            if (class_exists('CURLFile')) {
                $finfo = new finfo( FILEINFO_MIME );
                $mimetype = $finfo->file( $submission->getSubmissionDataPath() );
                $params_merge['custom_submission_data'] = new CurlFile($submission->getSubmissionDataPath(), $mimetype);
            } else {
                $params_merge['custom_submission_data'] = '@'.$submission->getSubmissionDataPath();
            }
        }
        $transport = $this->transportData( $params_merge );
        $response = new Response( $this );
        if ( $response->getStatusCode() == 'failure' ) {
            throw new TurnitinSDKException( $response->getStatusCode(), $response->getDescription() );
        } else {
            $dom = $response->getDomObject();
            $tiiSubmission = new TiiSubmission();
            $tiiSubmission->setSubmissionId( $dom->getElementsByTagName( 'lis_result_sourcedid' )->item(0)->nodeValue );
            $tiiSubmission->setTextExtract( @$dom->getElementsByTagName( 'submission_data_extract' )->item(0)->nodeValue );
            $response->setSubmission( $tiiSubmission );
        }
        return $response;
    }

    /**
     *
     * @return string
     */
    public function __getLastRequest() {
        return $this->lastrequest;
    }

    /**
     *
     * @param string $lastrequest
     */
    private function setLastRequestPrivate( $lastrequest ) {
        $this->lastrequest = $lastrequest;
    }

    /**
     *
     * @return string
     */
    public function __getLastResponse() {
        return $this->lastresponse;
    }

    /**
     *
     * @param string $lastresponse
     */
    private function setLastResponsePrivate( $lastresponse ) {
        $this->lastresponse = $lastresponse;
    }

    /**
     *
     * @param boolean $xmlresponse
     */
    public function setXmlResponse( $xmlresponse ) {
        $this->xmlresponse = $xmlresponse;
    }

    /**
     *
     * @return boolean
     */
    public function getXmlResponse() {
        return (boolean)(integer)$this->xmlresponse;
    }

    /**
     *
     * @return array
     */
    protected function getLtiParams() {
        return $this->ltiparams;
    }

    /**
     *
     * @param array $params
     */
    protected function setLtiParams( $params ) {
        if ( !is_null( $this->language ) ) $params["lang"] = $this->language;
        $params = array_merge( $this->ltiparams, $params );

        $params['custom_integration_version'] = $this->getIntegrationVersion();
        $params['custom_plugin_version'] = $this->getPluginVersion();

        $this->ltiparams = $params;
    }

    /**
     *
     * @return string
     */
    private function genUuid() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0x0fff ) | 0x4000,
            mt_rand( 0, 0x3fff ) | 0x8000,
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff )
        );
    }

    /**
     *
     * @return string
     */
    public function getApiBaseUrl() {
        return $this->apibaseurl;
    }

    /**
     *
     * @param string $apibaseurl
     */
    public function setApiBaseUrl($apibaseurl) {
        $this->apibaseurl = $apibaseurl;
    }

    /**
     *
     * @return string
     */
    public function getEndPoint() {
        return $this->endpoint;
    }

    /**
     *
     * @param string $endpoint
     */
    public function setEndPoint($endpoint) {
        $this->endpoint = $endpoint;
        $this->setAction( 'POST' );
        $this->setUrl( $this->getCleanEndPoint() );
    }

    /**
     *
     * @return string
     */
    private function getCleanEndPoint() {
        return preg_replace( '/:80|:443/', '', $this->endpoint );
    }

    /**
     *
     * @return integer
     */
    public function getIntegrationId() {
        return $this->integrationid;
    }

    /**
     *
     * @param integer $integrationid
     */
    public function setIntegrationId($integrationid) {
        $this->integrationid = $integrationid;
    }

    /**
     *
     * @return integer
     */
    public function getAccountId() {
        return $this->accountid;
    }

    /**
     *
     * @param integer $accountid
     */
    public function setAccountId($accountid) {
        $this->accountid = $accountid;
    }

    /**
     *
     * @return string
     */
    public function getSharedKey() {
        return $this->sharedkey;
    }

    /**
     *
     * @param string $sharedkey
     */
    public function setSharedKey($sharedkey) {
        $this->sharedkey = $sharedkey;
    }

    /**
     *
     * @return string
     */
    public function getLogPath() {
        return $this->logpath;
    }

    /**
     *
     * @param string $logpath
     */
    public function setLogPath($logpath) {
        $this->logpath = $logpath;
    }

    /**
     *
     * @return boolean
     */
    public function getDebug() {
        return $this->debug;
    }

    /**
     *
     * @param boolean $debug
     */
    public function setDebug(  $debug ) {
        $this->debug = $debug;
    }

    /**
     *
     * @return string
     */
    public function getProxyHost() {
        return $this->proxyhost;
    }

    /**
     *
     * @param string $proxyhost
     */
    public function setProxyHost($proxyhost) {
        $this->proxyhost = $proxyhost;
    }

    /**
     *
     * @return integer
     */
    public function getProxyPort() {
        return $this->proxyport;
    }

    /**
     *
     * @param integer $proxyport
     */
    public function setProxyPort($proxyport) {
        $this->proxyport = $proxyport;
    }

    /**
     *
     * @return string
     */
    public function getProxyType() {
        return $this->proxytype;
    }

    /**
     *
     * @param string $proxytype
     */
    public function setProxyType($proxytype) {
        $this->proxytype = $proxytype;
    }

    /**
     *
     * @return string
     */
    public function getProxyUser() {
        return $this->proxyuser;
    }

    /**
     *
     * @param string $proxyuser
     */
    public function setProxyUser($proxyuser) {
        $this->proxyuser = $proxyuser;
    }

    /**
     *
     * @return string
     */
    public function getProxyPassword() {
        return $this->proxypassword;
    }

    /**
     *
     * @param string $proxypassword
     */
    public function setProxyPassword($proxypassword) {
        $this->proxypassword = $proxypassword;
    }

    /**
     *
     * @return string
     */
    public function getProxyBypass() {
        return $this->proxybypass;
    }

    public function setProxyBypass($proxybypass) {
        $this->proxybypass = $proxybypass;
    }

    /**
     *
     * @return string
     */
    public function getSSLCertificate() {
        return $this->sslcertificate;
    }

    public function setSSLCertificate($sslcertificate) {
        $this->sslcertificate = $sslcertificate;
    }

    /**
     *
     * @return string
     */
    public function getTestingConnection() {
        return $this->$testingconnection;
    }

    public function setTestingConnection($testingconnection) {
        $this->testingconnection = $testingconnection;
    }

    /**
     *
     * @return string
     */
    public function getPerformanceLog() {
        return $this->performancelog;
    }

    public function setPerformanceLog($performancelog) {
        $this->performancelog = $performancelog;
    }

    /**
     *
     * @param array $params
     */
    private function transportData( $params ) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,            $this->endpoint );
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT,        600);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_POST,           true );
        curl_setopt($ch, CURLOPT_POSTFIELDS,     $params);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2 );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1 );
        if (isset($this->sslcertificate) AND !empty($this->sslcertificate)) {
            curl_setopt($ch, CURLOPT_CAINFO, $this->sslcertificate);
        }
        if (isset($this->proxyhost) AND !empty($this->proxyhost)) {
            curl_setopt($ch, CURLOPT_PROXY, $this->proxyhost.':'.$this->proxyport);
        }
        if (isset($this->proxyuser) AND !empty($this->proxyuser)) {
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, sprintf('%s:%s', $this->proxyuser, $this->proxypassword));
        }

        if ($this->performancelog !== null) {
            $this->performancelog->start_timer();
        }

        $result = curl_exec($ch);

        if ($this->performancelog !== null) {
            $this->performancelog->stop_timer($ch);
        }

        if( $result === false) {
            $err = 'Curl error: ' . curl_error($ch);
            $response = $err;
        } else {
            $response = $result;
        }

        $this->setLastRequestPrivate( print_r( $params, true ) );
        $this->setLastResponsePrivate( $result );

        curl_close($ch);
        return $response;
    }

    /**
     *
     * @return string
     */
    public function getHttpHeaders() {
        return 'POST ' . $this->endpoint;
    }

    /**
     * @ignore
     * @return string
     */
    public function getLanguage() {
        return $this->language;
    }

    /**
     * @ignore
     * @param string $language
     */
    public function setLanguage($language) {
        $this->language = $language;
    }

    public function setIntegrationVersion( $integrationversion = null ) {
        $this->integrationversion = $integrationversion;
    }

    public function getIntegrationVersion() {
        return (empty($this->integrationversion)) ? 'Not provided' : $this->integrationversion;
    }

    public function setPluginVersion( $pluginversion = null ) {
        $this->pluginversion = $pluginversion;
    }

    public function getPluginVersion() {
        return (empty($this->pluginversion)) ? 'Not provided' : $this->pluginversion;
    }

}

//?>
