<?php
/* @ignore
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once( __DIR__.'/oauthsimple.class.php' );
require_once( __DIR__.'/log.class.php' );

/**
 * @ignore
 */
class Soap extends SoapClient {

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

    private $testingconnection;
    private $performancelog;

    protected $extensions;

    public static $lislanguage = 'en-US';

    public function getLanguage() {
        return $this->language;
    }

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

    public function setIntegrationId( $product ) {
        $this->integrationid = $product;
    }

    public function getIntegrationId() {
        if (!isset($this->integrationid)) die('integrationid not set');
        return $this->integrationid;
    }

    public function setAccountId( $accountid ) {
        $this->accountid = $accountid;
    }

    public function getAccountId() {
        if (!isset($this->accountid)) die('accountid not set');
        return $this->accountid;
    }

    public function setSharedKey( $sharedkey ) {
        $this->sharedkey = $sharedkey;
    }

    public function getSharedKey() {
        if (!isset($this->sharedkey)) die('sharedkey not set');
        return $this->sharedkey;
    }

    public function getLogPath() {
        return $this->logpath;
    }

    public function setLogPath( $logpath ) {
        $this->logpath = $logpath;
    }

    public function getDebug() {
        return $this->debug;
    }

    public function setDebug( $debug ) {
        $this->debug = $debug;
    }

    public function getProxyHost() {
        return $this->proxyhost;
    }

    public function setProxyHost($proxyhost) {
        $this->proxyhost = $proxyhost;
    }

    public function getProxyPort() {
        return $this->proxyport;
    }

    public function setProxyPort($proxyport) {
        $this->proxyport = $proxyport;
    }

    public function getProxyType() {
        return $this->proxytype;
    }

    public function setProxyType($proxytype) {
        $this->proxytype = $proxytype;
    }

    public function getProxyUser() {
        return $this->proxyuser;
    }

    public function setProxyUser($proxyuser) {
        $this->proxyuser = $proxyuser;
    }

    public function getProxyPassword() {
        return $this->proxypassword;
    }

    public function setProxyPassword($proxypassword) {
        $this->proxypassword = $proxypassword;
    }

    public function getProxyBypass() {
        return $this->proxybypass;
    }

    public function setProxyBypass($proxybypass) {
        $this->proxybypass = $proxybypass;
    }

    public function getSSLCertificate() {
        return $this->sslcertificate;
    }

    public function setSSLCertificate($sslcertificate) {
        $this->sslcertificate = $sslcertificate;
    }

    public function getTestingConnection() {
        return $this->$testingconnection;
    }

    public function setTestingConnection($testingconnection) {
        $this->testingconnection = $testingconnection;
    }

    public function getPerformanceLog() {
        return $this->performancelog;
    }

    public function setPerformanceLog($performancelog) {
        $this->performancelog = $performancelog;
    }

    public function genUuid() {
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

    public function setHeaders() {
        $xml = new XMLWriter();
        $xml->openMemory();
        $xml->setIndent(true);
        $xml->setIndentString('    ');
        $xml->startElement( 'ns1:imsx_syncRequestHeaderInfo' );
        $xml->startElement( 'ns1:imsx_version' );
        $xml->text( 'V1.0' );
        $xml->endElement();
        $xml->startElement( 'ns1:imsx_messageIdentifier' );
        $xml->text( $this->genUuid() );
        $xml->endElement();
        $xml->endElement();
        $xml->endElement();
        $var = new SoapVar( $xml->outputMemory(), XSD_ANYXML );
        $header = new SoapHeader( $this->ns, 'header', $var );
        $this->__setSoapHeaders( $header );
    }

    private function getOAuthHeader( $location, $request ) {
        try {
            $oauth = new OAuthSimple( $this->getAccountId(), $this->getSharedKey() );
            $oauth->setAction("POST");
            $oauth->genBodyHash( $request );
            $parse = parse_url($location);
            $port = ( ( isset( $parse["port"] ) AND ( $parse["port"] == '80' OR $parse["port"] == '443' ) )
                ? '' : !isset( $parse["port"] ) ) ? '' : ':'.$parse["port"];
            if ( !is_null( $this->language ) ) $oauth->setParameters( array( 'lang' => $this->language ) );
            $oauth->setPath( $parse["scheme"].'://'.$parse["host"].$port.$parse["path"] );
            $header_string = $oauth->getHeaderString();
            $oauth->reset();
        } catch ( Exception $e ) {
            throw new TurnitinSDKException( 'oautherror', $e->getMessage(), $this->getLogPath() );
        }
        return $header_string;
    }

    public function __construct( $wsdl, $options, $logpath = null ) {
        $this->setLogPath( $logpath );
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
        $this->testingconnection = false;
        $this->performancelog = null;
        $this->integrationversion = '';
        $this->pluginversion = '';

        parent::__construct( $wsdl, $options );
    }
    
    public function __doRequest($request, $location, $action, $version, $one_way = null): ?string {

        $http_headers = array(
            'Content-type: text/xml;charset="utf-8"',
            'Accept: text/xml',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
            'SOAPAction: "'.$action.'"',
            'Content-length: '.strlen($request),
            'X-Integration-Version: '.$this->getIntegrationVersion(),
            'X-Plugin-Version: '.$this->getPluginVersion()
        );

        $location .= ( !is_null( $this->language ) ) ? '?lang='.$this->language : '';

        $auth_headers[] = 'Source: '.$this->getIntegrationId();
        $auth_headers[] = 'Authorization: '.$this->getOAuthHeader( $location, $request );
        $curl_headers = array_merge( $http_headers, $auth_headers );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,            $location );
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT,        120);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt($ch, CURLOPT_POST,           true );
        curl_setopt($ch, CURLOPT_POSTFIELDS,     $request);
        curl_setopt($ch, CURLOPT_HTTPHEADER,     $curl_headers );
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

        $this->setHttpHeaders( join( PHP_EOL, $curl_headers ) );

        if ($this->performancelog !== null) {
            $this->performancelog->start_timer();
        }

        $result = curl_exec($ch);

        if ($this->performancelog !== null) {
            $this->performancelog->stop_timer($ch);
        }

        if( $result === false) {
            $logger = new TurnitinLogger( $this->logpath );
            if ( $logger ) $logger->logError( 'Curl Error: ' . curl_error($ch)  );
            throw new TurnitinSDKException( 'Curl Error', curl_error($ch), $this->logpath );
        } else {
            $response = $result;
        }

        $this->setHeaders();
        curl_close($ch);
        return $response;
    }

    private function setHttpHeaders( $httpheaders ) {
        $this->httpheaders = $httpheaders;
    }

    public function getHttpHeaders() {
        return $this->httpheaders;
    }

}

//?>