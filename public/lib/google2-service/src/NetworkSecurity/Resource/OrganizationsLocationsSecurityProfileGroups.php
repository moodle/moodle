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

namespace Google\Service\NetworkSecurity\Resource;

use Google\Service\NetworkSecurity\ListSecurityProfileGroupsResponse;
use Google\Service\NetworkSecurity\Operation;
use Google\Service\NetworkSecurity\SecurityProfileGroup;

/**
 * The "securityProfileGroups" collection of methods.
 * Typical usage is:
 *  <code>
 *   $networksecurityService = new Google\Service\NetworkSecurity(...);
 *   $securityProfileGroups = $networksecurityService->organizations_locations_securityProfileGroups;
 *  </code>
 */
class OrganizationsLocationsSecurityProfileGroups extends \Google\Service\Resource
{
  /**
   * Creates a new SecurityProfileGroup in a given organization and location.
   * (securityProfileGroups.create)
   *
   * @param string $parent Required. The parent resource of the
   * SecurityProfileGroup. Must be in the format
   * `projects|organizations/locations/{location}`.
   * @param SecurityProfileGroup $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string securityProfileGroupId Required. Short name of the
   * SecurityProfileGroup resource to be created. This value should be 1-63
   * characters long, containing only letters, numbers, hyphens, and underscores,
   * and should not start with a number. E.g. "security_profile_group1".
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, SecurityProfileGroup $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single SecurityProfileGroup. (securityProfileGroups.delete)
   *
   * @param string $name Required. A name of the SecurityProfileGroup to delete.
   * Must be in the format `projects|organizations/locations/{location}/securityPr
   * ofileGroups/{security_profile_group}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag Optional. If client provided etag is out of date,
   * delete will return FAILED_PRECONDITION error.
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
   * Gets details of a single SecurityProfileGroup. (securityProfileGroups.get)
   *
   * @param string $name Required. A name of the SecurityProfileGroup to get. Must
   * be in the format `projects|organizations/locations/{location}/securityProfile
   * Groups/{security_profile_group}`.
   * @param array $optParams Optional parameters.
   * @return SecurityProfileGroup
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], SecurityProfileGroup::class);
  }
  /**
   * Lists SecurityProfileGroups in a given organization and location.
   * (securityProfileGroups.listOrganizationsLocationsSecurityProfileGroups)
   *
   * @param string $parent Required. The project or organization and location from
   * which the SecurityProfileGroups should be listed, specified in the format
   * `projects|organizations/locations/{location}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Maximum number of SecurityProfileGroups to return per
   * call.
   * @opt_param string pageToken The value returned by the last
   * `ListSecurityProfileGroupsResponse` Indicates that this is a continuation of
   * a prior `ListSecurityProfileGroups` call, and that the system should return
   * the next page of data.
   * @return ListSecurityProfileGroupsResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsLocationsSecurityProfileGroups($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListSecurityProfileGroupsResponse::class);
  }
  /**
   * Updates the parameters of a single SecurityProfileGroup.
   * (securityProfileGroups.patch)
   *
   * @param string $name Immutable. Identifier. Name of the SecurityProfileGroup
   * resource. It matches pattern `projects|organizations/locations/{location}/sec
   * urityProfileGroups/{security_profile_group}`.
   * @param SecurityProfileGroup $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten in the SecurityProfileGroup resource by the update.
   * The fields specified in the update_mask are relative to the resource, not the
   * full request. A field will be overwritten if it is in the mask.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, SecurityProfileGroup $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsLocationsSecurityProfileGroups::class, 'Google_Service_NetworkSecurity_Resource_OrganizationsLocationsSecurityProfileGroups');
