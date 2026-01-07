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

use Google\Service\Aiplatform\GoogleCloudAiplatformV1BatchCreateTensorboardTimeSeriesRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1BatchCreateTensorboardTimeSeriesResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListTensorboardExperimentsResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1TensorboardExperiment;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1WriteTensorboardExperimentDataRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1WriteTensorboardExperimentDataResponse;
use Google\Service\Aiplatform\GoogleLongrunningOperation;

/**
 * The "experiments" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $experiments = $aiplatformService->projects_locations_tensorboards_experiments;
 *  </code>
 */
class ProjectsLocationsTensorboardsExperiments extends \Google\Service\Resource
{
  /**
   * Batch create TensorboardTimeSeries that belong to a TensorboardExperiment.
   * (experiments.batchCreate)
   *
   * @param string $parent Required. The resource name of the
   * TensorboardExperiment to create the TensorboardTimeSeries in. Format: `projec
   * ts/{project}/locations/{location}/tensorboards/{tensorboard}/experiments/{exp
   * eriment}` The TensorboardRuns referenced by the parent fields in the
   * CreateTensorboardTimeSeriesRequest messages must be sub resources of this
   * TensorboardExperiment.
   * @param GoogleCloudAiplatformV1BatchCreateTensorboardTimeSeriesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1BatchCreateTensorboardTimeSeriesResponse
   * @throws \Google\Service\Exception
   */
  public function batchCreate($parent, GoogleCloudAiplatformV1BatchCreateTensorboardTimeSeriesRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchCreate', [$params], GoogleCloudAiplatformV1BatchCreateTensorboardTimeSeriesResponse::class);
  }
  /**
   * Creates a TensorboardExperiment. (experiments.create)
   *
   * @param string $parent Required. The resource name of the Tensorboard to
   * create the TensorboardExperiment in. Format:
   * `projects/{project}/locations/{location}/tensorboards/{tensorboard}`
   * @param GoogleCloudAiplatformV1TensorboardExperiment $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string tensorboardExperimentId Required. The ID to use for the
   * Tensorboard experiment, which becomes the final component of the Tensorboard
   * experiment's resource name. This value should be 1-128 characters, and valid
   * characters are `/a-z-/`.
   * @return GoogleCloudAiplatformV1TensorboardExperiment
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudAiplatformV1TensorboardExperiment $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudAiplatformV1TensorboardExperiment::class);
  }
  /**
   * Deletes a TensorboardExperiment. (experiments.delete)
   *
   * @param string $name Required. The name of the TensorboardExperiment to be
   * deleted. Format: `projects/{project}/locations/{location}/tensorboards/{tenso
   * rboard}/experiments/{experiment}`
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
   * Gets a TensorboardExperiment. (experiments.get)
   *
   * @param string $name Required. The name of the TensorboardExperiment resource.
   * Format: `projects/{project}/locations/{location}/tensorboards/{tensorboard}/e
   * xperiments/{experiment}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1TensorboardExperiment
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1TensorboardExperiment::class);
  }
  /**
   * Lists TensorboardExperiments in a Location.
   * (experiments.listProjectsLocationsTensorboardsExperiments)
   *
   * @param string $parent Required. The resource name of the Tensorboard to list
   * TensorboardExperiments. Format:
   * `projects/{project}/locations/{location}/tensorboards/{tensorboard}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Lists the TensorboardExperiments that match the
   * filter expression.
   * @opt_param string orderBy Field to use to sort the list.
   * @opt_param int pageSize The maximum number of TensorboardExperiments to
   * return. The service may return fewer than this value. If unspecified, at most
   * 50 TensorboardExperiments are returned. The maximum value is 1000; values
   * above 1000 are coerced to 1000.
   * @opt_param string pageToken A page token, received from a previous
   * TensorboardService.ListTensorboardExperiments call. Provide this to retrieve
   * the subsequent page. When paginating, all other parameters provided to
   * TensorboardService.ListTensorboardExperiments must match the call that
   * provided the page token.
   * @opt_param string readMask Mask specifying which fields to read.
   * @return GoogleCloudAiplatformV1ListTensorboardExperimentsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsTensorboardsExperiments($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListTensorboardExperimentsResponse::class);
  }
  /**
   * Updates a TensorboardExperiment. (experiments.patch)
   *
   * @param string $name Output only. Name of the TensorboardExperiment. Format: `
   * projects/{project}/locations/{location}/tensorboards/{tensorboard}/experiment
   * s/{experiment}`
   * @param GoogleCloudAiplatformV1TensorboardExperiment $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten in the TensorboardExperiment resource by the update.
   * The fields specified in the update_mask are relative to the resource, not the
   * full request. A field is overwritten if it's in the mask. If the user does
   * not provide a mask then all fields are overwritten if new values are
   * specified.
   * @return GoogleCloudAiplatformV1TensorboardExperiment
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudAiplatformV1TensorboardExperiment $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudAiplatformV1TensorboardExperiment::class);
  }
  /**
   * Write time series data points of multiple TensorboardTimeSeries in multiple
   * TensorboardRun's. If any data fail to be ingested, an error is returned.
   * (experiments.write)
   *
   * @param string $tensorboardExperiment Required. The resource name of the
   * TensorboardExperiment to write data to. Format: `projects/{project}/locations
   * /{location}/tensorboards/{tensorboard}/experiments/{experiment}`
   * @param GoogleCloudAiplatformV1WriteTensorboardExperimentDataRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1WriteTensorboardExperimentDataResponse
   * @throws \Google\Service\Exception
   */
  public function write($tensorboardExperiment, GoogleCloudAiplatformV1WriteTensorboardExperimentDataRequest $postBody, $optParams = [])
  {
    $params = ['tensorboardExperiment' => $tensorboardExperiment, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('write', [$params], GoogleCloudAiplatformV1WriteTensorboardExperimentDataResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsTensorboardsExperiments::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsTensorboardsExperiments');
