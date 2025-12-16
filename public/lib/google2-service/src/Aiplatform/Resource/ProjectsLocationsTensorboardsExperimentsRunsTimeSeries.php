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

use Google\Service\Aiplatform\GoogleCloudAiplatformV1ExportTensorboardTimeSeriesDataRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ExportTensorboardTimeSeriesDataResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListTensorboardTimeSeriesResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ReadTensorboardBlobDataResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ReadTensorboardTimeSeriesDataResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1TensorboardTimeSeries;
use Google\Service\Aiplatform\GoogleLongrunningOperation;

/**
 * The "timeSeries" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $timeSeries = $aiplatformService->projects_locations_tensorboards_experiments_runs_timeSeries;
 *  </code>
 */
class ProjectsLocationsTensorboardsExperimentsRunsTimeSeries extends \Google\Service\Resource
{
  /**
   * Creates a TensorboardTimeSeries. (timeSeries.create)
   *
   * @param string $parent Required. The resource name of the TensorboardRun to
   * create the TensorboardTimeSeries in. Format: `projects/{project}/locations/{l
   * ocation}/tensorboards/{tensorboard}/experiments/{experiment}/runs/{run}`
   * @param GoogleCloudAiplatformV1TensorboardTimeSeries $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string tensorboardTimeSeriesId Optional. The user specified unique
   * ID to use for the TensorboardTimeSeries, which becomes the final component of
   * the TensorboardTimeSeries's resource name. This value should match "a-z0-9{0,
   * 127}"
   * @return GoogleCloudAiplatformV1TensorboardTimeSeries
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudAiplatformV1TensorboardTimeSeries $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudAiplatformV1TensorboardTimeSeries::class);
  }
  /**
   * Deletes a TensorboardTimeSeries. (timeSeries.delete)
   *
   * @param string $name Required. The name of the TensorboardTimeSeries to be
   * deleted. Format: `projects/{project}/locations/{location}/tensorboards/{tenso
   * rboard}/experiments/{experiment}/runs/{run}/timeSeries/{time_series}`
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
   * Exports a TensorboardTimeSeries' data. Data is returned in paginated
   * responses. (timeSeries.exportTensorboardTimeSeries)
   *
   * @param string $tensorboardTimeSeries Required. The resource name of the
   * TensorboardTimeSeries to export data from. Format: `projects/{project}/locati
   * ons/{location}/tensorboards/{tensorboard}/experiments/{experiment}/runs/{run}
   * /timeSeries/{time_series}`
   * @param GoogleCloudAiplatformV1ExportTensorboardTimeSeriesDataRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1ExportTensorboardTimeSeriesDataResponse
   * @throws \Google\Service\Exception
   */
  public function exportTensorboardTimeSeries($tensorboardTimeSeries, GoogleCloudAiplatformV1ExportTensorboardTimeSeriesDataRequest $postBody, $optParams = [])
  {
    $params = ['tensorboardTimeSeries' => $tensorboardTimeSeries, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('exportTensorboardTimeSeries', [$params], GoogleCloudAiplatformV1ExportTensorboardTimeSeriesDataResponse::class);
  }
  /**
   * Gets a TensorboardTimeSeries. (timeSeries.get)
   *
   * @param string $name Required. The name of the TensorboardTimeSeries resource.
   * Format: `projects/{project}/locations/{location}/tensorboards/{tensorboard}/e
   * xperiments/{experiment}/runs/{run}/timeSeries/{time_series}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1TensorboardTimeSeries
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1TensorboardTimeSeries::class);
  }
  /**
   * Lists TensorboardTimeSeries in a Location.
   * (timeSeries.listProjectsLocationsTensorboardsExperimentsRunsTimeSeries)
   *
   * @param string $parent Required. The resource name of the TensorboardRun to
   * list TensorboardTimeSeries. Format: `projects/{project}/locations/{location}/
   * tensorboards/{tensorboard}/experiments/{experiment}/runs/{run}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Lists the TensorboardTimeSeries that match the
   * filter expression.
   * @opt_param string orderBy Field to use to sort the list.
   * @opt_param int pageSize The maximum number of TensorboardTimeSeries to
   * return. The service may return fewer than this value. If unspecified, at most
   * 50 TensorboardTimeSeries are returned. The maximum value is 1000; values
   * above 1000 are coerced to 1000.
   * @opt_param string pageToken A page token, received from a previous
   * TensorboardService.ListTensorboardTimeSeries call. Provide this to retrieve
   * the subsequent page. When paginating, all other parameters provided to
   * TensorboardService.ListTensorboardTimeSeries must match the call that
   * provided the page token.
   * @opt_param string readMask Mask specifying which fields to read.
   * @return GoogleCloudAiplatformV1ListTensorboardTimeSeriesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsTensorboardsExperimentsRunsTimeSeries($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListTensorboardTimeSeriesResponse::class);
  }
  /**
   * Updates a TensorboardTimeSeries. (timeSeries.patch)
   *
   * @param string $name Output only. Name of the TensorboardTimeSeries.
   * @param GoogleCloudAiplatformV1TensorboardTimeSeries $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten in the TensorboardTimeSeries resource by the update.
   * The fields specified in the update_mask are relative to the resource, not the
   * full request. A field is overwritten if it's in the mask. If the user does
   * not provide a mask then all fields are overwritten if new values are
   * specified.
   * @return GoogleCloudAiplatformV1TensorboardTimeSeries
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudAiplatformV1TensorboardTimeSeries $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudAiplatformV1TensorboardTimeSeries::class);
  }
  /**
   * Reads a TensorboardTimeSeries' data. By default, if the number of data points
   * stored is less than 1000, all data is returned. Otherwise, 1000 data points
   * is randomly selected from this time series and returned. This value can be
   * changed by changing max_data_points, which can't be greater than 10k.
   * (timeSeries.read)
   *
   * @param string $tensorboardTimeSeries Required. The resource name of the
   * TensorboardTimeSeries to read data from. Format: `projects/{project}/location
   * s/{location}/tensorboards/{tensorboard}/experiments/{experiment}/runs/{run}/t
   * imeSeries/{time_series}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Reads the TensorboardTimeSeries' data that match the
   * filter expression.
   * @opt_param int maxDataPoints The maximum number of TensorboardTimeSeries'
   * data to return. This value should be a positive integer. This value can be
   * set to -1 to return all data.
   * @return GoogleCloudAiplatformV1ReadTensorboardTimeSeriesDataResponse
   * @throws \Google\Service\Exception
   */
  public function read($tensorboardTimeSeries, $optParams = [])
  {
    $params = ['tensorboardTimeSeries' => $tensorboardTimeSeries];
    $params = array_merge($params, $optParams);
    return $this->call('read', [$params], GoogleCloudAiplatformV1ReadTensorboardTimeSeriesDataResponse::class);
  }
  /**
   * Gets bytes of TensorboardBlobs. This is to allow reading blob data stored in
   * consumer project's Cloud Storage bucket without users having to obtain Cloud
   * Storage access permission. (timeSeries.readBlobData)
   *
   * @param string $timeSeries Required. The resource name of the
   * TensorboardTimeSeries to list Blobs. Format: `projects/{project}/locations/{l
   * ocation}/tensorboards/{tensorboard}/experiments/{experiment}/runs/{run}/timeS
   * eries/{time_series}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string blobIds IDs of the blobs to read.
   * @return GoogleCloudAiplatformV1ReadTensorboardBlobDataResponse
   * @throws \Google\Service\Exception
   */
  public function readBlobData($timeSeries, $optParams = [])
  {
    $params = ['timeSeries' => $timeSeries];
    $params = array_merge($params, $optParams);
    return $this->call('readBlobData', [$params], GoogleCloudAiplatformV1ReadTensorboardBlobDataResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsTensorboardsExperimentsRunsTimeSeries::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsTensorboardsExperimentsRunsTimeSeries');
