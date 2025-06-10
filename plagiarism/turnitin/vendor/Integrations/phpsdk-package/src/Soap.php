<?php
/* @ignore
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Integrations\PhpSdk;
use SoapHeader;
use SoapVar;

/**
 * @ignore
 */
class Soap extends \SoapClient
{

    private $integrationid;
    private $accountid;
    private $sharedkey;
    private $logpath;
    private $debug;
    private $httpheaders;
    private $language;

    private $integrationversion;
    private $pluginversion;

    private $proxyhost;
    private $proxyport;
    private $proxytype;
    private $proxyuser;
    private $proxypassword;
    private $proxybypass;
    private $sslcertificate;

    public $httpresponse;
    public $httprequest;
    public $logresponse;

    protected $extensions;

    public static $lislanguage = 'en-US';


    /**
     * @param $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @param $product
     */
    public function setIntegrationId($product)
    {
        $this->integrationid = $product;
    }

    /**
     * @param null $integrationversion
     */
    public function setIntegrationVersion($integrationversion)
    {
        $this->integrationversion = $integrationversion;
    }

    /**
     * @return string
     */
    public function getIntegrationVersion()
    {
        return empty($this->integrationversion) ? 'Not provided' : $this->integrationversion;
    }

    /**
     * @param null $pluginversion
     */
    public function setPluginVersion($pluginversion)
    {
        $this->pluginversion = $pluginversion;
    }

    /**
     * @return string
     */
    public function getPluginVersion()
    {
        return empty($this->pluginversion) ? 'Not provided' : $this->pluginversion;
    }

    /**
     * @return mixed
     * @throws TurnitinSDKException
     */
    public function getIntegrationId()
    {
        if (!isset($this->integrationid)) {
            throw new TurnitinSDKException('Missing Parameter', 'Integration API Product ID Not Set');
        }
        return $this->integrationid;
    }

    /**
     * @param $accountid
     */
    public function setAccountId($accountid)
    {
        $this->accountid = $accountid;
    }

    /**
     * @return mixed
     * @throws TurnitinSDKException
     */
    public function getAccountId()
    {
        if (!isset($this->accountid)) {
            throw new TurnitinSDKException('Missing Parameter', 'Account ID Not Set');
        }
        return $this->accountid;
    }

    /**
     * @param $sharedkey
     */
    public function setSharedKey($sharedkey)
    {
        $this->sharedkey = $sharedkey;
    }

    /**
     * @return mixed
     * @throws TurnitinSDKException
     */
    public function getSharedKey()
    {
        if (!isset($this->sharedkey)) {
            throw new TurnitinSDKException('Missing Parameter', 'Shared Key / Secret Not Set');
        }
        return $this->sharedkey;
    }

    /**
     * @return mixed
     */
    public function getLogPath()
    {
        return $this->logpath;
    }

    /**
     * @param $logpath
     */
    public function setLogPath($logpath)
    {
        $this->logpath = $logpath;
    }

    /**
     * @return mixed
     */
    public function getDebug()
    {
        return $this->debug;
    }

    /**
     * @param $debug
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    /**
     * @param $proxyhost
     */
    public function setProxyHost($proxyhost)
    {
        $this->proxyhost = $proxyhost;
    }

    /**
     * @param $proxyport
     */
    public function setProxyPort($proxyport)
    {
        $this->proxyport = $proxyport;
    }

    /**
     * @param $proxytype
     */
    public function setProxyType($proxytype)
    {
        $this->proxytype = $proxytype;
    }

    /**
     * @param $proxyuser
     */
    public function setProxyUser($proxyuser)
    {
        $this->proxyuser = $proxyuser;
    }

    /**
     * @param $proxypassword
     */
    public function setProxyPassword($proxypassword)
    {
        $this->proxypassword = $proxypassword;
    }

    /**
     * @param $proxybypass
     */
    public function setProxyBypass($proxybypass)
    {
        $this->proxybypass = $proxybypass;
    }

    /**
     * @param $sslcertificate
     */
    public function setSSLCertificate($sslcertificate)
    {
        $this->sslcertificate = $sslcertificate;
    }

    /**
     * @return string
     */
    public function genUuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    public function setHeaders()
    {
        $xml = new \XMLWriter();
        $xml->openMemory();
        $xml->setIndent(true);
        $xml->setIndentString('    ');
        $xml->startElement('ns1:imsx_syncRequestHeaderInfo');
        $xml->startElement('ns1:imsx_version');
        $xml->text('V1.0');
        $xml->endElement();
        $xml->startElement('ns1:imsx_messageIdentifier');
        $xml->text($this->genUuid());
        $xml->endElement();
        $xml->endElement();
        $xml->endElement();
        $var = new SoapVar($xml->outputMemory(), XSD_ANYXML);
        $header = new SoapHeader($this->ns, 'header', $var);
        $this->__setSoapHeaders($header);
    }

    /**
     * @param $location
     * @param $request
     * @return null|string|string[]
     * @throws TurnitinSDKException
     */
    private function getOAuthHeader($location, $request)
    {
        try {
            $oauth = new OAuthSimple($this->getAccountId(), $this->getSharedKey());
            $oauth->setAction("POST");
            $oauth->genBodyHash($request);
            $parse = parse_url($location);
            $port = ((isset($parse["port"]) && ($parse["port"] == '80' || $parse["port"] == '443'))
                    ? '' : !isset($parse["port"])) ? '' : ':' . $parse["port"];
            if (!is_null($this->language)) {
                $oauth->setParameters(array('lang' => $this->language));
            }
            $oauth->setPath($parse["scheme"] . '://' . $parse["host"] . $port . $parse["path"]);
            $header_string = $oauth->getHeaderString();
            $oauth->reset();
        } catch (\Exception $e) {
            throw new TurnitinSDKException(
                'oautherror',
                $e->getMessage(),
                $this->getLogPath()
            );
        }
        return $header_string;
    }

    /**
     * Soap constructor.
     * @param $wsdl
     * @param $options
     * @param null $logpath
     */
    public function __construct($wsdl, $options, $logpath = null)
    {
        $this->setLogPath($logpath);
        $this->setHeaders();
        $this->extensions = array(
                        'StartDate' => 'DateTime',
                        'DueDate' => 'DateTime',
                        'FeedbackReleaseDate' => 'DateTime',
                        'Instructions' => 'String',
                        'AuthorOriginalityAccess' => 'Boolean',
                        'RubricId' => 'Integer',
                        'SubmittedDocumentsCheck' => 'Boolean',
                        'InternetCheck' => 'Boolean',
                        'PublicationsCheck' => 'Boolean',
                        'InstitutionCheck' => 'Boolean',
                        'MaxGrade' => 'Integer',
                        'LateSubmissionsAllowed' => 'Boolean',
                        'SubmitPapersTo' => 'Integer',
                        'ResubmissionRule' => 'Integer',
                        'BibliographyExcluded' => 'Boolean',
                        'QuotedExcluded' => 'Boolean',
                        'SmallMatchExclusionType' => 'Integer',
                        'SmallMatchExclusionThreshold' => 'Integer',
                        'AnonymousMarking' => 'Boolean',
                        'Erater' => 'Boolean',
                        'EraterSpelling' => 'Boolean',
                        'EraterGrammar' => 'Boolean',
                        'EraterUsage' => 'Boolean',
                        'EraterMechanics' => 'Boolean',
                        'EraterStyle' => 'Boolean',
                        'EraterSpellingDictionary' => 'String',
                        'EraterHandbook' => 'Integer',
                        'TranslatedMatching' => 'Boolean',
                        'Anonymous' => 'Boolean',
                        'AnonymousRevealReason' => 'String',
                        'AnonymousRevealUser' => 'String',
                        'AnonymousRevealDateTime' => 'DateTime',
                        'InstructorDefaults' => 'String',
                        'InstructorDefaultsSave' => 'String',
                        'PeermarkAssignments' => 'String',
                        'AllowNonOrSubmissions' => 'Boolean',
                        'Submitter' => 'Integer',
                        'OriginalityReportCapable' => 'Boolean',
                        'AcceptNothingSubmission' => 'Boolean',
                        'EraterPromptId' => 'String',
                        'EraterClientId' => 'String',
                        'EraterUsername' => 'String',
                        'EraterPassword' => 'String'
                        );
        parent::__construct($wsdl, $options);
    }

    /**
     * @param string $request
     * @param string $location
     * @param string $action
     * @param int $version
     * @param bool $one_way
     * @return mixed|string
     * @throws TurnitinSDKException
     */
    public function __doRequest($request, $location, $action, $version, $one_way = false): ?string {

        $http_headers = array(
            'Content-type: text/xml;charset="utf-8"',
            'Accept: text/xml',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
            'SOAPAction: "' . $action . '"',
            'Content-length: ' . strlen($request),
            'X-Integration-Version: ' . $this->getIntegrationVersion(),
            'X-Plugin-Version: ' . $this->getPluginVersion()
        );

        $location .= (!is_null($this->language)) ? '?lang=' . $this->language : '';

        $auth_headers[] = 'Source: ' . $this->getIntegrationId();
        $auth_headers[] = 'Authorization: ' . $this->getOAuthHeader($location, $request);
        $curl_headers = array_merge($http_headers, $auth_headers);

        $curl_handler = curl_init();
        curl_setopt($curl_handler, CURLOPT_URL, $location);
        curl_setopt($curl_handler, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl_handler, CURLOPT_TIMEOUT, 120);
        curl_setopt($curl_handler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_handler, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl_handler, CURLOPT_POST, true);
        curl_setopt($curl_handler, CURLOPT_POSTFIELDS, $request);
        curl_setopt($curl_handler, CURLOPT_HTTPHEADER, $curl_headers);
        curl_setopt($curl_handler, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl_handler, CURLOPT_SSL_VERIFYPEER, 1);
        if (isset($this->sslcertificate) && !empty($this->sslcertificate)) {
            curl_setopt($curl_handler, CURLOPT_CAINFO, $this->sslcertificate);
        }
        if (isset($this->proxyhost) && !empty($this->proxyhost)) {
            curl_setopt($curl_handler, CURLOPT_PROXY, $this->proxyhost . ':' . $this->proxyport);
        }
        if (isset($this->proxyuser) && !empty($this->proxyuser)) {
            curl_setopt($curl_handler, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($curl_handler, CURLOPT_PROXYUSERPWD, sprintf('%s:%s', $this->proxyuser, $this->proxypassword));
        }

        $this->setHttpHeaders(join(PHP_EOL, $curl_headers));

        $result = curl_exec($curl_handler);

        $this->httpresponse = $result;
        $this->httprequest = $request;

        if ($result === false) {
            $logger = new Logger($this->logpath);
            if ($logger) {
                $logger->logError('Curl Error: ' . curl_error($curl_handler));
            }
            throw new TurnitinSDKException('Curl Error', curl_error($curl_handler), $this->logpath);
        } else {
            $response = $result;
        }

        $this->setHeaders();
        curl_close($curl_handler);
        return $response;
    }

    /**
     * @param $httpheaders
     */
    private function setHttpHeaders($httpheaders)
    {
        $this->httpheaders = $httpheaders;
    }

    /**
     * @return mixed
     */
    public function getHttpHeaders()
    {
        return $this->httpheaders;
    }
}

