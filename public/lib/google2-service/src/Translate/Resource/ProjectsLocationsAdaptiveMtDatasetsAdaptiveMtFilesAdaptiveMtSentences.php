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

use Google\Service\Translate\ListAdaptiveMtSentencesResponse;

/**
 * The "adaptiveMtSentences" collection of methods.
 * Typical usage is:
 *  <code>
 *   $translateService = new Google\Service\Translate(...);
 *   $adaptiveMtSentences = $translateService->projects_locations_adaptiveMtDatasets_adaptiveMtFiles_adaptiveMtSentences;
 *  </code>
 */
class ProjectsLocationsAdaptiveMtDatasetsAdaptiveMtFilesAdaptiveMtSentences extends \Google\Service\Resource
{
  /**
   * Lists all AdaptiveMtSentences under a given file/dataset. (adaptiveMtSentence
   * s.listProjectsLocationsAdaptiveMtDatasetsAdaptiveMtFilesAdaptiveMtSentences)
   *
   * @param string $parent Required. The resource name of the project from which
   * to list the Adaptive MT files. The following format lists all sentences under
   * a file. `projects/{project}/locations/{location}/adaptiveMtDatasets/{dataset}
   * /adaptiveMtFiles/{file}` The following format lists all sentences within a
   * dataset.
   * `projects/{project}/locations/{location}/adaptiveMtDatasets/{dataset}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize
   * @opt_param string pageToken A token identifying a page of results the server
   * should return. Typically, this is the value of
   * ListAdaptiveMtSentencesRequest.next_page_token returned from the previous
   * call to `ListTranslationMemories` method. The first page is returned if
   * `page_token` is empty or missing.
   * @return ListAdaptiveMtSentencesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsAdaptiveMtDatasetsAdaptiveMtFilesAdaptiveMtSentences($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListAdaptiveMtSentencesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsAdaptiveMtDatasetsAdaptiveMtFilesAdaptiveMtSentences::class, 'Google_Service_Translate_Resource_ProjectsLocationsAdaptiveMtDatasetsAdaptiveMtFilesAdaptiveMtSentences');
