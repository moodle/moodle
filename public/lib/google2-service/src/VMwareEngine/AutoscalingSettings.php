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

namespace Google\Service\VMwareEngine;

class AutoscalingSettings extends \Google\Model
{
  protected $autoscalingPoliciesType = AutoscalingPolicy::class;
  protected $autoscalingPoliciesDataType = 'map';
  /**
   * Optional. The minimum duration between consecutive autoscale operations. It
   * starts once addition or removal of nodes is fully completed. Defaults to 30
   * minutes if not specified. Cool down period must be in whole minutes (for
   * example, 30, 31, 50, 180 minutes).
   *
   * @var string
   */
  public $coolDownPeriod;
  /**
   * Optional. Maximum number of nodes of any type in a cluster. If not
   * specified the default limits apply.
   *
   * @var int
   */
  public $maxClusterNodeCount;
  /**
   * Optional. Minimum number of nodes of any type in a cluster. If not
   * specified the default limits apply.
   *
   * @var int
   */
  public $minClusterNodeCount;

  /**
   * Required. The map with autoscaling policies applied to the cluster. The key
   * is the identifier of the policy. It must meet the following requirements: *
   * Only contains 1-63 alphanumeric characters and hyphens * Begins with an
   * alphabetical character * Ends with a non-hyphen character * Not formatted
   * as a UUID * Complies with [RFC
   * 1034](https://datatracker.ietf.org/doc/html/rfc1034) (section 3.5)
   * Currently there map must contain only one element that describes the
   * autoscaling policy for compute nodes.
   *
   * @param AutoscalingPolicy[] $autoscalingPolicies
   */
  public function setAutoscalingPolicies($autoscalingPolicies)
  {
    $this->autoscalingPolicies = $autoscalingPolicies;
  }
  /**
   * @return AutoscalingPolicy[]
   */
  public function getAutoscalingPolicies()
  {
    return $this->autoscalingPolicies;
  }
  /**
   * Optional. The minimum duration between consecutive autoscale operations. It
   * starts once addition or removal of nodes is fully completed. Defaults to 30
   * minutes if not specified. Cool down period must be in whole minutes (for
   * example, 30, 31, 50, 180 minutes).
   *
   * @param string $coolDownPeriod
   */
  public function setCoolDownPeriod($coolDownPeriod)
  {
    $this->coolDownPeriod = $coolDownPeriod;
  }
  /**
   * @return string
   */
  public function getCoolDownPeriod()
  {
    return $this->coolDownPeriod;
  }
  /**
   * Optional. Maximum number of nodes of any type in a cluster. If not
   * specified the default limits apply.
   *
   * @param int $maxClusterNodeCount
   */
  public function setMaxClusterNodeCount($maxClusterNodeCount)
  {
    $this->maxClusterNodeCount = $maxClusterNodeCount;
  }
  /**
   * @return int
   */
  public function getMaxClusterNodeCount()
  {
    return $this->maxClusterNodeCount;
  }
  /**
   * Optional. Minimum number of nodes of any type in a cluster. If not
   * specified the default limits apply.
   *
   * @param int $minClusterNodeCount
   */
  public function setMinClusterNodeCount($minClusterNodeCount)
  {
    $this->minClusterNodeCount = $minClusterNodeCount;
  }
  /**
   * @return int
   */
  public function getMinClusterNodeCount()
  {
    return $this->minClusterNodeCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutoscalingSettings::class, 'Google_Service_VMwareEngine_AutoscalingSettings');
