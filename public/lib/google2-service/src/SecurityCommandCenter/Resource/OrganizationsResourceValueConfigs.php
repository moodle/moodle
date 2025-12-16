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

namespace Google\Service\SecurityCommandCenter\Resource;

use Google\Service\SecurityCommandCenter\BatchCreateResourceValueConfigsRequest;
use Google\Service\SecurityCommandCenter\BatchCreateResourceValueConfigsResponse;
use Google\Service\SecurityCommandCenter\GoogleCloudSecuritycenterV1ResourceValueConfig;
use Google\Service\SecurityCommandCenter\ListResourceValueConfigsResponse;
use Google\Service\SecurityCommandCenter\SecuritycenterEmpty;

/**
 * The "resourceValueConfigs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $securitycenterService = new Google\Service\SecurityCommandCenter(...);
 *   $resourceValueConfigs = $securitycenterService->organizations_resourceValueConfigs;
 *  </code>
 */
class OrganizationsResourceValueConfigs extends \Google\Service\Resource
{
  /**
   * Creates a ResourceValueConfig for an organization. Maps user's tags to
   * difference resource values for use by the attack path simulation.
   * (resourceValueConfigs.batchCreate)
   *
   * @param string $parent Required. Resource name of the new
   * ResourceValueConfig's parent. The parent field in the
   * CreateResourceValueConfigRequest messages must either be empty or match this
   * field.
   * @param BatchCreateResourceValueConfigsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return BatchCreateResourceValueConfigsResponse
   * @throws \Google\Service\Exception
   */
  public function batchCreate($parent, BatchCreateResourceValueConfigsRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchCreate', [$params], BatchCreateResourceValueConfigsResponse::class);
  }
  /**
   * Deletes a ResourceValueConfig. (resourceValueConfigs.delete)
   *
   * @param string $name Required. Name of the ResourceValueConfig to delete
   * @param array $optParams Optional parameters.
   * @return SecuritycenterEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], SecuritycenterEmpty::class);
  }
  /**
   * Gets a ResourceValueConfig. (resourceValueConfigs.get)
   *
   * @param string $name Required. Name of the resource value config to retrieve.
   * Its format is
   * `organizations/{organization}/resourceValueConfigs/{config_id}`.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudSecuritycenterV1ResourceValueConfig
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudSecuritycenterV1ResourceValueConfig::class);
  }
  /**
   * Lists all ResourceValueConfigs.
   * (resourceValueConfigs.listOrganizationsResourceValueConfigs)
   *
   * @param string $parent Required. The parent, which owns the collection of
   * resource value configs. Its format is `organizations/[organization_id]`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The number of results to return. The service may
   * return fewer than this value. If unspecified, at most 10 configs will be
   * returned. The maximum value is 1000; values above 1000 will be coerced to
   * 1000.
   * @opt_param string pageToken A page token, received from a previous
   * `ListResourceValueConfigs` call. Provide this to retrieve the subsequent
   * page. When paginating, all other parameters provided to
   * `ListResourceValueConfigs` must match the call that provided the page token.
   * page_size can be specified, and the new page_size will be used.
   * @return ListResourceValueConfigsResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsResourceValueConfigs($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListResourceValueConfigsResponse::class);
  }
  /**
   * Updates an existing ResourceValueConfigs with new rules.
   * (resourceValueConfigs.patch)
   *
   * @param string $name Name for the resource value configuration
   * @param GoogleCloudSecuritycenterV1ResourceValueConfig $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask The list of fields to be updated. If empty all
   * mutable fields will be updated.
   * @return GoogleCloudSecuritycenterV1ResourceValueConfig
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudSecuritycenterV1ResourceValueConfig $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudSecuritycenterV1ResourceValueConfig::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsResourceValueConfigs::class, 'Google_Service_SecurityCommandCenter_Resource_OrganizationsResourceValueConfigs');
