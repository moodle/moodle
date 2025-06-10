<?php

/**
 * Exception that is thrown for errors in the SDK, covers SOAP Faults and API Errors
 *
 * @package TurnitinSDK
 * @subpackage Exception
 */
class TurnitinSDKException extends Exception {
    private $faultcode;
    private $outputtitle;

    /**
     * @ignore
     *
     * @param string $faultcode
     * @param string $message
     * @param boolean $json
     * @param string $logpath
     */
    public function __construct( $faultcode, $message, $logpath = null ) {
        $this->setFaultCode( $faultcode ); //$this->faultcode = $faultcode;
        $this->setMessage( $message );
        $this->setOutputTitle( 'Turnitin SDK Exception' );
        $logger = new TurnitinLogger( $logpath );
        if ( $logger ) $logger->logError( $this->getOutputTitle() . ': ' . $this->getFaultCode() . ' - ' . $this->getMessage()  );
    }

    /**
     * @ignore
     *
     * @param string $message
     */
    private function setMessage( $message ) {
        $this->message = $message;
    }

    /**
     * @ignore
     *
     * @param string $outputtitle
     */
    private function setOutputTitle( $outputtitle ) {
        $this->outputtitle = $outputtitle;
    }

    /**
     * @ignore
     *
     * @return string
     */
    private function getOutputTitle() {
        return $this->outputtitle;
    }

    /**
     * @ignore
     *
     * @param string $faultcode
     */
    private function setFaultCode( $faultcode ) {
        $this->faultcode = $faultcode;
    }

    /**
     * Get the API Fault Code
     *
     * Possible Fault Code strings:
     * <ul>
     * <li>invaliddata
     * <li>unknownobject
     * <li>nosourcedids
     * <li>unauthorizedrequest
     * <li>deletefailure
     * <li>unknownvocabulary
     * <li>targetreadfailure
     * <li>apiconnecterror
     * <li>Authentication Fault
     * </ul>
     *
     * @return string
     */
    public function getFaultCode() {
        return $this->faultcode;
    }

}

//?>