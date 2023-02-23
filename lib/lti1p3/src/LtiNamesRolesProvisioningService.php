<?php

namespace Packback\Lti1p3;

class LtiNamesRolesProvisioningService extends LtiAbstractService
{
    public const CONTENTTYPE_MEMBERSHIPCONTAINER = 'application/vnd.ims.lti-nrps.v2.membershipcontainer+json';

    public function getScope(): array
    {
        return [LtiConstants::NRPS_SCOPE_MEMBERSHIP_READONLY];
    }

    public function getMembers(): array
    {
        $request = new ServiceRequest(
            ServiceRequest::METHOD_GET,
            $this->getServiceData()['context_memberships_url'],
            ServiceRequest::TYPE_GET_MEMBERSHIPS
        );
        $request->setAccept(static::CONTENTTYPE_MEMBERSHIPCONTAINER);

        return $this->getAll($request, 'members');
    }
}
