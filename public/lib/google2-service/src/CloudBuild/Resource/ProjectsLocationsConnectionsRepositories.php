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

namespace Google\Service\CloudBuild\Resource;

use Google\Service\CloudBuild\BatchCreateRepositoriesRequest;
use Google\Service\CloudBuild\FetchGitRefsResponse;
use Google\Service\CloudBuild\FetchReadTokenRequest;
use Google\Service\CloudBuild\FetchReadTokenResponse;
use Google\Service\CloudBuild\FetchReadWriteTokenRequest;
use Google\Service\CloudBuild\FetchReadWriteTokenResponse;
use Google\Service\CloudBuild\ListRepositoriesResponse;
use Google\Service\CloudBuild\Operation;
use Google\Service\CloudBuild\Repository;

/**
 * The "repositories" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cloudbuildService = new Google\Service\CloudBuild(...);
 *   $repositories = $cloudbuildService->projects_locations_connections_repositories;
 *  </code>
 */
class ProjectsLocationsConnectionsRepositories extends \Google\Service\Resource
{
  /**
   * Fetches read token of a given repository. (repositories.accessReadToken)
   *
   * @param string $repository Required. The resource name of the repository in
   * the format `projects/locations/connections/repositories`.
   * @param FetchReadTokenRequest $postBody
   * @param array $optParams Optional parameters.
   * @return FetchReadTokenResponse
   * @throws \Google\Service\Exception
   */
  public function accessReadToken($repository, FetchReadTokenRequest $postBody, $optParams = [])
  {
    $params = ['repository' => $repository, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('accessReadToken', [$params], FetchReadTokenResponse::class);
  }
  /**
   * Fetches read/write token of a given repository.
   * (repositories.accessReadWriteToken)
   *
   * @param string $repository Required. The resource name of the repository in
   * the format `projects/locations/connections/repositories`.
   * @param FetchReadWriteTokenRequest $postBody
   * @param array $optParams Optional parameters.
   * @return FetchReadWriteTokenResponse
   * @throws \Google\Service\Exception
   */
  public function accessReadWriteToken($repository, FetchReadWriteTokenRequest $postBody, $optParams = [])
  {
    $params = ['repository' => $repository, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('accessReadWriteToken', [$params], FetchReadWriteTokenResponse::class);
  }
  /**
   * Creates multiple repositories inside a connection. (repositories.batchCreate)
   *
   * @param string $parent Required. The connection to contain all the
   * repositories being created. Format: projects/locations/connections The parent
   * field in the CreateRepositoryRequest messages must either be empty or match
   * this field.
   * @param BatchCreateRepositoriesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function batchCreate($parent, BatchCreateRepositoriesRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchCreate', [$params], Operation::class);
  }
  /**
   * Creates a Repository. (repositories.create)
   *
   * @param string $parent Required. The connection to contain the repository. If
   * the request is part of a BatchCreateRepositoriesRequest, this field should be
   * empty or match the parent specified there.
   * @param Repository $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string repositoryId Required. The ID to use for the repository,
   * which will become the final component of the repository's resource name. This
   * ID should be unique in the connection. Allows alphanumeric characters and any
   * of -._~%!$&'()*+,;=@.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Repository $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single repository. (repositories.delete)
   *
   * @param string $name Required. The name of the Repository to delete. Format:
   * `projects/locations/connections/repositories`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag The current etag of the repository. If an etag is
   * provided and does not match the current etag of the repository, deletion will
   * be blocked and an ABORTED error will be returned.
   * @opt_param bool validateOnly If set, validate the request, but do not
   * actually post it.
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
   * Fetch the list of branches or tags for a given repository.
   * (repositories.fetchGitRefs)
   *
   * @param string $repository Required. The resource name of the repository in
   * the format `projects/locations/connections/repositories`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. Number of results to return in the list.
   * Default to 20.
   * @opt_param string pageToken Optional. Page start.
   * @opt_param string refType Type of refs to fetch
   * @return FetchGitRefsResponse
   * @throws \Google\Service\Exception
   */
  public function fetchGitRefs($repository, $optParams = [])
  {
    $params = ['repository' => $repository];
    $params = array_merge($params, $optParams);
    return $this->call('fetchGitRefs', [$params], FetchGitRefsResponse::class);
  }
  /**
   * Gets details of a single repository. (repositories.get)
   *
   * @param string $name Required. The name of the Repository to retrieve. Format:
   * `projects/locations/connections/repositories`.
   * @param array $optParams Optional parameters.
   * @return Repository
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Repository::class);
  }
  /**
   * Lists Repositories in a given connection.
   * (repositories.listProjectsLocationsConnectionsRepositories)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * Repositories. Format: `projects/locations/connections`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter A filter expression that filters resources listed in
   * the response. Expressions must follow API improvement proposal
   * [AIP-160](https://google.aip.dev/160). e.g.
   * `remote_uri:"https://github.com*"`.
   * @opt_param int pageSize Number of results to return in the list.
   * @opt_param string pageToken Page start.
   * @opt_param bool returnPartialSuccess Optional. If set to true, the response
   * will return partial results when some regions are unreachable. If set to
   * false, the response will fail if any region is unreachable.
   * @return ListRepositoriesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsConnectionsRepositories($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListRepositoriesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsConnectionsRepositories::class, 'Google_Service_CloudBuild_Resource_ProjectsLocationsConnectionsRepositories');
