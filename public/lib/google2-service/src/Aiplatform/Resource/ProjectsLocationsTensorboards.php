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

use Google\Service\Aiplatform\GoogleCloudAiplatformV1BatchReadTensorboardTimeSeriesDataResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListTensorboardsResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ReadTensorboardSizeResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ReadTensorboardUsageResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1Tensorboard;
use Google\Service\Aiplatform\GoogleLongrunningOperation;

/**
 * The "tensorboards" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $tensorboards = $aiplatformService->projects_locations_tensorboards;
 *  </code>
 */
class ProjectsLocationsTensorboards extends \Google\Service\Resource
{
  /**
   * Reads multiple TensorboardTimeSeries' data. The data point number limit is
   * 1000 for scalars, 100 for tensors and blob references. If the number of data
   * points stored is less than the limit, all data is returned. Otherwise, the
   * number limit of data points is randomly selected from this time series and
   * returned. (tensorboards.batchRead)
   *
   * @param string $tensorboard Required. The resource name of the Tensorboard
   * containing TensorboardTimeSeries to read data from. Format:
   * `projects/{project}/locations/{location}/tensorboards/{tensorboard}`. The
   * TensorboardTimeSeries referenced by time_series must be sub resources of this
   * Tensorboard.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string timeSeries Required. The resource names of the
   * TensorboardTimeSeries to read data from. Format: `projects/{project}/location
   * s/{location}/tensorboards/{tensorboard}/experiments/{experiment}/runs/{run}/t
   * imeSeries/{time_series}`
   * @return GoogleCloudAiplatformV1BatchReadTensorboardTimeSeriesDataResponse
   * @throws \Google\Service\Exception
   */
  public function batchRead($tensorboard, $optParams = [])
  {
    $params = ['tensorboard' => $tensorboard];
    $params = array_merge($params, $optParams);
    return $this->call('batchRead', [$params], GoogleCloudAiplatformV1BatchReadTensorboardTimeSeriesDataResponse::class);
  }
  /**
   * Creates a Tensorboard. (tensorboards.create)
   *
   * @param string $parent Required. The resource name of the Location to create
   * the Tensorboard in. Format: `projects/{project}/locations/{location}`
   * @param GoogleCloudAiplatformV1Tensorboard $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudAiplatformV1Tensorboard $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes a Tensorboard. (tensorboards.delete)
   *
   * @param string $name Required. The name of the Tensorboard to be deleted.
   * Format: `projects/{project}/locations/{location}/tensorboards/{tensorboard}`
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
   * Gets a Tensorboard. (tensorboards.get)
   *
   * @param string $name Required. The name of the Tensorboard resource. Format:
   * `projects/{project}/locations/{location}/tensorboards/{tensorboard}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1Tensorboard
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1Tensorboard::class);
  }
  /**
   * Lists Tensorboards in a Location.
   * (tensorboards.listProjectsLocationsTensorboards)
   *
   * @param string $parent Required. The resource name of the Location to list
   * Tensorboards. Format: `projects/{project}/locations/{location}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Lists the Tensorboards that match the filter
   * expression.
   * @opt_param string orderBy Field to use to sort the list.
   * @opt_param int pageSize The maximum number of Tensorboards to return. The
   * service may return fewer than this value. If unspecified, at most 100
   * Tensorboards are returned. The maximum value is 100; values above 100 are
   * coerced to 100.
   * @opt_param string pageToken A page token, received from a previous
   * TensorboardService.ListTensorboards call. Provide this to retrieve the
   * subsequent page. When paginating, all other parameters provided to
   * TensorboardService.ListTensorboards must match the call that provided the
   * page token.
   * @opt_param string readMask Mask specifying which fields to read.
   * @return GoogleCloudAiplatformV1ListTensorboardsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsTensorboards($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListTensorboardsResponse::class);
  }
  /**
   * Updates a Tensorboard. (tensorboards.patch)
   *
   * @param string $name Output only. Name of the Tensorboard. Format:
   * `projects/{project}/locations/{location}/tensorboards/{tensorboard}`
   * @param GoogleCloudAiplatformV1Tensorboard $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten in the Tensorboard resource by the update. The
   * fields specified in the update_mask are relative to the resource, not the
   * full request. A field is overwritten if it's in the mask. If the user does
   * not provide a mask then all fields are overwritten if new values are
   * specified.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudAiplatformV1Tensorboard $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Returns the storage size for a given TensorBoard instance.
   * (tensorboards.readSize)
   *
   * @param string $tensorboard Required. The name of the Tensorboard resource.
   * Format: `projects/{project}/locations/{location}/tensorboards/{tensorboard}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1ReadTensorboardSizeResponse
   * @throws \Google\Service\Exception
   */
  public function readSize($tensorboard, $optParams = [])
  {
    $params = ['tensorboard' => $tensorboard];
    $params = array_merge($params, $optParams);
    return $this->call('readSize', [$params], GoogleCloudAiplatformV1ReadTensorboardSizeResponse::class);
  }
  /**
   * Returns a list of monthly active users for a given TensorBoard instance.
   * (tensorboards.readUsage)
   *
   * @param string $tensorboard Required. The name of the Tensorboard resource.
   * Format: `projects/{project}/locations/{location}/tensorboards/{tensorboard}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1ReadTensorboardUsageResponse
   * @throws \Google\Service\Exception
   */
  public function readUsage($tensorboard, $optParams = [])
  {
    $params = ['tensorboard' => $tensorboard];
    $params = array_merge($params, $optParams);
    return $this->call('readUsage', [$params], GoogleCloudAiplatformV1ReadTensorboardUsageResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsTensorboards::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsTensorboards');
