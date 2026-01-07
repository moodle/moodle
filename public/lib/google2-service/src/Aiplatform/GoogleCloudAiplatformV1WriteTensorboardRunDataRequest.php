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

class GoogleCloudAiplatformV1WriteTensorboardRunDataRequest extends \Google\Collection
{
  protected $collection_key = 'timeSeriesData';
  /**
   * Required. The resource name of the TensorboardRun to write data to. Format:
   * `projects/{project}/locations/{location}/tensorboards/{tensorboard}/experim
   * ents/{experiment}/runs/{run}`
   *
   * @var string
   */
  public $tensorboardRun;
  protected $timeSeriesDataType = GoogleCloudAiplatformV1TimeSeriesData::class;
  protected $timeSeriesDataDataType = 'array';

  /**
   * Required. The resource name of the TensorboardRun to write data to. Format:
   * `projects/{project}/locations/{location}/tensorboards/{tensorboard}/experim
   * ents/{experiment}/runs/{run}`
   *
   * @param string $tensorboardRun
   */
  public function setTensorboardRun($tensorboardRun)
  {
    $this->tensorboardRun = $tensorboardRun;
  }
  /**
   * @return string
   */
  public function getTensorboardRun()
  {
    return $this->tensorboardRun;
  }
  /**
   * Required. The TensorboardTimeSeries data to write. Values with in a time
   * series are indexed by their step value. Repeated writes to the same step
   * will overwrite the existing value for that step. The upper limit of data
   * points per write request is 5000.
   *
   * @param GoogleCloudAiplatformV1TimeSeriesData[] $timeSeriesData
   */
  public function setTimeSeriesData($timeSeriesData)
  {
    $this->timeSeriesData = $timeSeriesData;
  }
  /**
   * @return GoogleCloudAiplatformV1TimeSeriesData[]
   */
  public function getTimeSeriesData()
  {
    return $this->timeSeriesData;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1WriteTensorboardRunDataRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1WriteTensorboardRunDataRequest');
