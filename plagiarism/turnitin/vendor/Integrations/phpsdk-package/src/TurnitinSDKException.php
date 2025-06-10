<?php

namespace Integrations\PhpSdk;

/**
 * Exception that is thrown for errors in the SDK, covers SOAP Faults and API Errors
 * @package TurnitinSDK
 * @subpackage Exception
 */
class TurnitinSDKException extends \Exception
{
    private $faultcode;
    private $outputtitle;

    const TRUNCATE_LENGTH = 5000;

    /**
     * @ignore
     * @param string $faultcode
     * @param string $message
     * @param string $logpath
     * @param Soap $soap
     */
    public function __construct($faultcode, $message, $logpath = null, $soap = null)
    {
        $this->setFaultCode($faultcode);
        $this->setMessage($message);
        $this->setOutputTitle('Turnitin SDK Exception');
        $logger = new Logger($logpath);
        if ($logger) {
            $logger->logError($this->getOutputTitle() . ': ' . $this->getFaultCode() . ' - ' . $this->getMessage());
        }
        # If the $soap->logresponse boolean has been set, we log out the request and response to the error_log
        # This is used primarily to log out SoapFaults such as 'looks like we got no XML document'
        if (!is_null($soap) && !is_null($soap->logresponse) && $faultcode != 'Authentication Fault') {
            $this->logResponse($soap);
        }
    }

    /**
     * @ignore
     * @param $soap
     */
    private function logResponse($soap)
    {
        $separator = '-----------------------------------';
        error_log(
            $this->getOutputTitle() .
            "\n$separator\nRequest:\n$separator\n" . $this->truncateLog($soap->httprequest, self::TRUNCATE_LENGTH) .
            "\n$separator\nResponse:\n$separator\n" . $this->truncateLog($soap->httpresponse, self::TRUNCATE_LENGTH)
        );
    }

    /**
     * @ignore
     * @param $message
     * @param $length
     * @return bool|string
     */
    public function truncateLog($message, $length)
    {
        $truncated = substr($message, 0, $length);
        $message_length = strlen($message);
        if ($length < $message_length) {
            $truncated = "$truncated (truncated $message_length to $length chars)";
        }
        return $truncated;
    }

    /**
     * @ignore
     * @param string $message
     */
    private function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @ignore
     * @param string $outputtitle
     */
    private function setOutputTitle($outputtitle)
    {
        $this->outputtitle = $outputtitle;
    }

    /**
     * @ignore
     * @return string
     */
    private function getOutputTitle()
    {
        return $this->outputtitle;
    }

    /**
     * @ignore
     * @param string $faultcode
     */
    private function setFaultCode($faultcode)
    {
        $this->faultcode = $faultcode;
    }

    /**
     * Get the API Fault Code
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
     * @return string
     */
    public function getFaultCode()
    {
        return $this->faultcode;
    }
}
