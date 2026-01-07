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

use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1ImportSuggestionDenyListEntriesRequest;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1PurgeSuggestionDenyListEntriesRequest;
use Google\Service\DiscoveryEngine\GoogleLongrunningOperation;

/**
 * The "suggestionDenyListEntries" collection of methods.
 * Typical usage is:
 *  <code>
 *   $discoveryengineService = new Google\Service\DiscoveryEngine(...);
 *   $suggestionDenyListEntries = $discoveryengineService->projects_locations_dataStores_suggestionDenyListEntries;
 *  </code>
 */
class ProjectsLocationsDataStoresSuggestionDenyListEntries extends \Google\Service\Resource
{
  /**
   * Imports all SuggestionDenyListEntry for a DataStore.
   * (suggestionDenyListEntries.import)
   *
   * @param string $parent Required. The parent data store resource name for which
   * to import denylist entries. Follows pattern
   * projects/locations/collections/dataStores.
   * @param GoogleCloudDiscoveryengineV1ImportSuggestionDenyListEntriesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function import($parent, GoogleCloudDiscoveryengineV1ImportSuggestionDenyListEntriesRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('import', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Permanently deletes all SuggestionDenyListEntry for a DataStore.
   * (suggestionDenyListEntries.purge)
   *
   * @param string $parent Required. The parent data store resource name for which
   * to import denylist entries. Follows pattern
   * projects/locations/collections/dataStores.
   * @param GoogleCloudDiscoveryengineV1PurgeSuggestionDenyListEntriesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function purge($parent, GoogleCloudDiscoveryengineV1PurgeSuggestionDenyListEntriesRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('purge', [$params], GoogleLongrunningOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDataStoresSuggestionDenyListEntries::class, 'Google_Service_DiscoveryEngine_Resource_ProjectsLocationsDataStoresSuggestionDenyListEntries');
