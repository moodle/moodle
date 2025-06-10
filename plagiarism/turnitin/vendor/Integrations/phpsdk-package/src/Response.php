<?php

namespace Integrations\PhpSdk;

/**
 * Response object containing data parsed from the API response
 * 
 * @package TurnitinSDK
 * @subpackage APIResponse
 */
class Response {
    private $user;
    private $users;
    private $assignment;
    private $assignments;
    private $class;
    private $classes;
    private $membership;
    private $memberships;
    private $submission;
    private $submissions;
    private $messagerefid;
    private $messageid;
    private $status;
    private $statuscode;
    private $description;
    private $domobject;
    private $requestdomobject;

    /**
     * @ignore
     * @param Soap $soap
     * @throws TurnitinSDKException
     */
    public function __construct( $soap ) {
        $this->domobject = new \DomDocument();
        $this->requestdomobject = new \DomDocument();
        $logger = new Logger( $soap->getLogPath() );
        if ( $logger ) $logger->logInfo( $soap->getHttpHeaders() . PHP_EOL . $soap->__getLastRequest() );
        if ( $soap->getDebug() ) $this->outputDebug( $soap->__getLastRequest(), 'Request Message', $soap->getHttpHeaders() );
        @$this->requestdomobject->loadXML( $soap->__getLastRequest() );
        if ( $logger ) $logger->logInfo( $soap->__getLastResponse() );
        if ( $soap->getDebug() ) $this->outputDebug( $soap->__getLastResponse(), 'Response Message' );
        if ( ($load = @$this->domobject->loadXML( $soap->__getLastResponse() )) === false ) {
            throw new TurnitinSDKException( 'responsexmlerror', 'XML Response could not be parsed', $soap->getLogPath() );
        }
        $this->setMessageId( @$this->domobject->getElementsByTagName( 'imsx_messageIdentifier' )->item(0)->nodeValue );
        $this->setStatus( @$this->domobject->getElementsByTagName( 'imsx_severity' )->item(0)->nodeValue );
        if ( is_null( $this->getStatus() ) ) $this->setStatus( $this->domobject->getElementsByTagName( 'status' )->item(0)->nodeValue );
        if ( isset( $this->domobject->getElementsByTagName( 'imsx_codeMinorFieldValue' )->item(0)->nodeValue ) ) {
            $this->setStatusCode( @$this->domobject->getElementsByTagName( 'imsx_codeMinorFieldValue' )->item(0)->nodeValue );
        } else {
            $this->setStatus( 'status' );
            $this->setStatusCode( $this->domobject->getElementsByTagName( 'status' )->item(0)->nodeValue );
        }
        $this->setDescription( @$this->domobject->getElementsByTagName( 'imsx_description' )->item(0)->nodeValue );
        if ( is_null( $this->getDescription() ) ) $this->setDescription( $this->domobject->getElementsByTagName( 'message' )->item(0)->nodeValue );
        $this->setMessageRefId( @$this->domobject->getElementsByTagName( 'imsx_messageRefIdentifier' )->item(0)->nodeValue );
        
    }
    
    /**
     * @ignore
     * Output the request/response to screen for debugging purposes
     * 
     * @param string $message
     * @param string $title
     */
    private function outputDebug( $message, $title, $headers = '' ) {
        $style = 'font-size: small; display: block; padding: 4px; border: 1px solid black; background-color: #EFEFEF;';
        echo '<pre style="' . $style . '"><b>' . $title . '</b><hr />' . str_replace( ',', ','.PHP_EOL, $headers ) . PHP_EOL . htmlspecialchars( $this->xmlPretty( $message ) ) . '</pre>';
    }
    
    /**
     * @ignore
     * Formats the XML into a more human readable form to use in debugging output
     * 
     * @param string $xml
     * @return string
     */
    private function xmlPretty( $xml ) {
        
        try {
            @$xml_obj = new \SimpleXMLElement( $xml );
        } catch ( \Exception $e ) {
            return $xml;
        }
        
        $level = 4;
        $indent = 0;
        $pretty = array();

        $xml = explode( "\n", preg_replace( '/>\s*</', ">\n<", $xml_obj->asXML() ) );

        if ( count( $xml ) && preg_match( '/^<\?\s*xml/', $xml[0] ) ) {
            $pretty[] = array_shift( $xml );
        }

        foreach ( $xml as $el ) {
            if ( preg_match( '/^<([\w])+[^>\/]*>$/U', $el ) ) {
                $pretty[] = str_repeat( ' ', $indent ) . $el;
                $indent += $level;
            } else {
                if ( preg_match( '/^<\/.+>$/', $el ) ) {
                    $indent -= $level;
                }
                // @codeCoverageIgnoreStart
                if ( $indent < 0 ) {
                    $indent += $level;
                }
                // @codeCoverageIgnoreEnd
                $pretty[] = str_repeat( ' ', $indent ) . $el;
            }
        }
     
        $xml = implode( "\n", $pretty );
        return $xml;

    }

    /**
     * @ignore
     * Set the Message Ref Id from the API response.
     * 
     * @param string $messagerefid
     */
    private function setMessageRefId( $messagerefid ) {
        $this->messagerefid = $messagerefid;
    }

    /**
     * Get the API Response Message Reference Id
     * 
     * The Message Id from the request echoed back in the response,
     * logged on Turnitin for use in the event of error tracking.
     * 
     * @return string
     */
    public function getMessageRefId() {
        return $this->messagerefid;
    }

    /**
     * @ignore
     * Set the Message Id from the response
     * 
     * @param string $messageid
     */
    private function setMessageId( $messageid ) {
        $this->messageid = $messageid;
    }

    /**
     * Get the response Message Id
     * 
     * Useful for debugging, logged on the Turnitin side for use in error tracking.
     * 
     * @return string
     */
    public function getMessageId() {
        return $this->messageid;
    }

    /**
     * @ignore
     * Sets the status from the Response message
     * 
     * @param string $status
     */
    private function setStatus( $status ) {
        $this->status = $status;
    }

    /**
     * Get the status from the API response
     * 
     * @return string
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * @ignore
     * Set the API response status code.
     * 
     * @param string $statuscode
     */
    private function setStatusCode( $statuscode ) {
        $this->statuscode = $statuscode;
    }

    /**
     * Get the API response status code.
     * 
     * @return string
     */
    public function getStatusCode() {
        return $this->statuscode;
    }

    /**
     * @ignore
     * Set the API response message description.
     * 
     * @param string $description
     */
    private function setDescription( $description ) {
        $this->description = $description;
    }

    /**
     * Get the API response message description.
     * 
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @ignore
     * Set the TiiUser object in the response.
     * 
     * @param TiiUser $user
     */
    public function setUser( $user ) {
        $this->user = $user;
    }
    
    /**
     * Get the TiiUser object from the API response.
     * 
     * @return TiiUser
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @ignore
     * Set the array of TiiUser objects from the API response.
     * 
     * @param array $users
     */
    public function setUsers( $users ) {
        $this->users = $users;
    }

    /**
     * Get array of TiiUser objects from the API response.
     * 
     * @return array
     */
    public function getUsers() {
        return $this->users;
    }

    /**
     * @ignore
     * Set the TiiAssignment object from the API response.
     * 
     * @param TiiAssignment $assignment
     */
    public function setAssignment( $assignment ) {
        $this->assignment = $assignment;
    }

    /**
     * Get the TiiAssignment object from the API response.
     * 
     * @return TiiAssignment
     */
    public function getAssignment() {
        return $this->assignment;
    }
    
    /**
     * @ignore
     * Set an array of TiiAssignment objects from the API response.
     * 
     * @param array $assignments
     */
    public function setAssignments( $assignments ) {
        $this->assignments = $assignments;
    }

    /**
     * Get array of TiiAssignment objects from the API response.
     * 
     * @return array
     */
    public function getAssignments() {
        return $this->assignments;
    }

    /**
     * @ignore
     * Set the TiiClass object from the API response. 
     * 
     * @param TiiClass $class
     */
    public function setClass( $class ) {
        $this->class = $class;
    }

    /**
     * Get the TiiClass object from the API response.
     * 
     * @return TiiClass
     */
    public function getClass() {
        return $this->class;
    }

    /**
     * @ignore
     * Set an array of TiiClass objects from the API response.
     * 
     * @param array $classes
     */
    public function setClasses( $classes ) {
        $this->classes = $classes;
    }

    /**
     * Get array of TiiClass objects from the API response.
     * 
     * @return array
     */
    public function getClasses() {
        return $this->classes;
    }

    /**
     * @ignore
     * Set the TiiMembership object from the API response.
     * 
     * @param TiiMembership $membership
     */
    public function setMembership( $membership ) {
        $this->membership = $membership;
    }

    /**
     * Get the TiiMembership object from the API response.
     * 
     * @return TiiMembership
     */
    public function getMembership() {
        return $this->membership;
    }

    /**
     * @ignore
     * Set an array of TiiMembership objects from the API response.
     * 
     * @param array $memberships
     */
    public function setMemberships( $memberships ) {
        $this->memberships = $memberships;
    }

    /**
     * Get array of TiiMembership objects from API response.
     * 
     * @return array
     */
    public function getMemberships() {
        return $this->memberships;
    }

    /**
     * @ignore
     * Set the TiiSubmission object from the API response.
     * 
     * @param TiiSubmission $submission
     */
    public function setSubmission( $submission ) {
        $this->submission = $submission;
    }

    /**
     * Get the TiiSubmission object from the API response.
     * 
     * @return object
     */
    public function getSubmission() {
        return $this->submission;
    }
    
    /**
     * @ignore
     * Set an array of TiiSubmission objects from the API response.
     * 
     * @param array $submissions
     */
    public function setSubmissions( $submissions ) {
        $this->submissions = $submissions;
    }

    /**
     * Get array of TiiSubmission objects from the API response.
     * 
     * @return array
     */
    public function getSubmissions() {
        return $this->submissions;
    }

    /**
     * @ignore
     * Get a DomDocument object from the API response XML.
     *
     * @return \DomDocument
     */
    public function getDomObject() {
        return $this->domobject;
    }

}

