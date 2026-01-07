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

use Google\Service\SecureSourceManager\CloseIssueRequest;
use Google\Service\SecureSourceManager\Issue;
use Google\Service\SecureSourceManager\ListIssuesResponse;
use Google\Service\SecureSourceManager\OpenIssueRequest;
use Google\Service\SecureSourceManager\Operation;

/**
 * The "issues" collection of methods.
 * Typical usage is:
 *  <code>
 *   $securesourcemanagerService = new Google\Service\SecureSourceManager(...);
 *   $issues = $securesourcemanagerService->projects_locations_repositories_issues;
 *  </code>
 */
class ProjectsLocationsRepositoriesIssues extends \Google\Service\Resource
{
  /**
   * Closes an issue. (issues.close)
   *
   * @param string $name Required. Name of the issue to close. The format is `proj
   * ects/{project_number}/locations/{location_id}/repositories/{repository_id}/is
   * sues/{issue_id}`.
   * @param CloseIssueRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function close($name, CloseIssueRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('close', [$params], Operation::class);
  }
  /**
   * Creates an issue. (issues.create)
   *
   * @param string $parent Required. The repository in which to create the issue.
   * Format: `projects/{project_number}/locations/{location_id}/repositories/{repo
   * sitory_id}`
   * @param Issue $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Issue $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes an issue. (issues.delete)
   *
   * @param string $name Required. Name of the issue to delete. The format is `pro
   * jects/{project_number}/locations/{location_id}/repositories/{repository_id}/i
   * ssues/{issue_id}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag Optional. The current etag of the issue. If the etag
   * is provided and does not match the current etag of the issue, deletion will
   * be blocked and an ABORTED error will be returned.
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
   * Gets an issue. (issues.get)
   *
   * @param string $name Required. Name of the issue to retrieve. The format is `p
   * rojects/{project}/locations/{location}/repositories/{repository}/issues/{issu
   * e_id}`.
   * @param array $optParams Optional parameters.
   * @return Issue
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Issue::class);
  }
  /**
   * Lists issues in a repository.
   * (issues.listProjectsLocationsRepositoriesIssues)
   *
   * @param string $parent Required. The repository in which to list issues.
   * Format: `projects/{project_number}/locations/{location_id}/repositories/{repo
   * sitory_id}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Used to filter the resulting issues list.
   * @opt_param int pageSize Optional. Requested page size. Server may return
   * fewer items than requested. If unspecified, server will pick an appropriate
   * default.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return.
   * @return ListIssuesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsRepositoriesIssues($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListIssuesResponse::class);
  }
  /**
   * Opens an issue. (issues.open)
   *
   * @param string $name Required. Name of the issue to open. The format is `proje
   * cts/{project_number}/locations/{location_id}/repositories/{repository_id}/iss
   * ues/{issue_id}`.
   * @param OpenIssueRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function open($name, OpenIssueRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('open', [$params], Operation::class);
  }
  /**
   * Updates a issue. (issues.patch)
   *
   * @param string $name Identifier. Unique identifier for an issue. The issue id
   * is generated by the server. Format: `projects/{project}/locations/{location}/
   * repositories/{repository}/issues/{issue_id}`
   * @param Issue $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. Field mask is used to specify the
   * fields to be overwritten in the issue resource by the update. The fields
   * specified in the update_mask are relative to the resource, not the full
   * request. A field will be overwritten if it is in the mask. The special value
   * "*" means full replacement.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Issue $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsRepositoriesIssues::class, 'Google_Service_SecureSourceManager_Resource_ProjectsLocationsRepositoriesIssues');
