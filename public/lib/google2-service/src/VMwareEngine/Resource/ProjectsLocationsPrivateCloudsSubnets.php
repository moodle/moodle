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

namespace Google\Service\VMwareEngine\Resource;

use Google\Service\VMwareEngine\ListSubnetsResponse;
use Google\Service\VMwareEngine\Operation;
use Google\Service\VMwareEngine\Subnet;

/**
 * The "subnets" collection of methods.
 * Typical usage is:
 *  <code>
 *   $vmwareengineService = new Google\Service\VMwareEngine(...);
 *   $subnets = $vmwareengineService->projects_locations_privateClouds_subnets;
 *  </code>
 */
class ProjectsLocationsPrivateCloudsSubnets extends \Google\Service\Resource
{
  /**
   * Gets details of a single subnet. (subnets.get)
   *
   * @param string $name Required. The resource name of the subnet to retrieve.
   * Resource names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1-a/privateClouds/my-
   * cloud/subnets/my-subnet`
   * @param array $optParams Optional parameters.
   * @return Subnet
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Subnet::class);
  }
  /**
   * Lists subnets in a given private cloud.
   * (subnets.listProjectsLocationsPrivateCloudsSubnets)
   *
   * @param string $parent Required. The resource name of the private cloud to be
   * queried for subnets. Resource names are schemeless URIs that follow the
   * conventions in https://cloud.google.com/apis/design/resource_names. For
   * example: `projects/my-project/locations/us-central1-a/privateClouds/my-cloud`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of subnets to return in one page.
   * The service may return fewer than this value. The maximum value is coerced to
   * 1000. The default value of this field is 500.
   * @opt_param string pageToken A page token, received from a previous
   * `ListSubnetsRequest` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListSubnetsRequest` must match
   * the call that provided the page token.
   * @return ListSubnetsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsPrivateCloudsSubnets($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListSubnetsResponse::class);
  }
  /**
   * Updates the parameters of a single subnet. Only fields specified in
   * `update_mask` are applied. *Note*: This API is synchronous and always returns
   * a successful `google.longrunning.Operation` (LRO). The returned LRO will only
   * have `done` and `response` fields. (subnets.patch)
   *
   * @param string $name Output only. Identifier. The resource name of this
   * subnet. Resource names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1-a/privateClouds/my-
   * cloud/subnets/my-subnet`
   * @param Subnet $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten in the `Subnet` resource by the update. The fields
   * specified in the `update_mask` are relative to the resource, not the full
   * request. A field will be overwritten if it is in the mask. If the user does
   * not provide a mask then all fields will be overwritten.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Subnet $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsPrivateCloudsSubnets::class, 'Google_Service_VMwareEngine_Resource_ProjectsLocationsPrivateCloudsSubnets');
