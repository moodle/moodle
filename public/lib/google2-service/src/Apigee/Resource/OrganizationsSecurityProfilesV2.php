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

use Google\Service\Apigee\GoogleCloudApigeeV1ListSecurityProfilesV2Response;
use Google\Service\Apigee\GoogleCloudApigeeV1SecurityProfileV2;
use Google\Service\Apigee\GoogleProtobufEmpty;

/**
 * The "securityProfilesV2" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apigeeService = new Google\Service\Apigee(...);
 *   $securityProfilesV2 = $apigeeService->organizations_securityProfilesV2;
 *  </code>
 */
class OrganizationsSecurityProfilesV2 extends \Google\Service\Resource
{
  /**
   * Create a security profile v2. (securityProfilesV2.create)
   *
   * @param string $parent Required. The parent resource name. Format:
   * `organizations/{org}`
   * @param GoogleCloudApigeeV1SecurityProfileV2 $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string securityProfileV2Id Required. The security profile id.
   * @return GoogleCloudApigeeV1SecurityProfileV2
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudApigeeV1SecurityProfileV2 $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudApigeeV1SecurityProfileV2::class);
  }
  /**
   * Delete a security profile v2. (securityProfilesV2.delete)
   *
   * @param string $name Required. The name of the security profile v2 to delete.
   * Format: `organizations/{org}/securityProfilesV2/{profile}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string riskAssessmentType Optional. The risk assessment type of
   * the security profile. Defaults to ADVANCED_API_SECURITY.
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
   * Get a security profile v2. (securityProfilesV2.get)
   *
   * @param string $name Required. The name of the security profile v2 to get.
   * Format: `organizations/{org}/securityProfilesV2/{profile}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string riskAssessmentType Optional. The risk assessment type of
   * the security profile. Defaults to ADVANCED_API_SECURITY.
   * @return GoogleCloudApigeeV1SecurityProfileV2
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudApigeeV1SecurityProfileV2::class);
  }
  /**
   * List security profiles v2.
   * (securityProfilesV2.listOrganizationsSecurityProfilesV2)
   *
   * @param string $parent Required. For a specific organization, list of all the
   * security profiles. Format: `organizations/{org}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of profiles to return
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListSecurityProfilesV2` call. Provide this to retrieve the subsequent page.
   * @opt_param string riskAssessmentType Optional. The risk assessment type of
   * the security profiles. Defaults to ADVANCED_API_SECURITY.
   * @return GoogleCloudApigeeV1ListSecurityProfilesV2Response
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsSecurityProfilesV2($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudApigeeV1ListSecurityProfilesV2Response::class);
  }
  /**
   * Update a security profile V2. (securityProfilesV2.patch)
   *
   * @param string $name Identifier. Name of the security profile v2 resource.
   * Format: organizations/{org}/securityProfilesV2/{profile}
   * @param GoogleCloudApigeeV1SecurityProfileV2 $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The list of fields to update. Valid
   * fields to update are `description` and `profileAssessmentConfigs`.
   * @return GoogleCloudApigeeV1SecurityProfileV2
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudApigeeV1SecurityProfileV2 $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudApigeeV1SecurityProfileV2::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsSecurityProfilesV2::class, 'Google_Service_Apigee_Resource_OrganizationsSecurityProfilesV2');
