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

use Google\Service\SecureSourceManager\BatchCreatePullRequestCommentsRequest;
use Google\Service\SecureSourceManager\ListPullRequestCommentsResponse;
use Google\Service\SecureSourceManager\Operation;
use Google\Service\SecureSourceManager\PullRequestComment;
use Google\Service\SecureSourceManager\ResolvePullRequestCommentsRequest;
use Google\Service\SecureSourceManager\UnresolvePullRequestCommentsRequest;

/**
 * The "pullRequestComments" collection of methods.
 * Typical usage is:
 *  <code>
 *   $securesourcemanagerService = new Google\Service\SecureSourceManager(...);
 *   $pullRequestComments = $securesourcemanagerService->projects_locations_repositories_pullRequests_pullRequestComments;
 *  </code>
 */
class ProjectsLocationsRepositoriesPullRequestsPullRequestComments extends \Google\Service\Resource
{
  /**
   * Batch creates pull request comments. (pullRequestComments.batchCreate)
   *
   * @param string $parent Required. The pull request in which to create the pull
   * request comments. Format: `projects/{project_number}/locations/{location_id}/
   * repositories/{repository_id}/pullRequests/{pull_request_id}`
   * @param BatchCreatePullRequestCommentsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function batchCreate($parent, BatchCreatePullRequestCommentsRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchCreate', [$params], Operation::class);
  }
  /**
   * Creates a pull request comment. (pullRequestComments.create)
   *
   * @param string $parent Required. The pull request in which to create the pull
   * request comment. Format: `projects/{project_number}/locations/{location_id}/r
   * epositories/{repository_id}/pullRequests/{pull_request_id}`
   * @param PullRequestComment $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, PullRequestComment $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a pull request comment. (pullRequestComments.delete)
   *
   * @param string $name Required. Name of the pull request comment to delete. The
   * format is `projects/{project_number}/locations/{location_id}/repositories/{re
   * pository_id}/pullRequests/{pull_request_id}/pullRequestComments/{comment_id}`
   * .
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
   * Gets a pull request comment. (pullRequestComments.get)
   *
   * @param string $name Required. Name of the pull request comment to retrieve.
   * The format is `projects/{project_number}/locations/{location_id}/repositories
   * /{repository_id}/pullRequests/{pull_request_id}/pullRequestComments/{comment_
   * id}`.
   * @param array $optParams Optional parameters.
   * @return PullRequestComment
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], PullRequestComment::class);
  }
  /**
   * Lists pull request comments. (pullRequestComments.listProjectsLocationsReposi
   * toriesPullRequestsPullRequestComments)
   *
   * @param string $parent Required. The pull request in which to list pull
   * request comments. Format: `projects/{project_number}/locations/{location_id}/
   * repositories/{repository_id}/pullRequests/{pull_request_id}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. Requested page size. If unspecified, at
   * most 100 pull request comments will be returned. The maximum value is 100;
   * values above 100 will be coerced to 100.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return.
   * @return ListPullRequestCommentsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsRepositoriesPullRequestsPullRequestComments($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListPullRequestCommentsResponse::class);
  }
  /**
   * Updates a pull request comment. (pullRequestComments.patch)
   *
   * @param string $name Identifier. Unique identifier for the pull request
   * comment. The comment id is generated by the server. Format: `projects/{projec
   * t}/locations/{location}/repositories/{repository}/pullRequests/{pull_request}
   * /pullRequestComments/{comment_id}`
   * @param PullRequestComment $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. Field mask is used to specify the
   * fields to be overwritten in the pull request comment resource by the update.
   * Updatable fields are `body`.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, PullRequestComment $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Resolves pull request comments. (pullRequestComments.resolve)
   *
   * @param string $parent Required. The pull request in which to resolve the pull
   * request comments. Format: `projects/{project_number}/locations/{location_id}/
   * repositories/{repository_id}/pullRequests/{pull_request_id}`
   * @param ResolvePullRequestCommentsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function resolve($parent, ResolvePullRequestCommentsRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('resolve', [$params], Operation::class);
  }
  /**
   * Unresolves pull request comment. (pullRequestComments.unresolve)
   *
   * @param string $parent Required. The pull request in which to resolve the pull
   * request comments. Format: `projects/{project_number}/locations/{location_id}/
   * repositories/{repository_id}/pullRequests/{pull_request_id}`
   * @param UnresolvePullRequestCommentsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function unresolve($parent, UnresolvePullRequestCommentsRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('unresolve', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsRepositoriesPullRequestsPullRequestComments::class, 'Google_Service_SecureSourceManager_Resource_ProjectsLocationsRepositoriesPullRequestsPullRequestComments');
