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

namespace Google\Service\Contactcenterinsights\Resource;

use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1Issue;
use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1ListIssuesResponse;
use Google\Service\Contactcenterinsights\GoogleLongrunningOperation;
use Google\Service\Contactcenterinsights\GoogleProtobufEmpty;

/**
 * The "issues" collection of methods.
 * Typical usage is:
 *  <code>
 *   $contactcenterinsightsService = new Google\Service\Contactcenterinsights(...);
 *   $issues = $contactcenterinsightsService->projects_locations_issueModels_issues;
 *  </code>
 */
class ProjectsLocationsIssueModelsIssues extends \Google\Service\Resource
{
  /**
   * Creates an issue. (issues.create)
   *
   * @param string $parent Required. The parent resource of the issue.
   * @param GoogleCloudContactcenterinsightsV1Issue $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudContactcenterinsightsV1Issue $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes an issue. (issues.delete)
   *
   * @param string $name Required. The name of the issue to delete.
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Gets an issue. (issues.get)
   *
   * @param string $name Required. The name of the issue to get.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudContactcenterinsightsV1Issue
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudContactcenterinsightsV1Issue::class);
  }
  /**
   * Lists issues. (issues.listProjectsLocationsIssueModelsIssues)
   *
   * @param string $parent Required. The parent resource of the issue.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudContactcenterinsightsV1ListIssuesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsIssueModelsIssues($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudContactcenterinsightsV1ListIssuesResponse::class);
  }
  /**
   * Updates an issue. (issues.patch)
   *
   * @param string $name Immutable. The resource name of the issue. Format: projec
   * ts/{project}/locations/{location}/issueModels/{issue_model}/issues/{issue}
   * @param GoogleCloudContactcenterinsightsV1Issue $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask The list of fields to be updated.
   * @return GoogleCloudContactcenterinsightsV1Issue
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudContactcenterinsightsV1Issue $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudContactcenterinsightsV1Issue::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsIssueModelsIssues::class, 'Google_Service_Contactcenterinsights_Resource_ProjectsLocationsIssueModelsIssues');
