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

class PerWorkerMetrics extends \Google\Collection
{
  protected $collection_key = 'perStepNamespaceMetrics';
  protected $perStepNamespaceMetricsType = PerStepNamespaceMetrics::class;
  protected $perStepNamespaceMetricsDataType = 'array';

  /**
   * Optional. Metrics for a particular unfused step and namespace.
   *
   * @param PerStepNamespaceMetrics[] $perStepNamespaceMetrics
   */
  public function setPerStepNamespaceMetrics($perStepNamespaceMetrics)
  {
    $this->perStepNamespaceMetrics = $perStepNamespaceMetrics;
  }
  /**
   * @return PerStepNamespaceMetrics[]
   */
  public function getPerStepNamespaceMetrics()
  {
    return $this->perStepNamespaceMetrics;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PerWorkerMetrics::class, 'Google_Service_Dataflow_PerWorkerMetrics');
