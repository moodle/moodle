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

class WindowsBasedSli extends \Google\Model
{
  /**
   * A monitoring filter (https://cloud.google.com/monitoring/api/v3/filters)
   * specifying a TimeSeries with ValueType = BOOL. The window is good if any
   * true values appear in the window.
   *
   * @var string
   */
  public $goodBadMetricFilter;
  protected $goodTotalRatioThresholdType = PerformanceThreshold::class;
  protected $goodTotalRatioThresholdDataType = '';
  protected $metricMeanInRangeType = MetricRange::class;
  protected $metricMeanInRangeDataType = '';
  protected $metricSumInRangeType = MetricRange::class;
  protected $metricSumInRangeDataType = '';
  /**
   * Duration over which window quality is evaluated. Must be an integer
   * fraction of a day and at least 60s.
   *
   * @var string
   */
  public $windowPeriod;

  /**
   * A monitoring filter (https://cloud.google.com/monitoring/api/v3/filters)
   * specifying a TimeSeries with ValueType = BOOL. The window is good if any
   * true values appear in the window.
   *
   * @param string $goodBadMetricFilter
   */
  public function setGoodBadMetricFilter($goodBadMetricFilter)
  {
    $this->goodBadMetricFilter = $goodBadMetricFilter;
  }
  /**
   * @return string
   */
  public function getGoodBadMetricFilter()
  {
    return $this->goodBadMetricFilter;
  }
  /**
   * A window is good if its performance is high enough.
   *
   * @param PerformanceThreshold $goodTotalRatioThreshold
   */
  public function setGoodTotalRatioThreshold(PerformanceThreshold $goodTotalRatioThreshold)
  {
    $this->goodTotalRatioThreshold = $goodTotalRatioThreshold;
  }
  /**
   * @return PerformanceThreshold
   */
  public function getGoodTotalRatioThreshold()
  {
    return $this->goodTotalRatioThreshold;
  }
  /**
   * A window is good if the metric's value is in a good range, averaged across
   * returned streams.
   *
   * @param MetricRange $metricMeanInRange
   */
  public function setMetricMeanInRange(MetricRange $metricMeanInRange)
  {
    $this->metricMeanInRange = $metricMeanInRange;
  }
  /**
   * @return MetricRange
   */
  public function getMetricMeanInRange()
  {
    return $this->metricMeanInRange;
  }
  /**
   * A window is good if the metric's value is in a good range, summed across
   * returned streams.
   *
   * @param MetricRange $metricSumInRange
   */
  public function setMetricSumInRange(MetricRange $metricSumInRange)
  {
    $this->metricSumInRange = $metricSumInRange;
  }
  /**
   * @return MetricRange
   */
  public function getMetricSumInRange()
  {
    return $this->metricSumInRange;
  }
  /**
   * Duration over which window quality is evaluated. Must be an integer
   * fraction of a day and at least 60s.
   *
   * @param string $windowPeriod
   */
  public function setWindowPeriod($windowPeriod)
  {
    $this->windowPeriod = $windowPeriod;
  }
  /**
   * @return string
   */
  public function getWindowPeriod()
  {
    return $this->windowPeriod;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WindowsBasedSli::class, 'Google_Service_Monitoring_WindowsBasedSli');
