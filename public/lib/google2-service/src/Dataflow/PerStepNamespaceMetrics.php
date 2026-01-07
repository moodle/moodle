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

class PerStepNamespaceMetrics extends \Google\Collection
{
  protected $collection_key = 'metricValues';
  protected $metricValuesType = MetricValue::class;
  protected $metricValuesDataType = 'array';
  /**
   * The namespace of these metrics on the worker.
   *
   * @var string
   */
  public $metricsNamespace;
  /**
   * The original system name of the unfused step that these metrics are
   * reported from.
   *
   * @var string
   */
  public $originalStep;

  /**
   * Optional. Metrics that are recorded for this namespace and unfused step.
   *
   * @param MetricValue[] $metricValues
   */
  public function setMetricValues($metricValues)
  {
    $this->metricValues = $metricValues;
  }
  /**
   * @return MetricValue[]
   */
  public function getMetricValues()
  {
    return $this->metricValues;
  }
  /**
   * The namespace of these metrics on the worker.
   *
   * @param string $metricsNamespace
   */
  public function setMetricsNamespace($metricsNamespace)
  {
    $this->metricsNamespace = $metricsNamespace;
  }
  /**
   * @return string
   */
  public function getMetricsNamespace()
  {
    return $this->metricsNamespace;
  }
  /**
   * The original system name of the unfused step that these metrics are
   * reported from.
   *
   * @param string $originalStep
   */
  public function setOriginalStep($originalStep)
  {
    $this->originalStep = $originalStep;
  }
  /**
   * @return string
   */
  public function getOriginalStep()
  {
    return $this->originalStep;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PerStepNamespaceMetrics::class, 'Google_Service_Dataflow_PerStepNamespaceMetrics');
