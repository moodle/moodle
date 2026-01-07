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

class GoogleCloudContactcenterinsightsV1mainQueryMetricsResponseSliceDataPoint extends \Google\Model
{
  protected $conversationMeasureType = GoogleCloudContactcenterinsightsV1mainQueryMetricsResponseSliceDataPointConversationMeasure::class;
  protected $conversationMeasureDataType = '';
  protected $intervalType = GoogleTypeInterval::class;
  protected $intervalDataType = '';

  /**
   * The measure related to conversations.
   *
   * @param GoogleCloudContactcenterinsightsV1mainQueryMetricsResponseSliceDataPointConversationMeasure $conversationMeasure
   */
  public function setConversationMeasure(GoogleCloudContactcenterinsightsV1mainQueryMetricsResponseSliceDataPointConversationMeasure $conversationMeasure)
  {
    $this->conversationMeasure = $conversationMeasure;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainQueryMetricsResponseSliceDataPointConversationMeasure
   */
  public function getConversationMeasure()
  {
    return $this->conversationMeasure;
  }
  /**
   * The interval that this data point represents. * If this is the total data
   * point, the interval is [starting create time, ending create time) from the
   * request. * If this a data point from the time series, the interval is
   * [time, time + time granularity from the request).
   *
   * @param GoogleTypeInterval $interval
   */
  public function setInterval(GoogleTypeInterval $interval)
  {
    $this->interval = $interval;
  }
  /**
   * @return GoogleTypeInterval
   */
  public function getInterval()
  {
    return $this->interval;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1mainQueryMetricsResponseSliceDataPoint::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1mainQueryMetricsResponseSliceDataPoint');
