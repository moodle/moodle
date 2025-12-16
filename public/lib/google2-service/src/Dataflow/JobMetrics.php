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

class JobMetrics extends \Google\Collection
{
  protected $collection_key = 'metrics';
  /**
   * Timestamp as of which metric values are current.
   *
   * @var string
   */
  public $metricTime;
  protected $metricsType = MetricUpdate::class;
  protected $metricsDataType = 'array';

  /**
   * Timestamp as of which metric values are current.
   *
   * @param string $metricTime
   */
  public function setMetricTime($metricTime)
  {
    $this->metricTime = $metricTime;
  }
  /**
   * @return string
   */
  public function getMetricTime()
  {
    return $this->metricTime;
  }
  /**
   * All metrics for this job.
   *
   * @param MetricUpdate[] $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return MetricUpdate[]
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(JobMetrics::class, 'Google_Service_Dataflow_JobMetrics');
