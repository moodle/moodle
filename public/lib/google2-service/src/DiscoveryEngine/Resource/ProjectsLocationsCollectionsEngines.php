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

use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1Engine;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1ListEnginesResponse;
use Google\Service\DiscoveryEngine\GoogleLongrunningOperation;

/**
 * The "engines" collection of methods.
 * Typical usage is:
 *  <code>
 *   $discoveryengineService = new Google\Service\DiscoveryEngine(...);
 *   $engines = $discoveryengineService->projects_locations_collections_engines;
 *  </code>
 */
class ProjectsLocationsCollectionsEngines extends \Google\Service\Resource
{
  /**
   * Creates a Engine. (engines.create)
   *
   * @param string $parent Required. The parent resource name, such as
   * `projects/{project}/locations/{location}/collections/{collection}`.
   * @param GoogleCloudDiscoveryengineV1Engine $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string engineId Required. The ID to use for the Engine, which will
   * become the final component of the Engine's resource name. This field must
   * conform to [RFC-1034](https://tools.ietf.org/html/rfc1034) standard with a
   * length limit of 63 characters. Otherwise, an INVALID_ARGUMENT error is
   * returned.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudDiscoveryengineV1Engine $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes a Engine. (engines.delete)
   *
   * @param string $name Required. Full resource name of Engine, such as `projects
   * /{project}/locations/{location}/collections/{collection_id}/engines/{engine_i
   * d}`. If the caller does not have permission to delete the Engine, regardless
   * of whether or not it exists, a PERMISSION_DENIED error is returned. If the
   * Engine to delete does not exist, a NOT_FOUND error is returned.
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
   * Gets a Engine. (engines.get)
   *
   * @param string $name Required. Full resource name of Engine, such as `projects
   * /{project}/locations/{location}/collections/{collection_id}/engines/{engine_i
   * d}`.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDiscoveryengineV1Engine
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDiscoveryengineV1Engine::class);
  }
  /**
   * Lists all the Engines associated with the project.
   * (engines.listProjectsLocationsCollectionsEngines)
   *
   * @param string $parent Required. The parent resource name, such as
   * `projects/{project}/locations/{location}/collections/{collection_id}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filter by solution type. For example:
   * solution_type=SOLUTION_TYPE_SEARCH
   * @opt_param int pageSize Optional. Not supported.
   * @opt_param string pageToken Optional. Not supported.
   * @return GoogleCloudDiscoveryengineV1ListEnginesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsCollectionsEngines($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudDiscoveryengineV1ListEnginesResponse::class);
  }
  /**
   * Updates an Engine (engines.patch)
   *
   * @param string $name Immutable. Identifier. The fully qualified resource name
   * of the engine. This field must be a UTF-8 encoded string with a length limit
   * of 1024 characters. Format: `projects/{project}/locations/{location}/collecti
   * ons/{collection}/engines/{engine}` engine should be 1-63 characters, and
   * valid characters are /a-z0-9. Otherwise, an INVALID_ARGUMENT error is
   * returned.
   * @param GoogleCloudDiscoveryengineV1Engine $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Indicates which fields in the provided Engine to
   * update. If an unsupported or unknown field is provided, an INVALID_ARGUMENT
   * error is returned.
   * @return GoogleCloudDiscoveryengineV1Engine
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudDiscoveryengineV1Engine $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudDiscoveryengineV1Engine::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsCollectionsEngines::class, 'Google_Service_DiscoveryEngine_Resource_ProjectsLocationsCollectionsEngines');
