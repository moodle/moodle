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

namespace Google\Service\Datalineage\Resource;

use Google\Service\Datalineage\GoogleCloudDatacatalogLineageV1BatchSearchLinkProcessesRequest;
use Google\Service\Datalineage\GoogleCloudDatacatalogLineageV1BatchSearchLinkProcessesResponse;
use Google\Service\Datalineage\GoogleCloudDatacatalogLineageV1ProcessOpenLineageRunEventResponse;
use Google\Service\Datalineage\GoogleCloudDatacatalogLineageV1SearchLinksRequest;
use Google\Service\Datalineage\GoogleCloudDatacatalogLineageV1SearchLinksResponse;
use Google\Service\Datalineage\ProcessOpenLineageRunEventRequestContent;

/**
 * The "locations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $datalineageService = new Google\Service\Datalineage(...);
 *   $locations = $datalineageService->projects_locations;
 *  </code>
 */
class ProjectsLocations extends \Google\Service\Resource
{
  /**
   * Retrieve information about LineageProcesses associated with specific links.
   * LineageProcesses are transformation pipelines that result in data flowing
   * from **source** to **target** assets. Links between assets represent this
   * operation. If you have specific link names, you can use this method to verify
   * which LineageProcesses contribute to creating those links. See the
   * SearchLinks method for more information on how to retrieve link name. You can
   * retrieve the LineageProcess information in every project where you have the
   * `datalineage.events.get` permission. The project provided in the URL is used
   * for Billing and Quota. (locations.batchSearchLinkProcesses)
   *
   * @param string $parent Required. The project and location where you want to
   * search.
   * @param GoogleCloudDatacatalogLineageV1BatchSearchLinkProcessesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDatacatalogLineageV1BatchSearchLinkProcessesResponse
   * @throws \Google\Service\Exception
   */
  public function batchSearchLinkProcesses($parent, GoogleCloudDatacatalogLineageV1BatchSearchLinkProcessesRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchSearchLinkProcesses', [$params], GoogleCloudDatacatalogLineageV1BatchSearchLinkProcessesResponse::class);
  }
  /**
   * Creates new lineage events together with their parents: process and run.
   * Updates the process and run if they already exist. Mapped from Open Lineage
   * specification:
   * https://github.com/OpenLineage/OpenLineage/blob/main/spec/OpenLineage.json.
   * (locations.processOpenLineageRunEvent)
   *
   * @param string $parent Required. The name of the project and its location that
   * should own the process, run, and lineage event.
   * @param ProcessOpenLineageRunEventRequestContent $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. A unique identifier for this request.
   * Restricted to 36 ASCII characters. A random UUID is recommended. This request
   * is idempotent only if a `request_id` is provided.
   * @return GoogleCloudDatacatalogLineageV1ProcessOpenLineageRunEventResponse
   * @throws \Google\Service\Exception
   */
  public function processOpenLineageRunEvent($parent, ProcessOpenLineageRunEventRequestContent $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('processOpenLineageRunEvent', [$params], GoogleCloudDatacatalogLineageV1ProcessOpenLineageRunEventResponse::class);
  }
  /**
   * Retrieve a list of links connected to a specific asset. Links represent the
   * data flow between **source** (upstream) and **target** (downstream) assets in
   * transformation pipelines. Links are stored in the same project as the Lineage
   * Events that create them. You can retrieve links in every project where you
   * have the `datalineage.events.get` permission. The project provided in the URL
   * is used for Billing and Quota. (locations.searchLinks)
   *
   * @param string $parent Required. The project and location you want search in.
   * @param GoogleCloudDatacatalogLineageV1SearchLinksRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDatacatalogLineageV1SearchLinksResponse
   * @throws \Google\Service\Exception
   */
  public function searchLinks($parent, GoogleCloudDatacatalogLineageV1SearchLinksRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('searchLinks', [$params], GoogleCloudDatacatalogLineageV1SearchLinksResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocations::class, 'Google_Service_Datalineage_Resource_ProjectsLocations');
