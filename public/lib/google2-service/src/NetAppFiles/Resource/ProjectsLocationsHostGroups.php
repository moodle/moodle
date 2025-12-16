<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\NetAppFiles\Resource;

use Google\Service\NetAppFiles\HostGroup;
use Google\Service\NetAppFiles\ListHostGroupsResponse;
use Google\Service\NetAppFiles\Operation;

/**
 * The "hostGroups" collection of methods.
 * Typical usage is:
 *  <code>
 *   $netappService = new Google\Service\NetAppFiles(...);
 *   $hostGroups = $netappService->projects_locations_hostGroups;
 *  </code>
 */
class ProjectsLocationsHostGroups extends \Google\Service\Resource
{
  /**
   * Creates a new host group. (hostGroups.create)
   *
   * @param string $parent Required. Parent value for CreateHostGroupRequest
   * @param HostGroup $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string hostGroupId Required. ID of the host group to create. Must
   * be unique within the parent resource. Must contain only letters, numbers, and
   * hyphen, with the first character a letter or underscore, the last a letter or
   * underscore or a number, and a 63 character maximum.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, HostGroup $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a host group. (hostGroups.delete)
   *
   * @param string $name Required. The resource name of the host group. Format: `p
   * rojects/{project_number}/locations/{location_id}/hostGroups/{host_group_id}`.
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], Operation::class);
  }
  /**
   * Returns details of the specified host group. (hostGroups.get)
   *
   * @param string $name Required. The resource name of the host group. Format: `p
   * rojects/{project_number}/locations/{location_id}/hostGroups/{host_group_id}`.
   * @param array $optParams Optional parameters.
   * @return HostGroup
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], HostGroup::class);
  }
  /**
   * Returns a list of host groups in a `location`. Use `-` as location to list
   * host groups across all locations.
   * (hostGroups.listProjectsLocationsHostGroups)
   *
   * @param string $parent Required. Parent value for ListHostGroupsRequest
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filter to apply to the request.
   * @opt_param string orderBy Optional. Hint for how to order the results
   * @opt_param int pageSize Optional. Requested page size. Server may return
   * fewer items than requested. If unspecified, the server will pick an
   * appropriate default.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return.
   * @return ListHostGroupsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsHostGroups($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListHostGroupsResponse::class);
  }
  /**
   * Updates an existing host group. (hostGroups.patch)
   *
   * @param string $name Identifier. The resource name of the host group. Format:
   * `projects/{project_number}/locations/{location_id}/hostGroups/{host_group_id}
   * `.
   * @param HostGroup $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The list of fields to update.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, HostGroup $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsHostGroups::class, 'Google_Service_NetAppFiles_Resource_ProjectsLocationsHostGroups');
