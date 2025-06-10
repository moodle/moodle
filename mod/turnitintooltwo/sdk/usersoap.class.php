<?php
/* @ignore
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once( __DIR__.'/soap.class.php' );
require_once( __DIR__.'/tiiuser.class.php' );
require_once( __DIR__.'/tiipseudouser.class.php' );
require_once( __DIR__.'/response.class.php' );
require_once( __DIR__.'/sdkexception.class.php' );

/**
 * @ignore
 */
class UserSoap extends Soap {

    public $ns;

    public static $nametype_vocab = 'http://www.imsglobal.org/vdex/lis/pmsv2p0/nametypevocabularyv1p0.xml';
    public static $partname_vocab = 'http://www.imsglobal.org/vdex/lis/pmsv2p0/partnamevocabularyv1p0.xml';
    public static $contactinfotype_vocab = 'http://www.imsglobal.org/vdex/lis/pmsv2p0/contactinfotypevocabularyv1p0.xml';
    public static $enterpriseroletype_vocab = 'http://www.imsglobal.org/vdex/lis/pmsv2p0/epriserolestypevocabularyv1p0.xml';
    public static $institutionroletype_vocab = 'http://www.imsglobal.org/vdex/lis/pmsv2p0/institutionroletypevocabularyv1p0.xml';

    public static $extensionvalue_vocab = 'http://www.imsglobal.org/vdex/lis/pmsv2p0/extensionvocabularyv1p0.xml';
    public static $extensionname_vocab = 'http://www.turnitin.com/static/source/media/turnitinvocabularyv1p0.xml';

    public function __construct( $wsdl, $options ) {
        $this->ns = 'http://www.imsglobal.org/services/lis/pms2p0/wsdl11/sync/imspms_v2p0';
        parent::__construct( $wsdl, $options );
    }

    public function createUser( $user ) {
        try {
            $request = $this->buildUserRequest( $user );
            $soap = $this->createByProxyPerson( $request );
            $response = new Response( $this );
            if ( $response->getStatus() == 'error' ) {
                throw new TurnitinSDKException( $response->getStatusCode(), $response->getDescription() );
            } else {
                $tiiUser = new TiiUser();
                $tiiUser->setUserId( $soap->sourcedId );
                $response->setUser( $tiiUser );
            }
            return $response;
        } catch ( SoapFault $e ) {
            throw new TurnitinSDKException( $e->faultcode, $e->faultstring, parent::getLogPath() );
        }
    }

    public function updateUser( $user ) {
        try {
            $request = $this->buildUserRequest( $user, true );
            $this->updatePerson( $request );
            $response = new Response( $this );
            if ( $response->getStatus() == 'error' ) {
                throw new TurnitinSDKException( $response->getStatusCode(), $response->getDescription() );
            } else {
                $tiiUser = new TiiUser();
                $response->setUser( $tiiUser );
            }
            return $response;
        } catch ( SoapFault $e ) {
            throw new TurnitinSDKException( $e->faultcode, $e->faultstring, parent::getLogPath() );
        }
    }

    public function readUsers( $user ) {
        try {
            $soap = $this->readPersons( array( 'sourcedIdSet' => array( 'sourcedId' => $user->getUserIds() ) ) );
            $response = new Response( $this );
            if ( $response->getStatus() == 'error' ) {
                throw new TurnitinSDKException( $response->getStatusCode(), $response->getDescription() );
            } else {
                $users = array();
                if ( isset( $soap->personRecordSet->personRecord ) ) {
                    if ( !is_array( $soap->personRecordSet->personRecord ) ) $soap->personRecordSet->personRecord = array( $soap->personRecordSet->personRecord );
                    foreach ( $soap->personRecordSet->personRecord as $record ) {
                        $tiiUser = new TiiUser();
                        $tiiUser->setUserId( $record->sourcedGUID->sourcedId );
                        $tiiUser->setDefaultRole( $record->person->roles->institutionRole->institutionroletype->instanceValue->textString );
                        $tiiUser->setEmail( $record->person->contactinfo->contactinfoValue->textString );
                        foreach ( $record->person->name->partName as $partname ) {
                            if ( $partname->instanceName->textString == 'First' ) {
                                $tiiUser->setFirstname( $partname->instanceValue->textString );
                            } else if ( $partname->instanceName->textString == 'Last' ) {
                                $tiiUser->setLastname( $partname->instanceValue->textString );
                            }
                            $record->person->extension->extensionField = is_array( $record->person->extension->extensionField )
                                ? $record->person->extension->extensionField
                                : array( $record->person->extension->extensionField );
                            foreach ( $record->person->extension->extensionField as $field ) {
                                $name = $field->fieldName;
                                $method = 'set'.$name;
                                if ( is_callable( array( $tiiUser, $method ) ) ) $tiiUser->$method($field->fieldValue);
                            }
                        }
                        $users[] = $tiiUser;
                    }
                }
                $response->setUsers( $users );
            }
            return $response;
        } catch ( SoapFault $e ) {
            throw new TurnitinSDKException( $e->faultcode, $e->faultstring, parent::getLogPath() );
        }
    }

    public function readUser( $user ) {
        try {
            $soap = $this->readPerson( array( 'sourcedId' => $user->getUserId() ) );
            $response = new Response( $this );
            if ( $response->getStatus() == 'error' ) {
                throw new TurnitinSDKException( $response->getStatusCode(), $response->getDescription() );
            } else {
                $tiiUser = new TiiUser();
                $tiiUser->setUserId( $soap->personRecord->sourcedGUID->sourcedId );
                $tiiUser->setDefaultRole( $soap->personRecord->person->roles->institutionRole->institutionroletype->instanceValue->textString );
                $tiiUser->setEmail( $soap->personRecord->person->contactinfo->contactinfoValue->textString );
                foreach ( $soap->personRecord->person->name->partName as $partname ) {
                    if ( $partname->instanceName->textString == 'First' ) {
                        $tiiUser->setFirstname( $partname->instanceValue->textString );
                    } else if ( $partname->instanceName->textString == 'Last' ) {
                        $tiiUser->setLastname( $partname->instanceValue->textString );
                    }
                }
                $soap->personRecord->person->extension->extensionField = is_array( $soap->personRecord->person->extension->extensionField )
                    ? $soap->personRecord->person->extension->extensionField
                    : array( $soap->personRecord->person->extension->extensionField );
                foreach ( $soap->personRecord->person->extension->extensionField as $field ) {
                    $name = $field->fieldName;
                    $method = 'set'.$name;
                    if ( is_callable( array( $tiiUser, $method ) ) ) $tiiUser->$method($field->fieldValue);
                }
                $response->setUser( $tiiUser );
            }
            return $response;
        } catch ( SoapFault $e ) {
            throw new TurnitinSDKException( $e->faultcode, $e->faultstring, parent::getLogPath() );
        }
    }

    public function findUser( $user ) {
        try {
            $request = array( 'queryObject' => $user->getEmail() );
            $soap = $this->discoverPersonIds( $request );
            $response = new Response( $this );
            if ( $response->getStatus() == 'error' ) {
                throw new TurnitinSDKException( $response->getStatusCode(), $response->getDescription() );
            } else {
                $tiiUser = new TiiUser();
                if ( isset( $soap->sourcedIdSet->sourcedId ) ) $tiiUser->setUserId( $soap->sourcedIdSet->sourcedId );
                $response->setUser( $tiiUser );
            }
            return $response;
        } catch ( SoapFault $e ) {
            throw new TurnitinSDKException( $e->faultcode, $e->faultstring, parent::getLogPath() );
        }
    }

    private function buildUserRequest( $user, $update = false ) {
        $request = array();
        if ( $update ) { // Update
            $request['sourcedId'] = $user->getUserId();
        } else { // Create
            $request['sourcedId'] = null;
        }
        $request['personRecord']['sourcedGUID']['sourcedId'] = $request['sourcedId'];
        $request['personRecord']['person']['name']['nameType']['instanceIdentifier']['language'] = parent::$lislanguage;
        $request['personRecord']['person']['name']['nameType']['instanceIdentifier']['textString'] = '1';
        $request['personRecord']['person']['name']['nameType']['instanceVocabulary'] = self::$nametype_vocab;
        $request['personRecord']['person']['name']['nameType']['instanceValue']['language'] = parent::$lislanguage;
        $request['personRecord']['person']['name']['nameType']['instanceValue']['textString'] = 'Contact';
        $request['personRecord']['person']['name']['partName'][0]['instanceIdentifier']['language'] = parent::$lislanguage;
        $request['personRecord']['person']['name']['partName'][0]['instanceIdentifier']['textString'] = '1';
        $request['personRecord']['person']['name']['partName'][0]['instanceVocabulary'] = self::$partname_vocab;
        $request['personRecord']['person']['name']['partName'][0]['instanceName']['language'] = parent::$lislanguage;
        $request['personRecord']['person']['name']['partName'][0]['instanceName']['textString'] = 'First';
        $request['personRecord']['person']['name']['partName'][0]['instanceValue']['language'] = parent::$lislanguage;
        $request['personRecord']['person']['name']['partName'][0]['instanceValue']['textString'] = $user->getFirstname();
        $request['personRecord']['person']['name']['partName'][1]['instanceIdentifier']['language'] = parent::$lislanguage;
        $request['personRecord']['person']['name']['partName'][1]['instanceIdentifier']['textString'] = '2';
        $request['personRecord']['person']['name']['partName'][1]['instanceVocabulary'] = self::$partname_vocab;
        $request['personRecord']['person']['name']['partName'][1]['instanceName']['language'] = parent::$lislanguage;
        $request['personRecord']['person']['name']['partName'][1]['instanceName']['textString'] = 'Last';
        $request['personRecord']['person']['name']['partName'][1]['instanceValue']['language'] = parent::$lislanguage;
        $request['personRecord']['person']['name']['partName'][1]['instanceValue']['textString'] = $user->getLastname();
        if ( $user->getEmail() ) {
            $request['personRecord']['person']['contactinfo']['contactinfoType']['instanceIdentifier']['language'] = parent::$lislanguage;
            $request['personRecord']['person']['contactinfo']['contactinfoType']['instanceIdentifier']['textString'] = '1';
            $request['personRecord']['person']['contactinfo']['contactinfoType']['instanceVocabulary'] = self::$contactinfotype_vocab;
            $request['personRecord']['person']['contactinfo']['contactinfoType']['instanceValue']['language'] = parent::$lislanguage;
            $request['personRecord']['person']['contactinfo']['contactinfoType']['instanceValue']['textString'] = 'EmailWorkPrimary';
            $request['personRecord']['person']['contactinfo']['contactinfoValue']['language'] = parent::$lislanguage;
            $request['personRecord']['person']['contactinfo']['contactinfoValue']['textString'] = $user->getEmail();
        }
        $request['personRecord']['person']['roles']['enterpriserolesType']['instanceIdentifier']['language'] = parent::$lislanguage;
        $request['personRecord']['person']['roles']['enterpriserolesType']['instanceIdentifier']['textString'] = '1';
        $request['personRecord']['person']['roles']['enterpriserolesType']['instanceVocabulary'] = self::$enterpriseroletype_vocab;
        $request['personRecord']['person']['roles']['enterpriserolesType']['instanceName']['language'] = parent::$lislanguage;
        $request['personRecord']['person']['roles']['enterpriserolesType']['instanceName']['textString'] = 'Other';
        $request['personRecord']['person']['roles']['enterpriserolesType']['instanceValue']['language'] = parent::$lislanguage;
        $request['personRecord']['person']['roles']['enterpriserolesType']['instanceValue']['textString'] = 'Other';
        $request['personRecord']['person']['roles']['institutionRole']['institutionroletype']['instanceIdentifier']['language'] = parent::$lislanguage;
        $request['personRecord']['person']['roles']['institutionRole']['institutionroletype']['instanceIdentifier']['textString'] = '1';
        $request['personRecord']['person']['roles']['institutionRole']['institutionroletype']['instanceVocabulary'] = self::$institutionroletype_vocab;
        $request['personRecord']['person']['roles']['institutionRole']['institutionroletype']['instanceValue']['language'] = parent::$lislanguage;
        $request['personRecord']['person']['roles']['institutionRole']['institutionroletype']['instanceValue']['textString'] = ( $user->getDefaultRole() == 'Student' ) ? 'Learner' : $user->getDefaultRole();
        $request['personRecord']['person']['roles']['institutionRole']['primaryroletype'] = '1';

        if ( $user->getDefaultLanguage() ) {
            $request['personRecord']['person']['extension']['extensionNameVocabulary'] = self::$extensionname_vocab;
            $request['personRecord']['person']['extension']['extensionValueVocabulary'] = self::$extensionvalue_vocab;

            $request['personRecord']['person']['extension']['extensionField']['fieldName'] = 'DefaultLanguage';
            $request['personRecord']['person']['extension']['extensionField']['fieldType'] = 'String';
            $request['personRecord']['person']['extension']['extensionField']['fieldValue'] = $user->getDefaultLanguage();
        }
        return $request;
    }

}

//?>