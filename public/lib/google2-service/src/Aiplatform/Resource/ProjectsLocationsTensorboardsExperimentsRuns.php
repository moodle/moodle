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

use Google\Service\Aiplatform\GoogleCloudAiplatformV1BatchCreateTensorboardRunsRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1BatchCreateTensorboardRunsResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListTensorboardRunsResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1TensorboardRun;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1WriteTensorboardRunDataRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1WriteTensorboardRunDataResponse;
use Google\Service\Aiplatform\GoogleLongrunningOperation;

/**
 * The "runs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $runs = $aiplatformService->projects_locations_tensorboards_experiments_runs;
 *  </code>
 */
class ProjectsLocationsTensorboardsExperimentsRuns extends \Google\Service\Resource
{
  /**
   * Batch create TensorboardRuns. (runs.batchCreate)
   *
   * @param string $parent Required. The resource name of the
   * TensorboardExperiment to create the TensorboardRuns in. Format: `projects/{pr
   * oject}/locations/{location}/tensorboards/{tensorboard}/experiments/{experimen
   * t}` The parent field in the CreateTensorboardRunRequest messages must match
   * this field.
   * @param GoogleCloudAiplatformV1BatchCreateTensorboardRunsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1BatchCreateTensorboardRunsResponse
   * @throws \Google\Service\Exception
   */
  public function batchCreate($parent, GoogleCloudAiplatformV1BatchCreateTensorboardRunsRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchCreate', [$params], GoogleCloudAiplatformV1BatchCreateTensorboardRunsResponse::class);
  }
  /**
   * Creates a TensorboardRun. (runs.create)
   *
   * @param string $parent Required. The resource name of the
   * TensorboardExperiment to create the TensorboardRun in. Format: `projects/{pro
   * ject}/locations/{location}/tensorboards/{tensorboard}/experiments/{experiment
   * }`
   * @param GoogleCloudAiplatformV1TensorboardRun $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string tensorboardRunId Required. The ID to use for the
   * Tensorboard run, which becomes the final component of the Tensorboard run's
   * resource name. This value should be 1-128 characters, and valid characters
   * are `/a-z-/`.
   * @return GoogleCloudAiplatformV1TensorboardRun
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudAiplatformV1TensorboardRun $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudAiplatformV1TensorboardRun::class);
  }
  /**
   * Deletes a TensorboardRun. (runs.delete)
   *
   * @param string $name Required. The name of the TensorboardRun to be deleted.
   * Format: `projects/{project}/locations/{location}/tensorboards/{tensorboard}/e
   * xperiments/{experiment}/runs/{run}`
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
   * Gets a TensorboardRun. (runs.get)
   *
   * @param string $name Required. The name of the TensorboardRun resource.
   * Format: `projects/{project}/locations/{location}/tensorboards/{tensorboard}/e
   * xperiments/{experiment}/runs/{run}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1TensorboardRun
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1TensorboardRun::class);
  }
  /**
   * Lists TensorboardRuns in a Location.
   * (runs.listProjectsLocationsTensorboardsExperimentsRuns)
   *
   * @param string $parent Required. The resource name of the
   * TensorboardExperiment to list TensorboardRuns. Format: `projects/{project}/lo
   * cations/{location}/tensorboards/{tensorboard}/experiments/{experiment}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Lists the TensorboardRuns that match the filter
   * expression.
   * @opt_param string orderBy Field to use to sort the list.
   * @opt_param int pageSize The maximum number of TensorboardRuns to return. The
   * service may return fewer than this value. If unspecified, at most 50
   * TensorboardRuns are returned. The maximum value is 1000; values above 1000
   * are coerced to 1000.
   * @opt_param string pageToken A page token, received from a previous
   * TensorboardService.ListTensorboardRuns call. Provide this to retrieve the
   * subsequent page. When paginating, all other parameters provided to
   * TensorboardService.ListTensorboardRuns must match the call that provided the
   * page token.
   * @opt_param string readMask Mask specifying which fields to read.
   * @return GoogleCloudAiplatformV1ListTensorboardRunsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsTensorboardsExperimentsRuns($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListTensorboardRunsResponse::class);
  }
  /**
   * Updates a TensorboardRun. (runs.patch)
   *
   * @param string $name Output only. Name of the TensorboardRun. Format: `project
   * s/{project}/locations/{location}/tensorboards/{tensorboard}/experiments/{expe
   * riment}/runs/{run}`
   * @param GoogleCloudAiplatformV1TensorboardRun $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten in the TensorboardRun resource by the update. The
   * fields specified in the update_mask are relative to the resource, not the
   * full request. A field is overwritten if it's in the mask. If the user does
   * not provide a mask then all fields are overwritten if new values are
   * specified.
   * @return GoogleCloudAiplatformV1TensorboardRun
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudAiplatformV1TensorboardRun $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudAiplatformV1TensorboardRun::class);
  }
  /**
   * Write time series data points into multiple TensorboardTimeSeries under a
   * TensorboardRun. If any data fail to be ingested, an error is returned.
   * (runs.write)
   *
   * @param string $tensorboardRun Required. The resource name of the
   * TensorboardRun to write data to. Format: `projects/{project}/locations/{locat
   * ion}/tensorboards/{tensorboard}/experiments/{experiment}/runs/{run}`
   * @param GoogleCloudAiplatformV1WriteTensorboardRunDataRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1WriteTensorboardRunDataResponse
   * @throws \Google\Service\Exception
   */
  public function write($tensorboardRun, GoogleCloudAiplatformV1WriteTensorboardRunDataRequest $postBody, $optParams = [])
  {
    $params = ['tensorboardRun' => $tensorboardRun, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('write', [$params], GoogleCloudAiplatformV1WriteTensorboardRunDataResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsTensorboardsExperimentsRuns::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsTensorboardsExperimentsRuns');
