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

use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1AdvancedCompleteQueryRequest;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1AdvancedCompleteQueryResponse;

/**
 * The "completionConfig" collection of methods.
 * Typical usage is:
 *  <code>
 *   $discoveryengineService = new Google\Service\DiscoveryEngine(...);
 *   $completionConfig = $discoveryengineService->projects_locations_collections_dataStores_completionConfig;
 *  </code>
 */
class ProjectsLocationsCollectionsDataStoresCompletionConfig extends \Google\Service\Resource
{
  /**
   * Completes the user input with advanced keyword suggestions.
   * (completionConfig.completeQuery)
   *
   * @param string $completionConfig Required. The completion_config of the parent
   * dataStore or engine resource name for which the completion is performed, such
   * as `projects/locations/global/collections/default_collection/dataStores/compl
   * etionConfig` `projects/locations/global/collections/default_collection/engine
   * s/completionConfig`.
   * @param GoogleCloudDiscoveryengineV1AdvancedCompleteQueryRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDiscoveryengineV1AdvancedCompleteQueryResponse
   * @throws \Google\Service\Exception
   */
  public function completeQuery($completionConfig, GoogleCloudDiscoveryengineV1AdvancedCompleteQueryRequest $postBody, $optParams = [])
  {
    $params = ['completionConfig' => $completionConfig, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('completeQuery', [$params], GoogleCloudDiscoveryengineV1AdvancedCompleteQueryResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsCollectionsDataStoresCompletionConfig::class, 'Google_Service_DiscoveryEngine_Resource_ProjectsLocationsCollectionsDataStoresCompletionConfig');
