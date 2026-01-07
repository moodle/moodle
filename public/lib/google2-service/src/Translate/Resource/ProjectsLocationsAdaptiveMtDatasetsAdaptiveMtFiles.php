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

use Google\Service\Translate\AdaptiveMtFile;
use Google\Service\Translate\ListAdaptiveMtFilesResponse;
use Google\Service\Translate\TranslateEmpty;

/**
 * The "adaptiveMtFiles" collection of methods.
 * Typical usage is:
 *  <code>
 *   $translateService = new Google\Service\Translate(...);
 *   $adaptiveMtFiles = $translateService->projects_locations_adaptiveMtDatasets_adaptiveMtFiles;
 *  </code>
 */
class ProjectsLocationsAdaptiveMtDatasetsAdaptiveMtFiles extends \Google\Service\Resource
{
  /**
   * Deletes an AdaptiveMtFile along with its sentences. (adaptiveMtFiles.delete)
   *
   * @param string $name Required. The resource name of the file to delete, in
   * form of `projects/{project-number-or-id}/locations/{location_id}/adaptiveMtDa
   * tasets/{dataset}/adaptiveMtFiles/{file}`
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
   * Gets and AdaptiveMtFile (adaptiveMtFiles.get)
   *
   * @param string $name Required. The resource name of the file, in form of
   * `projects/{project-number-or-id}/locations/{location_id}/adaptiveMtDatasets/{
   * dataset}/adaptiveMtFiles/{file}`
   * @param array $optParams Optional parameters.
   * @return AdaptiveMtFile
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], AdaptiveMtFile::class);
  }
  /**
   * Lists all AdaptiveMtFiles associated to an AdaptiveMtDataset.
   * (adaptiveMtFiles.listProjectsLocationsAdaptiveMtDatasetsAdaptiveMtFiles)
   *
   * @param string $parent Required. The resource name of the project from which
   * to list the Adaptive MT files.
   * `projects/{project}/locations/{location}/adaptiveMtDatasets/{dataset}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return. Typically, this is the value of
   * ListAdaptiveMtFilesResponse.next_page_token returned from the previous call
   * to `ListAdaptiveMtFiles` method. The first page is returned if `page_token`is
   * empty or missing.
   * @return ListAdaptiveMtFilesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsAdaptiveMtDatasetsAdaptiveMtFiles($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListAdaptiveMtFilesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsAdaptiveMtDatasetsAdaptiveMtFiles::class, 'Google_Service_Translate_Resource_ProjectsLocationsAdaptiveMtDatasetsAdaptiveMtFiles');
