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

use Google\Service\BigQueryDataTransfer\CheckValidCredsRequest;
use Google\Service\BigQueryDataTransfer\CheckValidCredsResponse;
use Google\Service\BigQueryDataTransfer\DataSource;
use Google\Service\BigQueryDataTransfer\ListDataSourcesResponse;

/**
 * The "dataSources" collection of methods.
 * Typical usage is:
 *  <code>
 *   $bigquerydatatransferService = new Google\Service\BigQueryDataTransfer(...);
 *   $dataSources = $bigquerydatatransferService->projects_locations_dataSources;
 *  </code>
 */
class ProjectsLocationsDataSources extends \Google\Service\Resource
{
  /**
   * Returns true if valid credentials exist for the given data source and
   * requesting user. (dataSources.checkValidCreds)
   *
   * @param string $name Required. The name of the data source. If you are using
   * the regionless method, the location must be `US` and the name should be in
   * the following form: * `projects/{project_id}/dataSources/{data_source_id}` If
   * you are using the regionalized method, the name should be in the following
   * form: *
   * `projects/{project_id}/locations/{location_id}/dataSources/{data_source_id}`
   * @param CheckValidCredsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return CheckValidCredsResponse
   * @throws \Google\Service\Exception
   */
  public function checkValidCreds($name, CheckValidCredsRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('checkValidCreds', [$params], CheckValidCredsResponse::class);
  }
  /**
   * Retrieves a supported data source and returns its settings. (dataSources.get)
   *
   * @param string $name Required. The name of the resource requested. If you are
   * using the regionless method, the location must be `US` and the name should be
   * in the following form: * `projects/{project_id}/dataSources/{data_source_id}`
   * If you are using the regionalized method, the name should be in the following
   * form: *
   * `projects/{project_id}/locations/{location_id}/dataSources/{data_source_id}`
   * @param array $optParams Optional parameters.
   * @return DataSource
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], DataSource::class);
  }
  /**
   * Lists supported data sources and returns their settings.
   * (dataSources.listProjectsLocationsDataSources)
   *
   * @param string $parent Required. The BigQuery project id for which data
   * sources should be returned. Must be in the form: `projects/{project_id}` or
   * `projects/{project_id}/locations/{location_id}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Page size. The default page size is the maximum value
   * of 1000 results.
   * @opt_param string pageToken Pagination token, which can be used to request a
   * specific page of `ListDataSourcesRequest` list results. For multiple-page
   * results, `ListDataSourcesResponse` outputs a `next_page` token, which can be
   * used as the `page_token` value to request the next page of list results.
   * @return ListDataSourcesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsDataSources($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListDataSourcesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDataSources::class, 'Google_Service_BigQueryDataTransfer_Resource_ProjectsLocationsDataSources');
