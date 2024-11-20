<?php

declare(strict_types=1);

namespace SimpleSAML\Error;

use SimpleSAML\Locale\Translate;

/**
 * Class that maps SimpleSAMLphp error codes to translateable strings.
 *
 * @author Hanne Moa, UNINETT AS. <hanne.moa@uninett.no>
 * @package SimpleSAMLphp
 */
class ErrorCodes
{
    /**
     * Fetch all default translation strings for error code titles.
     *
     * @return array A map from error code to error code title
     */
    final public static function defaultGetAllErrorCodeTitles()
    {
        return [
            'ACSPARAMS' => Translate::noop('{errors:title_ACSPARAMS}'),
            'ARSPARAMS' => Translate::noop('No SAML message provided'),
            'AUTHSOURCEERROR' => Translate::noop('{errors:title_AUTHSOURCEERROR}'),
            'BADREQUEST' => Translate::noop('{errors:title_BADREQUEST}'),
            'CASERROR' => Translate::noop('{errors:title_CASERROR}'),
            'CONFIG' => Translate::noop('{errors:title_CONFIG}'),
            'CREATEREQUEST' => Translate::noop('{errors:title_CREATEREQUEST}'),
            'DISCOPARAMS' => Translate::noop('{errors:title_DISCOPARAMS}'),
            'GENERATEAUTHNRESPONSE' => Translate::noop('{errors:title_GENERATEAUTHNRESPONSE}'),
            'INVALIDCERT' => Translate::noop('{errors:title_INVALIDCERT}'),
            'LDAPERROR' => Translate::noop('{errors:title_LDAPERROR}'),
            'LOGOUTINFOLOST' => Translate::noop('{errors:title_LOGOUTINFOLOST}'),
            'LOGOUTREQUEST' => Translate::noop('{errors:title_LOGOUTREQUEST}'),
            'MEMCACHEDOWN' => Translate::noop('Cannot retrieve session data'),
            'METADATA' => Translate::noop('{errors:title_METADATA}'),
            'METADATANOTFOUND' => Translate::noop('{errors:title_METADATANOTFOUND}'),
            'NOACCESS' => Translate::noop('{errors:title_NOACCESS}'),
            'NOCERT' => Translate::noop('{errors:title_NOCERT}'),
            'NORELAYSTATE' => Translate::noop('{errors:title_NORELAYSTATE}'),
            'NOSTATE' => Translate::noop('{errors:title_NOSTATE}'),
            'NOTFOUND' => Translate::noop('{errors:title_NOTFOUND}'),
            'NOTFOUNDREASON' => Translate::noop('{errors:title_NOTFOUNDREASON}'),
            'NOTSET' => Translate::noop('{errors:title_NOTSET}'),
            'NOTVALIDCERT' => Translate::noop('{errors:title_NOTVALIDCERT}'),
            'PROCESSASSERTION' => Translate::noop('{errors:title_PROCESSASSERTION}'),
            'PROCESSAUTHNREQUEST' => Translate::noop('{errors:title_PROCESSAUTHNREQUEST}'),
            'RESPONSESTATUSNOSUCCESS' => Translate::noop('{errors:title_RESPONSESTATUSNOSUCCESS}'),
            'SLOSERVICEPARAMS' => Translate::noop('{errors:title_SLOSERVICEPARAMS}'),
            'SSOPARAMS' => Translate::noop('No SAML request provided'),
            'UNHANDLEDEXCEPTION' => Translate::noop('{errors:title_UNHANDLEDEXCEPTION}'),
            'UNKNOWNCERT' => Translate::noop('{errors:title_UNKNOWNCERT}'),
            'USERABORTED' => Translate::noop('{errors:title_USERABORTED}'),
            'WRONGUSERPASS' => Translate::noop('{errors:title_WRONGUSERPASS}'),
        ];
    }


    /**
     * Fetch all translation strings for error code titles.
     *
     * Extend this to add error codes.
     *
     * @return array A map from error code to error code title
     */
    public static function getAllErrorCodeTitles()
    {
        return self::defaultGetAllErrorCodeTitles();
    }


    /**
     * Fetch all default translation strings for error code descriptions.
     *
     * @return array A map from error code to error code description
     */
    final public static function defaultGetAllErrorCodeDescriptions()
    {
        return [
            'ACSPARAMS' => Translate::noop('{errors:descr_ACSPARAMS}'),
            'ARSPARAMS' => Translate::noop("" .
                "You accessed the Artifact Resolution Service interface, but did not " .
                "provide a SAML ArtifactResolve message. Please note that this endpoint is" .
                " not intended to be accessed directly."),
            'AUTHSOURCEERROR' => Translate::noop('{errors:descr_AUTHSOURCEERROR}'),
            'BADREQUEST' => Translate::noop('{errors:descr_BADREQUEST}'),
            'CASERROR' => Translate::noop('{errors:descr_CASERROR}'),
            'CONFIG' => Translate::noop('{errors:descr_CONFIG}'),
            'CREATEREQUEST' => Translate::noop('{errors:descr_CREATEREQUEST}'),
            'DISCOPARAMS' => Translate::noop('{errors:descr_DISCOPARAMS}'),
            'GENERATEAUTHNRESPONSE' => Translate::noop('{errors:descr_GENERATEAUTHNRESPONSE}'),
            'INVALIDCERT' => Translate::noop('{errors:descr_INVALIDCERT}'),
            'LDAPERROR' => Translate::noop('{errors:descr_LDAPERROR}'),
            'LOGOUTINFOLOST' => Translate::noop('{errors:descr_LOGOUTINFOLOST}'),
            'LOGOUTREQUEST' => Translate::noop('{errors:descr_LOGOUTREQUEST}'),
            'MEMCACHEDOWN' => Translate::noop('{errors:descr_MEMCACHEDOWN}'),
            'METADATA' => Translate::noop('{errors:descr_METADATA}'),
            'METADATANOTFOUND' => Translate::noop('{errors:descr_METADATANOTFOUND}'),
            'NOACCESS' => Translate::noop('{errors:descr_NOACCESS}'),
            'NOCERT' => Translate::noop('{errors:descr_NOCERT}'),
            'NORELAYSTATE' => Translate::noop('{errors:descr_NORELAYSTATE}'),
            'NOSTATE' => Translate::noop('{errors:descr_NOSTATE}'),
            'NOTFOUND' => Translate::noop('{errors:descr_NOTFOUND}'),
            'NOTFOUNDREASON' => Translate::noop('{errors:descr_NOTFOUNDREASON}'),
            'NOTSET' => Translate::noop('{errors:descr_NOTSET}'),
            'NOTVALIDCERT' => Translate::noop('{errors:descr_NOTVALIDCERT}'),
            'PROCESSASSERTION' => Translate::noop('{errors:descr_PROCESSASSERTION}'),
            'PROCESSAUTHNREQUEST' => Translate::noop('{errors:descr_PROCESSAUTHNREQUEST}'),
            'RESPONSESTATUSNOSUCCESS' => Translate::noop('{errors:descr_RESPONSESTATUSNOSUCCESS}'),
            'SLOSERVICEPARAMS' => Translate::noop('{errors:descr_SLOSERVICEPARAMS}'),
            'SSOPARAMS' => Translate::noop("" .
                "You accessed the Single Sign On Service interface, but did not provide a " .
                "SAML Authentication Request. Please note that this endpoint is not " .
                "intended to be accessed directly."),
            'UNHANDLEDEXCEPTION' => Translate::noop('{errors:descr_UNHANDLEDEXCEPTION}'),
            'UNKNOWNCERT' => Translate::noop('{errors:descr_UNKNOWNCERT}'),
            'USERABORTED' => Translate::noop('{errors:descr_USERABORTED}'),
            'WRONGUSERPASS' => Translate::noop('{errors:descr_WRONGUSERPASS}'),
        ];
    }

    /**
     * Fetch all translation strings for error code descriptions.
     *
     * Extend this to add error codes.
     *
     * @return array A map from error code to error code description
     */
    public static function getAllErrorCodeDescriptions()
    {
        return self::defaultGetAllErrorCodeDescriptions();
    }


    /**
     * Get a map of both errorcode titles and descriptions
     *
     * Convenience-method for template-callers
     *
     * @return array An array containing both errorcode maps.
     */
    public static function getAllErrorCodeMessages()
    {
        return [
            'title' => self::getAllErrorCodeTitles(),
            'descr' => self::getAllErrorCodeDescriptions(),
        ];
    }


    /**
     * Fetch a translation string for a title for a given error code.
     *
     * @param string $errorCode The error code to look up
     *
     * @return string A string to translate
     */
    public static function getErrorCodeTitle($errorCode)
    {
        if (array_key_exists($errorCode, self::getAllErrorCodeTitles())) {
            $errorCodeTitles = self::getAllErrorCodeTitles();
            return $errorCodeTitles[$errorCode];
        } else {
            return Translate::addTagPrefix($errorCode, 'title_');
        }
    }


    /**
     * Fetch a translation string for a description for a given error code.
     *
     * @param string $errorCode The error code to look up
     *
     * @return string A string to translate
     */
    public static function getErrorCodeDescription($errorCode)
    {
        if (array_key_exists($errorCode, self::getAllErrorCodeTitles())) {
            $errorCodeDescriptions = self::getAllErrorCodeDescriptions();
            return $errorCodeDescriptions[$errorCode];
        } else {
            return Translate::addTagPrefix($errorCode, 'descr_');
        }
    }


    /**
     * Get both title and description for a specific error code
     *
     * Convenience-method for template-callers
     *
     * @param string $errorCode The error code to look up
     *
     * @return array An array containing both errorcode strings.
     */
    public static function getErrorCodeMessage($errorCode)
    {
        return [
            'title' => self::getErrorCodeTitle($errorCode),
            'descr' => self::getErrorCodeDescription($errorCode),
        ];
    }
}
