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

use Google\Service\NetworkSecurity\ListSecurityProfilesResponse;
use Google\Service\NetworkSecurity\Operation;
use Google\Service\NetworkSecurity\SecurityProfile;

/**
 * The "securityProfiles" collection of methods.
 * Typical usage is:
 *  <code>
 *   $networksecurityService = new Google\Service\NetworkSecurity(...);
 *   $securityProfiles = $networksecurityService->organizations_locations_securityProfiles;
 *  </code>
 */
class OrganizationsLocationsSecurityProfiles extends \Google\Service\Resource
{
  /**
   * Creates a new SecurityProfile in a given organization and location.
   * (securityProfiles.create)
   *
   * @param string $parent Required. The parent resource of the SecurityProfile.
   * Must be in the format `projects|organizations/locations/{location}`.
   * @param SecurityProfile $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string securityProfileId Required. Short name of the
   * SecurityProfile resource to be created. This value should be 1-63 characters
   * long, containing only letters, numbers, hyphens, and underscores, and should
   * not start with a number. E.g. "security_profile1".
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, SecurityProfile $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single SecurityProfile. (securityProfiles.delete)
   *
   * @param string $name Required. A name of the SecurityProfile to delete. Must
   * be in the format `projects|organizations/locations/{location}/securityProfile
   * s/{security_profile_id}`.
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
   * Gets details of a single SecurityProfile. (securityProfiles.get)
   *
   * @param string $name Required. A name of the SecurityProfile to get. Must be
   * in the format `projects|organizations/locations/{location}/securityProfiles/{
   * security_profile_id}`.
   * @param array $optParams Optional parameters.
   * @return SecurityProfile
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], SecurityProfile::class);
  }
  /**
   * Lists SecurityProfiles in a given organization and location.
   * (securityProfiles.listOrganizationsLocationsSecurityProfiles)
   *
   * @param string $parent Required. The project or organization and location from
   * which the SecurityProfiles should be listed, specified in the format
   * `projects|organizations/locations/{location}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Maximum number of SecurityProfiles to return per
   * call.
   * @opt_param string pageToken The value returned by the last
   * `ListSecurityProfilesResponse` Indicates that this is a continuation of a
   * prior `ListSecurityProfiles` call, and that the system should return the next
   * page of data.
   * @return ListSecurityProfilesResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsLocationsSecurityProfiles($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListSecurityProfilesResponse::class);
  }
  /**
   * Updates the parameters of a single SecurityProfile. (securityProfiles.patch)
   *
   * @param string $name Immutable. Identifier. Name of the SecurityProfile
   * resource. It matches pattern `projects|organizations/locations/{location}/sec
   * urityProfiles/{security_profile}`.
   * @param SecurityProfile $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten in the SecurityProfile resource by the update. The
   * fields specified in the update_mask are relative to the resource, not the
   * full request. A field will be overwritten if it is in the mask.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, SecurityProfile $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsLocationsSecurityProfiles::class, 'Google_Service_NetworkSecurity_Resource_OrganizationsLocationsSecurityProfiles');
