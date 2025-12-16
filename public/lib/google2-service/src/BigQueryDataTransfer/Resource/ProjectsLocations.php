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

namespace Google\Service\BigQueryDataTransfer\Resource;

use Google\Service\BigQueryDataTransfer\BigquerydatatransferEmpty;
use Google\Service\BigQueryDataTransfer\EnrollDataSourcesRequest;
use Google\Service\BigQueryDataTransfer\ListLocationsResponse;
use Google\Service\BigQueryDataTransfer\Location;
use Google\Service\BigQueryDataTransfer\UnenrollDataSourcesRequest;

/**
 * The "locations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $bigquerydatatransferService = new Google\Service\BigQueryDataTransfer(...);
 *   $locations = $bigquerydatatransferService->projects_locations;
 *  </code>
 */
class ProjectsLocations extends \Google\Service\Resource
{
  /**
   * Enroll data sources in a user project. This allows users to create transfer
   * configurations for these data sources. They will also appear in the
   * ListDataSources RPC and as such, will appear in the [BigQuery
   * UI](https://console.cloud.google.com/bigquery), and the documents can be
   * found in the public guide for [BigQuery Web
   * UI](https://cloud.google.com/bigquery/bigquery-web-ui) and [Data Transfer
   * Service](https://cloud.google.com/bigquery/docs/working-with-transfers).
   * (locations.enrollDataSources)
   *
   * @param string $name Required. The name of the project resource in the form:
   * `projects/{project_id}`
   * @param EnrollDataSourcesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return BigquerydatatransferEmpty
   * @throws \Google\Service\Exception
   */
  public function enrollDataSources($name, EnrollDataSourcesRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('enrollDataSources', [$params], BigquerydatatransferEmpty::class);
  }
  /**
   * Gets information about a location. (locations.get)
   *
   * @param string $name Resource name for the location.
   * @param array $optParams Optional parameters.
   * @return Location
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Location::class);
  }
  /**
   * Lists information about the supported locations for this service.
   * (locations.listProjectsLocations)
   *
   * @param string $name The resource that owns the locations collection, if
   * applicable.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string extraLocationTypes Optional. Do not use this field. It is
   * unsupported and is ignored unless explicitly documented otherwise. This is
   * primarily for internal usage.
   * @opt_param string filter A filter to narrow down results to a preferred
   * subset. The filtering language accepts strings like `"displayName=tokyo"`,
   * and is documented in more detail in [AIP-160](https://google.aip.dev/160).
   * @opt_param int pageSize The maximum number of results to return. If not set,
   * the service selects a default.
   * @opt_param string pageToken A page token received from the `next_page_token`
   * field in the response. Send that page token to receive the subsequent page.
   * @return ListLocationsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocations($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListLocationsResponse::class);
  }
  /**
   * Unenroll data sources in a user project. This allows users to remove transfer
   * configurations for these data sources. They will no longer appear in the
   * ListDataSources RPC and will also no longer appear in the [BigQuery
   * UI](https://console.cloud.google.com/bigquery). Data transfers configurations
   * of unenrolled data sources will not be scheduled.
   * (locations.unenrollDataSources)
   *
   * @param string $name Required. The name of the project resource in the form:
   * `projects/{project_id}`
   * @param UnenrollDataSourcesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return BigquerydatatransferEmpty
   * @throws \Google\Service\Exception
   */
  public function unenrollDataSources($name, UnenrollDataSourcesRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('unenrollDataSources', [$params], BigquerydatatransferEmpty::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocations::class, 'Google_Service_BigQueryDataTransfer_Resource_ProjectsLocations');
