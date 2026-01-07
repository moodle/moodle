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

namespace Google\Service\GKEOnPrem;

class Metric extends \Google\Model
{
  /**
   * Not set.
   */
  public const METRIC_METRIC_ID_UNSPECIFIED = 'METRIC_ID_UNSPECIFIED';
  /**
   * The total number of nodes being actuated.
   */
  public const METRIC_NODES_TOTAL = 'NODES_TOTAL';
  /**
   * The number of nodes draining.
   */
  public const METRIC_NODES_DRAINING = 'NODES_DRAINING';
  /**
   * The number of nodes actively upgrading.
   */
  public const METRIC_NODES_UPGRADING = 'NODES_UPGRADING';
  /**
   * The number of nodes to be upgraded.
   */
  public const METRIC_NODES_PENDING_UPGRADE = 'NODES_PENDING_UPGRADE';
  /**
   * The number of nodes upgraded.
   */
  public const METRIC_NODES_UPGRADED = 'NODES_UPGRADED';
  /**
   * The number of nodes to fail actuation.
   */
  public const METRIC_NODES_FAILED = 'NODES_FAILED';
  /**
   * The number of nodes healthy.
   */
  public const METRIC_NODES_HEALTHY = 'NODES_HEALTHY';
  /**
   * The number of nodes reconciling.
   */
  public const METRIC_NODES_RECONCILING = 'NODES_RECONCILING';
  /**
   * The number of nodes in maintenance mode.
   */
  public const METRIC_NODES_IN_MAINTENANCE = 'NODES_IN_MAINTENANCE';
  /**
   * The number of completed preflight checks.
   */
  public const METRIC_PREFLIGHTS_COMPLETED = 'PREFLIGHTS_COMPLETED';
  /**
   * The number of preflight checks running.
   */
  public const METRIC_PREFLIGHTS_RUNNING = 'PREFLIGHTS_RUNNING';
  /**
   * The number of preflight checks failed.
   */
  public const METRIC_PREFLIGHTS_FAILED = 'PREFLIGHTS_FAILED';
  /**
   * The total number of preflight checks.
   */
  public const METRIC_PREFLIGHTS_TOTAL = 'PREFLIGHTS_TOTAL';
  /**
   * For metrics with floating point value.
   *
   * @var 
   */
  public $doubleValue;
  /**
   * For metrics with integer value.
   *
   * @var string
   */
  public $intValue;
  /**
   * Required. The metric name.
   *
   * @var string
   */
  public $metric;
  /**
   * For metrics with custom values (ratios, visual progress, etc.).
   *
   * @var string
   */
  public $stringValue;

  public function setDoubleValue($doubleValue)
  {
    $this->doubleValue = $doubleValue;
  }
  public function getDoubleValue()
  {
    return $this->doubleValue;
  }
  /**
   * For metrics with integer value.
   *
   * @param string $intValue
   */
  public function setIntValue($intValue)
  {
    $this->intValue = $intValue;
  }
  /**
   * @return string
   */
  public function getIntValue()
  {
    return $this->intValue;
  }
  /**
   * Required. The metric name.
   *
   * Accepted values: METRIC_ID_UNSPECIFIED, NODES_TOTAL, NODES_DRAINING,
   * NODES_UPGRADING, NODES_PENDING_UPGRADE, NODES_UPGRADED, NODES_FAILED,
   * NODES_HEALTHY, NODES_RECONCILING, NODES_IN_MAINTENANCE,
   * PREFLIGHTS_COMPLETED, PREFLIGHTS_RUNNING, PREFLIGHTS_FAILED,
   * PREFLIGHTS_TOTAL
   *
   * @param self::METRIC_* $metric
   */
  public function setMetric($metric)
  {
    $this->metric = $metric;
  }
  /**
   * @return self::METRIC_*
   */
  public function getMetric()
  {
    return $this->metric;
  }
  /**
   * For metrics with custom values (ratios, visual progress, etc.).
   *
   * @param string $stringValue
   */
  public function setStringValue($stringValue)
  {
    $this->stringValue = $stringValue;
  }
  /**
   * @return string
   */
  public function getStringValue()
  {
    return $this->stringValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Metric::class, 'Google_Service_GKEOnPrem_Metric');
