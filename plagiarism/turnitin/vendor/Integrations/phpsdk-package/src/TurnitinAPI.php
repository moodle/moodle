<?php

namespace Integrations\PhpSdk;

/**
 * The Turnitin SDK Wrapper class. This class will give you access to all available API functionality.
 *
 * @package TurnitinSDK
 * @subpackage APIRequest
 */
class TurnitinAPI {
    public $logpath;

    protected $accountid;
    protected $sharedkey;
    protected $apibaseurl;
    protected $integrationid;
    protected $language;
    protected $debug;

    protected $integrationversion;
    protected $pluginversion;

    protected $proxyhost;
    protected $proxyport;
    protected $proxytype;
    protected $proxyuser;
    protected $proxypassword;
    protected $proxybypass;
    protected $sslcertificate;

    protected $personwsdl;
    protected $coursesectionwsdl;
    protected $lineitemwsdl;
    protected $membershipwsdl;
    protected $resultwsdl;

    const VERSION = '1.2.0';

    /**
     * Instantiate the API object.
     *
     * Enter the account parameters required for authenticating with the Turnitin API
     *
     * <ul>
     * <li><b>Account ID</b><br />The Account ID of the account to access via the API.</li>
     * <li><b>API Base URL</b><br />The base URL of the service url to access via the API<br />
     * e.g. https://sandbox.turnitin.com / https://api.turnitin.com / https://submit.ac.uk</li>
     * <li><b>Secret Key</b><br />The secret key for the integration to connect to.</li>
     * <li><b>Integration Identifier</b><br />The identifier for the integration you want to connect to.</li>
     * <li><b>Language</b> (Optional)<br />The language to be used in API response messages (en_us,fr,es,de,cn,zh_tw,pt_br,th,ja,ko,ms,tr,sv,nl,fi,ar)</li>
     * </ul>
     *
     * @param integer $accountid <br/>
     * @param string $apibaseurl <br/>
     * @param string $secretkey <br/>
     * @param integer $integrationid <br/>
     * @param string $language <br/>
     */
    public function __construct( $accountid, $apibaseurl, $secretkey, $integrationid, $language = null ) {
        $this->accountid  = $accountid;
        $this->sharedkey  = $secretkey;
        $this->apibaseurl = $apibaseurl;
        $this->integrationid = $integrationid;
        $this->language   = $language;

        $this->personwsdl = dirname(__FILE__).'/wsdl/lis-person.wsdl';
        $this->coursesectionwsdl = dirname(__FILE__).'/wsdl/lis-coursesection.wsdl';
        $this->lineitemwsdl = dirname(__FILE__).'/wsdl/lis-lineitem.wsdl';
        $this->membershipwsdl = dirname(__FILE__).'/wsdl/lis-membership.wsdl';
        $this->resultwsdl = dirname(__FILE__).'/wsdl/lis-result.wsdl';
    }

    /**
     * Get the proxy host address
     *
     * @return string
     */
    public function getProxyHost() {
        return $this->proxyhost;
    }

    /**
     * Set the proxy host address
     *
     * @param string $proxyhost
     */
    public function setProxyHost($proxyhost) {
        $this->proxyhost = $proxyhost;
    }

    /**
     * Get the proxy port
     *
     * @return integer
     */
    public function getProxyPort() {
        return $this->proxyport;
    }

    /**
     * Set the proxy port
     *
     * @param integer $proxyport
     */
    public function setProxyPort($proxyport) {
        $this->proxyport = $proxyport;
    }

    /**
     * Get the proxy type
     *
     * @return string
     */
    public function getProxyType() {
        return $this->proxytype;
    }

    /**
     * Set the proxy type
     *
     * @param string $proxytype
     */
    public function setProxyType($proxytype) {
        $this->proxytype = $proxytype;
    }

    /**
     * Get the proxy username
     *
     * @return string
     */
    public function getProxyUser() {
        return $this->proxyuser;
    }

    /**
     * Set the proxy user
     *
     * @param string $proxyuser
     */
    public function setProxyUser($proxyuser) {
        $this->proxyuser = $proxyuser;
    }

    /**
     * Get the proxy password
     *
     * @return string
     */
    public function getProxyPassword() {
        return $this->proxypassword;
    }

    /**
     * Set the proxy password
     *
     * @param string $proxypassword
     */
    public function setProxyPassword($proxypassword) {
        $this->proxypassword = $proxypassword;
    }

    /**
     * Get the proxy bypass
     *
     * @return string
     */
    public function getProxyBypass() {
        return $this->proxybypass;
    }

    /**
     * Set the proxy bypass
     *
     * @return string $proxybypass
     */
    public function setProxyBypass($proxybypass) {
        $this->proxybypass = $proxybypass;
    }

    /**
     * Get the SSL certificate
     *
     * @return string
     */
    public function getSSLCertificate() {
        return $this->sslcertificate;
    }

    /**
     * Set the SSL certificate
     *
     * @param string $sslcertificate
     */
    public function setSSLCertificate($sslcertificate) {
        $this->sslcertificate = $sslcertificate;
    }

    /**
     * Get SDK Version
     *
     * Get the current release version of the SDK
     *
     * @return string
     */
    public function getVersion() {
        return self::VERSION;
    }

    /**
     * Set the directory path for writing communication logs.
     *
     * Sets the path to write API logs to, if set to null or not set then no logs are written at all
     *
     * @param string $logpath
     */
    public function setLogPath( $logpath = null ) {
        $this->logpath = $logpath;
    }

    /**
     * Set the integration version.
     *
     * @param string $integrationversion
     */
    public function setIntegrationVersion( $integrationversion ) {
        $this->integrationversion = $integrationversion;
    }

    /**
     * Get the integration version.
     *
     * @return string
     */
    public function getIntegrationVersion() {
        return $this->integrationversion;
    }

    /**
     * Set the plugin version.
     *
     * @param string $pluginversion
     */
    public function setPluginVersion( $pluginversion ) {
        $this->pluginversion = $pluginversion;
    }

    /**
     * Get the plugin version.
     *
     * @return string
     */
    public function getPluginVersion() {
        return $this->pluginversion;
    }

    /**
     * Set API communication debugging level.
     *
     * Determines whether debugging will be output to the screen, if true then API request and response data will be output to the screen.
     *
     * @param boolean $debug
     */
    public function setDebug( $debug = false ) {
        $this->debug = $debug;
    }

    /**
     * Get API communication debugging level.
     *
     * Gets the value for debugging, defaults to false if debugging has not been set.
     *
     * @return boolean
     */
    public function getDebug() {
        if ( isset( $this->debug ) ) return $this->debug;
        return false;
    }

    /**
     * Get API base URL
     *
     * @return string
     */
    public function getApiBaseUrl() {
        return $this->apibaseurl;
    }

    /**
     * Take an instantiated Soap or LTI worker object and set the required options for it, then return the altered object.
     *
     * @param stdClass $service
     * @return stdClass
     */
    protected function setOptions( $service ) {
        $service->setLogPath( $this->logpath );
        $service->setDebug( $this->debug );
        $service->setAccountId( $this->accountid );
        $service->setSharedKey( $this->sharedkey );
        $service->setIntegrationId( $this->integrationid );
        $service->setLanguage( $this->language );

        if ((isset($this->proxyhost)) AND ($this->proxyhost != '')) {
            $service->setProxyHost( $this->proxyhost );
        }

        if ((isset($this->proxyport)) AND ($this->proxyport != '')) {
            $service->setProxyPort( $this->proxyport );
        }

        if ((isset($this->proxytype)) AND ($this->proxytype != '')) {
            $service->setProxyType( $this->proxytype );
        }

        if ((isset($this->proxyuser)) AND ($this->proxyuser != '')) {
            $service->setProxyUser( $this->proxyuser );
            $service->setProxyPassword( $this->proxypassword );
        }

        if ((isset($this->proxybypass)) AND ($this->proxybypass != '')) {
            $service->setProxyBypass( $this->proxybypass );
        }

        if ((isset($this->sslcertificate)) AND ($this->sslcertificate != '')) {
            $service->setSSLCertificate( $this->sslcertificate );
        }

        if (isset($this->integrationversion) && !empty($this->integrationversion)) {
            $service->setIntegrationVersion( $this->integrationversion);
        }

        if (isset($this->pluginversion) && !empty($this->pluginversion)) {
            $service->setPluginVersion( $this->pluginversion);
        }

        return $service;
    }

    /**
     * Takes the service name and returns the correct SoapClient options for it containing the correct service endpoint:
     *
     * Available service names are:
     * - person (PersonService)
     * - coursesection (CourseSectionService)
     * - lineitem (LineItemService)
     * - membership (MembershipService)
     * - result (ResultService)
     *
     * @param string $service
     * @return array
     */
    private function getServiceOptions( $service ) {
        $returnvalue = array(
            'location' => $this->apibaseurl.'/api/soap/1p0/lis-'.$service,
            'trace' => 1,
            'soap_version' => SOAP_1_1
        );

        // Set proxy parameters (if available).
        if ((isset($this->proxyhost)) && ($this->proxyhost != '')) {
            $returnvalue['proxy_host'] = $this->proxyhost;
        }

        if ((isset($this->proxyport)) && ($this->proxyport != '')) {
            $returnvalue['proxy_port'] = $this->proxyport;
        }

        if ((isset($this->proxyuser)) && ($this->proxyuser != '')) {
            $returnvalue['proxy_login'] = $this->proxyuser;
            $returnvalue['proxy_password'] = $this->proxypassword;
        }

        return $returnvalue;
    }

    /**
     * Create a new User on Turnitin.
     *
     * Takes a {@link TiiUser.html TiiUser} or a
     * {@link TiiPseudoUser.html TiiPseudoUser} object containing the required parameters
     * and returns a {@link Response.html Response} object containing the data from the response.
     *
     * createUser accepts:
     * <ul>
     * <li><b>First Name</b><br />{@link TiiUser.html#setFirstName TiiUser->setFirstName( <i>string</i> FirstName )}</li>
     * <li><b>Last Name</b><br />{@link TiiUser.html#setLastName TiiUser->setLastName( <i>string</i> LastName )}</li>
     * <li><b>Email Address</b><br />{@link TiiUser.html#setEmail TiiUser->setEmail( <i>string</i> EmailAddress )}</li>
     * <li><b>Default Role</b><br />{@link TiiUser.html#setDefaultRole TiiUser->setDefaultRole( <i>string</i> DefaultRole )}</li>
     * </ul>
     * createUser returns a {@link Response.html Response} object which contains a {@link TiiUser.html TiiUser} object:
     * <ul>
     * <li>{@link Response.html Response->getUser()} returns a {@link TiiUser.html TiiUser} object
     *   <ul>
     *   <li><b>User ID</b><br />{@link TiiUser.html#getUserId TiiUser->getUserId()}</li>
     *   </ul>
     * </ul>
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $user = new TiiUser();
     * $user->setFirstName( 'Demo' );
     * $user->setLastName( 'User' );
     * $user->setEmail( 'demo.user@turnitin.com' );
     * $user->setDefaultRole( 'Learner' );
     *
     * $response = $api->createUser( $user );
     * $newuser = $response->getUser();
     * $newuserid = $newuser->getUserId();
     * </pre>
     *
     * @param TiiUser $user
     * @return Response
     */
    public function createUser( $user ) {
        $userSoap = $this->setOptions( new UserSoap( $this->personwsdl, $this->getServiceOptions( 'person' ) ) );
        return $userSoap->createUser( $user );
    }

    /**
     * Create a new Student User on Turnitin.
     *
     * Takes a {@link TiiUser.html TiiUser} or a
     * {@link TiiPseudoUser.html TiiPseudoUser} object containing the required parameters
     * and returns a {@link Response.html Response} object containing the data from the response.
     *
     * Convenience method that adds the Learner role (see: {@link TurnitinAPI.html#createUser createUser})
     *
     * @param TiiUser $user
     * @return Response
     */
    public function createStudentUser( $user ) {
        $userSoap = $this->setOptions( new UserSoap( $this->personwsdl, $this->getServiceOptions( 'person' ) ) );
        $user->setDefaultRole( 'Learner' );
        return $userSoap->createUser( $user );
    }

    /**
     * Create a new Instructor User on Turnitin.
     *
     * Takes a {@link TiiUser.html TiiUser} or a
     * {@link TiiPseudoUser.html TiiPseudoUser} object containing the required parameters
     * and returns a {@link Response.html Response} object containing the data from the response.
     *
     * Convenience method that adds the Instructor role (see: {@link TurnitinAPI.html#createUser createUser})
     *
     * @param TiiUser $user
     * @return Response
     */
    public function createInstructorUser( $user ) {
        $userSoap = $this->setOptions( new UserSoap( $this->personwsdl, $this->getServiceOptions( 'person' ) ) );
        $user->setDefaultRole( 'Instructor' );
        return $userSoap->createUser( $user );
    }

    /**
     * Read a User from Turnitin.
     *
     * Takes a {@link TiiUser.html TiiUser} object containing the required parameters
     * and returns a {@link Response.html Response} containing the data from the response.
     *
     * readUser accepts:
     * <ul>
     * <li><b>User ID</b><br />{@link TiiUser.html TiiUser->setUserId( <i>integer</i> UserId )}</li>
     * </ul>
     * readUser returns a {@link Response.html Response} object which contains a {@link TiiUser.html TiiUser} object:
     * <ul>
     * <li>{@link Response.html Response->getUser()} returns a {@link TiiUser.html TiiUser} object</li>
     * <ul>
     * <li><b>First Name</b><br />{@link TiiUser.html#getFirstName TiiUser->getFirstName()}</li>
     * <li><b>Last Name</b><br />{@link TiiUser.html#getLastName TiiUser->getLastName()}</li>
     * <li><b>Email Address</b><br />{@link TiiUser.html#getEmail TiiUser->getEmail()}</li>
     * <li><b>Default Role</b><br />{@link TiiUser.html#getDefaultRole TiiUser->getDefaultRole()}</li>
     * <li><b>User Messages</b><br />{@link TiiUser.html#getUserMessages TiiUser->getUserMessages()}</li>
     * <li><b>Instructor Rubrics</b><br/>{@link TiiUser.html#getInstructorRubrics TiiUser->getInstructorRubrics()}</li>
     * </ul>
     * </ul>
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $user = new TiiUser();
     * $user->setUserId( 1234 );
     *
     * $response = $api->readUser( $user );
     * $readuser = $response->getUser();
     * $readuseremail = $readuser->getEmail();
     * </pre>
     *
     * @param TiiUser $user
     * @return Response
     */
    public function readUser( $user ) {
        $userSoap = $this->setOptions( new UserSoap( $this->personwsdl, $this->getServiceOptions( 'person' ) ) );
        return $userSoap->readUser( $user );
    }

    /**
     * Read a set of Users from Turnitin.
     *
     * Takes a {@link TiiUser.html TiiUser} object containing the required parameters
     * and returns a {@link Response.html Response} containing the data from the response.
     *
     * readUsers accepts:
     * <ul>
     * <li><b>User IDs</b><br />{@link TiiUser.html#setUserIds TiiUser->setUserIds( <i>array</i> UserIds )}</li>
     * </ul>
     * readUsers returns a {@link Response} object which contains a {@link TiiUser} object:
     * <ul>
     * <li>{@link Response.html#getUsers Response->getUsers()} returns an array of {@link TiiUser.html TiiUser} objects</li>
     * <ul>
     * <li><b>First Name</b><br />{@link TiiUser.html#getFirstName TiiUser->getFirstName()}</li>
     * <li><b>Last Name</b><br />{@link TiiUser.html#getLastName TiiUser->getLastName()}</li>
     * <li><b>Email Address</b><br />{@link TiiUser.html#getEmail TiiUser->getEmail()}</li>
     * <li><b>Default Role</b><br />{@link TiiUser.html#getDefaultRole TiiUser->getDefaultRole()}</li>
     * <li><b>User Messages</b><br />{@link TiiUser.html#getUserMessages TiiUser->getUserMessages()}</li>
     * <li><b>Instructor Rubrics</b><br/>{@link TiiUser.html#getInstructorRubrics TiiUser->getInstructorRubrics()}</li>
     * </ul>
     * </ul>
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $user = new TiiUser();
     * $users = array( 1234, 1235, 1236 );
     * $user->setUserIds( $users );
     *
     * $response = $api->readUsers( $user );
     * $readusers = $response->getUsers();
     * foreach ( $readusers as $readuser )
     *    $readusersemail = $readuser->getEmail();
     * }
     * </pre>
     *
     * @param TiiUser $user
     * @return Response
     */
    public function readUsers( $user ) {
        $userSoap = $this->setOptions( new UserSoap( $this->personwsdl, $this->getServiceOptions( 'person' ) ) );
        return $userSoap->readUsers( $user );
    }

    /**
     * Update a user on Turnitin.
     *
     * Takes a {@link TiiUser.html TiiUser} object containing the required parameters
     * and returns a {@link Response.html Response} object containing the data from the response.
     *
     * Email address can not be updated, if an email address is included then an error will be returned.
     *
     * updateUser accepts:
     * <ul>
     * <li><b>User Id</b><br />{@link TiiUser.html#setUserId TiiUser->setUserId( <i>integer</i> UserId )}</li>
     * <li><b>First Name</b><br />{@link TiiUser.html#setFirstName TiiUser->setFirstName( <i>string</i> FirstName )}</li>
     * <li><b>Last Name</b><br />{@link TiiUser.html#setLastName TiiUser->setLastName( <i>string</i> LastName )}</li>
     * <li><b>Default Role</b><br />{@link TiiUser.html#setDefaultRole TiiUser->setDefaultRole( <i>string</i> DefaultRole )}</li>
     * </ul>
     * updateUser returns a {@link Response.html Response} object, no user data is returned
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $user = new TiiUser();
     * $user->setUserId( 1234 );
     * $user->setFirstName( 'Demo' );
     * $user->setLastName( 'User' );
     * $user->setEmail( 'demo.user@turnitin.com' );
     * $user->setDefaultRole( 'Learner' );
     *
     * $response = $api->updateUser( $user );
     * </pre>
     *
     * @param TiiUser $user
     * @return Response
     */
    public function updateUser( $user ) {
        $userSoap = $this->setOptions( new UserSoap( $this->personwsdl, $this->getServiceOptions( 'person' ) ) );
        return $userSoap->updateUser( $user );
    }

    /**
     * Find a Turnitin User Id based on an email address.
     *
     * Takes a {@link TiiUser.html TiiUser}
     * or a {@link TiiPseudoUser.html TiiPseudoUser} object containing the required parameters
     * and returns a {@link Response.html Response} object containing the data from the response.
     *
     * findUser accepts:
     * <ul>
     * <li><b>Email Address</b><br />{@link TiiUser.html#setEmail TiiUser->setEmail( <i>string</i> Email )}</li>
     * </ul>
     * findUser returns a {@link Response.html Response} object which contains a {@link TiiUser.html TiiUser} object:
     * <ul>
     * <li>{@link Response.html#getUser Response->getUser()} returns a {@link TiiUser.html TiiUser} object</li>
     * <ul>
     * <li><b>User ID</b><br />{@link TiiUser.html#getUserId TiiUser->getUserId()}</li>
     * </ul>
     * </ul>
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $user = new TiiUser();
     * $user->setEmail( 'demo.user@turnitin.com' );
     *
     * $response = $api->findUser( $user );
     * $finduser = $response->getUser();
     * $finduserid = $finduser->getUserId();
     * </pre>
     *
     * @param TiiUser $user
     * @return Response
     */
    public function findUser( $user ) {
        $userSoap = $this->setOptions( new UserSoap( $this->personwsdl, $this->getServiceOptions( 'person' ) ) );
        return $userSoap->findUser( $user );
    }

    /**
     * Create a new Class on Turnitin.
     *
     * Takes a {@link TiiClass.html TiiClass} object containing the required parameters
     * and returns a {@link Response.html Response} object containing the data from the response.
     *
     * createClass accepts:
     * <ul>
     * <li><b>Title</b><br />{@link TiiClass.html#setTitle TiiClass->setTitle( <i>string</i> Title )}</li>
     * <li><b>End Date</b>(Optional)<br />{@link TiiClass.html#setEndDate TiiClass->setEndDate( <i>string</i> EndDate )}</li>
     * </ul>
     * createClass returns a {@link Response} object which contains a {@link TiiClass} object:
     * <ul>
     * <li>{@link Response.html#getClass Response->getClass()} returns a {@link TiiClass.html TiiClass} object</li>
     * <ul>
     * <li><b>Class ID</b><br />{@link TiiClass.html#getClassId TiiClass->getClassId()}</li>
     * </ul>
     * </ul>
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $class = new TiiClass();
     * $class->setTitle( 'Demo Class' );
     * $class->setEndDate( '2014-09-18T09:30:00Z' );
     *
     * $response = $api->createClass( $class );
     * $newclass = $response->getClass();
     * $newclassid = $newclass->getClassId();
     * </pre>
     *
     * @param TiiClass $class
     * @return Response
     */
    public function createClass( $class ) {
        $classSoap = $this->setOptions( new ClassSoap( $this->coursesectionwsdl, $this->getServiceOptions( 'coursesection' ) ) );
        return $classSoap->createClass( $class );
    }

    /**
     * Read a Class on the Turnitin account.
     *
     * Takes a {@link TiiClass.html TiiClass} object containing the required parameters
     * and returns a {@link Response.html Response} object containing the data from the response.
     *
     * readClass accepts:
     * <ul>
     * <li><b>Class Id</b><br />{@link TiiClass.html#setClassId TiiClass->setClassId( <i>integer</i> ClassId )}</li>
     * </ul>
     * readClass returns a {@link Response.html Response} object which contains a {@link TiiClass.html TiiClass} object:
     * <ul>
     * <li>{@link Response.html#getClass Response->getClass()} returns a {@link TiiClass.html TiiClass} object</li>
     * <ul>
     * <li><b>Title</b><br />{@link TiiClass.html#getTitle TiiClass->getTitle()}</li>
     * <li><b>End Date</b><br />{@link TiiClass.html#getEndDate TiiClass->getEndDate()}</li>
     * </ul>
     * </ul>
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $class = new TiiClass();
     * $class->setClassId( 1234 );
     *
     * $response = $api->readClass( $class );
     * $readclass = $response->getClass();
     * $readclass_title = $readclass->getTitle();
     * $readclass_ced = $readclass->getEndDate();
     * </pre>
     *
     * @param TiiClass $class
     * @return Response
     */
    public function readClass( $class ) {
        $classSoap = $this->setOptions( new ClassSoap( $this->coursesectionwsdl, $this->getServiceOptions( 'coursesection' ) ) );
        return $classSoap->readClass( $class );
    }

    /**
     * Read a set of Classes on the Turnitin account.
     *
     * Takes a {@link TiiClass.html TiiClass} object containing the required parameters
     * and returns a {@link Response.html Response} object containing the data from the response.
     *
     * readClasses accepts:
     * <ul>
     * <li><b>Class Ids</b><br />{@link TiiClass.html#setClassIds TiiClass->setClassIds( <i>array</i> ClassIds )}</li>
     * </ul>
     * readClasses returns a {@link Response.html Response} object which contains a {@link TiiClass} object:
     * <ul>
     * <li>{@link Response.html#getClasses Response->getClasses()} returns an array of {@link TiiClass.html TiiClass} objects</li>
     * <ul>
     * <li><b>Title</b><br />{@link TiiClass.html#getTitle TiiClass->getTitle()}</li>
     * <li><b>End Date</b><br />{@link TiiClass.html#getEndDate TiiClass->getEndDate()}</li>
     * </ul>
     * </ul>
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $class = new TiiClass();
     * $classids = array( 1234, 1235, 1236 );
     * $class->setClassIds( $classids );
     *
     * $response = $api->readClasses( $class );
     * $readclasses = $response->getClasses();
     * foreach ( $readclasses as $readclass ) {
     *     $readclass_title = $readclass->getTitle();
     *     $readclass_ced = $readclass->getEndDate();
     * }
     * </pre>
     *
     * @param TiiClass $class
     * @return Response
     */
    public function readClasses( $class ) {
        $classSoap = $this->setOptions( new ClassSoap( $this->coursesectionwsdl, $this->getServiceOptions( 'coursesection' ) ) );
        return $classSoap->readClasses( $class );
    }

    /**
     * Update a Class on the Turnitin account.
     *
     * Takes a {@link TiiClass.html TiiClass} object containing the required parameters
     * and returns a {@link Response.html Response} object containing the data from the response.
     *
     * updateClass accepts:
     * <ul>
     * <li><b>Class ID</b><br />{@link TiiClass.html#setClassId TiiClass->setClassId( <i>integer</i> ClassId )}</li>
     * <li><b>Title</b><br />{@link TiiClass.html#setTitle TiiClass->setTitle( <i>string</i> Title )}</li>
     * <li><b>End Date</b> (Optional)<br />{@link TiiClass.html#setEndDate TiiClass->setEndDate( <i>string</i> EndDate )}</li>
     * </ul>
     * updateClass returns a {@link Response.html Response} object, no class data is returned
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $class = new TiiClass();
     * $class->setClassId( 1234 );
     * $class->setTitle( 'Demo Class' );
     * $class->setEndDate( '2014-09-18T09:30:00Z' );
     *
     * $response = $api->updateClass( $class );
     * </pre>
     *
     * @param TiiClass $class
     * @return Response
     */
    public function updateClass( $class ) {
        $classSoap = $this->setOptions( new ClassSoap( $this->coursesectionwsdl, $this->getServiceOptions( 'coursesection' ) ) );
        return $classSoap->updateClass( $class );
    }

    /**
     * Delete a Class from the Turnitin account.
     *
     * Takes a {@link TiiClass.html TiiClass} object containing the required parameters
     * and returns a {@link Response.html Response} object containing the data from the response.
     *
     * deleteClass accepts:
     * <ul>
     * <li><b>Class Id</b><br />{@link TiiClass.html#setClassId TiiClass->setClassId( <i>integer</i> ClassId )}</li>
     * </ul>
     * deleteClass returns a {@link Response.html Response} object, no class data is returned
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $class = new TiiClass();
     * $class->setClassId( 1234 );
     *
     * $response = $api->deleteClass( $class );
     * </pre>
     *
     * @param TiiClass $class
     * @return Response
     */
    public function deleteClass( $class ) {
        $classSoap = $this->setOptions( new ClassSoap( $this->coursesectionwsdl, $this->getServiceOptions( 'coursesection' ) ) );
        return $classSoap->deleteClass( $class );
    }

    /**
     * Find Class IDs based on class title.
     *
     * Searches based on a case sensitive partial string match against class titles for classes with a Turnitin account
     * and returns a list of Class Ids. Takes a {@link TiiClass.html TiiClass} object containing the required parameters
     * and returns a {@link Response.html Response} object containing the data from the response.
     *
     * findClasses accepts:
     * <ul>
     * <li><b>Title</b><br />{@link TiiClass.html#setTitle TiiClass->setTitle( <i>string</i> Title )}</li>
     * <li><b>Date From</b> (Optional)<br />{@link TiiClass.html#setDateFrom TiiClass->setDateFrom( <i>string</i> DateFrom )}</li>
     * </ul>
     * findClasses returns a {@link Response} object which contains a {@link TiiClass} object:
     * <ul>
     * <li>{@link Response.html#getClass Response->getClass()} returns a {@link TiiClass.html TiiClass} object</li>
     * <ul>
     * <li><b>Class IDs</b> (array)<br />{@link TiiClass.html#getClassIds TiiClass->getClassIds()}</li>
     * </ul>
     * </ul>
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $class = new TiiClass();
     * $class->setTitle( 'Demo' );
     * $class->setDateFrom( '2012-09-01T09:30:00Z' );
     *
     * $response = $api->findClasses( $class );
     * $findclass = $response->getClass();
     * $classids = $findclass->getClassIds();
     * </pre>
     *
     * @param TiiClass $class
     * @return Response
     */
    public function findClasses( $class ) {
        $classSoap = $this->setOptions( new ClassSoap( $this->coursesectionwsdl, $this->getServiceOptions( 'coursesection' ) ) );
        return $classSoap->findClasses( $class );
    }

    /**
     * Create a new Membership on a Class.
     *
     * Takes a {@link TiiMembership.html TiiMembership} object containing the required parameters
     * and returns a {@link Response.html Response} object containing the data from the response.
     *
     * createMembership accepts:
     * <ul>
     * <li><b>Class Id</b><br />{@link TiiMembership.html#setClassId TiiMembership->setClassId( <i>integer</i> ClassId )}</li>
     * <li><b>User Id</b><br />{@link TiiMembership.html#setUserId TiiMembership->setUserId( <i>integer</i> UserId )}</li>
     * <li><b>Role</b><br />{@link TiiMembership.html#setRole TiiMembership->setRole( <i>string</i> Role )}</li>
     * </ul>
     * createMembership returns a {@link Response.html Response} object which contains a {@link TiiMembership.html TiiMembership} object:
     * <ul>
     * <li>{@link Response.html#getMembership Response->getMembership()} returns a {@link TiiMembership.html TiiMembership} object</li>
     * <ul>
     * <li><b>Membership ID</b> (array)<br />{@link TiiMembership.html#getMembershipId TiiMembership->getMembershipId()}</li>
     * </ul>
     * </ul>
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $membership = new TiiMembership();
     * $membership->setClassId( 1234 );
     * $membership->setUserId( 1234 );
     * $membership->setRole( 'Learner' );
     *
     * $response = $api->createMembership( $membership );
     * $newmembership = $response->getMembership();
     * $membershipids = $newmembership->getMembershipId();
     * </pre>
     *
     * @param TiiMembership $membership
     * @return Response
     */
    public function createMembership( $membership ) {
        $membershipSoap = $this->setOptions( new MembershipSoap( $this->membershipwsdl, $this->getServiceOptions( 'membership' ) ) );
        return $membershipSoap->createMembership( $membership );
    }

    /**
     * Create a new Student Membership on a Class.
     *
     * Takes a {@link TiiMembership.html TiiMembership} object containing the required parameters
     * and returns a {@link Response.html Response} object containing the data from the response.
     *
     * Convenience method that adds the Learner role (see: {@link TurnitinAPI.html#createMembership createMembership})
     *
     * @param TiiMembership $membership
     * @return Response
     */
    public function createStudentMembership( $membership ) {
        $membershipSoap = $this->setOptions( new MembershipSoap( $this->membershipwsdl, $this->getServiceOptions( 'membership' ) ) );
        $membership->setRole( 'Learner' );
        return $membershipSoap->createMembership( $membership );
    }

    /**
     * Create a new Instructor Membership on a Class.
     *
     * Takes a {@link TiiMembership.html TiiMembership} object containing the required parameters
     * and returns a {@link Response.html Response} object containing the data from the response.
     *
     * Convenience method that adds the Instructor role (see: {@link TurnitinAPI.html#createMembership createMembership})
     *
     * @param TiiMembership $membership
     * @return Response
     */
    public function createInstructorMembership( $membership ) {
        $membershipSoap = $this->setOptions( new MembershipSoap( $this->membershipwsdl, $this->getServiceOptions( 'membership' ) ) );
        $membership->setRole( 'Instructor' );
        return $membershipSoap->createMembership( $membership );
    }

    /**
     * Read a membership on Turnitin.
     *
     * Takes a {@link TiiMembership.html TiiMembership} object containing the required parameters
     * and returns a {@link Response.html Response} object containing the data from the response.
     *
     * readMembership accepts:
     * <ul>
     * <li><b>Membership Id</b><br />{@link TiiMembership.html#setMembership TiiMembership->setMembershipId( <i>integer</i> MembershipId )}</li>
     * </ul>
     * readMembership returns a {@link Response.html Response} object which contains a {@link TiiMembership.html TiiMembership} object:
     * <ul>
     * <li>{@link Response.html#getMembership Response->getMembership()} returns a {@link TiiMembership.html TiiMembership} object</li>
     * <ul>
     * <li><b>Class Id</b><br />{@link TiiMembership.html#getClassId TiiMembership->getClassId()}</li>
     * <li><b>User Id</b><br />{@link TiiMembership.html#getUserId TiiMembership->getUserId()}</li>
     * <li><b>Role</b><br />{@link TiiMembership.html#getRole TiiMembership->getRole()}</li>
     * </ul>
     * </ul>
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $membership = new TiiMembership();
     * $membership->setMembershipId( 1234 );
     *
     * $response = $api->readMembership( $membership );
     * $readmembership = $response->getMembership();
     * $membershipuserid = $readmembership->getUserId();
     * $membershipclassid = $readmembership->getClassId();
     * $membershiprole = $readmembership->getRole();
     * </pre>
     *
     * @param TiiMembership $membership
     * @return Response
     */
    public function readMembership( $membership ) {
        $membershipSoap = $this->setOptions( new MembershipSoap( $this->membershipwsdl, $this->getServiceOptions( 'membership' ) ) );
        return $membershipSoap->readMembershipSoap( $membership );
    }

    /**
     * Read a set of memberships on Turnitin.
     *
     * Takes a {@link TiiMembership.html TiiMembership} object containing the required parameters
     * and returns a {@link Response.html Response} object containing the data from the response.
     *
     * readMemberships accepts:
     * <ul>
     * <li><b>Membership Ids</b><br />{@link TiiMembership.html#setMembershipIds TiiMembership->setMembershipIds( <i>array</i> MembershipIds )}</li>
     * </ul>
     * readMemberships returns a {@link Response.html Response} object which contains a {@link TiiMembership.html TiiMembership} object:
     * <ul>
     * <li>{@link Response.html#getMemberships Response->getMemberships()} returns an array of {@link TiiMembership.html TiiMembership} objects</li>
     * <ul>
     * <li><b>Class Id</b><br />{@link TiiMembership.html#getClassId TiiMembership->getClassId()}</li>
     * <li><b>User Id</b><br />{@link TiiMembership.html#getUserId TiiMembership->getUserId()}</li>
     * <li><b>Role</b><br />{@link TiiMembership.html#getRole TiiMembership->getRole()}</li>
     * </ul>
     * </ul>
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $membership = new TiiMembership();
     * $memberships = array( 1234, 1235 );
     * $membership->setMembershipIds( $memberships );
     *
     * $response = $api->readMemberships( $membership );
     * $readmemberships = $response->getMemberships();
     * foreach ( $readmemberships as $readmembership ) {
     *     $membershipuserid = $readmembership->getUserId();
     *     $membershipclassid = $readmembership->getClassId();
     *     $membershiprole = $readmembership->getRole();
     * }
     * </pre>
     *
     * @param TiiMembership $membership
     * @return Response
     */
    public function readMemberships( $membership ) {
        $membershipSoap = $this->setOptions( new MembershipSoap( $this->membershipwsdl, $this->getServiceOptions( 'membership' ) ) );
        return $membershipSoap->readMembershipsSoap( $membership );
    }

    /**
     * Delete a membership from the Turnitin class.
     *
     * Takes a {@link TiiMembership.html TiiMembership} object containing the required parameters
     * and returns a {@link Response.html Response} object containing the data from the response.
     *
     * deleteMembership accepts:
     * <ul>
     * <li><b>Membership Id</b><br />{@link TiiMembership.html#setMembershipId TiiMembership->setMembershipId( <i>integer</i> MembershipId )}</li>
     * </ul>
     * deleteMembership returns a {@link Response.html Response} object, no membership data is returned
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $membership = new TiiMembership();
     * $membership->setMembershipId( 1234 );
     *
     * $response = $api->deleteMembership( $membership );
     * </pre>
     *
     * @param TiiMembership $membership
     * @return Response
     */
    public function deleteMembership( $membership ) {
        $membershipSoap = $this->setOptions( new MembershipSoap( $this->membershipwsdl, $this->getServiceOptions( 'membership' ) ) );
        return $membershipSoap->deleteMembershipSoap( $membership );
    }

    /**
     * Find Membership Ids from a Turnitin class based on Class Id.
     *
     * Takes a {@link TiiMembership.html TiiMembership} object containing the required parameters
     * and returns a {@link Response.html Response} object containing the data from the response.
     *
     * findMemberships accepts:
     * <ul>
     * <li><b>Class Id</b><br />{@link TiiMembership.html#setClassId TiiMembership->setClassId( <i>integer</i> ClassId )}</li>
     * </ul>
     * findMemberships returns a {@link Response.html Response} object which contains a {@link TiiMembership.html TiiMembership} object:
     * <ul>
     * <li>{@link Response.html#getMembership Response->getMembership()} returns a {@link TiiMembership.html TiiMembership} object</li>
     * <ul>
     * <li><b>Membership Ids</b> (array)<br /><br />{@link TiiMembership.html#getMembershipIds TiiMembership->getMembershipIds()}</li>
     * </ul>
     * </ul>
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $membership = new TiiMembership();
     * $membership->setClassId( 1234 );
     *
     * $response = $api->findMemberships( $membership );
     * $findmembership = $response->getMembership();
     * $findmembershipids = $findmembership->getMembershipIds();
     * </pre>
     *
     * @param TiiMembership $membership
     * @return Response
     */
    public function findMemberships( $membership ) {
        $membershipSoap = $this->setOptions( new MembershipSoap( $this->membershipwsdl, $this->getServiceOptions( 'membership' ) ) );
        return $membershipSoap->findMemberships( $membership );
    }

    /**
     * Create a new Assignment on Turnitin.
     *
     * Takes a {@link TiiAssignment.html TiiAssignment} object containing the required parameters
     * and returns a {@link Response.html Response} object containing the data from the response.
     *
     * createAssignment accepts:
     * <ul>
     * <li><b>Class Id</b><br />{@link TiiAssignment.html#setClassId TiiAssignment->setClassId( <i>integer</i> ClassId )}</li>
     * <li><b>Title</b><br />{@link TiiAssignment.html#setTitle TiiAssignment->setTitle( <i>string</i> Title )}</li>
     * <li><b>Start Date</b><br />{@link TiiAssignment.html#setStartDate TiiAssignment->setStartDate( <i>string</i> StartDate )}</li>
     * <li><b>Due Date</b><br />{@link TiiAssignment.html#setDueDate TiiAssignment->setDueDate( <i>string</i> DueDate )}</li>
     * <li><b>Feedback Release Date</b> (Optional)<br />{@link TiiAssignment.html#setFeedbackReleaseDate TiiAssignment->setFeedbackReleaseDate( <i>string</i> FeedbackReleaseDate )}</li>
     * <li><b>Instructions</b> (Optional)<br />{@link TiiAssignment.html#setInstructions TiiAssignment->setInstructions( <i>string</i> Instructions )}</li>
     * <li><b>Author Originality Access</b> (Optional)<br />{@link TiiAssignment.html#setAuthorOriginalityAccess TiiAssignment->setAuthorOriginalityAccess( <i>boolean</i> AuthorOriginalityAccess )}</li>
     * <li><b>Rubric Id</b> (Optional)<br/>{@link TiiAssignment.html#setRubricId TiiAssignment->setRubricId( <i>integer</i> RubricId )}</li>
     * <li><b>Submitted Documents Check</b> (Optional)<br />{@link TiiAssignment.html#setSubmittedDocumentsCheck TiiAssignment->setSubmittedDocumentsCheck( <i>boolean</i> SubmittedDocumentsCheck )}</li>
     * <li><b>Internet Check</b> (Optional)<br />{@link TiiAssignment.html#setInternetCheck TiiAssignment->setInternetCheck( <i>boolean</i> InternetCheck )}</li>
     * <li><b>Publications Check</b> (Optional)<br />{@link TiiAssignment.html#setPublicationsCheck TiiAssignment->setPublicationsCheck( <i>boolean</i> PublicationsCheck )}</li>
     * <li><b>Institution Check</b> (Optional)<br />{@link TiiAssignment.html#setInstitutionCheck TiiAssignment->setInstitutionCheck( <i>boolean</i> InstitutionalCheck )}</li>
     * <li><b>Max Grade</b> (Optional)<br />{@link TiiAssignment.html#setMaxGrade TiiAssignment->setMaxGrade( <i>integer</i> MaxGrade )}</li>
     * <li><b>Late Submissions Allowed</b> (Optional)<br />{@link TiiAssignment.html#setLateSubmissionsAllowed TiiAssignment->setLateSubmissionsAllowed( <i>boolean</i> LateSubmissionsAllowed )}</li>
     * <li><b>Submit Papers To</b> (Optional)<br />{@link TiiAssignment.html#setSubmitPapersTo TiiAssignment->setSubmitPapersTo( <i>integer</i> SubmitPapersTo )}</li>
     * <li><b>Resubmission Rule</b> (Optional)<br />{@link TiiAssignment.html#setResubmissionRule TiiAssignment->setResubmissionRule( <i>integer</i> ResubmissionRule )}</li>
     * <li><b>Bibliography Excluded</b> (Optional)<br />{@link TiiAssignment.html#setBibliographyExcluded TiiAssignment->setBibliographyExcluded( <i>boolean</i> BibliographyExcluded )}</li>
     * <li><b>Quoted Excluded</b> (Optional)<br />{@link TiiAssignment.html#setQuotedExcluded TiiAssignment->setQuotedExcluded( <i>boolean</i> QuotedExcluded )}</li>
     * <li><b>Small Match Exclusion Type</b> (Optional)<br />{@link TiiAssignment.html#setSmallMatchExclusionType TiiAssignment->setSmallMatchExclusionType( <i>integer</i> SmallMatchExclusionType )}</li>
     * <li><b>Small Match Exclusion Threshold</b> (Optional)<br />{@link TiiAssignment.html#setSmallMatchExclusionThreshold TiiAssignment->setSmallMatchExclusionThreshold( <i>integer</i> SmallMatchExclusionThreshold )}</li>
     * <li><b>Anonymous Marking</b> (Optional)<br />{@link TiiAssignment.html#setAnonymousMarking TiiAssignment->setAnonymousMarking( <i>boolean</i> AnonymousMarking )}</li>
     * <li><b>Erater</b> (Optional)<br />{@link TiiAssignment.html#setErater TiiAssignment->setErater( <i>boolean</i> Erater )}</li>
     * <li><b>Erater Spelling</b> (Optional)<br />{@link TiiAssignment.html#setEraterSpelling TiiAssignment->setEraterSpelling( <i>boolean</i> EraterSpelling )}</li>
     * <li><b>Erater Grammar</b> (Optional)<br />{@link TiiAssignment.html#setEraterGrammar TiiAssignment->setEraterGrammar( <i>boolean</i> EraterGrammar )}</li>
     * <li><b>Erater Usage</b> (Optional)<br />{@link TiiAssignment.html#setEraterUsage TiiAssignment->setEraterUsage( <i>boolean</i> EraterUsage )}</li>
     * <li><b>Erater Mechanics</b> (Optional)<br />{@link TiiAssignment.html#setEraterMechanics TiiAssignment->setEraterMechanics( <i>boolean</i> EraterMechanics )}</li>
     * <li><b>Erater Style</b> (Optional)<br />{@link TiiAssignment.html#setEraterStyle TiiAssignment->setEraterStyle( <i>boolean</i> EraterStyle )}</li>
     * <li><b>Erater Spelling Dictionary</b> (Optional)<br />{@link TiiAssignment.html#setEraterSpellingDictionary TiiAssignment->setEraterSpellingDictionary( <i>string</i> EraterSpellingDictionary )}</li>
     * <li><b>Erater Handbook</b> (Optional)<br />{@link TiiAssignment.html#setEraterHandbook TiiAssignment->setEraterHandbook( <i>integer</i> EraterHandbook )}</li>
     * <li><b>Translated Matching</b> (Optional)<br />{@link TiiAssignment.html#setTranslatedMatching TiiAssignment->setTranslatedMatching( <i>boolean</i> TranslatedMatching )}</li>
     * <li><b>Instructor Defaults Save</b> (Optional)<br />{@link TiiAssignment.html#setInstructorDefaultsSave TiiAssignment->setInstructorDefaultsSave( <i>integer</i> instructorId )}</li>
     * <li><b>Instructor Defaults</b> (Optional)<br />{@link TiiAssignment.html#setInstructorDefaults TiiAssignment->setInstructorDefaults( <i>integer</i> instructorId )}</li>
     * </ul>
     * createAssignment returns a {@link Response} object which contains a {@link TiiAssignment} object:
     * <ul>
     * <li>{@link Response.html#getAssignment Response->getAssignment()} returns a {@link TiiAssignment.html TiiAssignment} object</li>
     * <ul>
     * <li><b>Assignment ID</b><br />{@link TiiAssignment.html#getAssignmentId TiiAssignment->getAssignmentId()}</li>
     * </ul>
     * </ul>
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $assignment = new TiiAssignment();
     * $assignment->setTitle( 'Test Assignment' );
     * $assignment->setStartDate( '2012-08-12T09:30:00Z' );
     * $assignment->setDueDate( '2013-12-12T09:30:00Z' );
     * $assignment->setDuePost( '2012-12-12T09:30:00Z' );
     * $assignment->setTranslatedMatching( true );
     * $assignment->setMaxGrade( 50 );
     *
     * $response = $api->createAssignment( $assignment );
     * $newassignment = $response->getAssignment();
     * $newassignmentid = $newassignment->getAssignmentId();
     * </pre>
     *
     * @param TiiAssignment $assignment
     * @return Response
     */
    public function createAssignment( $assignment ) {
        $assignmentSoap = $this->setOptions( new AssignmentSoap( $this->lineitemwsdl, $this->getServiceOptions( 'lineitem' ) ) );
        return $assignmentSoap->createAssignment( $assignment );
    }

    /**
     * Read an Assignment on Turnitin.
     *
     * Takes a {@link TiiAssignment.html TiiAssignment} object containing the required parameters
     * and returns a {@link Response.html Response} object containing the data from the response.
     *
     * readAssignment accepts:
     * <ul>
     * <li><b>Assignment Id</b><br />{@link TiiAssignment.html#setAssignmentIds TiiAssignment->setAssignmentId( <i>integer</i> AssignmentId )}</li>
     * </ul>
     * readAssignment returns a {@link Response.html Response} object which contains a {@link TiiAssignment.html TiiAssignment} object:
     * <ul>
     * <li>{@link Response.html#getAssignment Response->getAssignment()} returns a {@link TiiAssignment.html TiiAssignment} object</li>
     * <ul>
     * <li><b>Assignment Id</b><br />{@link TiiAssignment.html#getAssignmentId TiiAssignment->getAssignmentId()}</li>
     * <li><b>Class Id</b><br />{@link TiiAssignment.html#getClasssId TiiAssignment->getClassId()}</li>
     * <li><b>Title</b><br />{@link TiiAssignment.html#getTitle TiiAssignment->getTitle()}</li>
     * <li><b>Start Date</b><br />{@link TiiAssignment.html#getStartDate TiiAssignment->getStartDate()}</li>
     * <li><b>Due Date</b><br />{@link TiiAssignment.html#getDueDate TiiAssignment->getDueDate()}</li>
     * <li><b>Feedback Release Date</b><br />{@link TiiAssignment.html#getFeedbackReleaseDate TiiAssignment->getFeedbackReleaseDate()}</li>
     * <li><b>Instructions</b><br />{@link TiiAssignment.html#getInstructions TiiAssignment->getInstructions()}</li>
     * <li><b>Author Originality Access</b><br />{@link TiiAssignment.html#getAuthorOriginalityAccess TiiAssignment->getAuthorOriginalityAccess()}</li>
     * <li><b>Rubric Id</b> (Optional)<br/>{@link TiiAssignment.html#getRubricId TiiAssignment->getRubricId()}</li>
     * <li><b>Submitted Documents Check</b><br />{@link TiiAssignment.html#getSubmittedDocumentsCheck TiiAssignment->getSubmittedDocumentsCheck()}</li>
     * <li><b>Internet Check</b><br />{@link TiiAssignment.html#getInternetCheck TiiAssignment->getInternetCheck()}</li>
     * <li><b>Publications Check</b><br />{@link TiiAssignment.html#getPublicationsCheck TiiAssignment->getPublicationsCheck()}</li>
     * <li><b>Institutional Check</b><br />{@link TiiAssignment.html#getInstitutionalCheck TiiAssignment->getInstitutionalCheck()}</li>
     * <li><b>Max Grade</b><br />{@link TiiAssignment.html#getMaxGrade TiiAssignment->getMaxGrade()}</li>
     * <li><b>Late Submissions Allowed</b><br />{@link TiiAssignment.html#getLateSubmissionsAllowed TiiAssignment->getLateSubmissionsAllowed()}</li>
     * <li><b>Submit Papers To</b><br />{@link TiiAssignment.html#getSubmitPaperTo TiiAssignment->getSubmitPapersTo()}</li>
     * <li><b>Resubmission Rule</b><br />{@link TiiAssignment.html#getResubmissionRule TiiAssignment->getResubmissionRule()}</li>
     * <li><b>Bibliography Excluded</b><br />{@link TiiAssignment.html#getBibliographyExcluded TiiAssignment->getBibliographyExcluded()}</li>
     * <li><b>Quoted Excluded</b><br />{@link TiiAssignment.html#getQuotedExcluded TiiAssignment->getQuotedExcluded()}</li>
     * <li><b>Small Match Exclusion Type</b><br />{@link TiiAssignment.html#getSmallMatchExclusionType TiiAssignment->getSmallMatchExclusionType()}</li>
     * <li><b>Small Match Exclusion Threshold</b><br />{@link TiiAssignment.html#getSmallMatchExclusionThreshold TiiAssignment->getSmallMatchExclusionThreshold()}</li>
     * <li><b>Anonymous Marking</b><br />{@link TiiAssignment.html#getAnonymousMarking TiiAssignment->getAnonymousMarking()}</li>
     * <li><b>Erater</b><br />{@link TiiAssignment.html#getErater TiiAssignment->getErater()}</li>
     * <li><b>Erater Spelling</b><br />{@link TiiAssignment.html#getEraterSpelling TiiAssignment->getEraterSpelling()}</li>
     * <li><b>Erater Grammar</b><br />{@link TiiAssignment.html#getEraterGrammer TiiAssignment->getEraterGrammar()}</li>
     * <li><b>Erater Usage</b><br />{@link TiiAssignment.html#getEraterUsage TiiAssignment->getEraterUsage()}</li>
     * <li><b>Erater Mechanics</b><br />{@link TiiAssignment.html#getEraterMechanics TiiAssignment->getEraterMechanics()}</li>
     * <li><b>Erater Style</b><br />{@link TiiAssignment.html#getEraterStyle TiiAssignment->getEraterStyle()}</li>
     * <li><b>Erater Spelling Dictionary</b><br />{@link TiiAssignment.html#getEraterSpellingDictionary TiiAssignment->getEraterSpellingDictionary()}</li>
     * <li><b>Erater Handbook</b><br />{@link TiiAssignment.html#getEraterHandbook TiiAssignment->getEraterHandbook()}</li>
     * <li><b>Translated Matching</b><br />{@link TiiAssignment.html#getTranslatedMatching TiiAssignment->getTranslatedMatching()}</li>
     * <li><b>Peermark Assignments</b><br />{@link TiiAssignment.html#getPeermarkAssignments TiiAssignment->getPeermarkAssignments()}</li>
     * </ul>
     * </ul>
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $assignment = new TiiAssignment();
     * $assignment->setAssignmentId( 1234 );
     *
     * $response = $api->readAssignment( $assignment );
     * $readassignment = $response->getAssignment();
     * $readassignmenttitle = $readassignment->getTitle();
     * $readassignmentduedate = $readassignment->getDueDate();
     * </pre>
     *
     * @param TiiAssignment $assignment
     * @return Response
     */
    public function readAssignment( $assignment ) {
        $assignmentSoap = $this->setOptions( new AssignmentSoap( $this->lineitemwsdl, $this->getServiceOptions( 'lineitem' ) ) );
        return $assignmentSoap->readAssignment( $assignment );
    }

    /**
     * Read a set of Assignments from Turnitin.
     *
     * Takes a {@link TiiAssignment} object containing the required parameters
     * and returns a {@link Response} object containing the data from the response.
     *
     * readAssignments accepts:
     * <ul>
     * <li><b>Assignment Ids</b><br />{@link TiiAssignment.html#setAssignmentIds TiiAssignment->setAssignmentIds( <i>array</i> AssignmentIds )}</li>
     * </ul>
     * readAssignments returns a {@link Response.html Response} object which contains a {@link TiiAssignment.html TiiAssignment} object:
     * <ul>
     * <li>{@link Response.html#getAssignments Response->getAssignments()} returns an array of {@link TiiAssignment.html TiiAssignment} objects</li>
     * <ul>
     * <li><b>Assignment Id</b><br />{@link TiiAssignment.html#getAssignmentId TiiAssignment->getAssignmentId()}</li>
     * <li><b>Class Id</b><br />{@link TiiAssignment.html#getClasssId TiiAssignment->getClassId()}</li>
     * <li><b>Title</b><br />{@link TiiAssignment.html#getTitle TiiAssignment->getTitle()}</li>
     * <li><b>Start Date</b><br />{@link TiiAssignment.html#getStartDate TiiAssignment->getStartDate()}</li>
     * <li><b>Due Date</b><br />{@link TiiAssignment.html#getDueDate TiiAssignment->getDueDate()}</li>
     * <li><b>Feedback Release Date</b><br />{@link TiiAssignment.html#getFeedbackReleaseDate TiiAssignment->getFeedbackReleaseDate()}</li>
     * <li><b>Instructions</b><br />{@link TiiAssignment.html#getInstructions TiiAssignment->getInstructions()}</li>
     * <li><b>Author Originality Access</b><br />{@link TiiAssignment.html#getAuthorOriginalityAccess TiiAssignment->getAuthorOriginalityAccess()}</li>
     * <li><b>Rubric Id</b> (Optional)<br/>{@link TiiAssignment.html#getRubricId TiiAssignment->getRubricId()}</li>
     * <li><b>Submitted Documents Check</b><br />{@link TiiAssignment.html#getSubmittedDocumentsCheck TiiAssignment->getSubmittedDocumentsCheck()}</li>
     * <li><b>Internet Check</b><br />{@link TiiAssignment.html#getInternetCheck TiiAssignment->getInternetCheck()}</li>
     * <li><b>Publications Check</b><br />{@link TiiAssignment.html#getPublicationsCheck TiiAssignment->getPublicationsCheck()}</li>
     * <li><b>Institutional Check</b><br />{@link TiiAssignment.html#getInstitutionalCheck TiiAssignment->getInstitutionalCheck()}</li>
     * <li><b>Max Grade</b><br />{@link TiiAssignment.html#getMaxGrade TiiAssignment->getMaxGrade()}</li>
     * <li><b>Late Submissions Allowed</b><br />{@link TiiAssignment.html#getLateSubmissionsAllowed TiiAssignment->getLateSubmissionsAllowed()}</li>
     * <li><b>Submit Papers To</b><br />{@link TiiAssignment.html#getSubmitPaperTo TiiAssignment->getSubmitPapersTo()}</li>
     * <li><b>Resubmission Rule</b><br />{@link TiiAssignment.html#getResubmissionRule TiiAssignment->getResubmissionRule()}</li>
     * <li><b>Bibliography Excluded</b><br />{@link TiiAssignment.html#getBibliographyExcluded TiiAssignment->getBibliographyExcluded()}</li>
     * <li><b>Quoted Excluded</b><br />{@link TiiAssignment.html#getQuotedExcluded TiiAssignment->getQuotedExcluded()}</li>
     * <li><b>Small Match Exclusion Type</b><br />{@link TiiAssignment.html#getSmallMatchExclusionType TiiAssignment->getSmallMatchExclusionType()}</li>
     * <li><b>Small Match Exclusion Threshold</b><br />{@link TiiAssignment.html#getSmallMatchExclusionThreshold TiiAssignment->getSmallMatchExclusionThreshold()}</li>
     * <li><b>Anonymous Marking</b><br />{@link TiiAssignment.html#getAnonymousMarking TiiAssignment->getAnonymousMarking()}</li>
     * <li><b>Erater</b><br />{@link TiiAssignment.html#getErater TiiAssignment->getErater()}</li>
     * <li><b>Erater Spelling</b><br />{@link TiiAssignment.html#getEraterSpelling TiiAssignment->getEraterSpelling()}</li>
     * <li><b>Erater Grammar</b><br />{@link TiiAssignment.html#getEraterGrammer TiiAssignment->getEraterGrammar()}</li>
     * <li><b>Erater Usage</b><br />{@link TiiAssignment.html#getEraterUsage TiiAssignment->getEraterUsage()}</li>
     * <li><b>Erater Mechanics</b><br />{@link TiiAssignment.html#getEraterMechanics TiiAssignment->getEraterMechanics()}</li>
     * <li><b>Erater Style</b><br />{@link TiiAssignment.html#getEraterStyle TiiAssignment->getEraterStyle()}</li>
     * <li><b>Erater Spelling Dictionary</b><br />{@link TiiAssignment.html#getEraterSpellingDictionary TiiAssignment->getEraterSpellingDictionary()}</li>
     * <li><b>Erater Handbook</b><br />{@link TiiAssignment.html#getEraterHandbook TiiAssignment->getEraterHandbook()}</li>
     * <li><b>Translated Matching</b><br />{@link TiiAssignment.html#getTranslatedMatching TiiAssignment->getTranslatedMatching()}</li>
     * <li><b>Peermark Assignments</b><br />{@link TiiAssignment.html#getPeermarkAssignments TiiAssignment->getPeermarkAssignments()}</li>
     * </ul>
     * </ul>
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $assignment = new TiiAssignment();
     * $assignments = array( 1234, 1235, 1236 );
     * $assignment->setAssignmentIds( $assignments );
     *
     * $response = $api->readAssignments( $assignment );
     * $readassignments = $response->getAssignments();
     * foreach ( $readassignments as $readassignment ) {
     *     $readassignmenttitle = $readassignment->getTitle();
     *     $readassignmentduedate = $readassignment->getDueDate();
     * }
     * </pre>
     *
     * @param TiiAssignment $assignment
     * @return Response
     */
    public function readAssignments( $assignment ) {
        $assignmentSoap = $this->setOptions( new AssignmentSoap( $this->lineitemwsdl, $this->getServiceOptions( 'lineitem' ) ) );
        return $assignmentSoap->readAssignments( $assignment );
    }

    /**
     * Update an Assignment on Turnitin.
     *
     * Takes a {@link TiiAssignment.html TiiAssignment} object containing the required parameters
     * and returns a {@link Response.html Response} object containing the data from the response.
     *
     * updateAssignment accepts:
     * <ul>
     * <li><b>Assignment Id</b><br />{@link TiiAssignment.html#setAssignmentId TiiAssignment->setAssignmentId( <i>integer</i> AssignmentId )}</li>
     * <li><b>Class Id</b><br />{@link TiiAssignment.html#setClasssId TiiAssignment->setClassId( <i>integer</i> ClassId )}</li>
     * <li><b>Title</b><br />{@link TiiAssignment.html#setTitle TiiAssignment->setTitle( <i>string</i> Title )}</li>
     * <li><b>Start Date</b><br />{@link TiiAssignment#setStartDate TiiAssignment->setStartDate( <i>string</i> StartDate )}</li>
     * <li><b>Due Date</b><br />{@link TiiAssignment.html#setDueDate TiiAssignment->setDueDate( <i>string</i> DueDate )}</li>
     * <li><b>Feedback Release Date</b> (Optional)<br />{@link TiiAssignment.html#setFeedbackReleaseDate TiiAssignment->setFeedbackReleaseDate( <i>string</i> FeedbackReleaseDate )}</li>
     * <li><b>Instructions</b> (Optional)<br />{@link TiiAssignment.html#setInstructions TiiAssignment->setInstructions( <i>string</i> Instructions )}</li>
     * <li><b>Author Originality Access</b> (Optional)<br />{@link TiiAssignment.html#setAuthorOriginalityAccess TiiAssignment->setAuthorOriginalityAccess( <i>boolean</i> AuthorOriginalityAccess )}</li>
     * <li><b>Rubric Id</b> (Optional)<br/>{@link TiiAssignment.html#setRubricId TiiAssignment->setRubricId( <i>integer</i> RubricId )}</li>
     * <li><b>Submitted Documents Check</b> (Optional)<br />{@link TiiAssignment.html#setSubmittedDocumentsCheck TiiAssignment->setSubmittedDocumentsCheck( <i>boolean</i> SubmittedDocumentsCheck )}</li>
     * <li><b>Internet Check</b> (Optional)<br />{@link TiiAssignment.html#setInternetCheck TiiAssignment->setInternetCheck( <i>boolean</i> InternetCheck )}</li>
     * <li><b>Publications Check</b> (Optional)<br />{@link TiiAssignment.html#setPublicationsCheck TiiAssignment->setPublicationsCheck( <i>boolean</i> PublicationsCheck )}</li>
     * <li><b>Institutional Check</b> (Optional)<br />{@link TiiAssignment.html#setInstitutionalCheck TiiAssignment->setInstitutionalCheck( <i>boolean</i> InstitutionalCheck )}</li>
     * <li><b>Max Grade</b> (Optional)<br />{@link TiiAssignment.html#setMaxGrade TiiAssignment->setMaxGrade( <i>integer</i> MaxGrade )}</li>
     * <li><b>Late Submissions Allowed</b> (Optional)<br />{@link TiiAssignment.html#setLateSubmissionsAllowed TiiAssignment->setLateSubmissionsAllowed( <i>boolean</i> LateSubmissionsAllowed )}</li>
     * <li><b>Submit Papers To</b> (Optional)<br />{@link TiiAssignment.html#setSubmitPaperTo TiiAssignment->setSubmitPapersTo( <i>integer</i> SubmitPapersTo )}</li>
     * <li><b>Resubmission Rule</b> (Optional)<br />{@link TiiAssignment.html#setResubmissionRule TiiAssignment->setResubmissionRule( <i>integer</i> ResubmissionRule )}</li>
     * <li><b>Bibliography Excluded</b> (Optional)<br />{@link TiiAssignment.html#setBibliographyExcluded TiiAssignment->setBibliographyExcluded( <i>boolean</i> BibliographyExcluded )}</li>
     * <li><b>Quoted Excluded</b> (Optional)<br />{@link TiiAssignment.html#setQuotedExcluded TiiAssignment->setQuotedExcluded( <i>boolean</i> QuotedExcluded )}</li>
     * <li><b>Small Match Exclusion Type</b> (Optional)<br />{@link TiiAssignment.html#setSmallMatchExclusionType TiiAssignment->setSmallMatchExclusionType( <i>integer</i> SmallMatchExclusionType )}</li>
     * <li><b>Small Match Exclusion Threshold</b> (Optional)<br />{@link TiiAssignment.html#setSmallMatchExclusionThreshold TiiAssignment->setSmallMatchExclusionThreshold( <i>integer</i> SmallMatchExclusionThreshold )}</li>
     * <li><b>Anonymous Marking</b> (Optional)<br />{@link TiiAssignment.html#setAnonymousMarking TiiAssignment->setAnonymousMarking( <i>boolean</i> AnonymousMarking )}</li>
     * <li><b>Erater</b> (Optional)<br />{@link TiiAssignment.html#setErater TiiAssignment->setErater( <i>boolean</i> Erater )}</li>
     * <li><b>Erater Spelling</b> (Optional)<br />{@link TiiAssignment.html#setEraterSpelling TiiAssignment->setEraterSpelling( <i>boolean</i> EraterSpelling )}</li>
     * <li><b>Erater Grammar</b> (Optional)<br />{@link TiiAssignment.html#setEraterGrammar TiiAssignment->setEraterGrammar( <i>boolean</i> EraterGrammar )}</li>
     * <li><b>Erater Usage</b> (Optional)<br />{@link TiiAssignment.html#setEraterUsage TiiAssignment->setEraterUsage( <i>boolean</i> EraterUsage )}</li>
     * <li><b>Erater Mechanics</b> (Optional)<br />{@link TiiAssignment.html#setEraterMechanics TiiAssignment->setEraterMechanics( <i>boolean</i> EraterMechanics )}</li>
     * <li><b>Erater Style</b> (Optional)<br />{@link TiiAssignment.html#setEraterStyle TiiAssignment->setEraterStyle( <i>boolean</i> EraterStyle )}</li>
     * <li><b>Erater Spelling Dictionary</b> (Optional)<br />{@link TiiAssignment.html#setEraterSpellingDictionary TiiAssignment->setEraterSpellingDictionary( <i>string</i> EraterSpellingDictionary )}</li>
     * <li><b>Erater Handbook</b> (Optional)<br />{@link TiiAssignment.html#setEraterHandbook TiiAssignment->setEraterHandbook( <i>integer</i> EraterHandbook )}</li>
     * <li><b>Translated Matching</b> (Optional)<br />{@link TiiAssignment.html#setTranslatedMatching TiiAssignment->setTranslatedMatching( <i>boolean</i> TranslatedMatching )}</li>
     * </ul>
     * updateAssignment returns a {@link Response.html Response} object, no assignment data is returned
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $assignment = new TiiAssignment();
     * $assignment->setAssignmentId( 1234 );
     * $assignment->setTitle( 'Test Assignment' );
     * $assignment->setStartDate( '2012-08-12T09:30:00Z' );
     * $assignment->setDueDate( '2013-12-12T09:30:00Z' );
     * $assignment->setDuePost( '2012-12-12T09:30:00Z' );
     * $assignment->setTranslatedMatching( true );
     * $assignment->setMaxGrade( 50 );
     *
     * $response = $api->updateAssignment( $assignment );
     * </pre>
     *
     * @param TiiAssignment $assignment
     * @return Response
     */
    public function updateAssignment( $assignment ) {
        $assignmentSoap = $this->setOptions( new AssignmentSoap( $this->lineitemwsdl, $this->getServiceOptions( 'lineitem' ) ) );
        return $assignmentSoap->updateAssignment( $assignment );
    }

    /**
     * Delete an Assignment from Turnitin.
     *
     * Takes a {@link TiiAssignment.html TiiAssignment} object containing the required parameters
     * and returns a {@link Response.html Response} object containing the data from the response.
     *
     * deleteAssignment accepts:
     * <ul>
     * <li><b>Assignment Id</b><br />{@link TiiAssignment.html#setAssignmentId TiiAssignment->setAssignmentId( <i>array</i> AssignmentId )}</li>
     * </ul>
     * deleteAssignment returns a {@link Response.html Response} object, no assignment data is returned
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $assignment = new TiiAssignment();
     * $assignment->setAssignmentId( 1234 );
     *
     * $response = $api->deleteAssignment( $assignment );
     * </pre>
     *
     * @param TiiAssignment $assignment
     * @return Response
     */
    public function deleteAssignment( $assignment ) {
        $assignmentSoap = $this->setOptions( new AssignmentSoap( $this->lineitemwsdl, $this->getServiceOptions( 'lineitem' ) ) );
        return $assignmentSoap->deleteAssignment( $assignment );
    }

    /**
     * Find Assignment Ids from a Turnitin class based on a Class Id.
     *
     * Takes a {@link TiiAssignment.html TiiAssignment} object containing the required parameters
     * and returns a {@link Response.html Response} object containing the data from the response.
     *
     * findAssignments accepts:
     * <ul>
     * <li><b>Class Id</b><br />{@link TiiAssignment.html#setClassId TiiAssignment->setClassId( <i>integer</i> ClassId )}</li>
     * </ul>
     * findAssignments returns a {@link Response.html Response} object which contains a {@link TiiAssignment.html TiiAssignment} object:
     * <ul>
     * <li>{@link Response.html#setAssignment Response->getAssignment()} returns a {@link TiiAssignment.html TiiAssignment} object</li>
     * <ul>
     * <li><b>Assignment Ids</b> (array)<br />{@link TiiAssignment.html#getAssignmentIds TiiAssignment->getAssignmentIds()}</li>
     * </ul>
     * </ul>
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $assignment = new TiiAssignment();
     * $assignment->setClassId( 1234 );
     *
     * $response = $api->findAssignments( $assignment );
     * $findassignment = $response->getAssignment();
     * $findassignmentids = $findassignment->getAssignmentIds();
     * </pre>
     *
     * @param TiiAssignment $assignment
     * @return Response
     */
    public function findAssignments( $assignment ) {
        $assignmentSoap = $this->setOptions( new AssignmentSoap( $this->lineitemwsdl, $this->getServiceOptions( 'lineitem' ) ) );
        return $assignmentSoap->findAssignments( $assignment );
    }

    /**
     * Read Submission data from Turnitin.
     *
     * Takes a {@link TiiSubmission.html TiiSubmission} object containing the required parameters
     * and returns a {@link Response.html Response} object containing the data from the response.
     *
     * readSubmission accepts:
     * <ul>
     * <li><b>Submission Id</b><br />{@link Tiisubmission.html#setSubmissionId TiiSubmission->setSubmissionId( <i>integer</i> SubmissionId )}</li>
     * </ul>
     * readSubmission returns a {@link Response.html Response} object which contains a {@link TiiSubmission.html TiiSubmission} object:
     * <ul>
     * <li>{@link Response.html#getSubmission Response->getSubmission()} returns a {@link TiiSubmission.html TiiSubmission} object</li>
     * <ul>
     * <li><b>Submission Id</b><br />{@link TiiSubmission.html#getSubmissionId TiiSubmission->getSubmissionId()}</li>
     * <li><b>Assignment Id</b><br />{@link TiiSubmission.html#getAssignmentId TiiSubmission->getAssignmentId()}</li>
     * <li><b>Title</b><br />{@link TiiSubmission.html#getTitle TiiSubmission->getTitle()}</li>
     * <li><b>Author User Id</b><br />{@link TiiSubmission.html#getAuthorUserId TiiSubmission->getAuthorUserId()}</li>
     * <li><b>Submitter User Id</b><br />{@link TiiSubmission.html#getSubmitterUserIdUserId TiiSubmission->getSubmitterUserIdUserId()}</li>
     * <li><b>Grade</b><br />{@link TiiSubmission.html#getGrade TiiSubmission->getGrade()}</li>
     * <li><b>Feedback Exists</b><br />{@link TiiSubmission.html#getFeedbackExists TiiSubmission->getFeedbackExists()}</li>
     * <li><b>Overall Similarity</b><br />{@link TiiSubmission.html#getOverallSimilarity TiiSubmission->getOverallSimilarity()}</li>
     * <li><b>Submitted Documents Similarity</b><br />{@link TiiSubmission.html#getSubmittedDocumentsSimilarity TiiSubmission->getSubmittedDocumentsSimilarity()}</li>
     * <li><b>Internet Similarity</b><br />{@link TiiSubmission.html#getInternetSimilarity TiiSubmission->getInternetSimilarity()}</li>
     * <li><b>Publications Similarity</b><br />{@link TiiSubmission.html#getPublicationsSimilarity TiiSubmission->getPublicationsSimilarity()}</li>
     * <li><b>Translated Overall Similarity</b><br />{@link TiiSubmission.html#getTranslatedOverallSimilarity TiiSubmission->getTranslatedOverallSimilarity()}</li>
     * <li><b>Translated Submitted Documents Similarity</b><br />{@link TiiSubmission.html#getTranslatedSubmittedDocumentsSimilarity TiiSubmission->getTranslatedSubmittedDocumentsSimilarity()}</li>
     * <li><b>Translated Internet Similarity</b><br />{@link TiiSubmission.html#getTranslatedInternetSimilarity TiiSubmission->getTranslatedInternetSimilarity()}</li>
     * <li><b>Translated Publications Similarity</b><br />{@link TiiSubmission.html#getTranslatedPublicationsSimilarity TiiSubmission->getTranslatedPublicationsSimilarity()}</li>
     * <li><b>Author Last Viewed Feedback</b><br />{@link TiiSubmission.html#getAuthorLastViewedFeedback TiiSubmission->getAuthorLastViewedFeedback()}</li>
     * <li><b>Voice Comment</b><br />{@link TiiSubmission.html#getVoiceComment TiiSubmission->getVoiceComment()}</li>
     * <li><b>Date Submitted</b><br />{@link TiiSubmission.html#getDate TiiSubmission->getDate()}</li>
     * </ul>
     * </ul>
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $submission = new TiiSubmission();
     * $submission->setSubmissionId( 1234 );
     *
     * $response = $api->readSubmission( $submission );
     * $readsubmission = $response->getSubmission();
     * $readsubmissiontitle = $readsubmission->getTitle();
     * $readsubmissiondate = $readsubmission->getDate();
     * $readsubmissiongrademark = $readsubmission->getGrade();
     * $readsubmissionsimilarity = $readsubmission->getOverallSimilarity();
     * </pre>
     *
     * @param TiiSubmission $submission
     * @return Response
     */
    public function readSubmission( $submission ) {
        $submissionSoap = $this->setOptions( new SubmissionSoap( $this->resultwsdl, $this->getServiceOptions( 'result' ) ) );
        return $submissionSoap->readSubmission( $submission );
    }

    /**
     * Read a set of Submissions from Turnitin.
     *
     * Takes a {@link TiiSubmission.html} object containing the required parameters
     * and returns a {@link Response.html} object containing the data from the response.
     *
     * readSubmissions accepts:
     * <ul>
     * <li><b>Submission Ids</b><br />{@link TiiSubmission.html#setSubmissionIds TiiSubmission->setSubmissionIds( <i>array</i> SubmissionIds )}</li>
     * </ul>
     * readSubmissions returns a {@link Response.html Response} object which contains a {@link TiiSubmission.html TiiSubmission} object:
     * <ul>
     * <li>{@link Response.html#getSubmissions Response->getSubmissions()} returns an array of {@link TiiSubmission.html TiiSubmission} objects</li>
     * <ul>
     * <li><b>Submission Id</b><br />{@link TiiSubmission.html#getSubmissionId TiiSubmission->getSubmissionId()}</li>
     * <li><b>Assignment Id</b><br />{@link TiiSubmission.html#getAssignmentId TiiSubmission->getAssignmentId()}</li>
     * <li><b>Title</b><br />{@link TiiSubmission.html#getTitle TiiSubmission->getTitle()}</li>
     * <li><b>Author User Id</b><br />{@link TiiSubmission.html#getAuthorUserId TiiSubmission->getAuthorUserId()}</li>
     * <li><b>Submitter User Id</b><br />{@link TiiSubmission.html#getSubmitterUserId TiiSubmission->getSubmitterUserId()}</li>
     * <li><b>Grade</b><br />{@link TiiSubmission.html#getGrade TiiSubmission->getGrade()}</li>
     * <li><b>Feedback Exists</b><br />{@link TiiSubmission.html#getFeedbackExists TiiSubmission->getFeedbackExists()}</li>
     * <li><b>Overall Similarity</b><br />{@link TiiSubmission.html#getOverallSimilarity TiiSubmission->getOverallSimilarity()}</li>
     * <li><b>Submitted Documents Similarity</b><br />{@link TiiSubmission.html#getSubmittedDocumentsSimilarity TiiSubmission->getSubmittedDocumentsSimilarity()}</li>
     * <li><b>Internet Similarity</b><br />{@link TiiSubmission.html#getInternetSimilarity TiiSubmission->getInternetSimilarity()}</li>
     * <li><b>Publications Similarity</b><br />{@link TiiSubmission.html#getPublicationsSimilarity TiiSubmission->getPublicationsSimilarity()}</li>
     * <li><b>Translated Overall Similarity</b><br />{@link TiiSubmission.html#getTranslatedOverallSimilarity TiiSubmission->getTranslatedOverallSimilarity()}</li>
     * <li><b>Translated Submitted Documents Similarity</b><br />{@link TiiSubmission.html#getTranslatedSubmittedDocumentsSimilarity TiiSubmission->getTranslatedSubmittedDocumentsSimilarity()}</li>
     * <li><b>Translated Internet Similarity</b><br />{@link TiiSubmission.html#getTranslatedInternetSimilarity TiiSubmission->getTranslatedInternetSimilarity()}</li>
     * <li><b>Translated Publications Similarity</b><br />{@link TiiSubmission.html#getTranslatedPublicationsSimilarity TiiSubmission->getTranslatedPublicationsSimilarity()}</li>
     * <li><b>Author Last Viewed Feedback</b><br />{@link TiiSubmission.html#getAuthorLastViewedFeedback TiiSubmission->getAuthorLastViewedFeedback()}</li>
     * <li><b>Voice Comment</b><br />{@link TiiSubmission.html#getVoiceComment TiiSubmission->getVoiceComment()}</li>
     * <li><b>Date Submitted</b><br />{@link TiiSubmission.html#getDate TiiSubmission->getDate()}</li>
     * </ul>
     * </ul>
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $submission = new TiiSubmission();
     * $submissions = array( 1234, 1235, 1236 );
     * $submission->setSubmissionIds( $submissions );
     *
     * $response = $api->readSubmissions( $submission );
     * $readsubmission = $response->getSubmissions();
     * foreach ( $readsubmissions as $readsubmission ) {
     *     $readsubmissiontitle = $readsubmission->getTitle();
     *     $readsubmissiondate = $readsubmission->getDate();
     *     $readsubmissiongrademark = $readsubmission->getGrade();
     *     $readsubmissionsimilarity = $readsubmission->getOverallSimilarity();
     * }
     * </pre>
     *
     * @param TiiSubmission $submission
     * @return Response
     */
    public function readSubmissions( $submission ) {
        $submissionSoap = $this->setOptions( new SubmissionSoap( $this->resultwsdl, $this->getServiceOptions( 'result' ) ) );
        return $submissionSoap->readSubmissions( $submission );
    }

    /**
     * Update a Submission on Turnitin.
     *
     * Takes a {@link TiiSubmission.html TiiSubmission} object containing the required parameters
     * and returns a {@link Response.html Response} object containing the data from the response.
     *
     * updateSubmission accepts:
     * <ul>
     * <li><b>Submission Id</b><br />{@link TiiSubmission.html#setSubmissionId TiiSubmission->setSubmissionId( <i>integer</i> SubmissionId )}</li>
     * <li><b>Title</b> (Optional)<br />{@link TiiSubmission.html#setTitle TiiSubmission->setTitle( <i>string</i> Title )}</li>
     * <li><b>Anonymous Reveal Reason</b> (Optional)<br />{@link TiiSubmission.html#setAnonymousRevealReason TiiSubmission->setAnonymousRevealReason( <i>string</i> AnonymousRevealReason )}</li>
     * <li><b>Anonymous Reveal User</b> (Optional)<br />{@link TiiSubmission.html#setAnonymousRevealUser TiiSubmission->setAnonymousRevealUser( <i>string</i> AnonymousRevealUser )}</li>
     * </ul>
     * updateSubmission returns a {@link Response.html Response} object which contains a {@link TiiSubmission.html TiiSubmission} object:
     * <ul>
     * <li>{@link Response.html#getSubmission Response->getSubmission()} returns a {@link TiiSubmission.html TiiSubmission} object</li>
     * </ul>
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $submission = new TiiSubmission();
     * $submission->setSubmissionId( 1234 );
     * $submission->setTitle( 'Test Submission' );
     * $submission->setAnonymousRevealReason( 'Example Reason' );
     * $submission->setAnonymousRevealUser( 1234 );
     *
     * $response = $api->updateSubmission( $submission );
     * $newsubmission = $response->getSubmission();
     * </pre>
     *
     * @param TiiSubmission $submission
     * @return Response
     */
    public function updateSubmission( $submission ) {
        $submissionSoap = $this->setOptions( new SubmissionSoap( $this->resultwsdl, $this->getServiceOptions( 'result' ) ) );
        return $submissionSoap->updateSubmission( $submission );
    }

    /**
     * Delete Submission from Turnitin.
     *
     * Takes a {@link TiiSubmission.html TiiSubmission} object containing the required parameters
     * and returns a {@link Response.html Response} object containing the data from the response.
     *
     * deleteSubmission accepts:
     * <ul>
     * <li><b>Submission Id</b><br />{@link Tiisubmission.html#setSubmissionId TiiSubmission->setSubmissionId( <i>integer</i> SubmissionId )}</li>
     * </ul>
     * deleteSubmission returns a {@link Response.html Response} object, no submission data is returned
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $submission = new TiiSubmission();
     * $submission->setSubmissionId( 1234 );
     *
     * $response = $api->deleteSubmission( $submission );
     * </pre>
     *
     * @param TiiSubmission $submission
     * @return Response
     */
    public function deleteSubmission( $submission ) {
        $submissionSoap = $this->setOptions( new SubmissionSoap( $this->resultwsdl, $this->getServiceOptions( 'result' ) ) );
        return $submissionSoap->deleteSubmission( $submission );
    }

    /**
     * Find Submission Ids on a Turnitin Assignment.
     *
     * Takes a {@link TiiSubmission.html TiiSubmission} object containing the required parameters
     * and returns a {@link Response.html Response} object containing the data from the response.
     *
     * findSubmissions accepts:
     * <ul>
     * <li><b>Assignment Id</b><br />{@link TiiSubmission.html#setAssignmentId TiiSubmission->setAssignmentId( <i>integer</i> AssignmentId )}</li>
     * <li><b>Date From</b> (Optional)<br />{@link TiiSubmission.html#setDateFrom TiiSubmission->setDateFrom( <i>string</i> DateFrom )}</li>
     * </ul>
     * findSubmissions returns a {@link Response.html Response} object which contains a {@link TiiSubmission.html TiiSubmission} object:
     * <ul>
     * <li>{@link Response.html#setSubmission Response->getSubmission()} returns a {@link TiiSubmission.html TiiSubmission} object</li>
     * <ul>
     * <li><b>Submission Ids</b> (array)<br />{@link TiiSubmission.html#getSubmissionIds TiiSubmission->getSubmissionIds()}</li>
     * </ul>
     * </ul>
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $submission = new TiiSubmission();
     * $submission->setAssignmentId( 1234 );
     * $submission->setDateFrom( '2012-09-12T09:00:00Z' );
     *
     * $response = $api->findSubmissions( $submission );
     * $findsubmission = $response->getSubmission();
     * $findsubmissionids = $findsubmission->getSubmissionIds();
     * </pre>
     *
     * @param TiiSubmission $submission
     * @return Response
     */
    public function findSubmissions( $submission ) {
        $submissionSoap = $this->setOptions( new SubmissionSoap( $this->resultwsdl, $this->getServiceOptions( 'result' ) ) );
        if ( !is_null( $submission->getDateFrom() ) ) {
            return $submissionSoap->findRecentSubmissions( $submission );
        } else {
            return $submissionSoap->findSubmissions( $submission );
        }
    }

    /**
     * Create a new Submission on Turnitin (Submit).
     *
     * Takes a {@link TiiSubmission.html TiiSubmission} object containing the required parameters
     * and returns a {@link Response.html Response} object containing the data from the response.
     *
     * createSubmission accepts:
     * <ul>
     * <li><b>Submitter User Id</b><br />{@link TiiSubmission.html#setSubmitterUserId TiiSubmission->setSubmitterUserId( <i>integer</i> SubmitterUserId )}</li>
     * <li><b>Assignment Id</b><br />{@link TiiSubmission.html#setAssignmentId TiiSubmission->setAssignmentId( <i>integer</i> AssignmentId )}</li>
     * <li><b>Role</b><br />{@link TiiSubmission.html#setRole TiiSubmission->setRole( <i>string</i> Role )}</li>
     * <li><b>Title</b><br />{@link TiiSubmission.html#setTitle TiiSubmission->setTitle( <i>string</i> Title )}</li>
     * <li><b>Author User Id</b>* (Optional)<br />{@link TiiSubmission.html#setAuthorUserId TiiSubmission->setAuthorUserId( <i>integer</i> AuthorUserId )}</li>
     * <li><b>Submission Data Path</b><br />{@link TiiSubmission.html#setSubmissionDataPath TiiSubmission->setSubmissionDataPath( <i>string</i> SubmissionDataPath )}<br />
     * <b>[ <i>OR</i> ]</b><br />
     * <b>Submission Data Text</b><br />{@link TiiSubmission.html#setSubmissionDataText TiiSubmission->setSubmissionDataText( <i>string</i> SubmissionDataText )}</li>
     * </ul>
     * createSubmission returns a {@link Response.html Response} object which contains a {@link TiiAssignment.html TiiAssignment} object:
     * <ul>
     * <li>{@link Response.html#getSubmission Response->getSubmission()} returns a {@link TiiSubmission.html TiiSubmission} object</li>
     * <ul>
     * <li><b>Submission ID</b><br />{@link TiiSubmission.html#getSubmissionId TiiSubmission->getSubmissionId()}</li>
     * <li><b>Text Extract</b><br />{@link TiiSubmission.html#getTextExtract TiiSubmission->getTextExtract()}</li>
     * </ul>
     * </ul>
     *
     * <i>* Required if submitting as Instructor on behalf of the Author</i>
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $submission = new TiiSubmission();
     * $submission->setAssignmentId( 1234 );
     * $submission->setTitle( 'Test Submission' );
     * $submission->setSubmitterUserId( 1234 );
     * $submission->setRole( 'Learner' );
     * $submission->setSubmissionDataPath( '/path/to/submission.txt' );
     *
     * $response = $api->createSubmission( $submission );
     * $newsubmission = $response->getSubmission();
     * $newsubmissionid = $newsubmission->getSubmissionId();
     * $newsubmissionextract = $newsubmission->getTextExtract();
     * </pre>
     *
     * @param TiiSubmission $submission
     * @return Response
     */
    public function createSubmission( $submission ) {
        $submissionLti = $this->setOptions( new LTI( $this->apibaseurl ) );
        $submissionLti->setXmlResponse( true );
        return $submissionLti->createSubmission( $submission );
    }

    /**
     * Create a Nothing Submission on Turnitin (Submit).
     *
     * Takes a {@link TiiSubmission.html TiiSubmission} object containing the required parameters
     * and returns a {@link Response.html Response} object containing the data from the response.
     *
     * A nothing submission will create a blank marking template into an assignment for a student. A nothing submission must be submitted by an instructor on behalf of a student.
     *
     * createSubmission accepts:
     * <ul>
     * <li><b>Submitter User Id</b><br />{@link TiiSubmission.html#setSubmitterUserId TiiSubmission->setSubmitterUserId( <i>integer</i> SubmitterUserId )}</li>
     * <li><b>Assignment Id</b><br />{@link TiiSubmission.html#setAssignmentId TiiSubmission->setAssignmentId( <i>integer</i> AssignmentId )}</li>
     * <li><b>Author User Id</b>* (Optional)<br />{@link TiiSubmission.html#setAuthorUserId TiiSubmission->setAuthorUserId( <i>integer</i> AuthorUserId )}</li>
     * </ul>
     * createNothingSubmission returns a {@link Response.html Response} object which contains a {@link TiiSubmission.html TiiSubmission} object:
     * <ul>
     * <li>{@link Response.html#getSubmission Response->getSubmission()} returns a {@link TiiSubmission.html TiiSubmission} object</li>
     * <ul>
     * <li><b>Submission ID</b><br />{@link TiiSubmission.html#getSubmissionId TiiSubmission->getSubmissionId()}</li>
     * </ul>
     * </ul>
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $submission = new TiiSubmission();
     * $submission->setAssignmentId( 1234 );
     * $submission->setSubmitterUserId( 1234 );
     * $submission->setAuthorUserId( 1234 );
     *
     * $response = $api->createSubmission( $submission );
     * $newsubmission = $response->getSubmission();
     * $newsubmissionid = $newsubmission->getSubmissionId();
     * </pre>
     *
     * @param TiiSubmission $submission
     * @return Response
     */
    public function createNothingSubmission( $submission ) {
        $submissionSoap = $this->setOptions( new SubmissionSoap( $this->resultwsdl, $this->getServiceOptions( 'result' ) ) );
        return $submissionSoap->createSubmission( $submission );
    }

    /**
     * Replace a Submission on Turnitin (Resubmit).
     *
     * Takes a {@link TiiSubmission.html TiiSubmission} object containing the required parameters
     * and returns a {@link Response.html Response} object containing the data from the response.
     *
     * replaceSubmission accepts:
     * <ul>
     * <li><b>Submitter User Id</b><br />{@link TiiSubmission.html#setSubmitterUserId TiiSubmission->setSubmitterUserId( <i>integer</i> SubmitterUserId )}</li>
     * <li><b>Submission Id</b><br />{@link TiiSubmission.html#setSubmissionId TiiSubmission->setSubmissionId( <i>integer</i> SubmissionId )}</li>
     * <li><b>Role</b><br />{@link TiiSubmission.html#setRole TiiSubmission->setRole( <i>string</i> Role )}</li>
     * <li><b>Title</b><br />{@link TiiSubmission.html#setTitle TiiSubmission->setTitle( <i>string</i> Title )}</li>
     * <li><b>Author User Id</b>* (Optional)<br />{@link TiiSubmission.html#setAuthorUserId TiiSubmission->setAuthorUserId( <i>integer</i> AuthorUserId )}</li>
     * <li><b>Submission Data Path</b><br />{@link TiiSubmission.html#setSubmissionDataPath TiiSubmission->setSubmissionDataPath( <i>string</i> SubmissionDataPath )}<br />
     * <b>[ <i>OR</i> ]</b><br />
     * <b>Submission Data Text</b><br />{@link TiiSubmission.html#setSubmissionDataText TiiSubmission->setSubmissionDataText( <i>string</i> SubmissionDataText )}</li>
     * </ul>
     * updateSubmission returns a {@link Response.html Response} object which contains a {@link TiiSubmission.html TiiSubmission} object:
     * <ul>
     * <li>{@link Response.html#getSubmission Response->getSubmission()} returns a {@link TiiSubmission.html TiiSubmission} object</li>
     * <ul>
     * <li><b>Submission ID</b><br />{@link TiiSubmission.html#getSubmissionId TiiSubmission->getSubmissionId()}</li>
     * <li><b>Text Extract</b><br />{@link TiiSubmission.html#getTextExtract TiiSubmission->getTextExtract()}</li>
     * </ul>
     * </ul>
     *
     * <i>* Required if submitting as Instructor on behalf of the Author</i>
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $submission = new TiiSubmission();
     * $submission->setSubmissionId( 1234 );
     * $submission->setTitle( 'Test Submission' );
     * $submission->setSubmitterUserId( 1234 );
     * $submission->setRole( 'Learner' );
     * $submission->setSubmissionDataPath( '/path/to/resubmission.txt' );
     *
     * $response = $api->replaceSubmission( $submission );
     * $newsubmission = $response->getSubmission();
     * $newsubmissionid = $newsubmission->getSubmissionId();
     * $newsubmissionextract = $newsubmission->getTextExtract();
     * </pre>
     *
     * @param TiiSubmission $submission
     * @return Response
     */
    public function replaceSubmission( $submission ) {
        $submissionLti = $this->setOptions( new LTI( $this->apibaseurl ) );
        $submissionLti->setXmlResponse( true );
        return $submissionLti->replaceSubmission( $submission );
    }

    /**
     * Output an HTML Submission Form
     *
     * Creates an html form with the required LTI parameters to upload a submission as either plain text or as a supported file type.
     *
     * outputSubmissionForm accepts:
     * <ul>
     * <li><b>Submitter User Id</b><br />{@link TiiSubmission.html#setSubmitterUserId TiiSubmission->setSubmitterUserId( <i>integer</i> SubmitterUserId )}</li>
     * <li><b>Assignment Id</b><br />{@link TiiSubmission.html#setAssignmentId TiiSubmission->setAssignmentId( <i>integer</i> AssignmentId )}</li>
     * <li><b>Role</b><br />{@link TiiSubmission.html#setRole TiiSubmission->setRole( <i>string</i> Role )}</li>
     * <li><b>Title</b><br />{@link TiiSubmission.html#setTitle TiiSubmission->setTitle( <i>string</i> Title )}</li>
     * <li><b>Author User Id</b>* (Optional)<br />{@link TiiSubmission.html#setAuthorUserId TiiSubmission->setAuthorUserId( <i>integer</i> AuthorUserId )}</li>
     * <li><b>Button Text</b> (Optional)<br />{@link TiiSubmission.html#setButtonText TiiSubmission->setButtonText( <i>string</i> ButtonText )}</li>
     * <li><b>Button Image</b> (Optional)<br />{@link TiiSubmission.html#setButtonImage TiiSubmission->setButtonImage( <i>string</i> ButtonImage )}</li>
     * <li><b>Form Target</b> (Optional)<br />{@link TiiSubmission.html#setFormTarget TiiSubmission->setFormTarget( <i>string</i> FormTarget )}</li>
     * </ul>
     * outputSubmissionForm outputs an html form or returns the html as a string
     *
     * <i>* Required if submitting as Instructor on behalf of the Author</i>
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $submission = new TiiSubmission();
     * $submission->setAssignmentId( 1234 );
     * $submission->setTitle( 'Test Submission' );
     * $submission->setSubmitterUserId( 1234 );
     * $submission->setRole( 'Learner' );
     * $submission->setButtonText( 'Upload Your Submission' );
     *
     * $api->outputSubmissionForm( $submission, true );
     * </pre>
     *
     * @param TiiSubmission $submission
     * <br />
     * @param boolean $uploadfile
     * (Optional) Determines if the form should output an upload field<br />
     * @param boolean $uploadtext
     * (Optional) Determines if the form should output a textarea<br />
     * @param boolean $return
     * (Optional) Determines if the form html should be output or returned by the method<br />
     * @return mixed
     */
    public function outputSubmissionForm( $submission, $uploadfile = false, $uploadtext = false, $return = false ) {
        $submissionLti = $this->setOptions( new LTI( $this->apibaseurl ) );
        $params = $submissionLti->getSubmissionFormHash($submission);
        $output = $submissionLti->getFormHtml( $submission, $params, $uploadfile, $uploadtext );
        if ( $return ) {
            return $output;
        } else {
            echo $output;
        }
    }

    /**
     * Output an HTML Resubmission Form
     *
     * Creates an html form with the required LTI parameters to upload a resubmission as either plain text or as a supported file type.
     *
     * outputResubmissionForm accepts:
     * <ul>
     * <li><b>Submitter User Id</b><br />{@link TiiSubmission.html#setSubmitterUserId TiiSubmission->setSubmitterUserId( <i>integer</i> SubmitterUserId )}</li>
     * <li><b>Submission Id</b><br />{@link TiiSubmission.html#setSubmissionId TiiSubmission->setSubmissionId( <i>integer</i> SubmissionId )}</li>
     * <li><b>Role</b><br />{@link TiiSubmission.html#setRole TiiSubmission->setRole( <i>string</i> Role )}</li>
     * <li><b>Title</b><br />{@link TiiSubmission.html#setTitle TiiSubmission->setTitle( <i>string</i> Title )}</li>
     * <li><b>Author User Id</b>* (Optional)<br />{@link TiiSubmission.html#setAuthorUserId TiiSubmission->setAuthorUserId( <i>integer</i> AuthorUserId )}</li>
     * <li><b>Button Text</b> (Optional)<br />{@link TiiSubmission.html#setButtonText TiiSubmission->setButtonText( <i>string</i> ButtonText )}</li>
     * <li><b>Button Image</b> (Optional)<br />{@link TiiSubmission.html#setButtonImage TiiSubmission->setButtonImage( <i>string</i> ButtonImage )}</li>
     * <li><b>Form Target</b> (Optional)<br />{@link TiiSubmission.html#setFormTarget TiiSubmission->setFormTarget( <i>string</i> FormTarget )}</li>
     * </ul>
     * outputResubmissionForm outputs an html form or returns the html as a string
     *
     * <i>* Required if submitting as Instructor on behalf of the Author</i>
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $submission = new TiiSubmission();
     * $submission->setSubmissionId( 1234 );
     * $submission->setTitle( 'Test Submission' );
     * $submission->setSubmitterUserId( 1234 );
     * $submission->setRole( 'Learner' );
     * $submission->setButtonText( 'Resubmit Your File' );
     *
     * $api->outputResubmissionForm( $submission, true );
     * </pre>
     *
     * @param TiiSubmission $submission
     * <br />
     * @param boolean $uploadfile
     * (Optional) Determines if the form should output an upload field<br />
     * @param boolean $uploadtext
     * (Optional) Determines if the form should output a textarea<br />
     * @param boolean $return
     * (Optional) Determines if the form html should be output or returned by the method<br />
     * @return mixed
     */
    public function outputResubmissionForm( $submission, $uploadfile = false, $uploadtext = false, $return = false ) {
        $submissionLti = $this->setOptions( new LTI( $this->apibaseurl ) );
        $params = $submissionLti->getResubmissionFormHash($submission);
        $output = $submissionLti->getFormHtml( $submission, $params, $uploadfile, $uploadtext );
        if ( $return ) {
            return $output;
        } else {
            echo $output;
        }
    }

    /**
     * Output a GradeMark Document Viewer Launch HTML Form
     *
     * Creates an html form with the required LTI parameters to launch a user into the Document Viewer with the GradeMark service active.
     *
     * outputDVGradeMarkForm accepts:
     * <ul>
     * <li><b>User Id</b><br />{@link TiiLTI.html#setUserId TiiLTI->setUserId( <i>integer</i> UserId )}</li>
     * <li><b>Submission Id</b><br />{@link TiiLTI.html#setSubmissionId TiiLTI->setSubmissionId( <i>integer</i> SubmissionId )}</li>
     * <li><b>Role</b><br />{@link TiiLTI.html#setRole TiiLTI->setRole( <i>string</i> Role )}</li>
     * <li><b>Button Text</b> (Optional)<br />{@link TiiLTI.html#setButtonText TiiLTI->setButtonText( <i>string</i> ButtonText )}</li>
     * <li><b>Button Image</b> (Optional)<br />{@link TiiLTI.html#setButtonImage TiiLTI->setButtonImage( <i>string</i> ButtonImage )}</li>
     * <li><b>Form Target</b> (Optional)<br />{@link TiiLTI.html#setFormTarget TiiLTI->setFormTarget( <i>string</i> FormTarget )}</li>
     * </ul>
     * outputDVGradeMarkForm outputs an html form or returns the html as a string
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $lti = new TiiLTI();
     * $lti->setSubmissionId( 1234 );
     * $lti->setUserId( 1234 );
     * $lti->setRole( 'Learner' );
     *
     * $api->outputDVGradeMarkForm( $lti );
     * </pre>
     *
     * @param TiiLTI $lti
     * <br />
     * @param boolean $return
     * (Optional) Determines if the form html should be output or returned by the method<br />
     * @return mixed
     */
    public function outputDVGradeMarkForm( $lti, $return = false ) {
        $submissionLti = $this->setOptions( new LTI( $this->apibaseurl ) );
        $params = $submissionLti->getDVGradeMarkFormHash($lti);
        $output = $submissionLti->getFormHtml( $lti, $params, false, false );
        if ( $lti->getAsJson() ) $output = json_encode( $params );
        if ( $return ) {
            return $output;
        } else {
            echo $output;
        }
    }

    /**
     * Output a Report Document Viewer Launch HTML Form
     *
     * Creates an html form with the required LTI parameters to launch a user into the Document Viewer with the Similarity Report service active.
     *
     * outputDVReportForm accepts:
     * <ul>
     * <li><b>User Id</b><br />{@link TiiLTI.html#setUserId TiiLTI->setUserId( <i>integer</i> UserId )}</li>
     * <li><b>Submission Id</b><br />{@link TiiLTI.html#setSubmissionId TiiLTI->setSubmissionId( <i>integer</i> SubmissionId )}</li>
     * <li><b>Role</b><br />{@link TiiLTI.html#setRole TiiLTI->setRole( <i>string</i> Role )}</li>
     * <li><b>Button Text</b> (Optional)<br />{@link TiiLTI.html#setButtonText TiiLTI->setButtonText( <i>string</i> ButtonText )}</li>
     * <li><b>Button Image</b> (Optional)<br />{@link TiiLTI.html#setButtonImage TiiLTI->setButtonImage( <i>string</i> ButtonImage )}</li>
     * <li><b>Form Target</b> (Optional)<br />{@link TiiLTI.html#setFormTarget TiiLTI->setFormTarget( <i>string</i> FormTarget )}</li>
     * </ul>
     * outputDVReportForm outputs an html form or returns the html as a string
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $lti = new TiiLTI();
     * $lti->setSubmissionId( 1234 );
     * $lti->setUserId( 1234 );
     * $lti->setRole( 'Learner' );
     *
     * $api->outputDVReportForm( $lti );
     * </pre>
     *
     * @param TiiLTI $lti
     * <br />
     * @param boolean $return
     * (Optional) Determines if the form html should be output or returned by the method<br />
     * @return mixed
     */
    public function outputDVReportForm( $lti, $return = false ) {
        $submissionLti = $this->setOptions( new LTI( $this->apibaseurl ) );
        $params = $submissionLti->getDVReportFormHash($lti);
        $output = $submissionLti->getFormHtml( $lti, $params, false, false );
        if ( $lti->getAsJson() ) $output = json_encode( $params );
        if ( $return ) {
            return $output;
        } else {
            echo $output;
        }
    }

    /**
     * Output a Default Document Viewer Launch HTML Form
     *
     * Creates an html form with the required LTI parameters to launch a user into the Document Viewer with the no service active.
     *
     * outputDVDefaultForm accepts:
     * <ul>
     * <li><b>User Id</b><br />{@link TiiLTI.html#setUserId TiiLTI->setUserId( <i>integer</i> UserId )}</li>
     * <li><b>Submission Id</b><br />{@link TiiLTI.html#setSubmissionId TiiLTI->setSubmissionId( <i>integer</i> SubmissionId )}</li>
     * <li><b>Role</b><br />{@link TiiLTI.html#setRole TiiLTI->setRole( <i>string</i> Role )}</li>
     * <li><b>Button Text</b> (Optional)<br />{@link TiiLTI.html#setButtonText TiiLTI->setButtonText( <i>string</i> ButtonText )}</li>
     * <li><b>Button Image</b> (Optional)<br />{@link TiiLTI.html#setButtonImage TiiLTI->setButtonImage( <i>string</i> ButtonImage )}</li>
     * <li><b>Form Target</b> (Optional)<br />{@link TiiLTI.html#setFormTarget TiiLTI->setFormTarget( <i>string</i> FormTarget )}</li>
     * </ul>
     * outputDVDefaultForm outputs an html form or returns the html as a string
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $lti = new TiiLTI();
     * $lti->setSubmissionId( 1234 );
     * $lti->setUserId( 1234 );
     * $lti->setRole( 'Learner' );
     *
     * $api->outputDVDefaultForm( $lti );
     * </pre>
     *
     * @param TiiLTI $lti
     * <br />
     * @param boolean $return
     * (Optional) Determines if the form html should be output or returned by the method<br />
     * @return mixed
     */
    public function outputDVDefaultForm( $lti, $return = false ) {
        $submissionLti = $this->setOptions( new LTI( $this->apibaseurl ) );
        $params = $submissionLti->getDVDefaultFormHash($lti);
        $output = $submissionLti->getFormHtml( $lti, $params, false, false );
        if ( $lti->getAsJson() ) $output = json_encode( $params );
        if ( $return ) {
            return $output;
        } else {
            echo $output;
        }
    }

    /**
     * Output a PeerMark Document Viewer Launch HTML Form
     *
     * Creates an html form with the required LTI parameters to launch a user into the Document Viewer with the PeerMark service active.
     *
     * outputDVPeerMarkForm accepts:
     * <ul>
     * <li><b>User Id</b><br />{@link TiiLTI.html#setUserId TiiLTI->setUserId( <i>integer</i> UserId )}</li>
     * <li><b>Submission Id</b><br />{@link TiiLTI.html#setSubmissionId TiiLTI->setSubmissionId( <i>integer</i> SubmissionId )}</li>
     * <li><b>Role</b><br />{@link TiiLTI.html#setRole TiiLTI->setRole( <i>string</i> Role )}</li>
     * <li><b>Button Text</b> (Optional)<br />{@link TiiLTI.html#setButtonText TiiLTI->setButtonText( <i>string</i> ButtonText )}</li>
     * <li><b>Button Image</b> (Optional)<br />{@link TiiLTI.html#setButtonImage TiiLTI->setButtonImage( <i>string</i> ButtonImage )}</li>
     * <li><b>Form Target</b> (Optional)<br />{@link TiiLTI.html#setFormTarget TiiLTI->setFormTarget( <i>string</i> FormTarget )}</li>
     * </ul>
     * outputDVPeerMarkForm outputs an html form or returns the html as a string
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $lti = new TiiLTI();
     * $lti->setSubmissionId( 1234 );
     * $lti->setUserId( 1234 );
     * $lti->setRole( 'Learner' );
     *
     * $api->outputDVPeerMarkForm( $lti );
     * </pre>
     *
     * @param TiiLTI $lti
     * <br />
     * @param boolean $return
     * (Optional) Determines if the form html should be output or returned by the method<br />
     * @return mixed
     */
    public function outputDVPeerMarkForm( $lti, $return = false ) {
        $submissionLti = $this->setOptions( new LTI( $this->apibaseurl ) );
        $params = $submissionLti->getDVPeerMarkFormHash($lti);
        $output = $submissionLti->getFormHtml( $lti, $params, false, false );
        if ( $lti->getAsJson() ) $output = json_encode( $params );
        if ( $return ) {
            return $output;
        } else {
            echo $output;
        }
    }

    /**
     * Output a Rubric Manager Launch HTML Form
     *
     * Creates an html form with the required LTI parameters to launch a user into the rubric manager.
     *
     * outputMessagesForm accepts:
     * <ul>
     * <li><b>User Id</b><br />{@link TiiLTI.html#setUserId TiiLTI->setUserId( <i>integer</i> UserId )}</li>
     * <li><b>Role</b><br />{@link TiiLTI.html#setRole TiiLTI->setRole( <i>string</i> Role )}</li>
     * <li><b>Button Text</b> (Optional)<br />{@link TiiLTI.html#setButtonText TiiLTI->setButtonText( <i>string</i> ButtonText )}</li>
     * <li><b>Button Image</b> (Optional)<br />{@link TiiLTI.html#setButtonImage TiiLTI->setButtonImage( <i>string</i> ButtonImage )}</li>
     * <li><b>Form Target</b> (Optional)<br />{@link TiiLTI.html#setFormTarget TiiLTI->setFormTarget( <i>string</i> FormTarget )}</li>
     * </ul>
     * outputMessagesForm outputs an html form or returns the html as a string
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $lti = new TiiLTI();
     * $lti->setUserId( 1234 );
     * $lti->setRole( 'Instructor' );
     *
     * $api->outputRubricManagerForm( $lti );
     * </pre>
     *
     * @param TiiLTI $lti
     * <br />
     * @param boolean $return
     * (Optional) Determines if the form html should be output or returned by the method<br />
     * @return mixed
     */
    public function outputRubricManagerForm( $lti, $return = false ) {
        $submissionLti = $this->setOptions( new LTI( $this->apibaseurl ) );
        $params = $submissionLti->getRubricManagerFormHash($lti);
        $output = $submissionLti->getFormHtml( $lti, $params, false, false );
        if ( $lti->getAsJson() ) $output = json_encode( $params );
        if ( $return ) {
            return $output;
        } else {
            echo $output;
        }
    }

    /**
     * Output a Rubric View Launch HTML Form
     *
     * Creates an html form with the required LTI parameters to launch a user into the rubric view.
     *
     * outputMessagesForm accepts:
     * <ul>
     * <li><b>User Id</b><br />{@link TiiLTI.html#setUserId TiiLTI->setUserId( <i>integer</i> UserId )}</li>
     * <li><b>Role</b><br />{@link TiiLTI.html#setRole TiiLTI->setRole( <i>string</i> Role )}</li>
     * <li><b>Button Text</b> (Optional)<br />{@link TiiLTI.html#setButtonText TiiLTI->setButtonText( <i>string</i> ButtonText )}</li>
     * <li><b>Button Image</b> (Optional)<br />{@link TiiLTI.html#setButtonImage TiiLTI->setButtonImage( <i>string</i> ButtonImage )}</li>
     * <li><b>Form Target</b> (Optional)<br />{@link TiiLTI.html#setFormTarget TiiLTI->setFormTarget( <i>string</i> FormTarget )}</li>
     * </ul>
     * outputMessagesForm outputs an html form or returns the html as a string
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $lti = new TiiLTI();
     * $lti->setAssignmentId( 1234 );
     * $lti->setUserId( 1234 );
     * $lti->setRole( 'Instructor' );
     *
     * $api->outputRubricViewForm( $lti );
     * </pre>
     *
     * @param TiiLTI $lti
     * <br />
     * @param boolean $return
     * (Optional) Determines if the form html should be output or returned by the method<br />
     * @return mixed
     */
    public function outputRubricViewForm( $lti, $return = false ) {
        $submissionLti = $this->setOptions( new LTI( $this->apibaseurl ) );
        $params = $submissionLti->getRubricViewFormHash($lti);
        $output = $submissionLti->getFormHtml( $lti, $params, false, false );
        if ( $lti->getAsJson() ) $output = json_encode( $params );
        if ( $return ) {
            return $output;
        } else {
            echo $output;
        }
    }

    /**
     * Output a Quickmark Manager Launch HTML Form
     *
     * Creates an html form with the required LTI parameters to launch quickmark manager.
     *
     * outputMessagesForm accepts:
     * <ul>
     * <li><b>User Id</b><br />{@link TiiLTI.html#setUserId TiiLTI->setUserId( <i>integer</i> UserId )}</li>
     * <li><b>Role</b><br />{@link TiiLTI.html#setRole TiiLTI->setRole( <i>string</i> Role )}</li>
     * <li><b>Button Text</b> (Optional)<br />{@link TiiLTI.html#setButtonText TiiLTI->setButtonText( <i>string</i> ButtonText )}</li>
     * <li><b>Button Image</b> (Optional)<br />{@link TiiLTI.html#setButtonImage TiiLTI->setButtonImage( <i>string</i> ButtonImage )}</li>
     * <li><b>Form Target</b> (Optional)<br />{@link TiiLTI.html#setFormTarget TiiLTI->setFormTarget( <i>string</i> FormTarget )}</li>
     * </ul>
     * outputMessagesForm outputs an html form or returns the html as a string
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $lti = new TiiLTI();
     * $lti->setUserId( 1234 );
     * $lti->setRole( 'Instructor' );
     *
     * $api->outputQuickmarkManagerForm( $lti );
     * </pre>
     *
     * @param TiiLTI $lti
     * <br />
     * @param boolean $return
     * (Optional) Determines if the form html should be output or returned by the method<br />
     * @return mixed
     */
    public function outputQuickmarkManagerForm( $lti, $return = false ) {
        $submissionLti = $this->setOptions( new LTI( $this->apibaseurl ) );
        $params = $submissionLti->getQuickmarkFormHash($lti);
        $output = $submissionLti->getFormHtml( $lti, $params, false, false );
        if ( $lti->getAsJson() ) $output = json_encode( $params );
        if ( $return ) {
            return $output;
        } else {
            echo $output;
        }
    }

    /**
     * Output a User Messages Launch HTML Form
     *
     * Creates an html form with the required LTI parameters to launch a user into user's Messages inbox.
     * This launch will display messages from Turnitin such as service announcements and the bulk download process notifications.
     *
     * outputMessagesForm accepts:
     * <ul>
     * <li><b>User Id</b><br />{@link TiiLTI.html#setUserId TiiLTI->setUserId( <i>integer</i> UserId )}</li>
     * <li><b>Role</b><br />{@link TiiLTI.html#setRole TiiLTI->setRole( <i>string</i> Role )}</li>
     * <li><b>Button Text</b> (Optional)<br />{@link TiiLTI.html#setButtonText TiiLTI->setButtonText( <i>string</i> ButtonText )}</li>
     * <li><b>Button Image</b> (Optional)<br />{@link TiiLTI.html#setButtonImage TiiLTI->setButtonImage( <i>string</i> ButtonImage )}</li>
     * <li><b>Form Target</b> (Optional)<br />{@link TiiLTI.html#setFormTarget TiiLTI->setFormTarget( <i>string</i> FormTarget )}</li>
     * </ul>
     * outputMessagesForm outputs an html form or returns the html as a string
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $lti = new TiiLTI();
     * $lti->setUserId( 1234 );
     * $lti->setRole( 'Learner' );
     *
     * $api->outputMessagesForm( $lti );
     * </pre>
     *
     * @param TiiLTI $lti
     * <br />
     * @param boolean $return
     * (Optional) Determines if the form html should be output or returned by the method<br />
     * @return mixed
     */
    public function outputMessagesForm( $lti, $return = false ) {
        $submissionLti = $this->setOptions( new LTI( $this->apibaseurl ) );
        $params = $submissionLti->getMessagesFormHash($lti);
        $output = $submissionLti->getFormHtml( $lti, $params, false, false );
        if ( $lti->getAsJson() ) $output = json_encode( $params );
        if ( $return ) {
            return $output;
        } else {
            echo $output;
        }
    }

    /**
     * Output a User Agreement Launch HTML Form
     *
     * Creates an html form with the required LTI parameters to launch a user into user's Messages inbox.
     * This launch will display messages from Turnitin such as service announcements and the bulk download process notifications.
     *
     * outputUserAgreementForm accepts:
     * <ul>
     * <li><b>User Id</b><br />{@link TiiLTI.html#setUserId TiiLTI->setUserId( <i>integer</i> UserId )}</li>
     * <li><b>Role</b><br />{@link TiiLTI.html#setRole TiiLTI->setRole( <i>string</i> Role )}</li>
     * <li><b>Button Text</b> (Optional)<br />{@link TiiLTI.html#setButtonText TiiLTI->setButtonText( <i>string</i> ButtonText )}</li>
     * <li><b>Button Image</b> (Optional)<br />{@link TiiLTI.html#setButtonImage TiiLTI->setButtonImage( <i>string</i> ButtonImage )}</li>
     * <li><b>Form Target</b> (Optional)<br />{@link TiiLTI.html#setFormTarget TiiLTI->setFormTarget( <i>string</i> FormTarget )}</li>
     * </ul>
     * outputUserAgreementForm outputs an html form or returns the html as a string
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $lti = new TiiLTI();
     * $lti->setUserId( 1234 );
     * $lti->setRole( 'Learner' );
     *
     * $api->outputUserAgreementForm( $lti );
     * </pre>
     *
     * @param TiiLTI $lti
     * <br />
     * @param boolean $return
     * (Optional) Determines if the form html should be output or returned by the method<br />
     * @return mixed
     */
    public function outputUserAgreementForm( $lti, $return = false ) {
        $submissionLti = $this->setOptions( new LTI( $this->apibaseurl ) );
        $params = $submissionLti->getUserAgreementFormHash($lti);
        $output = $submissionLti->getFormHtml( $lti, $params, false, false );
        if ( $lti->getAsJson() ) $output = json_encode( $params );
        if ( $return ) {
            return $output;
        } else {
            echo $output;
        }
    }

    /**
     * Output a Zip Download Launch HTML Form
     *
     * Creates an html form with the required LTI parameters to launch a bulk download request.
     * The launch will return a zip archive containing submissions for an assignment in the original format as they were submitted.
     *
     * outputDownloadZipForm accepts:
     * <ul>
     * <li><b>User Id</b><br />{@link TiiLTI.html#setUserId TiiLTI->setUserId( <i>integer</i> UserId )}</li>
     * <li><b>Assignment Id</b><br />{@link TiiLTI.html#setAssignmentId TiiLTI->setAssignmentId( <i>integer</i> AssignmentId )}</li>
     * <li><b>Role</b><br />{@link TiiLTI.html#setRole TiiLTI->setRole( <i>string</i> Role )}</li>
     * <li><b>Submission Ids</b> (Optional)<br />{@link TiiLTI.html#setSubmissionIds TiiLTI->setSubmissionIds( <i>array</i> SubmissionIds )}</li>
     * <li><b>Button Text</b> (Optional)<br />{@link TiiLTI.html#setButtonText TiiLTI->setButtonText( <i>string</i> ButtonText )}</li>
     * <li><b>Button Image</b> (Optional)<br />{@link TiiLTI.html#setButtonImage TiiLTI->setButtonImage( <i>string</i> ButtonImage )}</li>
     * <li><b>Form Target</b> (Optional)<br />{@link TiiLTI.html#setFormTarget TiiLTI->setFormTarget( <i>string</i> FormTarget )}</li>
     * </ul>
     * outputDownloadZipForm outputs an html form or returns the html as a string
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $lti = new TiiLTI();
     * $lti->setAssignmentId( 1234 );
     * $lti->setUserId( 1234 );
     * $lti->setRole( 'Learner' );
     *
     * $api->outputDownloadZipForm( $lti );
     * </pre>
     *
     * @param TiiLTI $lti
     * <br />
     * @param boolean $return
     * (Optional) Determines if the form html should be output or returned by the method<br />
     * @return mixed
     */
    public function outputDownloadZipForm( $lti, $return = false ) {
        $submissionLti = $this->setOptions( new LTI( $this->apibaseurl ) );
        $params = $submissionLti->getDownloadZipFormHash($lti);
        $output = $submissionLti->getFormHtml( $lti, $params, false, false );
        if ( $lti->getAsJson() ) $output = json_encode( $params );
        if ( $return ) {
            return $output;
        } else {
            echo $output;
        }
    }

    /**
     * Output a PDF Zip Download Launch HTML Form
     *
     * Creates an html form with the required LTI parameters to launch a bulk download request.
     * The launch will return a zip archive containing submissions for an assignment in PDF format.
     *
     * outputDownloadPDFZipForm accepts:
     * <ul>
     * <li><b>User Id</b><br />{@link TiiLTI.html#setUserId TiiLTI->setUserId( <i>integer</i> UserId )}</li>
     * <li><b>Assignment Id</b><br />{@link TiiLTI.html#setAssignmentId TiiLTI->setAssignmentId( <i>integer</i> AssignmentId )}</li>
     * <li><b>Role</b><br />{@link TiiLTI.html#setRole TiiLTI->setRole( <i>string</i> Role )}</li>
     * <li><b>Submission Ids</b> (Optional)<br />{@link TiiLTI.html#setSubmissionIds TiiLTI->setSubmissionIds( <i>array</i> SubmissionIds )}</li>
     * <li><b>Button Text</b> (Optional)<br />{@link TiiLTI.html#setButtonText TiiLTI->setButtonText( <i>string</i> ButtonText )}</li>
     * <li><b>Button Image</b> (Optional)<br />{@link TiiLTI.html#setButtonImage TiiLTI->setButtonImage( <i>string</i> ButtonImage )}</li>
     * <li><b>Form Target</b> (Optional)<br />{@link TiiLTI.html#setFormTarget TiiLTI->setFormTarget( <i>string</i> FormTarget )}</li>
     * </ul>
     * outputDownloadPDFZipForm outputs an html form or returns the html as a string
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $lti = new TiiLTI();
     * $lti->setAssignmentId( 1234 );
     * $lti->setUserId( 1234 );
     * $lti->setRole( 'Learner' );
     *
     * $api->outputDownloadPDFZipForm( $lti );
     * </pre>
     *
     * @param TiiLTI $lti
     * <br />
     * @param boolean $return
     * (Optional) Determines if the form html should be output or returned by the method<br />
     * @return mixed
     */
    public function outputDownloadPDFZipForm( $lti, $return = false ) {
        $submissionLti = $this->setOptions( new LTI( $this->apibaseurl ) );
        $params = $submissionLti->getDownloadPDFZipFormHash($lti);
        $output = $submissionLti->getFormHtml( $lti, $params, false, false );
        if ( $lti->getAsJson() ) $output = json_encode( $params );
        if ( $return ) {
            return $output;
        } else {
            echo $output;
        }
    }

    /**
     * Output a GradeMark Zip Download Launch HTML Form
     *
     * Creates an html form with the required LTI parameters to launch a bulk download request.
     * The launch will return a zip archive containing submissions in PDF format for an assignment with GradeMark markup overlayed.
     *
     * outputDownloadGradeMarkZipForm accepts:
     * <ul>
     * <li><b>User Id</b><br />{@link TiiLTI.html#setUserId TiiLTI->setUserId( <i>integer</i> UserId )}</li>
     * <li><b>Assignment Id</b><br />{@link TiiLTI.html#setAssignmentId TiiLTI->setAssignmentId( <i>integer</i> AssignmentId )}</li>
     * <li><b>Role</b><br />{@link TiiLTI.html#setRole TiiLTI->setRole( <i>string</i> Role )}</li>
     * <li><b>Submission Ids</b> (Optional)<br />{@link TiiLTI.html#setSubmissionIds TiiLTI->setSubmissionIds( <i>array</i> SubmissionIds )}</li>
     * <li><b>Button Text</b> (Optional)<br />{@link TiiLTI.html#setButtonText TiiLTI->setButtonText( <i>string</i> ButtonText )}</li>
     * <li><b>Button Image</b> (Optional)<br />{@link TiiLTI.html#setButtonImage TiiLTI->setButtonImage( <i>string</i> ButtonImage )}</li>
     * <li><b>Form Target</b> (Optional)<br />{@link TiiLTI.html#setFormTarget TiiLTI->setFormTarget( <i>string</i> FormTarget )}</li>
     * </ul>
     * outputDownloadGradeMarkZipForm outputs an html form or returns the html as a string
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $lti = new TiiLTI();
     * $lti->setAssignmentId( 1234 );
     * $lti->setUserId( 1234 );
     * $lti->setRole( 'Learner' );
     *
     * $api->outputDownloadGradeMarkZipForm( $lti );
     * </pre>
     *
     * @param TiiLTI $lti
     * <br />
     * @param boolean $return
     * (Optional) Determines if the form html should be output or returned by the method<br />
     * @return mixed
     */
    public function outputDownloadGradeMarkZipForm( $lti, $return = false ) {
        $submissionLti = $this->setOptions( new LTI( $this->apibaseurl ) );
        $params = $submissionLti->getDownloadGradeMarkZipFormHash($lti);
        $output = $submissionLti->getFormHtml( $lti, $params, false, false );
        if ( $lti->getAsJson() ) $output = json_encode( $params );
        if ( $return ) {
            return $output;
        } else {
            echo $output;
        }
    }

    /**
     * Output a Grade Report XLS Spreadsheet Launch HTML Form
     *
     * Creates an html form with the required LTI parameters to launch a XLS grade report request.
     * The launch will return a XLS Spreadsheet file containing information about the submissions in an assignment e.g. Users, Grades and Similarity
     *
     * outputDownloadXLSForm accepts:
     * <ul>
     * <li><b>User Id</b><br />{@link TiiLTI.html#setUserId TiiLTI->setUserId( <i>integer</i> UserId )}</li>
     * <li><b>Assignment Id</b><br />{@link TiiLTI.html#setAssignmentId TiiLTI->setAssignmentId( <i>integer</i> AssignmentId )}</li>
     * <li><b>Role</b><br />{@link TiiLTI.html#setRole TiiLTI->setRole( <i>string</i> Role )}</li>
     * <li><b>Button Text</b> (Optional)<br />{@link TiiLTI.html#setButtonText TiiLTI->setButtonText( <i>string</i> ButtonText )}</li>
     * <li><b>Button Image</b> (Optional)<br />{@link TiiLTI.html#setButtonImage TiiLTI->setButtonImage( <i>string</i> ButtonImage )}</li>
     * <li><b>Form Target</b> (Optional)<br />{@link TiiLTI.html#setFormTarget TiiLTI->setFormTarget( <i>string</i> FormTarget )}</li>
     * </ul>
     * outputDownloadXLSForm outputs an html form or returns the html as a string
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $lti = new TiiLTI();
     * $lti->setAssignmentId( 1234 );
     * $lti->setUserId( 1234 );
     * $lti->setRole( 'Learner' );
     *
     * $api->outputDownloadXLSForm( $lti );
     * </pre>
     *
     * @param TiiLTI $lti
     * <br />
     * @param boolean $return
     * (Optional) Determines if the form html should be output or returned by the method<br />
     * @return mixed
     */
    public function outputDownloadXLSForm( $lti, $return = false ) {
        $submissionLti = $this->setOptions( new LTI( $this->apibaseurl ) );
        $params = $submissionLti->getDownloadXLSFormHash($lti);
        $output = $submissionLti->getFormHtml( $lti, $params, false, false );
        if ( $lti->getAsJson() ) $output = json_encode( $params );
        if ( $return ) {
            return $output;
        } else {
            echo $output;
        }
    }

    /**
     * Output a PeerMark Setup Launch HTML Form
     *
     * Creates an html form with the required LTI parameters to launch a PeerMark Setup request.
     * The launch will return a PeerMark setup screen
     *
     * outputPeerMarkSetupForm accepts:
     * <ul>
     * <li><b>User Id</b><br />{@link TiiLTI.html#setUserId TiiLTI->setUserId( <i>integer</i> UserId )}</li>
     * <li><b>Assignment Id</b><br />{@link TiiLTI.html#setAssignmentId TiiLTI->setAssignmentId( <i>integer</i> AssignmentId )}</li>
     * <li><b>Role</b><br />{@link TiiLTI.html#setRole TiiLTI->setRole( <i>string</i> Role )}</li>
     * <li><b>Button Text</b> (Optional)<br />{@link TiiLTI.html#setButtonText TiiLTI->setButtonText( <i>string</i> ButtonText )}</li>
     * <li><b>Button Image</b> (Optional)<br />{@link TiiLTI.html#setButtonImage TiiLTI->setButtonImage( <i>string</i> ButtonImage )}</li>
     * <li><b>Form Target</b> (Optional)<br />{@link TiiLTI.html#setFormTarget TiiLTI->setFormTarget( <i>string</i> FormTarget )}</li>
     * </ul>
     * outputPeerMarkSetupForm outputs an html form or returns the html as a string
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $lti = new TiiLTI();
     * $lti->setAssignmentId( 1234 );
     * $lti->setUserId( 1234 );
     * $lti->setRole( 'Instructor' );
     *
     * $api->outputPeerMarkSetupForm( $lti );
     * </pre>
     *
     * @param TiiLTI $lti
     * <br />
     * @param boolean $return
     * (Optional) Determines if the form html should be output or returned by the method<br />
     * @return mixed
     */
    public function outputPeerMarkSetupForm( $lti, $return = false ) {
        $submissionLti = $this->setOptions( new LTI( $this->apibaseurl ) );
        $params = $submissionLti->getPeerMarkSetupFormHash($lti);
        $output = $submissionLti->getFormHtml( $lti, $params, false, false );
        if ( $lti->getAsJson() ) $output = json_encode( $params );
        if ( $return ) {
            return $output;
        } else {
            echo $output;
        }
    }

    /**
     * Output a PeerMark Review Launch HTML Form
     *
     * Creates an html form with the required LTI parameters to launch a PeerMark Review request.
     * The launch will return a PeerMark Review launch screen for either a student or instructor
     *
     * outputPeerMarkReviewForm accepts:
     * <ul>
     * <li><b>User Id</b><br />{@link TiiLTI.html#setUserId TiiLTI->setUserId( <i>integer</i> UserId )}</li>
     * <li><b>Assignment Id</b><br />{@link TiiLTI.html#setAssignmentId TiiLTI->setAssignmentId( <i>integer</i> AssignmentId )}</li>
     * <li><b>Role</b><br />{@link TiiLTI.html#setRole TiiLTI->setRole( <i>string</i> Role )}</li>
     * <li><b>Button Text</b> (Optional)<br />{@link TiiLTI.html#setButtonText TiiLTI->setButtonText( <i>string</i> ButtonText )}</li>
     * <li><b>Button Image</b> (Optional)<br />{@link TiiLTI.html#setButtonImage TiiLTI->setButtonImage( <i>string</i> ButtonImage )}</li>
     * <li><b>Form Target</b> (Optional)<br />{@link TiiLTI.html#setFormTarget TiiLTI->setFormTarget( <i>string</i> FormTarget )}</li>
     * </ul>
     * outputPeerMarkReviewForm outputs an html form or returns the html as a string
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $lti = new TiiLTI();
     * $lti->setAssignmentId( 1234 );
     * $lti->setUserId( 1234 );
     * $lti->setRole( 'Learner' );
     *
     * $api->outputPeerMarkReviewForm( $lti );
     * </pre>
     *
     * @param TiiLTI $lti
     * <br />
     * @param boolean $return
     * (Optional) Determines if the form html should be output or returned by the method<br />
     * @return mixed
     */
    public function outputPeerMarkReviewForm( $lti, $return = false ) {
        $submissionLti = $this->setOptions( new LTI( $this->apibaseurl ) );
        $params = $submissionLti->getPeerMarkReviewFormHash($lti);
        $output = $submissionLti->getFormHtml( $lti, $params, false, false );
        if ( $lti->getAsJson() ) $output = json_encode( $params );
        if ( $return ) {
            return $output;
        } else {
            echo $output;
        }
    }

    /**
     * Output a Original Format File Launch HTML Form
     *
     * Creates an html form with the required LTI parameters to launch a request for the submission in it's original format.
     *
     * outputDownloadOriginalFileForm accepts:
     * <ul>
     * <li><b>User Id</b><br />{@link TiiLTI.html#setUserId TiiLTI->setUserId( <i>integer</i> UserId )}</li>
     * <li><b>Submission Id</b><br />{@link TiiLTI.html#setSubmissionId TiiLTI->setSubmissionId( <i>integer</i> SubmissionId )}</li>
     * <li><b>Role</b><br />{@link TiiLTI.html#setRole TiiLTI->setRole( <i>string</i> Role )}</li>
     * <li><b>Button Text</b> (Optional)<br />{@link TiiLTI.html#setButtonText TiiLTI->setButtonText( <i>string</i> ButtonText )}</li>
     * <li><b>Button Image</b> (Optional)<br />{@link TiiLTI.html#setButtonImage TiiLTI->setButtonImage( <i>string</i> ButtonImage )}</li>
     * <li><b>Form Target</b> (Optional)<br />{@link TiiLTI.html#setFormTarget TiiLTI->setFormTarget( <i>string</i> FormTarget )}</li>
     * </ul>
     * outputDownloadOriginalFileForm outputs an html form or returns the html as a string
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $lti = new TiiLTI();
     * $lti->setSubmissionId( 1234 );
     * $lti->setUserId( 1234 );
     * $lti->setRole( 'Learner' );
     *
     * $api->outputDownloadOriginalFileForm( $lti );
     * </pre>
     *
     * @param TiiLTI $lti
     * <br />
     * @param boolean $return
     * (Optional) Determines if the form html should be output or returned by the method<br />
     * @return mixed
     */
    public function outputDownloadOriginalFileForm( $lti, $return = false ) {
        $submissionLti = $this->setOptions( new LTI( $this->apibaseurl ) );
        $params = $submissionLti->getDownloadOriginalFileFormHash($lti);
        $output = $submissionLti->getFormHtml( $lti, $params, false, false );
        if ( $lti->getAsJson() ) $output = json_encode( $params );
        if ( $return ) {
            return $output;
        } else {
            echo $output;
        }
    }

    /**
     * Output a Submission PDF File Launch HTML Form
     *
     * Creates an html form with the required LTI parameters to launch a request for the submission in PDF format.
     *
     * outputDownloadDefaultPDFForm accepts:
     * <ul>
     * <li><b>User Id</b><br />{@link TiiLTI.html#setUserId TiiLTI->setUserId( <i>integer</i> UserId )}</li>
     * <li><b>Submission Id</b><br />{@link TiiLTI.html#setSubmissionId TiiLTI->setSubmissionId( <i>integer</i> SubmissionId )}</li>
     * <li><b>Role</b><br />{@link TiiLTI.html#setRole TiiLTI->setRole( <i>string</i> Role )}</li>
     * <li><b>Button Text</b> (Optional)<br />{@link TiiLTI.html#setButtonText TiiLTI->setButtonText( <i>string</i> ButtonText )}</li>
     * <li><b>Button Image</b> (Optional)<br />{@link TiiLTI.html#setButtonImage TiiLTI->setButtonImage( <i>string</i> ButtonImage )}</li>
     * <li><b>Form Target</b> (Optional)<br />{@link TiiLTI.html#setFormTarget TiiLTI->setFormTarget( <i>string</i> FormTarget )}</li>
     * </ul>
     * outputDownloadDefaultPDFForm outputs an html form or returns the html as a string
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $lti = new TiiLTI();
     * $lti->setSubmissionId( 1234 );
     * $lti->setUserId( 1234 );
     * $lti->setRole( 'Learner' );
     *
     * $api->outputDownloadDefaultPDFForm( $lti );
     * </pre>
     *
     * @param TiiLTI $lti
     * <br />
     * @param boolean $return
     * (Optional) Determines if the form html should be output or returned by the method<br />
     * @return mixed
     */
    public function outputDownloadDefaultPDFForm( $lti, $return = false ) {
        $submissionLti = $this->setOptions( new LTI( $this->apibaseurl ) );
        $params = $submissionLti->getDownloadDefaultPDFFormHash($lti);
        $output = $submissionLti->getFormHtml( $lti, $params, false, false );
        if ( $lti->getAsJson() ) $output = json_encode( $params );
        if ( $return ) {
            return $output;
        } else {
            echo $output;
        }
    }

    /**
     * Output a GradeMark PDF File Launch HTML Form
     *
     * Creates an html form with the required LTI parameters to launch a request for the submission in PDF format containing GradeMark information.
     *
     * outputDownloadGradeMarkPDFForm accepts:
     * <ul>
     * <li><b>User Id</b><br />{@link TiiLTI.html#setUserId TiiLTI->setUserId( <i>integer</i> UserId )}</li>
     * <li><b>Submission Id</b><br />{@link TiiLTI.html#setSubmissionId TiiLTI->setSubmissionId( <i>integer</i> SubmissionId )}</li>
     * <li><b>Role</b><br />{@link TiiLTI.html#setRole TiiLTI->setRole( <i>string</i> Role )}</li>
     * <li><b>Button Text</b> (Optional)<br />{@link TiiLTI.html#setButtonText TiiLTI->setButtonText( <i>string</i> ButtonText )}</li>
     * <li><b>Button Image</b> (Optional)<br />{@link TiiLTI.html#setButtonImage TiiLTI->setButtonImage( <i>string</i> ButtonImage )}</li>
     * <li><b>Form Target</b> (Optional)<br />{@link TiiLTI.html#setFormTarget TiiLTI->setFormTarget( <i>string</i> FormTarget )}</li>
     * </ul>
     * outputDownloadGradeMarkPDFForm outputs an html form or returns the html as a string
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $lti = new TiiLTI();
     * $lti->setSubmissionId( 1234 );
     * $lti->setUserId( 1234 );
     * $lti->setRole( 'Learner' );
     *
     * $api->outputDownloadGradeMarkPDFForm( $lti );
     * </pre>
     *
     * @param TiiLTI $lti
     * <br />
     * @param boolean $return
     * (Optional) Determines if the form html should be output or returned by the method<br />
     * @return mixed
     */
    public function outputDownloadGradeMarkPDFForm( $lti, $return = false ) {
        $submissionLti = $this->setOptions( new LTI( $this->apibaseurl ) );
        $params = $submissionLti->getDownloadGradeMarkPDFFormHash($lti);
        $output = $submissionLti->getFormHtml( $lti, $params, false, false );
        if ( $lti->getAsJson() ) $output = json_encode( $params );
        if ( $return ) {
            return $output;
        } else {
            echo $output;
        }
    }

    /**
     * Output an Assignment Create Launch HTML Form
     *
     * Creates an html form with the required LTI parameters to launch a request to present a Turnitin Assignment Creation form.
     *
     * outputCreateAssignmentForm accepts:
     * <ul>
     * <li><b>User Id</b><br />{@link TiiLTI.html#setUserId TiiLTI->setUserId( <i>integer</i> UserId )}</li>
     * <li><b>Class Id</b><br />{@link TiiLTI.html#setClassId TiiLTI->setClassId( <i>integer</i> ClassId )}</li>
     * <li><b>Role</b><br />{@link TiiLTI.html#setRole TiiLTI->setRole( <i>string</i> Role )}</li>
     * <li><b>Button Text</b> (Optional)<br />{@link TiiLTI.html#setButtonText TiiLTI->setButtonText( <i>string</i> ButtonText )}</li>
     * <li><b>Button Image</b> (Optional)<br />{@link TiiLTI.html#setButtonImage TiiLTI->setButtonImage( <i>string</i> ButtonImage )}</li>
     * <li><b>Form Target</b> (Optional)<br />{@link TiiLTI.html#setFormTarget TiiLTI->setFormTarget( <i>string</i> FormTarget )}</li>
     * </ul>
     * outputCreateAssignmentForm outputs an html form or returns the html as a string
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $lti = new TiiLTI();
     * $lti->setClassId( 1234 );
     * $lti->setUserId( 1234 );
     * $lti->setRole( 'Instructor' );
     *
     * $api->outputCreateAssignmentForm( $lti );
     * </pre>
     *
     * @param TiiLTI $lti
     * <br />
     * @param boolean $return
     * (Optional) Determines if the form html should be output or returned by the method<br />
     * @return mixed
     */
    public function outputCreateAssignmentForm( $lti, $return = false ) {
        $submissionLti = $this->setOptions( new LTI( $this->apibaseurl ) );
        $params = $submissionLti->getCreateAssignmentFormHash($lti);
        $output = $submissionLti->getFormHtml( $lti, $params, false, false );
        if ( $lti->getAsJson() ) $output = json_encode( $params );
        if ( $return ) {
            return $output;
        } else {
            echo $output;
        }
    }

    /**
     * Output an Assignment Edit Launch HTML Form
     *
     * Creates an html form with the required LTI parameters to launch a request to present a Turnitin Assignment Edit form.
     *
     * outputEditAssignmentForm accepts:
     * <ul>
     * <li><b>User Id</b><br />{@link TiiLTI.html#setUserId TiiLTI->setUserId( <i>integer</i> UserId )}</li>
     * <li><b>Assignment Id</b><br />{@link TiiLTI.html#setAssignmentId TiiLTI->setAssignmentId( <i>integer</i> AssignmentId )}</li>
     * <li><b>Role</b><br />{@link TiiLTI.html#setRole TiiLTI->setRole( <i>string</i> Role )}</li>
     * <li><b>Button Text</b> (Optional)<br />{@link TiiLTI.html#setButtonText TiiLTI->setButtonText( <i>string</i> ButtonText )}</li>
     * <li><b>Button Image</b> (Optional)<br />{@link TiiLTI.html#setButtonImage TiiLTI->setButtonImage( <i>string</i> ButtonImage )}</li>
     * <li><b>Form Target</b> (Optional)<br />{@link TiiLTI.html#setFormTarget TiiLTI->setFormTarget( <i>string</i> FormTarget )}</li>
     * </ul>
     * outputEditAssignmentForm outputs an html form or returns the html as a string
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $lti = new TiiLTI();
     * $lti->setAssignmentId( 1234 );
     * $lti->setUserId( 1234 );
     * $lti->setRole( 'Instructor' );
     *
     * $api->outputEditAssignmentForm( $lti );
     * </pre>
     *
     * @param TiiLTI $lti
     * <br />
     * @param boolean $return
     * (Optional) Determines if the form html should be output or returned by the method<br />
     * @return mixed
     */
    public function outputEditAssignmentForm( $lti, $return = false ) {
        $submissionLti = $this->setOptions( new LTI( $this->apibaseurl ) );
        $params = $submissionLti->getEditAssignmentFormHash($lti);
        $output = $submissionLti->getFormHtml( $lti, $params, false, false );
        if ( $lti->getAsJson() ) $output = json_encode( $params );
        if ( $return ) {
            return $output;
        } else {
            echo $output;
        }
    }

    /**
     * Output an Assignment Inbox Launch HTML Form
     *
     * Creates an html form with the required LTI parameters to launch a request to present a Turnitin Assignment Inbox or Dashboard.
     * When executed usder the role 'Instructor' a full inbox will be launched, if the role 'Learner' is used a Student Dashboard will be launched
     *
     * outputAssignmentInboxForm accepts:
     * <ul>
     * <li><b>User Id</b><br />{@link TiiLTI.html#setUserId TiiLTI->setUserId( <i>integer</i> UserId )}</li>
     * <li><b>Assignment Id</b><br />{@link TiiLTI.html#setAssignmentId TiiLTI->setAssignmentId( <i>integer</i> AssignmentId )}</li>
     * <li><b>Role</b><br />{@link TiiLTI.html#setRole TiiLTI->setRole( <i>string</i> Role )}</li>
     * <li><b>Button Text</b> (Optional)<br />{@link TiiLTI.html#setButtonText TiiLTI->setButtonText( <i>string</i> ButtonText )}</li>
     * <li><b>Button Image</b> (Optional)<br />{@link TiiLTI.html#setButtonImage TiiLTI->setButtonImage( <i>string</i> ButtonImage )}</li>
     * <li><b>Form Target</b> (Optional)<br />{@link TiiLTI.html#setFormTarget TiiLTI->setFormTarget( <i>string</i> FormTarget )}</li>
     * </ul>
     * outputAssignmentInboxForm outputs an html form or returns the html as a string
     *
     * <h3>Example Code:</h3>
     * <pre class="prettyprint lang-perl" style="padding: 12px;">
     * $api = new APITurnitin( 1234, 'https://sandbox.turnitin.com', 'mysecret', 16 );
     * $lti = new TiiLTI();
     * $lti->setAssignmentId( 1234 );
     * $lti->setUserId( 1234 );
     * $lti->setRole( 'Instructor' );
     *
     * $api->outputAssignmentInboxForm( $lti );
     * </pre>
     *
     * @param TiiLTI $lti
     * <br />
     * @param boolean $return
     * (Optional) Determines if the form html should be output or returned by the method<br />
     * @return mixed
     */
    public function outputAssignmentInboxForm( $lti, $return = false ) {
        $submissionLti = $this->setOptions( new LTI( $this->apibaseurl ) );
        $params = $submissionLti->getAssignmentInboxFormHash($lti);
        $output = $submissionLti->getFormHtml( $lti, $params, false, false );
        if ( $lti->getAsJson() ) $output = json_encode( $params );
        if ( $return ) {
            return $output;
        } else {
            echo $output;
        }
    }

}

