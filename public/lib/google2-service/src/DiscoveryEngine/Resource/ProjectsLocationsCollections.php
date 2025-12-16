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

use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1DataConnector;
use Google\Service\DiscoveryEngine\GoogleLongrunningOperation;

/**
 * The "collections" collection of methods.
 * Typical usage is:
 *  <code>
 *   $discoveryengineService = new Google\Service\DiscoveryEngine(...);
 *   $collections = $discoveryengineService->projects_locations_collections;
 *  </code>
 */
class ProjectsLocationsCollections extends \Google\Service\Resource
{
  /**
   * Deletes a Collection. (collections.delete)
   *
   * @param string $name Required. The full resource name of the Collection, in
   * the format of
   * `projects/{project}/locations/{location}/collections/{collection}`.
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
   * Gets the DataConnector. DataConnector is a singleton resource for each
   * Collection. (collections.getDataConnector)
   *
   * @param string $name Required. Full resource name of DataConnector, such as `p
   * rojects/{project}/locations/{location}/collections/{collection_id}/dataConnec
   * tor`. If the caller does not have permission to access the DataConnector,
   * regardless of whether or not it exists, a PERMISSION_DENIED error is
   * returned. If the requested DataConnector does not exist, a NOT_FOUND error is
   * returned.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDiscoveryengineV1DataConnector
   * @throws \Google\Service\Exception
   */
  public function getDataConnector($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getDataConnector', [$params], GoogleCloudDiscoveryengineV1DataConnector::class);
  }
  /**
   * Updates a DataConnector. (collections.updateDataConnector)
   *
   * @param string $name Output only. The full resource name of the Data
   * Connector. Format: `projects/locations/collections/dataConnector`.
   * @param GoogleCloudDiscoveryengineV1DataConnector $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Indicates which fields in the provided
   * DataConnector to update. Supported field paths include: - refresh_interval -
   * params - auto_run_disabled - action_config - action_config.action_params -
   * action_config.service_name - destination_configs - blocking_reasons -
   * sync_mode - incremental_sync_disabled - incremental_refresh_interval Note:
   * Support for these fields may vary depending on the connector type. For
   * example, not all connectors support `destination_configs`. If an unsupported
   * or unknown field path is provided, the request will return an
   * INVALID_ARGUMENT error.
   * @return GoogleCloudDiscoveryengineV1DataConnector
   * @throws \Google\Service\Exception
   */
  public function updateDataConnector($name, GoogleCloudDiscoveryengineV1DataConnector $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('updateDataConnector', [$params], GoogleCloudDiscoveryengineV1DataConnector::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsCollections::class, 'Google_Service_DiscoveryEngine_Resource_ProjectsLocationsCollections');
