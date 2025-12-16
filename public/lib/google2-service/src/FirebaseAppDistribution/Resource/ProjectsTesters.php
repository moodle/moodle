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

namespace Google\Service\FirebaseAppDistribution\Resource;

use Google\Service\FirebaseAppDistribution\GoogleFirebaseAppdistroV1BatchAddTestersRequest;
use Google\Service\FirebaseAppDistribution\GoogleFirebaseAppdistroV1BatchAddTestersResponse;
use Google\Service\FirebaseAppDistribution\GoogleFirebaseAppdistroV1BatchRemoveTestersRequest;
use Google\Service\FirebaseAppDistribution\GoogleFirebaseAppdistroV1BatchRemoveTestersResponse;
use Google\Service\FirebaseAppDistribution\GoogleFirebaseAppdistroV1ListTestersResponse;
use Google\Service\FirebaseAppDistribution\GoogleFirebaseAppdistroV1Tester;

/**
 * The "testers" collection of methods.
 * Typical usage is:
 *  <code>
 *   $firebaseappdistributionService = new Google\Service\FirebaseAppDistribution(...);
 *   $testers = $firebaseappdistributionService->projects_testers;
 *  </code>
 */
class ProjectsTesters extends \Google\Service\Resource
{
  /**
   * Batch adds testers. This call adds testers for the specified emails if they
   * don't already exist. Returns all testers specified in the request, including
   * newly created and previously existing testers. This action is idempotent.
   * (testers.batchAdd)
   *
   * @param string $project Required. The name of the project resource. Format:
   * `projects/{project_number}`
   * @param GoogleFirebaseAppdistroV1BatchAddTestersRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleFirebaseAppdistroV1BatchAddTestersResponse
   * @throws \Google\Service\Exception
   */
  public function batchAdd($project, GoogleFirebaseAppdistroV1BatchAddTestersRequest $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchAdd', [$params], GoogleFirebaseAppdistroV1BatchAddTestersResponse::class);
  }
  /**
   * Batch removes testers. If found, this call deletes testers for the specified
   * emails. Returns all deleted testers. (testers.batchRemove)
   *
   * @param string $project Required. The name of the project resource. Format:
   * `projects/{project_number}`
   * @param GoogleFirebaseAppdistroV1BatchRemoveTestersRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleFirebaseAppdistroV1BatchRemoveTestersResponse
   * @throws \Google\Service\Exception
   */
  public function batchRemove($project, GoogleFirebaseAppdistroV1BatchRemoveTestersRequest $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchRemove', [$params], GoogleFirebaseAppdistroV1BatchRemoveTestersResponse::class);
  }
  /**
   * Lists testers and their resource ids. (testers.listProjectsTesters)
   *
   * @param string $parent Required. The name of the project resource, which is
   * the parent of the tester resources. Format: `projects/{project_number}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. The expression to filter testers listed in
   * the response. To learn more about filtering, refer to [Google's AIP-160
   * standard](http://aip.dev/160). Supported fields: - `name` - `displayName` -
   * `groups` Example: - `name = "projects/-/testers@example.com"` - `displayName
   * = "Joe Sixpack"` - `groups = "projects/groups/qa-team"`
   * @opt_param int pageSize Optional. The maximum number of testers to return.
   * The service may return fewer than this value. The valid range is [1-1000]; If
   * unspecified (0), at most 10 testers are returned. Values above 1000 are
   * coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListTesters` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListTesters` must match the
   * call that provided the page token.
   * @return GoogleFirebaseAppdistroV1ListTestersResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsTesters($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleFirebaseAppdistroV1ListTestersResponse::class);
  }
  /**
   * Update a tester. If the testers joins a group they gain access to all
   * releases that the group has access to. (testers.patch)
   *
   * @param string $name The name of the tester resource. Format:
   * `projects/{project_number}/testers/{email_address}`
   * @param GoogleFirebaseAppdistroV1Tester $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The list of fields to update.
   * @return GoogleFirebaseAppdistroV1Tester
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleFirebaseAppdistroV1Tester $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleFirebaseAppdistroV1Tester::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsTesters::class, 'Google_Service_FirebaseAppDistribution_Resource_ProjectsTesters');
