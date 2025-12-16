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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1QueryMetricsResponseSlice extends \Google\Collection
{
  protected $collection_key = 'dimensions';
  protected $dimensionsType = GoogleCloudContactcenterinsightsV1Dimension::class;
  protected $dimensionsDataType = 'array';
  protected $timeSeriesType = GoogleCloudContactcenterinsightsV1QueryMetricsResponseSliceTimeSeries::class;
  protected $timeSeriesDataType = '';
  protected $totalType = GoogleCloudContactcenterinsightsV1QueryMetricsResponseSliceDataPoint::class;
  protected $totalDataType = '';

  /**
   * A unique combination of dimensions that this slice represents.
   *
   * @param GoogleCloudContactcenterinsightsV1Dimension[] $dimensions
   */
  public function setDimensions($dimensions)
  {
    $this->dimensions = $dimensions;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1Dimension[]
   */
  public function getDimensions()
  {
    return $this->dimensions;
  }
  /**
   * A time series of metric values. This is only populated if the request
   * specifies a time granularity other than NONE.
   *
   * @param GoogleCloudContactcenterinsightsV1QueryMetricsResponseSliceTimeSeries $timeSeries
   */
  public function setTimeSeries(GoogleCloudContactcenterinsightsV1QueryMetricsResponseSliceTimeSeries $timeSeries)
  {
    $this->timeSeries = $timeSeries;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1QueryMetricsResponseSliceTimeSeries
   */
  public function getTimeSeries()
  {
    return $this->timeSeries;
  }
  /**
   * The total metric value. The interval of this data point is [starting create
   * time, ending create time) from the request.
   *
   * @param GoogleCloudContactcenterinsightsV1QueryMetricsResponseSliceDataPoint $total
   */
  public function setTotal(GoogleCloudContactcenterinsightsV1QueryMetricsResponseSliceDataPoint $total)
  {
    $this->total = $total;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1QueryMetricsResponseSliceDataPoint
   */
  public function getTotal()
  {
    return $this->total;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1QueryMetricsResponseSlice::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1QueryMetricsResponseSlice');
