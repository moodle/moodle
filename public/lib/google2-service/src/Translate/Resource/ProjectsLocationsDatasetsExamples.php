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

use Google\Service\Translate\ListExamplesResponse;

/**
 * The "examples" collection of methods.
 * Typical usage is:
 *  <code>
 *   $translateService = new Google\Service\Translate(...);
 *   $examples = $translateService->projects_locations_datasets_examples;
 *  </code>
 */
class ProjectsLocationsDatasetsExamples extends \Google\Service\Resource
{
  /**
   * Lists sentence pairs in the dataset.
   * (examples.listProjectsLocationsDatasetsExamples)
   *
   * @param string $parent Required. Name of the parent dataset. In form of
   * `projects/{project-number-or-id}/locations/{location-id}/datasets/{dataset-
   * id}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An expression for filtering the examples
   * that will be returned. Example filter: * `usage=TRAIN`
   * @opt_param int pageSize Optional. Requested page size. The server can return
   * fewer results than requested.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * for the server to return. Typically obtained from next_page_token field in
   * the response of a ListExamples call.
   * @return ListExamplesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsDatasetsExamples($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListExamplesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDatasetsExamples::class, 'Google_Service_Translate_Resource_ProjectsLocationsDatasetsExamples');
