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

use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1ListCustomModelsResponse;

/**
 * The "customModels" collection of methods.
 * Typical usage is:
 *  <code>
 *   $discoveryengineService = new Google\Service\DiscoveryEngine(...);
 *   $customModels = $discoveryengineService->projects_locations_collections_dataStores_customModels;
 *  </code>
 */
class ProjectsLocationsCollectionsDataStoresCustomModels extends \Google\Service\Resource
{
  /**
   * Gets a list of all the custom models.
   * (customModels.listProjectsLocationsCollectionsDataStoresCustomModels)
   *
   * @param string $dataStore Required. The resource name of the parent Data
   * Store, such as `projects/locations/global/collections/default_collection/data
   * Stores/default_data_store`. This field is used to identify the data store
   * where to fetch the models from.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDiscoveryengineV1ListCustomModelsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsCollectionsDataStoresCustomModels($dataStore, $optParams = [])
  {
    $params = ['dataStore' => $dataStore];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudDiscoveryengineV1ListCustomModelsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsCollectionsDataStoresCustomModels::class, 'Google_Service_DiscoveryEngine_Resource_ProjectsLocationsCollectionsDataStoresCustomModels');
