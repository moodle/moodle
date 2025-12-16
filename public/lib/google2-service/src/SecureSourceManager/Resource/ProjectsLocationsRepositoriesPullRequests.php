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

namespace Google\Service\SecureSourceManager\Resource;

use Google\Service\SecureSourceManager\ClosePullRequestRequest;
use Google\Service\SecureSourceManager\ListPullRequestFileDiffsResponse;
use Google\Service\SecureSourceManager\ListPullRequestsResponse;
use Google\Service\SecureSourceManager\MergePullRequestRequest;
use Google\Service\SecureSourceManager\OpenPullRequestRequest;
use Google\Service\SecureSourceManager\Operation;
use Google\Service\SecureSourceManager\PullRequest;

/**
 * The "pullRequests" collection of methods.
 * Typical usage is:
 *  <code>
 *   $securesourcemanagerService = new Google\Service\SecureSourceManager(...);
 *   $pullRequests = $securesourcemanagerService->projects_locations_repositories_pullRequests;
 *  </code>
 */
class ProjectsLocationsRepositoriesPullRequests extends \Google\Service\Resource
{
  /**
   * Closes a pull request without merging. (pullRequests.close)
   *
   * @param string $name Required. The pull request to close. Format: `projects/{p
   * roject_number}/locations/{location_id}/repositories/{repository_id}/pullReque
   * sts/{pull_request_id}`
   * @param ClosePullRequestRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function close($name, ClosePullRequestRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('close', [$params], Operation::class);
  }
  /**
   * Creates a pull request. (pullRequests.create)
   *
   * @param string $parent Required. The repository that the pull request is
   * created from. Format: `projects/{project_number}/locations/{location_id}/repo
   * sitories/{repository_id}`
   * @param PullRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, PullRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Gets a pull request. (pullRequests.get)
   *
   * @param string $name Required. Name of the pull request to retrieve. The
   * format is `projects/{project}/locations/{location}/repositories/{repository}/
   * pullRequests/{pull_request}`.
   * @param array $optParams Optional parameters.
   * @return PullRequest
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], PullRequest::class);
  }
  /**
   * Lists pull requests in a repository.
   * (pullRequests.listProjectsLocationsRepositoriesPullRequests)
   *
   * @param string $parent Required. The repository in which to list pull
   * requests. Format: `projects/{project_number}/locations/{location_id}/reposito
   * ries/{repository_id}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. Requested page size. Server may return
   * fewer items than requested. If unspecified, server will pick an appropriate
   * default.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return.
   * @return ListPullRequestsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsRepositoriesPullRequests($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListPullRequestsResponse::class);
  }
  /**
   * Lists a pull request's file diffs. (pullRequests.listFileDiffs)
   *
   * @param string $name Required. The pull request to list file diffs for.
   * Format: `projects/{project_number}/locations/{location_id}/repositories/{repo
   * sitory_id}/pullRequests/{pull_request_id}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. Requested page size. Server may return
   * fewer items than requested. If unspecified, server will pick an appropriate
   * default.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return.
   * @return ListPullRequestFileDiffsResponse
   * @throws \Google\Service\Exception
   */
  public function listFileDiffs($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('listFileDiffs', [$params], ListPullRequestFileDiffsResponse::class);
  }
  /**
   * Merges a pull request. (pullRequests.merge)
   *
   * @param string $name Required. The pull request to merge. Format: `projects/{p
   * roject_number}/locations/{location_id}/repositories/{repository_id}/pullReque
   * sts/{pull_request_id}`
   * @param MergePullRequestRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function merge($name, MergePullRequestRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('merge', [$params], Operation::class);
  }
  /**
   * Opens a pull request. (pullRequests.open)
   *
   * @param string $name Required. The pull request to open. Format: `projects/{pr
   * oject_number}/locations/{location_id}/repositories/{repository_id}/pullReques
   * ts/{pull_request_id}`
   * @param OpenPullRequestRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function open($name, OpenPullRequestRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('open', [$params], Operation::class);
  }
  /**
   * Updates a pull request. (pullRequests.patch)
   *
   * @param string $name Output only. A unique identifier for a PullRequest. The
   * number appended at the end is generated by the server. Format: `projects/{pro
   * ject}/locations/{location}/repositories/{repository}/pullRequests/{pull_reque
   * st_id}`
   * @param PullRequest $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. Field mask is used to specify the
   * fields to be overwritten in the pull request resource by the update. The
   * fields specified in the update_mask are relative to the resource, not the
   * full request. A field will be overwritten if it is in the mask. The special
   * value "*" means full replacement.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, PullRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsRepositoriesPullRequests::class, 'Google_Service_SecureSourceManager_Resource_ProjectsLocationsRepositoriesPullRequests');
