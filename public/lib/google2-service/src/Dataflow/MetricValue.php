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

namespace Google\Service\Dataflow;

class MetricValue extends \Google\Model
{
  /**
   * Base name for this metric.
   *
   * @var string
   */
  public $metric;
  /**
   * Optional. Set of metric labels for this metric.
   *
   * @var string[]
   */
  public $metricLabels;
  protected $valueGauge64Type = DataflowGaugeValue::class;
  protected $valueGauge64DataType = '';
  protected $valueHistogramType = DataflowHistogramValue::class;
  protected $valueHistogramDataType = '';
  /**
   * Integer value of this metric.
   *
   * @var string
   */
  public $valueInt64;

  /**
   * Base name for this metric.
   *
   * @param string $metric
   */
  public function setMetric($metric)
  {
    $this->metric = $metric;
  }
  /**
   * @return string
   */
  public function getMetric()
  {
    return $this->metric;
  }
  /**
   * Optional. Set of metric labels for this metric.
   *
   * @param string[] $metricLabels
   */
  public function setMetricLabels($metricLabels)
  {
    $this->metricLabels = $metricLabels;
  }
  /**
   * @return string[]
   */
  public function getMetricLabels()
  {
    return $this->metricLabels;
  }
  /**
   * Non-cumulative int64 value of this metric.
   *
   * @param DataflowGaugeValue $valueGauge64
   */
  public function setValueGauge64(DataflowGaugeValue $valueGauge64)
  {
    $this->valueGauge64 = $valueGauge64;
  }
  /**
   * @return DataflowGaugeValue
   */
  public function getValueGauge64()
  {
    return $this->valueGauge64;
  }
  /**
   * Histogram value of this metric.
   *
   * @param DataflowHistogramValue $valueHistogram
   */
  public function setValueHistogram(DataflowHistogramValue $valueHistogram)
  {
    $this->valueHistogram = $valueHistogram;
  }
  /**
   * @return DataflowHistogramValue
   */
  public function getValueHistogram()
  {
    return $this->valueHistogram;
  }
  /**
   * Integer value of this metric.
   *
   * @param string $valueInt64
   */
  public function setValueInt64($valueInt64)
  {
    $this->valueInt64 = $valueInt64;
  }
  /**
   * @return string
   */
  public function getValueInt64()
  {
    return $this->valueInt64;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MetricValue::class, 'Google_Service_Dataflow_MetricValue');
