<?php
/* @ignore
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Integrations\PhpSdk;

/**
 * @ignore
 */
class MembershipSoap extends Soap
{

    public $ns;

    public function __construct($wsdl, $options)
    {
        $this->ns = 'http://www.imsglobal.org/services/lis/oms1p0/wsdl11/sync/imsoms_v1p0';
        parent::__construct($wsdl, $options);
    }

    /**
     * @param $membership
     * @return Response
     * @throws TurnitinApiException
     * @throws TurnitinSDKException
     */
    public function createMembership($membership)
    {
        try {
            $request = $this->buildMembershipRequest($membership);
            $soap = $this->createByProxyMembership($request);
            $response = new Response($this);
            if ($response->getStatus() == 'error') {
                throw new TurnitinApiException(
                    $response->getStatusCode(),
                    $response->getDescription()
                );
            } else {
                $tiiMembership = new TiiMembership();
                $tiiMembership->setMembershipId($soap->sourcedId);
                $response->setMembership($tiiMembership);
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
     * @param $membership
     * @return Response
     * @throws TurnitinApiException
     * @throws TurnitinSDKException
     */
    public function readMembershipSoap($membership)
    {
        try {
            $soap = $this->readMembership(array('sourcedId' => $membership->getMembershipId()));
            $response = new Response($this);
            if ($response->getStatus() == 'error') {
                throw new TurnitinApiException(
                    $response->getStatusCode(),
                    $response->getDescription()
                );
            } else {
                $tiiMembership = new TiiMembership();
                $tiiMembership->setMembershipId($soap->membershipRecord->sourcedGUID->sourcedId);
                $tiiMembership->setClassId($soap->membershipRecord->membership->collectionSourcedId);
                $tiiMembership->setUserId($soap->membershipRecord->membership->member->personSourcedId);
                $tiiMembership->setRole($soap->membershipRecord->membership->member->role->roleType);
                $response->setMembership($tiiMembership);
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
     * @param $membership
     * @return Response
     * @throws TurnitinApiException
     * @throws TurnitinSDKException
     */
    public function readMembershipsSoap($membership)
    {
        try {
            $soap = $this->readMemberships(array(
                'sourcedIdSet' => array (
                    'sourcedId' => $membership->getMembershipIds()
                )
            ));
            $response = new Response($this);
            if ($response->getStatus() == 'error') {
                throw new TurnitinApiException(
                    $response->getStatusCode(),
                    $response->getDescription()
                );
            } else {
                $memberships = array();
                if (isset($soap->membershipRecordSet->membershipRecord)) {
                    if (!is_array($soap->membershipRecordSet->membershipRecord)) {
                        $soap->membershipRecordSet->membershipRecord = array(
                            $soap->membershipRecordSet->membershipRecord
                        );
                    }
                    foreach ($soap->membershipRecordSet->membershipRecord as $record) {
                        $tiiMembership = new TiiMembership();
                        $tiiMembership->setMembershipId($record->sourcedGUID->sourcedId);
                        $tiiMembership->setClassId($record->membership->collectionSourcedId);
                        $tiiMembership->setUserId($record->membership->member->personSourcedId);
                        $tiiMembership->setRole($record->membership->member->role->roleType);
                        $memberships[] = $tiiMembership;
                    }
                }
                $response->setMemberships($memberships);
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
     * @param $membership
     * @return Response
     * @throws TurnitinApiException
     * @throws TurnitinSDKException
     */
    public function deleteMembershipSoap($membership)
    {
        try {
            $this->deleteMembership(array('sourcedId' => $membership->getMembershipId()));
            $response = new Response($this);
            if ($response->getStatus() == 'error') {
                throw new TurnitinApiException(
                    $response->getStatusCode(),
                    $response->getDescription()
                );
            } else {
                $tiiMembership = new TiiMembership();
                $response->setMembership($tiiMembership);
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
     * @param $membership
     * @return Response
     * @throws TurnitinApiException
     * @throws TurnitinSDKException
     */
    public function findMemberships($membership)
    {
        try {
            $soap = $this->readMembershipIdsForCollection(array(
                'groupSourcedId' => $membership->getClassId(),
                'collection' => 'courseSection'
            ));
            $response = new Response($this);
            if ($response->getStatus() == 'error') {
                throw new TurnitinApiException(
                    $response->getStatusCode(),
                    $response->getDescription()
                );
            } else {
                $tiiMembership = new TiiMembership();
                $membershipids = array();
                if (isset($soap->sourcedIdSet->sourcedId)) {
                    if (!is_array($soap->sourcedIdSet->sourcedId)) {
                        $soap->sourcedIdSet->sourcedId = array($soap->sourcedIdSet->sourcedId);
                    }
                    foreach ($soap->sourcedIdSet->sourcedId as $id) {
                        $membershipids[] = $id;
                    }
                }
                $tiiMembership->setMembershipIds($membershipids);
                $response->setMembership($tiiMembership);
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
     * @param $membership
     * @return array
     */
    private function buildMembershipRequest($membership)
    {
        $request = array();
        $request['membershipRecord']['sourcedGUID']['sourcedId'] = null;
        $request['membershipRecord']['membership']['collectionSourcedId'] = $membership->getClassId();
        $request['membershipRecord']['membership']['membershipIdType'] = 'courseSection';
        $request['membershipRecord']['membership']['member']['personSourcedId'] = $membership->getUserId();
        $request['membershipRecord']['membership']['member']['role']['roleType'] = $membership->getRole();
        return $request;
    }
}
