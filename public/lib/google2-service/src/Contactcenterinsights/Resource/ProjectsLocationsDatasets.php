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
use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1Dataset;
use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1ListAllFeedbackLabelsResponse;
use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1ListDatasetsResponse;
use Google\Service\Contactcenterinsights\GoogleLongrunningOperation;

/**
 * The "datasets" collection of methods.
 * Typical usage is:
 *  <code>
 *   $contactcenterinsightsService = new Google\Service\Contactcenterinsights(...);
 *   $datasets = $contactcenterinsightsService->projects_locations_datasets;
 *  </code>
 */
class ProjectsLocationsDatasets extends \Google\Service\Resource
{
  /**
   * Delete feedback labels in bulk using a filter.
   * (datasets.bulkDeleteFeedbackLabels)
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
   * bodies. (datasets.bulkDownloadFeedbackLabels)
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
   * (datasets.bulkUploadFeedbackLabels)
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
   * Creates a dataset. (datasets.create)
   *
   * @param string $parent Required. The parent resource of the dataset.
   * @param GoogleCloudContactcenterinsightsV1Dataset $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string datasetId Optional. The ID to use for the dataset.
   * @return GoogleCloudContactcenterinsightsV1Dataset
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudContactcenterinsightsV1Dataset $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudContactcenterinsightsV1Dataset::class);
  }
  /**
   * Delete a dataset. (datasets.delete)
   *
   * @param string $name Required. The name of the dataset to delete.
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Gets a dataset. (datasets.get)
   *
   * @param string $name Required. The name of the dataset to get.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudContactcenterinsightsV1Dataset
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudContactcenterinsightsV1Dataset::class);
  }
  /**
   * List datasets matching the input. (datasets.listProjectsLocationsDatasets)
   *
   * @param string $parent Required. The parent resource of the dataset.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. A filter to reduce results to a specific
   * subset. Useful for querying datasets with specific properties. Supported
   * fields include, for Q2 though we only support list by project: - `type` -
   * `description` - `project_number`
   * @opt_param int pageSize Optional. The maximum number of datasets to return in
   * the response. If this value is zero, the service will select a default size.
   * A call might return fewer objects than requested. A non-empty
   * `next_page_token` in the response indicates that more data is available.
   * @opt_param string pageToken Optional. The value returned by the last
   * `ListDatasetsResponse`; indicates that this is a continuation of a prior
   * `ListDatasets` call and the system should return the next page of data.
   * @return GoogleCloudContactcenterinsightsV1ListDatasetsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsDatasets($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudContactcenterinsightsV1ListDatasetsResponse::class);
  }
  /**
   * List all feedback labels by project number. (datasets.listAllFeedbackLabels)
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
   * Updates a dataset. (datasets.patch)
   *
   * @param string $name Immutable. Identifier. Resource name of the dataset.
   * Format: projects/{project}/locations/{location}/datasets/{dataset}
   * @param GoogleCloudContactcenterinsightsV1Dataset $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The list of fields to update.
   * @return GoogleCloudContactcenterinsightsV1Dataset
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudContactcenterinsightsV1Dataset $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudContactcenterinsightsV1Dataset::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDatasets::class, 'Google_Service_Contactcenterinsights_Resource_ProjectsLocationsDatasets');
