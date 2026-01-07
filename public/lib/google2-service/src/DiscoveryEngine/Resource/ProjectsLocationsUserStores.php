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

namespace Google\Service\DiscoveryEngine\Resource;

use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1BatchUpdateUserLicensesRequest;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1UserStore;
use Google\Service\DiscoveryEngine\GoogleLongrunningOperation;

/**
 * The "userStores" collection of methods.
 * Typical usage is:
 *  <code>
 *   $discoveryengineService = new Google\Service\DiscoveryEngine(...);
 *   $userStores = $discoveryengineService->projects_locations_userStores;
 *  </code>
 */
class ProjectsLocationsUserStores extends \Google\Service\Resource
{
  /**
   * Updates the User License. This method is used for batch assign/unassign
   * licenses to users. (userStores.batchUpdateUserLicenses)
   *
   * @param string $parent Required. The parent UserStore resource name, format:
   * `projects/{project}/locations/{location}/userStores/{user_store_id}`.
   * @param GoogleCloudDiscoveryengineV1BatchUpdateUserLicensesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function batchUpdateUserLicenses($parent, GoogleCloudDiscoveryengineV1BatchUpdateUserLicensesRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchUpdateUserLicenses', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Creates a new User Store. (userStores.create)
   *
   * @param string $parent Required. The parent collection resource name, such as
   * `projects/{project}/locations/{location}`.
   * @param GoogleCloudDiscoveryengineV1UserStore $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string userStoreId Required. The ID of the User Store to create.
   * The ID must contain only letters (a-z, A-Z), numbers (0-9), underscores (_),
   * and hyphens (-). The maximum length is 63 characters.
   * @return GoogleCloudDiscoveryengineV1UserStore
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudDiscoveryengineV1UserStore $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudDiscoveryengineV1UserStore::class);
  }
  /**
   * Deletes the User Store. (userStores.delete)
   *
   * @param string $name Required. The name of the User Store to delete. Format:
   * `projects/{project}/locations/{location}/userStores/{user_store_id}`
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Gets the User Store. (userStores.get)
   *
   * @param string $name Required. The name of the User Store to get. Format:
   * `projects/{project}/locations/{location}/userStores/{user_store_id}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDiscoveryengineV1UserStore
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDiscoveryengineV1UserStore::class);
  }
  /**
   * Updates the User Store. (userStores.patch)
   *
   * @param string $name Immutable. The full resource name of the User Store, in
   * the format of
   * `projects/{project}/locations/{location}/userStores/{user_store}`. This field
   * must be a UTF-8 encoded string with a length limit of 1024 characters.
   * @param GoogleCloudDiscoveryengineV1UserStore $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The list of fields to update.
   * @return GoogleCloudDiscoveryengineV1UserStore
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudDiscoveryengineV1UserStore $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudDiscoveryengineV1UserStore::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsUserStores::class, 'Google_Service_DiscoveryEngine_Resource_ProjectsLocationsUserStores');
