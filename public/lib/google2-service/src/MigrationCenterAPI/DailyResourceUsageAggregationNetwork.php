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

namespace Google\Service\MigrationCenterAPI;

class DailyResourceUsageAggregationNetwork extends \Google\Model
{
  protected $egressBpsType = DailyResourceUsageAggregationStats::class;
  protected $egressBpsDataType = '';
  protected $ingressBpsType = DailyResourceUsageAggregationStats::class;
  protected $ingressBpsDataType = '';

  /**
   * Network egress in B/s.
   *
   * @param DailyResourceUsageAggregationStats $egressBps
   */
  public function setEgressBps(DailyResourceUsageAggregationStats $egressBps)
  {
    $this->egressBps = $egressBps;
  }
  /**
   * @return DailyResourceUsageAggregationStats
   */
  public function getEgressBps()
  {
    return $this->egressBps;
  }
  /**
   * Network ingress in B/s.
   *
   * @param DailyResourceUsageAggregationStats $ingressBps
   */
  public function setIngressBps(DailyResourceUsageAggregationStats $ingressBps)
  {
    $this->ingressBps = $ingressBps;
  }
  /**
   * @return DailyResourceUsageAggregationStats
   */
  public function getIngressBps()
  {
    return $this->ingressBps;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DailyResourceUsageAggregationNetwork::class, 'Google_Service_MigrationCenterAPI_DailyResourceUsageAggregationNetwork');
