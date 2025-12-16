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

use Google\Service\VMwareEngine\ListUpgradesResponse;
use Google\Service\VMwareEngine\Operation;
use Google\Service\VMwareEngine\Upgrade;

/**
 * The "upgrades" collection of methods.
 * Typical usage is:
 *  <code>
 *   $vmwareengineService = new Google\Service\VMwareEngine(...);
 *   $upgrades = $vmwareengineService->projects_locations_privateClouds_upgrades;
 *  </code>
 */
class ProjectsLocationsPrivateCloudsUpgrades extends \Google\Service\Resource
{
  /**
   * Retrieves a private cloud `Upgrade` resource by its resource name.
   * (upgrades.get)
   *
   * @param string $name Required. The name of the `Upgrade` resource to be
   * retrieved. Resource names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-west1-a/privateClouds/my-cloud/upgrades/my-
   * upgrade`
   * @param array $optParams Optional parameters.
   * @return Upgrade
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Upgrade::class);
  }
  /**
   * Lists past, ongoing and upcoming `Upgrades` for the given private cloud.
   * (upgrades.listProjectsLocationsPrivateCloudsUpgrades)
   *
   * @param string $parent Required. Query a list of `Upgrades` for the given
   * private cloud resource name. Resource names are schemeless URIs that follow
   * the conventions in https://cloud.google.com/apis/design/resource_names. For
   * example: `projects/my-project/locations/us-west1-a/privateClouds/my-cloud`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter A filter expression that matches resources returned
   * in the response. The expression must specify the field name, a comparison
   * operator, and the value that you want to use for filtering. The value must be
   * a string, a number, or a boolean. The comparison operator must be `=`, `!=`,
   * `>`, or `<`. For example, if you are filtering a list of upgrades, you can
   * exclude the ones named `example-upgrade1` by specifying `name != "example-
   * upgrade1"`. You can also filter nested fields. To filter on multiple
   * expressions, provide each separate expression within parentheses. For
   * example: ``` (name = "example-upgrade") (createTime >
   * "2021-04-12T08:15:10.40Z") ``` By default, each expression is an `AND`
   * expression. However, you can include `AND` and `OR` expressions explicitly.
   * For example: ``` (name = "upgrade-1") AND (createTime >
   * "2021-04-12T08:15:10.40Z") OR (name = "upgrade-2") ```
   * @opt_param string orderBy Sorts list results by a certain order. By default,
   * returned results are ordered by `name` in ascending order. You can also sort
   * results in descending order based on the `name` value using `orderBy="name
   * desc"`. Currently, only ordering by `name` is supported.
   * @opt_param int pageSize The maximum number of `Upgrades` to return in one
   * page. The service may return fewer resources than this value. The maximum
   * value is coerced to 1000. The default value of this field is 500.
   * @opt_param string pageToken A page token, received from a previous
   * `ListUpgrades` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListUpgrades` must match the
   * call that provided the page token.
   * @return ListUpgradesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsPrivateCloudsUpgrades($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListUpgradesResponse::class);
  }
  /**
   * Update the private cloud `Upgrade` resource. Only `schedule` field can
   * updated. The schedule can only be updated when the upgrade has not started
   * and schedule edit window is open. Only fields specified in `update_mask` are
   * considered. (upgrades.patch)
   *
   * @param string $name Output only. Identifier. The resource name of the private
   * cloud `Upgrade`. Resource names are schemeless URIs that follow the
   * conventions in https://cloud.google.com/apis/design/resource_names. For
   * example: `projects/my-project/locations/us-west1-a/privateClouds/my-
   * cloud/upgrades/my-upgrade`
   * @param Upgrade $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. A request ID to identify requests.
   * Specify a unique request ID so that if you must retry your request, the
   * server will know to ignore the request if it has already been completed. The
   * server guarantees that a request doesn't result in creation of duplicate
   * commitments for at least 60 minutes. For example, consider a situation where
   * you make an initial request and the request times out. If you make the
   * request again with the same request ID, the server can check if original
   * operation with the same request ID was received, and if so, will ignore the
   * second request. This prevents clients from accidentally creating duplicate
   * commitments. The request ID must be a valid UUID with the exception that zero
   * UUID is not supported (00000000-0000-0000-0000-000000000000).
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten in the `Upgrade` resource by the update. The fields
   * specified in the `update_mask` are relative to the resource, not the full
   * request. A field will be overwritten if it is in the mask. If the user does
   * not provide a mask then all fields will be overwritten.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Upgrade $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsPrivateCloudsUpgrades::class, 'Google_Service_VMwareEngine_Resource_ProjectsLocationsPrivateCloudsUpgrades');
