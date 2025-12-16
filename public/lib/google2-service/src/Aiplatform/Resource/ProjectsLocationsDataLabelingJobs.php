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

namespace Google\Service\Aiplatform\Resource;

use Google\Service\Aiplatform\GoogleCloudAiplatformV1CancelDataLabelingJobRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1DataLabelingJob;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListDataLabelingJobsResponse;
use Google\Service\Aiplatform\GoogleLongrunningOperation;
use Google\Service\Aiplatform\GoogleProtobufEmpty;

/**
 * The "dataLabelingJobs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $dataLabelingJobs = $aiplatformService->projects_locations_dataLabelingJobs;
 *  </code>
 */
class ProjectsLocationsDataLabelingJobs extends \Google\Service\Resource
{
  /**
   * Cancels a DataLabelingJob. Success of cancellation is not guaranteed.
   * (dataLabelingJobs.cancel)
   *
   * @param string $name Required. The name of the DataLabelingJob. Format: `proje
   * cts/{project}/locations/{location}/dataLabelingJobs/{data_labeling_job}`
   * @param GoogleCloudAiplatformV1CancelDataLabelingJobRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function cancel($name, GoogleCloudAiplatformV1CancelDataLabelingJobRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('cancel', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Creates a DataLabelingJob. (dataLabelingJobs.create)
   *
   * @param string $parent Required. The parent of the DataLabelingJob. Format:
   * `projects/{project}/locations/{location}`
   * @param GoogleCloudAiplatformV1DataLabelingJob $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1DataLabelingJob
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudAiplatformV1DataLabelingJob $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudAiplatformV1DataLabelingJob::class);
  }
  /**
   * Deletes a DataLabelingJob. (dataLabelingJobs.delete)
   *
   * @param string $name Required. The name of the DataLabelingJob to be deleted.
   * Format: `projects/{project}/locations/{location}/dataLabelingJobs/{data_label
   * ing_job}`
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
   * Gets a DataLabelingJob. (dataLabelingJobs.get)
   *
   * @param string $name Required. The name of the DataLabelingJob. Format: `proje
   * cts/{project}/locations/{location}/dataLabelingJobs/{data_labeling_job}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1DataLabelingJob
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1DataLabelingJob::class);
  }
  /**
   * Lists DataLabelingJobs in a Location.
   * (dataLabelingJobs.listProjectsLocationsDataLabelingJobs)
   *
   * @param string $parent Required. The parent of the DataLabelingJob. Format:
   * `projects/{project}/locations/{location}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter The standard list filter. Supported fields: *
   * `display_name` supports `=`, `!=` comparisons, and `:` wildcard. * `state`
   * supports `=`, `!=` comparisons. * `create_time` supports `=`, `!=`,`<`,
   * `<=`,`>`, `>=` comparisons. `create_time` must be in RFC 3339 format. *
   * `labels` supports general map functions that is: `labels.key=value` -
   * key:value equality `labels.key:* - key existence Some examples of using the
   * filter are: * `state="JOB_STATE_SUCCEEDED" AND display_name:"my_job_*"` *
   * `state!="JOB_STATE_FAILED" OR display_name="my_job"` * `NOT
   * display_name="my_job"` * `create_time>"2021-05-18T00:00:00Z"` *
   * `labels.keyA=valueA` * `labels.keyB:*`
   * @opt_param string orderBy A comma-separated list of fields to order by,
   * sorted in ascending order by default. Use `desc` after a field name for
   * descending.
   * @opt_param int pageSize The standard list page size.
   * @opt_param string pageToken The standard list page token.
   * @opt_param string readMask Mask specifying which fields to read. FieldMask
   * represents a set of symbolic field paths. For example, the mask can be
   * `paths: "name"`. The "name" here is a field in DataLabelingJob. If this field
   * is not set, all fields of the DataLabelingJob are returned.
   * @return GoogleCloudAiplatformV1ListDataLabelingJobsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsDataLabelingJobs($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListDataLabelingJobsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDataLabelingJobs::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsDataLabelingJobs');
