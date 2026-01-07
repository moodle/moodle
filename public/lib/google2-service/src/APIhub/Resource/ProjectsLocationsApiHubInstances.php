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

namespace Google\Service\APIhub\Resource;

use Google\Service\APIhub\GoogleCloudApihubV1ApiHubInstance;
use Google\Service\APIhub\GoogleCloudApihubV1LookupApiHubInstanceResponse;
use Google\Service\APIhub\GoogleLongrunningOperation;

/**
 * The "apiHubInstances" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apihubService = new Google\Service\APIhub(...);
 *   $apiHubInstances = $apihubService->projects_locations_apiHubInstances;
 *  </code>
 */
class ProjectsLocationsApiHubInstances extends \Google\Service\Resource
{
  /**
   * Provisions instance resources for the API Hub. (apiHubInstances.create)
   *
   * @param string $parent Required. The parent resource for the Api Hub instance
   * resource. Format: `projects/{project}/locations/{location}`
   * @param GoogleCloudApihubV1ApiHubInstance $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string apiHubInstanceId Optional. Identifier to assign to the Api
   * Hub instance. Must be unique within scope of the parent resource. If the
   * field is not provided, system generated id will be used. This value should be
   * 4-40 characters, and valid characters are `/a-z[0-9]-_/`.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudApihubV1ApiHubInstance $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes the API hub instance. Deleting the API hub instance will also result
   * in the removal of all associated runtime project attachments and the host
   * project registration. (apiHubInstances.delete)
   *
   * @param string $name Required. The name of the Api Hub instance to delete.
   * Format:
   * `projects/{project}/locations/{location}/apiHubInstances/{apiHubInstance}`.
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
   * Gets details of a single API Hub instance. (apiHubInstances.get)
   *
   * @param string $name Required. The name of the Api Hub instance to retrieve.
   * Format:
   * `projects/{project}/locations/{location}/apiHubInstances/{apiHubInstance}`.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApihubV1ApiHubInstance
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudApihubV1ApiHubInstance::class);
  }
  /**
   * Looks up an Api Hub instance in a given GCP project. There will always be
   * only one Api Hub instance for a GCP project across all locations.
   * (apiHubInstances.lookup)
   *
   * @param string $parent Required. There will always be only one Api Hub
   * instance for a GCP project across all locations. The parent resource for the
   * Api Hub instance resource. Format: `projects/{project}/locations/{location}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApihubV1LookupApiHubInstanceResponse
   * @throws \Google\Service\Exception
   */
  public function lookup($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('lookup', [$params], GoogleCloudApihubV1LookupApiHubInstanceResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsApiHubInstances::class, 'Google_Service_APIhub_Resource_ProjectsLocationsApiHubInstances');
