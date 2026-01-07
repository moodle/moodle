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

namespace Google\Service\Dataform\Resource;

use Google\Service\Dataform\CompilationResult;
use Google\Service\Dataform\ListCompilationResultsResponse;
use Google\Service\Dataform\QueryCompilationResultActionsResponse;

/**
 * The "compilationResults" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dataformService = new Google\Service\Dataform(...);
 *   $compilationResults = $dataformService->projects_locations_repositories_compilationResults;
 *  </code>
 */
class ProjectsLocationsRepositoriesCompilationResults extends \Google\Service\Resource
{
  /**
   * Creates a new CompilationResult in a given project and location.
   * (compilationResults.create)
   *
   * @param string $parent Required. The repository in which to create the
   * compilation result. Must be in the format `projects/locations/repositories`.
   * @param CompilationResult $postBody
   * @param array $optParams Optional parameters.
   * @return CompilationResult
   * @throws \Google\Service\Exception
   */
  public function create($parent, CompilationResult $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], CompilationResult::class);
  }
  /**
   * Fetches a single CompilationResult. (compilationResults.get)
   *
   * @param string $name Required. The compilation result's name.
   * @param array $optParams Optional parameters.
   * @return CompilationResult
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], CompilationResult::class);
  }
  /**
   * Lists CompilationResults in a given Repository.
   * (compilationResults.listProjectsLocationsRepositoriesCompilationResults)
   *
   * @param string $parent Required. The repository in which to list compilation
   * results. Must be in the format `projects/locations/repositories`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filter for the returned list.
   * @opt_param string orderBy Optional. This field only supports ordering by
   * `name` and `create_time`. If unspecified, the server will choose the
   * ordering. If specified, the default order is ascending for the `name` field.
   * @opt_param int pageSize Optional. Maximum number of compilation results to
   * return. The server may return fewer items than requested. If unspecified, the
   * server will pick an appropriate default.
   * @opt_param string pageToken Optional. Page token received from a previous
   * `ListCompilationResults` call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to `ListCompilationResults`,
   * with the exception of `page_size`, must match the call that provided the page
   * token.
   * @return ListCompilationResultsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsRepositoriesCompilationResults($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListCompilationResultsResponse::class);
  }
  /**
   * Returns CompilationResultActions in a given CompilationResult.
   * (compilationResults.query)
   *
   * @param string $name Required. The compilation result's name.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Optional filter for the returned list.
   * Filtering is only currently supported on the `file_path` field.
   * @opt_param int pageSize Optional. Maximum number of compilation results to
   * return. The server may return fewer items than requested. If unspecified, the
   * server will pick an appropriate default.
   * @opt_param string pageToken Optional. Page token received from a previous
   * `QueryCompilationResultActions` call. Provide this to retrieve the subsequent
   * page. When paginating, all other parameters provided to
   * `QueryCompilationResultActions`, with the exception of `page_size`, must
   * match the call that provided the page token.
   * @return QueryCompilationResultActionsResponse
   * @throws \Google\Service\Exception
   */
  public function query($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('query', [$params], QueryCompilationResultActionsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsRepositoriesCompilationResults::class, 'Google_Service_Dataform_Resource_ProjectsLocationsRepositoriesCompilationResults');
