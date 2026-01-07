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

namespace Google\Service\Apigee\Resource;

use Google\Service\Apigee\GoogleCloudApigeeV1ListSecurityProfileRevisionsResponse;
use Google\Service\Apigee\GoogleCloudApigeeV1ListSecurityProfilesResponse;
use Google\Service\Apigee\GoogleCloudApigeeV1SecurityProfile;
use Google\Service\Apigee\GoogleProtobufEmpty;

/**
 * The "securityProfiles" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apigeeService = new Google\Service\Apigee(...);
 *   $securityProfiles = $apigeeService->organizations_securityProfiles;
 *  </code>
 */
class OrganizationsSecurityProfiles extends \Google\Service\Resource
{
  /**
   * CreateSecurityProfile create a new custom security profile.
   * (securityProfiles.create)
   *
   * @param string $parent Required. Name of organization. Format:
   * organizations/{org}
   * @param GoogleCloudApigeeV1SecurityProfile $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string securityProfileId Required. The ID to use for the
   * SecurityProfile, which will become the final component of the action's
   * resource name. This value should be 1-63 characters and validated by
   * "(^[a-z]([a-z0-9-]{0,61}[a-z0-9])?$)".
   * @return GoogleCloudApigeeV1SecurityProfile
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudApigeeV1SecurityProfile $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudApigeeV1SecurityProfile::class);
  }
  /**
   * DeleteSecurityProfile delete a profile with all its revisions.
   * (securityProfiles.delete)
   *
   * @param string $name Required. Name of profile. Format:
   * organizations/{org}/securityProfiles/{profile}
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * GetSecurityProfile gets the specified security profile. Returns NOT_FOUND if
   * security profile is not present for the specified organization.
   * (securityProfiles.get)
   *
   * @param string $name Required. Security profile in the following format:
   * `organizations/{org}/securityProfiles/{profile}'. Profile may optionally
   * contain revision ID. If revision ID is not provided, the response will
   * contain latest revision by default. Example:
   * organizations/testOrg/securityProfiles/testProfile@5
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1SecurityProfile
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudApigeeV1SecurityProfile::class);
  }
  /**
   * ListSecurityProfiles lists all the security profiles associated with the org
   * including attached and unattached profiles.
   * (securityProfiles.listOrganizationsSecurityProfiles)
   *
   * @param string $parent Required. For a specific organization, list of all the
   * security profiles. Format: `organizations/{org}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of profiles to return. The service
   * may return fewer than this value. If unspecified, at most 50 profiles will be
   * returned.
   * @opt_param string pageToken A page token, received from a previous
   * `ListSecurityProfiles` call. Provide this to retrieve the subsequent page.
   * @return GoogleCloudApigeeV1ListSecurityProfilesResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsSecurityProfiles($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudApigeeV1ListSecurityProfilesResponse::class);
  }
  /**
   * ListSecurityProfileRevisions lists all the revisions of the security profile.
   * (securityProfiles.listRevisions)
   *
   * @param string $name Required. For a specific profile, list all the revisions.
   * Format: `organizations/{org}/securityProfiles/{profile}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of profile revisions to return.
   * The service may return fewer than this value. If unspecified, at most 50
   * revisions will be returned.
   * @opt_param string pageToken A page token, received from a previous
   * `ListSecurityProfileRevisions` call. Provide this to retrieve the subsequent
   * page.
   * @return GoogleCloudApigeeV1ListSecurityProfileRevisionsResponse
   * @throws \Google\Service\Exception
   */
  public function listRevisions($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('listRevisions', [$params], GoogleCloudApigeeV1ListSecurityProfileRevisionsResponse::class);
  }
  /**
   * UpdateSecurityProfile update the metadata of security profile.
   * (securityProfiles.patch)
   *
   * @param string $name Immutable. Name of the security profile resource. Format:
   * organizations/{org}/securityProfiles/{profile}
   * @param GoogleCloudApigeeV1SecurityProfile $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The list of fields to update.
   * @return GoogleCloudApigeeV1SecurityProfile
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudApigeeV1SecurityProfile $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudApigeeV1SecurityProfile::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsSecurityProfiles::class, 'Google_Service_Apigee_Resource_OrganizationsSecurityProfiles');
