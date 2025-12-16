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

use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1Control;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1ListControlsResponse;
use Google\Service\DiscoveryEngine\GoogleProtobufEmpty;

/**
 * The "controls" collection of methods.
 * Typical usage is:
 *  <code>
 *   $discoveryengineService = new Google\Service\DiscoveryEngine(...);
 *   $controls = $discoveryengineService->projects_locations_collections_engines_controls;
 *  </code>
 */
class ProjectsLocationsCollectionsEnginesControls extends \Google\Service\Resource
{
  /**
   * Creates a Control. By default 1000 controls are allowed for a data store. A
   * request can be submitted to adjust this limit. If the Control to create
   * already exists, an ALREADY_EXISTS error is returned. (controls.create)
   *
   * @param string $parent Required. Full resource name of parent data store.
   * Format: `projects/{project}/locations/{location}/collections/{collection_id}/
   * dataStores/{data_store_id}` or `projects/{project}/locations/{location}/colle
   * ctions/{collection_id}/engines/{engine_id}`.
   * @param GoogleCloudDiscoveryengineV1Control $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string controlId Required. The ID to use for the Control, which
   * will become the final component of the Control's resource name. This value
   * must be within 1-63 characters. Valid characters are /a-z-_/.
   * @return GoogleCloudDiscoveryengineV1Control
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudDiscoveryengineV1Control $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudDiscoveryengineV1Control::class);
  }
  /**
   * Deletes a Control. If the Control to delete does not exist, a NOT_FOUND error
   * is returned. (controls.delete)
   *
   * @param string $name Required. The resource name of the Control to delete.
   * Format: `projects/{project}/locations/{location}/collections/{collection_id}/
   * dataStores/{data_store_id}/controls/{control_id}`
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
   * Gets a Control. (controls.get)
   *
   * @param string $name Required. The resource name of the Control to get.
   * Format: `projects/{project}/locations/{location}/collections/{collection_id}/
   * dataStores/{data_store_id}/controls/{control_id}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDiscoveryengineV1Control
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDiscoveryengineV1Control::class);
  }
  /**
   * Lists all Controls by their parent DataStore.
   * (controls.listProjectsLocationsCollectionsEnginesControls)
   *
   * @param string $parent Required. The data store resource name. Format: `projec
   * ts/{project}/locations/{location}/collections/{collection_id}/dataStores/{dat
   * a_store_id}` or `projects/{project}/locations/{location}/collections/{collect
   * ion_id}/engines/{engine_id}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. A filter to apply on the list results.
   * Supported features: * List all the products under the parent branch if filter
   * is unset. Currently this field is unsupported.
   * @opt_param int pageSize Optional. Maximum number of results to return. If
   * unspecified, defaults to 50. Max allowed value is 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListControls` call. Provide this to retrieve the subsequent page.
   * @return GoogleCloudDiscoveryengineV1ListControlsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsCollectionsEnginesControls($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudDiscoveryengineV1ListControlsResponse::class);
  }
  /**
   * Updates a Control. Control action type cannot be changed. If the Control to
   * update does not exist, a NOT_FOUND error is returned. (controls.patch)
   *
   * @param string $name Immutable. Fully qualified name
   * `projects/locations/global/dataStore/controls`
   * @param GoogleCloudDiscoveryengineV1Control $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. Indicates which fields in the provided
   * Control to update. The following are NOT supported: * Control.name *
   * Control.solution_type If not set or empty, all supported fields are updated.
   * @return GoogleCloudDiscoveryengineV1Control
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudDiscoveryengineV1Control $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudDiscoveryengineV1Control::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsCollectionsEnginesControls::class, 'Google_Service_DiscoveryEngine_Resource_ProjectsLocationsCollectionsEnginesControls');
