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

namespace Google\Service\Translate\Resource;

use Google\Service\Translate\Dataset;
use Google\Service\Translate\ExportDataRequest;
use Google\Service\Translate\ImportDataRequest;
use Google\Service\Translate\ListDatasetsResponse;
use Google\Service\Translate\Operation;

/**
 * The "datasets" collection of methods.
 * Typical usage is:
 *  <code>
 *   $translateService = new Google\Service\Translate(...);
 *   $datasets = $translateService->projects_locations_datasets;
 *  </code>
 */
class ProjectsLocationsDatasets extends \Google\Service\Resource
{
  /**
   * Creates a Dataset. (datasets.create)
   *
   * @param string $parent Required. The project name.
   * @param Dataset $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Dataset $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a dataset and all of its contents. (datasets.delete)
   *
   * @param string $name Required. The name of the dataset to delete.
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], Operation::class);
  }
  /**
   * Exports dataset's data to the provided output location. (datasets.exportData)
   *
   * @param string $dataset Required. Name of the dataset. In form of
   * `projects/{project-number-or-id}/locations/{location-id}/datasets/{dataset-
   * id}`
   * @param ExportDataRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function exportData($dataset, ExportDataRequest $postBody, $optParams = [])
  {
    $params = ['dataset' => $dataset, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('exportData', [$params], Operation::class);
  }
  /**
   * Gets a Dataset. (datasets.get)
   *
   * @param string $name Required. The resource name of the dataset to retrieve.
   * @param array $optParams Optional parameters.
   * @return Dataset
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Dataset::class);
  }
  /**
   * Import sentence pairs into translation Dataset. (datasets.importData)
   *
   * @param string $dataset Required. Name of the dataset. In form of
   * `projects/{project-number-or-id}/locations/{location-id}/datasets/{dataset-
   * id}`
   * @param ImportDataRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function importData($dataset, ImportDataRequest $postBody, $optParams = [])
  {
    $params = ['dataset' => $dataset, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('importData', [$params], Operation::class);
  }
  /**
   * Lists datasets. (datasets.listProjectsLocationsDatasets)
   *
   * @param string $parent Required. Name of the parent project. In form of
   * `projects/{project-number-or-id}/locations/{location-id}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. Requested page size. The server can return
   * fewer results than requested.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * for the server to return. Typically obtained from next_page_token field in
   * the response of a ListDatasets call.
   * @return ListDatasetsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsDatasets($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListDatasetsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDatasets::class, 'Google_Service_Translate_Resource_ProjectsLocationsDatasets');
