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

use Google\Service\Aiplatform\GoogleCloudAiplatformV1CancelHyperparameterTuningJobRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1HyperparameterTuningJob;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListHyperparameterTuningJobsResponse;
use Google\Service\Aiplatform\GoogleLongrunningOperation;
use Google\Service\Aiplatform\GoogleProtobufEmpty;

/**
 * The "hyperparameterTuningJobs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $hyperparameterTuningJobs = $aiplatformService->projects_locations_hyperparameterTuningJobs;
 *  </code>
 */
class ProjectsLocationsHyperparameterTuningJobs extends \Google\Service\Resource
{
  /**
   * Cancels a HyperparameterTuningJob. Starts asynchronous cancellation on the
   * HyperparameterTuningJob. The server makes a best effort to cancel the job,
   * but success is not guaranteed. Clients can use
   * JobService.GetHyperparameterTuningJob or other methods to check whether the
   * cancellation succeeded or whether the job completed despite cancellation. On
   * successful cancellation, the HyperparameterTuningJob is not deleted; instead
   * it becomes a job with a HyperparameterTuningJob.error value with a
   * google.rpc.Status.code of 1, corresponding to `Code.CANCELLED`, and
   * HyperparameterTuningJob.state is set to `CANCELLED`.
   * (hyperparameterTuningJobs.cancel)
   *
   * @param string $name Required. The name of the HyperparameterTuningJob to
   * cancel. Format: `projects/{project}/locations/{location}/hyperparameterTuning
   * Jobs/{hyperparameter_tuning_job}`
   * @param GoogleCloudAiplatformV1CancelHyperparameterTuningJobRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function cancel($name, GoogleCloudAiplatformV1CancelHyperparameterTuningJobRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('cancel', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Creates a HyperparameterTuningJob (hyperparameterTuningJobs.create)
   *
   * @param string $parent Required. The resource name of the Location to create
   * the HyperparameterTuningJob in. Format:
   * `projects/{project}/locations/{location}`
   * @param GoogleCloudAiplatformV1HyperparameterTuningJob $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1HyperparameterTuningJob
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudAiplatformV1HyperparameterTuningJob $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudAiplatformV1HyperparameterTuningJob::class);
  }
  /**
   * Deletes a HyperparameterTuningJob. (hyperparameterTuningJobs.delete)
   *
   * @param string $name Required. The name of the HyperparameterTuningJob
   * resource to be deleted. Format: `projects/{project}/locations/{location}/hype
   * rparameterTuningJobs/{hyperparameter_tuning_job}`
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
   * Gets a HyperparameterTuningJob (hyperparameterTuningJobs.get)
   *
   * @param string $name Required. The name of the HyperparameterTuningJob
   * resource. Format: `projects/{project}/locations/{location}/hyperparameterTuni
   * ngJobs/{hyperparameter_tuning_job}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1HyperparameterTuningJob
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1HyperparameterTuningJob::class);
  }
  /**
   * Lists HyperparameterTuningJobs in a Location.
   * (hyperparameterTuningJobs.listProjectsLocationsHyperparameterTuningJobs)
   *
   * @param string $parent Required. The resource name of the Location to list the
   * HyperparameterTuningJobs from. Format:
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
   * @opt_param int pageSize The standard list page size.
   * @opt_param string pageToken The standard list page token. Typically obtained
   * via ListHyperparameterTuningJobsResponse.next_page_token of the previous
   * JobService.ListHyperparameterTuningJobs call.
   * @opt_param string readMask Mask specifying which fields to read.
   * @return GoogleCloudAiplatformV1ListHyperparameterTuningJobsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsHyperparameterTuningJobs($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListHyperparameterTuningJobsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsHyperparameterTuningJobs::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsHyperparameterTuningJobs');
