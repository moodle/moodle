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

use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1BulkDeleteFeedbackLabelsRequest;
use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1BulkDownloadFeedbackLabelsRequest;
use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1BulkUploadFeedbackLabelsRequest;
use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1EncryptionSpec;
use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1ListAllFeedbackLabelsResponse;
use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1QueryMetricsRequest;
use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1QueryPerformanceOverviewRequest;
use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1Settings;
use Google\Service\Contactcenterinsights\GoogleLongrunningOperation;

/**
 * The "locations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $contactcenterinsightsService = new Google\Service\Contactcenterinsights(...);
 *   $locations = $contactcenterinsightsService->projects_locations;
 *  </code>
 */
class ProjectsLocations extends \Google\Service\Resource
{
  /**
   * Delete feedback labels in bulk using a filter.
   * (locations.bulkDeleteFeedbackLabels)
   *
   * @param string $parent Required. The parent resource for new feedback labels.
   * @param GoogleCloudContactcenterinsightsV1BulkDeleteFeedbackLabelsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function bulkDeleteFeedbackLabels($parent, GoogleCloudContactcenterinsightsV1BulkDeleteFeedbackLabelsRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('bulkDeleteFeedbackLabels', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Download feedback labels in bulk from an external source. Currently supports
   * exporting Quality AI example conversations with transcripts and question
   * bodies. (locations.bulkDownloadFeedbackLabels)
   *
   * @param string $parent Required. The parent resource for new feedback labels.
   * @param GoogleCloudContactcenterinsightsV1BulkDownloadFeedbackLabelsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function bulkDownloadFeedbackLabels($parent, GoogleCloudContactcenterinsightsV1BulkDownloadFeedbackLabelsRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('bulkDownloadFeedbackLabels', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Upload feedback labels from an external source in bulk. Currently supports
   * labeling Quality AI example conversations.
   * (locations.bulkUploadFeedbackLabels)
   *
   * @param string $parent Required. The parent resource for new feedback labels.
   * @param GoogleCloudContactcenterinsightsV1BulkUploadFeedbackLabelsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function bulkUploadFeedbackLabels($parent, GoogleCloudContactcenterinsightsV1BulkUploadFeedbackLabelsRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('bulkUploadFeedbackLabels', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Gets location-level encryption key specification.
   * (locations.getEncryptionSpec)
   *
   * @param string $name Required. The name of the encryption spec resource to
   * get.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudContactcenterinsightsV1EncryptionSpec
   * @throws \Google\Service\Exception
   */
  public function getEncryptionSpec($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getEncryptionSpec', [$params], GoogleCloudContactcenterinsightsV1EncryptionSpec::class);
  }
  /**
   * Gets project-level settings. (locations.getSettings)
   *
   * @param string $name Required. The name of the settings resource to get.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudContactcenterinsightsV1Settings
   * @throws \Google\Service\Exception
   */
  public function getSettings($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getSettings', [$params], GoogleCloudContactcenterinsightsV1Settings::class);
  }
  /**
   * List all feedback labels by project number. (locations.listAllFeedbackLabels)
   *
   * @param string $parent Required. The parent resource of all feedback labels
   * per project.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. A filter to reduce results to a specific
   * subset in the entire project. Supports disjunctions (OR) and conjunctions
   * (AND). Supported fields: * `issue_model_id` * `qa_question_id` *
   * `min_create_time` * `max_create_time` * `min_update_time` * `max_update_time`
   * * `feedback_label_type`: QUALITY_AI, TOPIC_MODELING
   * @opt_param int pageSize Optional. The maximum number of feedback labels to
   * return in the response. A valid page size ranges from 0 to 100,000 inclusive.
   * If the page size is zero or unspecified, a default page size of 100 will be
   * chosen. Note that a call might return fewer results than the requested page
   * size.
   * @opt_param string pageToken Optional. The value returned by the last
   * `ListAllFeedbackLabelsResponse`. This value indicates that this is a
   * continuation of a prior `ListAllFeedbackLabels` call and that the system
   * should return the next page of data.
   * @return GoogleCloudContactcenterinsightsV1ListAllFeedbackLabelsResponse
   * @throws \Google\Service\Exception
   */
  public function listAllFeedbackLabels($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('listAllFeedbackLabels', [$params], GoogleCloudContactcenterinsightsV1ListAllFeedbackLabelsResponse::class);
  }
  /**
   * Query metrics. (locations.queryMetrics)
   *
   * @param string $location Required. The location of the data.
   * "projects/{project}/locations/{location}"
   * @param GoogleCloudContactcenterinsightsV1QueryMetricsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function queryMetrics($location, GoogleCloudContactcenterinsightsV1QueryMetricsRequest $postBody, $optParams = [])
  {
    $params = ['location' => $location, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('queryMetrics', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Generates a summary of predefined performance metrics for a set of
   * conversations. Conversations can be specified by specifying a time window and
   * an agent id, for now. The summary includes a comparison of metrics computed
   * for conversations in the previous time period, and also a comparison with
   * peers in the same time period. (locations.queryPerformanceOverview)
   *
   * @param string $parent Required. The parent resource of the conversations to
   * derive performance stats from. "projects/{project}/locations/{location}"
   * @param GoogleCloudContactcenterinsightsV1QueryPerformanceOverviewRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function queryPerformanceOverview($parent, GoogleCloudContactcenterinsightsV1QueryPerformanceOverviewRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('queryPerformanceOverview', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Updates project-level settings. (locations.updateSettings)
   *
   * @param string $name Immutable. The resource name of the settings resource.
   * Format: projects/{project}/locations/{location}/settings
   * @param GoogleCloudContactcenterinsightsV1Settings $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The list of fields to be updated.
   * @return GoogleCloudContactcenterinsightsV1Settings
   * @throws \Google\Service\Exception
   */
  public function updateSettings($name, GoogleCloudContactcenterinsightsV1Settings $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('updateSettings', [$params], GoogleCloudContactcenterinsightsV1Settings::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocations::class, 'Google_Service_Contactcenterinsights_Resource_ProjectsLocations');
