<?php
/* @ignore
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Integrations\PhpSdk;

class UserSoap extends Soap
{

    public $ns;

    public static $nametype_vocab = 'http://www.imsglobal.org/vdex/lis/pmsv2p0/nametypevocabularyv1p0.xml';
    public static $partname_vocab = 'http://www.imsglobal.org/vdex/lis/pmsv2p0/partnamevocabularyv1p0.xml';
    public static $contactinfotype_vocab = 'http://www.imsglobal.org/vdex/lis/pmsv2p0/contactinfotypevocabularyv1p0.xml';
    public static $enterpriseroletype_vocab = 'http://www.imsglobal.org/vdex/lis/pmsv2p0/epriserolestypevocabularyv1p0.xml';
    public static $institutionroletype_vocab = 'http://www.imsglobal.org/vdex/lis/pmsv2p0/institutionroletypevocabularyv1p0.xml';

    public static $extensionvalue_vocab = 'http://www.imsglobal.org/vdex/lis/pmsv2p0/extensionvocabularyv1p0.xml';
    public static $extensionname_vocab = 'http://www.turnitin.com/static/source/media/turnitinvocabularyv1p0.xml';

    /**
     * UserSoap constructor.
     * @param $wsdl
     * @param $options
     */
    public function __construct($wsdl, $options)
    {
        $this->ns = 'http://www.imsglobal.org/services/lis/pms2p0/wsdl11/sync/imspms_v2p0';
        parent::__construct($wsdl, $options);
    }

    /**
     * @param $user
     * @return Response
     * @throws TurnitinApiException
     * @throws TurnitinSDKException
     */
    public function createUser($user)
    {
        try {
            $request = $this->buildUserRequest($user);
            $soap = $this->createByProxyPerson($request);
            $response = new Response($this);
            if ($response->getStatus() == 'error') {
                throw new TurnitinApiException(
                    $response->getStatusCode(),
                    $response->getDescription()
                );
            } else {
                $tiiUser = new TiiUser();
                $tiiUser->setUserId($soap->sourcedId);
                $response->setUser($tiiUser);
            }
            return $response;
        } catch (\SoapFault $e) {
            $this->logresponse = true;
            throw new TurnitinSDKException(
                $e->faultcode,
                $e->faultstring,
                parent::getLogPath(),
                $this
            );
        }
    }

    /**
     * @param $user
     * @return Response
     * @throws TurnitinApiException
     * @throws TurnitinSDKException
     */
    public function updateUser($user)
    {
        try {
            $request = $this->buildUserRequest($user, true);
            $this->updatePerson($request);
            $response = new Response($this);
            if ($response->getStatus() == 'error') {
                throw new TurnitinApiException(
                    $response->getStatusCode(),
                    $response->getDescription()
                );
            } else {
                $tiiUser = new TiiUser();
                $response->setUser($tiiUser);
            }
            return $response;
        } catch (\SoapFault $e) {
            $this->logresponse = true;
            throw new TurnitinSDKException(
                $e->faultcode,
                $e->faultstring,
                parent::getLogPath(),
                $this
            );
        }
    }

    /**
     * @param $user
     * @return Response
     * @throws TurnitinApiException
     * @throws TurnitinSDKException
     */
    public function readUsers($user)
    {
        try {
            $soap = $this->readPersons(array('sourcedIdSet' => array('sourcedId' => $user->getUserIds())));
            $response = new Response($this);
            if ($response->getStatus() == 'error') {
                throw new TurnitinApiException(
                    $response->getStatusCode(),
                    $response->getDescription()
                );
            } else {
                $users = array();
                if (isset($soap->personRecordSet->personRecord)) {
                    if (!is_array($soap->personRecordSet->personRecord)) {
                        $soap->personRecordSet->personRecord = array($soap->personRecordSet->personRecord);
                    }
                    foreach ($soap->personRecordSet->personRecord as $record) {
                        $tiiUser = new TiiUser();
                        $tiiUser->setUserId($record->sourcedGUID->sourcedId);
                        $role = $record->person->roles->institutionRole->institutionroletype->instanceValue->textString;
                        $tiiUser->setDefaultRole($role);
                        $tiiUser->setEmail($record->person->contactinfo->contactinfoValue->textString);
                        foreach ($record->person->name->partName as $partname) {
                            if ($partname->instanceName->textString == 'First') {
                                $tiiUser->setFirstname($partname->instanceValue->textString);
                            } elseif ($partname->instanceName->textString == 'Last') {
                                $tiiUser->setLastname($partname->instanceValue->textString);
                            }
                            foreach ($record->person->extension->extensionField as $field) {
                                $name = $field->fieldName;
                                $method = 'set' . $name;
                                if (is_callable(array($tiiUser, $method))) {
                                    $tiiUser->$method($field->fieldValue);
                                }
                            }
                        }
                        $users[] = $tiiUser;
                    }
                }
                $response->setUsers($users);
            }
            return $response;
        } catch (\SoapFault $e) {
            $this->logresponse = true;
            throw new TurnitinSDKException(
                $e->faultcode,
                $e->faultstring,
                parent::getLogPath(),
                $this
            );
        }
    }

    /**
     * @param $user
     * @return Response
     * @throws TurnitinApiException
     * @throws TurnitinSDKException
     */
    public function readUser($user)
    {
        try {
            $soap = $this->readPerson(array('sourcedId' => $user->getUserId()));
            $response = new Response($this);
            if ($response->getStatus() == 'error') {
                throw new TurnitinApiException(
                    $response->getStatusCode(),
                    $response->getDescription()
                );
            } else {
                $tiiUser = new TiiUser();
                $tiiUser->setUserId($soap->personRecord->sourcedGUID->sourcedId);
                $person = $soap->personRecord->person;
                $role = $person->roles->institutionRole->institutionroletype->instanceValue->textString;
                $tiiUser->setDefaultRole($role);
                $tiiUser->setEmail($person->contactinfo->contactinfoValue->textString);
                foreach ($person->name->partName as $partname) {
                    if ($partname->instanceName->textString == 'First') {
                        $tiiUser->setFirstname($partname->instanceValue->textString);
                    } elseif ($partname->instanceName->textString == 'Last') {
                        $tiiUser->setLastname($partname->instanceValue->textString);
                    }
                }
                foreach ($person->extension->extensionField as $field) {
                    $name = $field->fieldName;
                    $method = 'set' . $name;
                    if (is_callable(array($tiiUser, $method))) {
                        $tiiUser->$method($field->fieldValue);
                    }
                }
                $response->setUser($tiiUser);
            }
            return $response;
        } catch (\SoapFault $e) {
            $this->logresponse = true;
            throw new TurnitinSDKException(
                $e->faultcode,
                $e->faultstring,
                parent::getLogPath(),
                $this
            );
        }
    }

    /**
     * @param $user
     * @return Response
     * @throws TurnitinApiException
     * @throws TurnitinSDKException
     */
    public function findUser($user)
    {
        try {
            $request = array('queryObject' => $user->getEmail());
            $soap = $this->discoverPersonIds($request);
            $response = new Response($this);
            if ($response->getStatus() == 'warning') {
                throw new TurnitinApiException(
                    $response->getStatusCode(),
                    $response->getDescription()
                );
            } else {
                $tiiUser = new TiiUser();
                if (isset($soap->sourcedIdSet->sourcedId)) {
                    $tiiUser->setUserId($soap->sourcedIdSet->sourcedId);
                }
                $response->setUser($tiiUser);
            }
            return $response;
        } catch (\SoapFault $e) {
            $this->logresponse = true;
            throw new TurnitinSDKException(
                $e->faultcode,
                $e->faultstring,
                parent::getLogPath(),
                $this
            );
        }
    }

    /**
     * @param $user
     * @param bool $update
     * @return array
     */
    private function buildUserRequest($user, $update = false)
    {
        $request = array();
        if ($update) { // Update
            $request['sourcedId'] = $user->getUserId();
        } else { // Create
            $request['sourcedId'] = null;
        }
        $person = array();
        $request['personRecord']['sourcedGUID']['sourcedId'] = $request['sourcedId'];
        $person['name']['nameType']['instanceIdentifier']['language'] = parent::$lislanguage;
        $person['name']['nameType']['instanceIdentifier']['textString'] = '1';
        $person['name']['nameType']['instanceVocabulary'] = self::$nametype_vocab;
        $person['name']['nameType']['instanceValue']['language'] = parent::$lislanguage;
        $person['name']['nameType']['instanceValue']['textString'] = 'Contact';
        $person['name']['partName'][0]['instanceIdentifier']['language'] = parent::$lislanguage;
        $person['name']['partName'][0]['instanceIdentifier']['textString'] = '1';
        $person['name']['partName'][0]['instanceVocabulary'] = self::$partname_vocab;
        $person['name']['partName'][0]['instanceName']['language'] = parent::$lislanguage;
        $person['name']['partName'][0]['instanceName']['textString'] = 'First';
        $person['name']['partName'][0]['instanceValue']['language'] = parent::$lislanguage;
        $person['name']['partName'][0]['instanceValue']['textString'] = $user->getFirstname();
        $person['name']['partName'][1]['instanceIdentifier']['language'] = parent::$lislanguage;
        $person['name']['partName'][1]['instanceIdentifier']['textString'] = '2';
        $person['name']['partName'][1]['instanceVocabulary'] = self::$partname_vocab;
        $person['name']['partName'][1]['instanceName']['language'] = parent::$lislanguage;
        $person['name']['partName'][1]['instanceName']['textString'] = 'Last';
        $person['name']['partName'][1]['instanceValue']['language'] = parent::$lislanguage;
        $person['name']['partName'][1]['instanceValue']['textString'] = $user->getLastname();
        if ($user->getEmail()) {
            $person['contactinfo']['contactinfoType']['instanceIdentifier']['language'] = parent::$lislanguage;
            $person['contactinfo']['contactinfoType']['instanceIdentifier']['textString'] = '1';
            $person['contactinfo']['contactinfoType']['instanceVocabulary'] = self::$contactinfotype_vocab;
            $person['contactinfo']['contactinfoType']['instanceValue']['language'] = parent::$lislanguage;
            $person['contactinfo']['contactinfoType']['instanceValue']['textString'] = 'EmailWorkPrimary';
            $person['contactinfo']['contactinfoValue']['language'] = parent::$lislanguage;
            $person['contactinfo']['contactinfoValue']['textString'] = $user->getEmail();
        }
        $person['roles']['enterpriserolesType']['instanceIdentifier']['language'] = parent::$lislanguage;
        $person['roles']['enterpriserolesType']['instanceIdentifier']['textString'] = '1';
        $person['roles']['enterpriserolesType']['instanceVocabulary'] = self::$enterpriseroletype_vocab;
        $person['roles']['enterpriserolesType']['instanceName']['language'] = parent::$lislanguage;
        $person['roles']['enterpriserolesType']['instanceName']['textString'] = 'Other';
        $person['roles']['enterpriserolesType']['instanceValue']['language'] = parent::$lislanguage;
        $person['roles']['enterpriserolesType']['instanceValue']['textString'] = 'Other';

        $institutionrole = [];
        $institutionrole['institutionroletype']['instanceIdentifier']['language'] = parent::$lislanguage;
        $institutionrole['institutionroletype']['instanceIdentifier']['textString'] = '1';
        $institutionrole['institutionroletype']['instanceVocabulary'] = self::$institutionroletype_vocab;
        $institutionrole['institutionroletype']['instanceValue']['language'] = parent::$lislanguage;
        $institutionrole['institutionroletype']['instanceValue']['textString'] = $user->getDefaultRole();

        $institutionrole['primaryroletype'] = '1';
        $person['roles']['institutionRole'] = $institutionrole;

        if ($user->getDefaultLanguage()) {
            $person['extension']['extensionNameVocabulary'] = self::$extensionname_vocab;
            $person['extension']['extensionValueVocabulary'] = self::$extensionvalue_vocab;

            $person['extension']['extensionField']['fieldName'] = 'DefaultLanguage';
            $person['extension']['extensionField']['fieldType'] = 'String';
            $person['extension']['extensionField']['fieldValue'] = $user->getDefaultLanguage();
        }
        $request['personRecord']['person'] = $person;
        return $request;
    }
}
