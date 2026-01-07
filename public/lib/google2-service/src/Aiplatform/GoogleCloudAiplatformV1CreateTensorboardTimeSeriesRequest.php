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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1CreateTensorboardTimeSeriesRequest extends \Google\Model
{
  /**
   * Required. The resource name of the TensorboardRun to create the
   * TensorboardTimeSeries in. Format: `projects/{project}/locations/{location}/
   * tensorboards/{tensorboard}/experiments/{experiment}/runs/{run}`
   *
   * @var string
   */
  public $parent;
  protected $tensorboardTimeSeriesType = GoogleCloudAiplatformV1TensorboardTimeSeries::class;
  protected $tensorboardTimeSeriesDataType = '';
  /**
   * Optional. The user specified unique ID to use for the
   * TensorboardTimeSeries, which becomes the final component of the
   * TensorboardTimeSeries's resource name. This value should match "a-z0-9{0,
   * 127}"
   *
   * @var string
   */
  public $tensorboardTimeSeriesId;

  /**
   * Required. The resource name of the TensorboardRun to create the
   * TensorboardTimeSeries in. Format: `projects/{project}/locations/{location}/
   * tensorboards/{tensorboard}/experiments/{experiment}/runs/{run}`
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
  /**
   * Required. The TensorboardTimeSeries to create.
   *
   * @param GoogleCloudAiplatformV1TensorboardTimeSeries $tensorboardTimeSeries
   */
  public function setTensorboardTimeSeries(GoogleCloudAiplatformV1TensorboardTimeSeries $tensorboardTimeSeries)
  {
    $this->tensorboardTimeSeries = $tensorboardTimeSeries;
  }
  /**
   * @return GoogleCloudAiplatformV1TensorboardTimeSeries
   */
  public function getTensorboardTimeSeries()
  {
    return $this->tensorboardTimeSeries;
  }
  /**
   * Optional. The user specified unique ID to use for the
   * TensorboardTimeSeries, which becomes the final component of the
   * TensorboardTimeSeries's resource name. This value should match "a-z0-9{0,
   * 127}"
   *
   * @param string $tensorboardTimeSeriesId
   */
  public function setTensorboardTimeSeriesId($tensorboardTimeSeriesId)
  {
    $this->tensorboardTimeSeriesId = $tensorboardTimeSeriesId;
  }
  /**
   * @return string
   */
  public function getTensorboardTimeSeriesId()
  {
    return $this->tensorboardTimeSeriesId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1CreateTensorboardTimeSeriesRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1CreateTensorboardTimeSeriesRequest');
