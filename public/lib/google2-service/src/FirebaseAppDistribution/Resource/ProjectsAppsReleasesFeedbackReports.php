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

use Google\Service\FirebaseAppDistribution\GoogleFirebaseAppdistroV1FeedbackReport;
use Google\Service\FirebaseAppDistribution\GoogleFirebaseAppdistroV1ListFeedbackReportsResponse;
use Google\Service\FirebaseAppDistribution\GoogleProtobufEmpty;

/**
 * The "feedbackReports" collection of methods.
 * Typical usage is:
 *  <code>
 *   $firebaseappdistributionService = new Google\Service\FirebaseAppDistribution(...);
 *   $feedbackReports = $firebaseappdistributionService->projects_apps_releases_feedbackReports;
 *  </code>
 */
class ProjectsAppsReleasesFeedbackReports extends \Google\Service\Resource
{
  /**
   * Deletes a feedback report. (feedbackReports.delete)
   *
   * @param string $name Required. The name of the feedback report to delete.
   * Format: projects/{project_number}/apps/{app}/releases/{release}/feedbackRepor
   * ts/{feedback_report}
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
   * Gets a feedback report. (feedbackReports.get)
   *
   * @param string $name Required. The name of the feedback report to retrieve.
   * Format: projects/{project_number}/apps/{app}/releases/{release}/feedbackRepor
   * ts/{feedback_report}
   * @param array $optParams Optional parameters.
   * @return GoogleFirebaseAppdistroV1FeedbackReport
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleFirebaseAppdistroV1FeedbackReport::class);
  }
  /**
   * Lists feedback reports. By default, sorts by `createTime` in descending
   * order. (feedbackReports.listProjectsAppsReleasesFeedbackReports)
   *
   * @param string $parent Required. The name of the release resource, which is
   * the parent of the feedback report resources. Format:
   * `projects/{project_number}/apps/{app}/releases/{release}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Output only. The maximum number of feedback reports
   * to return. The service may return fewer than this value. The valid range is
   * [1-100]; If unspecified (0), at most 25 feedback reports are returned. Values
   * above 100 are coerced to 100.
   * @opt_param string pageToken Output only. A page token, received from a
   * previous `ListFeedbackReports` call. Provide this to retrieve the subsequent
   * page. When paginating, all other parameters provided to `ListFeedbackReports`
   * must match the call that provided the page token.
   * @return GoogleFirebaseAppdistroV1ListFeedbackReportsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsAppsReleasesFeedbackReports($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleFirebaseAppdistroV1ListFeedbackReportsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsAppsReleasesFeedbackReports::class, 'Google_Service_FirebaseAppDistribution_Resource_ProjectsAppsReleasesFeedbackReports');
