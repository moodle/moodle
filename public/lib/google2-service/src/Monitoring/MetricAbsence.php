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

namespace Google\Service\Monitoring;

class MetricAbsence extends \Google\Collection
{
  protected $collection_key = 'aggregations';
  protected $aggregationsType = Aggregation::class;
  protected $aggregationsDataType = 'array';
  /**
   * Required. The amount of time that a time series must fail to report new
   * data to be considered failing. The minimum value of this field is 120
   * seconds. Larger values that are a multiple of a minute--for example, 240 or
   * 300 seconds--are supported. If an invalid value is given, an error will be
   * returned.
   *
   * @var string
   */
  public $duration;
  /**
   * Required. A filter (https://cloud.google.com/monitoring/api/v3/filters)
   * that identifies which time series should be compared with the threshold.The
   * filter is similar to the one that is specified in the ListTimeSeries
   * request (https://cloud.google.com/monitoring/api/ref_v3/rest/v3/projects.ti
   * meSeries/list) (that call is useful to verify the time series that will be
   * retrieved / processed). The filter must specify the metric type and the
   * resource type. Optionally, it can specify resource labels and metric
   * labels. This field must not exceed 2048 Unicode characters in length.
   *
   * @var string
   */
  public $filter;
  protected $triggerType = Trigger::class;
  protected $triggerDataType = '';

  /**
   * Specifies the alignment of data points in individual time series as well as
   * how to combine the retrieved time series together (such as when aggregating
   * multiple streams on each resource to a single stream for each resource or
   * when aggregating streams across all members of a group of resources).
   * Multiple aggregations are applied in the order specified.This field is
   * similar to the one in the ListTimeSeries request (https://cloud.google.com/
   * monitoring/api/ref_v3/rest/v3/projects.timeSeries/list). It is advisable to
   * use the ListTimeSeries method when debugging this field.
   *
   * @param Aggregation[] $aggregations
   */
  public function setAggregations($aggregations)
  {
    $this->aggregations = $aggregations;
  }
  /**
   * @return Aggregation[]
   */
  public function getAggregations()
  {
    return $this->aggregations;
  }
  /**
   * Required. The amount of time that a time series must fail to report new
   * data to be considered failing. The minimum value of this field is 120
   * seconds. Larger values that are a multiple of a minute--for example, 240 or
   * 300 seconds--are supported. If an invalid value is given, an error will be
   * returned.
   *
   * @param string $duration
   */
  public function setDuration($duration)
  {
    $this->duration = $duration;
  }
  /**
   * @return string
   */
  public function getDuration()
  {
    return $this->duration;
  }
  /**
   * Required. A filter (https://cloud.google.com/monitoring/api/v3/filters)
   * that identifies which time series should be compared with the threshold.The
   * filter is similar to the one that is specified in the ListTimeSeries
   * request (https://cloud.google.com/monitoring/api/ref_v3/rest/v3/projects.ti
   * meSeries/list) (that call is useful to verify the time series that will be
   * retrieved / processed). The filter must specify the metric type and the
   * resource type. Optionally, it can specify resource labels and metric
   * labels. This field must not exceed 2048 Unicode characters in length.
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * The number/percent of time series for which the comparison must hold in
   * order for the condition to trigger. If unspecified, then the condition will
   * trigger if the comparison is true for any of the time series that have been
   * identified by filter and aggregations.
   *
   * @param Trigger $trigger
   */
  public function setTrigger(Trigger $trigger)
  {
    $this->trigger = $trigger;
  }
  /**
   * @return Trigger
   */
  public function getTrigger()
  {
    return $this->trigger;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MetricAbsence::class, 'Google_Service_Monitoring_MetricAbsence');
