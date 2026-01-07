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

use Google\Service\Translate\AdaptiveMtDataset;
use Google\Service\Translate\ImportAdaptiveMtFileRequest;
use Google\Service\Translate\ImportAdaptiveMtFileResponse;
use Google\Service\Translate\ListAdaptiveMtDatasetsResponse;
use Google\Service\Translate\TranslateEmpty;

/**
 * The "adaptiveMtDatasets" collection of methods.
 * Typical usage is:
 *  <code>
 *   $translateService = new Google\Service\Translate(...);
 *   $adaptiveMtDatasets = $translateService->projects_locations_adaptiveMtDatasets;
 *  </code>
 */
class ProjectsLocationsAdaptiveMtDatasets extends \Google\Service\Resource
{
  /**
   * Creates an Adaptive MT dataset. (adaptiveMtDatasets.create)
   *
   * @param string $parent Required. Name of the parent project. In form of
   * `projects/{project-number-or-id}/locations/{location-id}`
   * @param AdaptiveMtDataset $postBody
   * @param array $optParams Optional parameters.
   * @return AdaptiveMtDataset
   * @throws \Google\Service\Exception
   */
  public function create($parent, AdaptiveMtDataset $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], AdaptiveMtDataset::class);
  }
  /**
   * Deletes an Adaptive MT dataset, including all its entries and associated
   * metadata. (adaptiveMtDatasets.delete)
   *
   * @param string $name Required. Name of the dataset. In the form of
   * `projects/{project-number-or-id}/locations/{location-
   * id}/adaptiveMtDatasets/{adaptive-mt-dataset-id}`
   * @param array $optParams Optional parameters.
   * @return TranslateEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], TranslateEmpty::class);
  }
  /**
   * Gets the Adaptive MT dataset. (adaptiveMtDatasets.get)
   *
   * @param string $name Required. Name of the dataset. In the form of
   * `projects/{project-number-or-id}/locations/{location-
   * id}/adaptiveMtDatasets/{adaptive-mt-dataset-id}`
   * @param array $optParams Optional parameters.
   * @return AdaptiveMtDataset
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], AdaptiveMtDataset::class);
  }
  /**
   * Imports an AdaptiveMtFile and adds all of its sentences into the
   * AdaptiveMtDataset. (adaptiveMtDatasets.importAdaptiveMtFile)
   *
   * @param string $parent Required. The resource name of the file, in form of
   * `projects/{project-number-or-
   * id}/locations/{location_id}/adaptiveMtDatasets/{dataset}`
   * @param ImportAdaptiveMtFileRequest $postBody
   * @param array $optParams Optional parameters.
   * @return ImportAdaptiveMtFileResponse
   * @throws \Google\Service\Exception
   */
  public function importAdaptiveMtFile($parent, ImportAdaptiveMtFileRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('importAdaptiveMtFile', [$params], ImportAdaptiveMtFileResponse::class);
  }
  /**
   * Lists all Adaptive MT datasets for which the caller has read permission.
   * (adaptiveMtDatasets.listProjectsLocationsAdaptiveMtDatasets)
   *
   * @param string $parent Required. The resource name of the project from which
   * to list the Adaptive MT datasets. `projects/{project-number-or-
   * id}/locations/{location-id}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An expression for filtering the results of
   * the request. Filter is not supported yet.
   * @opt_param int pageSize Optional. Requested page size. The server may return
   * fewer results than requested. If unspecified, the server picks an appropriate
   * default.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return. Typically, this is the value of
   * ListAdaptiveMtDatasetsResponse.next_page_token returned from the previous
   * call to `ListAdaptiveMtDatasets` method. The first page is returned if
   * `page_token`is empty or missing.
   * @return ListAdaptiveMtDatasetsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsAdaptiveMtDatasets($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListAdaptiveMtDatasetsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsAdaptiveMtDatasets::class, 'Google_Service_Translate_Resource_ProjectsLocationsAdaptiveMtDatasets');
