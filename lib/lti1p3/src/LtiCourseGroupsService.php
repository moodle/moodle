<?php

namespace Packback\Lti1p3;

class LtiCourseGroupsService extends LtiAbstractService
{
    public const CONTENTTYPE_CONTEXTGROUPCONTAINER = 'application/vnd.ims.lti-gs.v1.contextgroupcontainer+json';

    public function getScope(): array
    {
        return $this->getServiceData()['scope'];
    }

    public function getGroups(): array
    {
        $request = new ServiceRequest(
            LtiServiceConnector::METHOD_GET,
            $this->getServiceData()['context_groups_url']
        );
        $request->setAccept(static::CONTENTTYPE_CONTEXTGROUPCONTAINER);

        return $this->getAll($request, 'groups');
    }

    public function getSets(): array
    {
        // Sets are optional.
        if (!isset($this->getServiceData()['context_group_sets_url'])) {
            return [];
        }

        $request = new ServiceRequest(
            LtiServiceConnector::METHOD_GET,
            $this->getServiceData()['context_group_sets_url']
        );
        $request->setAccept(static::CONTENTTYPE_CONTEXTGROUPCONTAINER);

        return $this->getAll($request, 'sets');
    }

    public function getGroupsBySet()
    {
        $groups = $this->getGroups();
        $sets = $this->getSets();

        $groupsBySet = [];
        $unsetted = [];

        foreach ($sets as $key => $set) {
            $groupsBySet[$set['id']] = $set;
            $groupsBySet[$set['id']]['groups'] = [];
        }

        foreach ($groups as $key => $group) {
            if (isset($group['set_id']) && isset($groupsBySet[$group['set_id']])) {
                $groupsBySet[$group['set_id']]['groups'][$group['id']] = $group;
            } else {
                $unsetted[$group['id']] = $group;
            }
        }

        if (!empty($unsetted)) {
            $groupsBySet['none'] = [
                'name' => 'None',
                'id' => 'none',
                'groups' => $unsetted,
            ];
        }

        return $groupsBySet;
    }
}
