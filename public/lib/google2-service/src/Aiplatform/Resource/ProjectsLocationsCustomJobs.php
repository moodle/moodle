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

use Google\Service\Aiplatform\GoogleCloudAiplatformV1CancelCustomJobRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1CustomJob;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListCustomJobsResponse;
use Google\Service\Aiplatform\GoogleLongrunningOperation;
use Google\Service\Aiplatform\GoogleProtobufEmpty;

/**
 * The "customJobs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $customJobs = $aiplatformService->projects_locations_customJobs;
 *  </code>
 */
class ProjectsLocationsCustomJobs extends \Google\Service\Resource
{
  /**
   * Cancels a CustomJob. Starts asynchronous cancellation on the CustomJob. The
   * server makes a best effort to cancel the job, but success is not guaranteed.
   * Clients can use JobService.GetCustomJob or other methods to check whether the
   * cancellation succeeded or whether the job completed despite cancellation. On
   * successful cancellation, the CustomJob is not deleted; instead it becomes a
   * job with a CustomJob.error value with a google.rpc.Status.code of 1,
   * corresponding to `Code.CANCELLED`, and CustomJob.state is set to `CANCELLED`.
   * (customJobs.cancel)
   *
   * @param string $name Required. The name of the CustomJob to cancel. Format:
   * `projects/{project}/locations/{location}/customJobs/{custom_job}`
   * @param GoogleCloudAiplatformV1CancelCustomJobRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function cancel($name, GoogleCloudAiplatformV1CancelCustomJobRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('cancel', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Creates a CustomJob. A created CustomJob right away will be attempted to be
   * run. (customJobs.create)
   *
   * @param string $parent Required. The resource name of the Location to create
   * the CustomJob in. Format: `projects/{project}/locations/{location}`
   * @param GoogleCloudAiplatformV1CustomJob $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1CustomJob
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudAiplatformV1CustomJob $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudAiplatformV1CustomJob::class);
  }
  /**
   * Deletes a CustomJob. (customJobs.delete)
   *
   * @param string $name Required. The name of the CustomJob resource to be
   * deleted. Format:
   * `projects/{project}/locations/{location}/customJobs/{custom_job}`
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
   * Gets a CustomJob. (customJobs.get)
   *
   * @param string $name Required. The name of the CustomJob resource. Format:
   * `projects/{project}/locations/{location}/customJobs/{custom_job}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1CustomJob
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1CustomJob::class);
  }
  /**
   * Lists CustomJobs in a Location. (customJobs.listProjectsLocationsCustomJobs)
   *
   * @param string $parent Required. The resource name of the Location to list the
   * CustomJobs from. Format: `projects/{project}/locations/{location}`
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
   * via ListCustomJobsResponse.next_page_token of the previous
   * JobService.ListCustomJobs call.
   * @opt_param string readMask Mask specifying which fields to read.
   * @return GoogleCloudAiplatformV1ListCustomJobsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsCustomJobs($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListCustomJobsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsCustomJobs::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsCustomJobs');
