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

namespace Google\Service\GKEHub;

class ClusterUpgradeGKEUpgradeState extends \Google\Model
{
  /**
   * @var string[]
   */
  public $stats;
  protected $statusType = ClusterUpgradeUpgradeStatus::class;
  protected $statusDataType = '';
  protected $upgradeType = ClusterUpgradeGKEUpgrade::class;
  protected $upgradeDataType = '';

  /**
   * @param string[]
   */
  public function setStats($stats)
  {
    $this->stats = $stats;
  }
  /**
   * @return string[]
   */
  public function getStats()
  {
    return $this->stats;
  }
  /**
   * @param ClusterUpgradeUpgradeStatus
   */
  public function setStatus(ClusterUpgradeUpgradeStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return ClusterUpgradeUpgradeStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * @param ClusterUpgradeGKEUpgrade
   */
  public function setUpgrade(ClusterUpgradeGKEUpgrade $upgrade)
  {
    $this->upgrade = $upgrade;
  }
  /**
   * @return ClusterUpgradeGKEUpgrade
   */
  public function getUpgrade()
  {
    return $this->upgrade;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ClusterUpgradeGKEUpgradeState::class, 'Google_Service_GKEHub_ClusterUpgradeGKEUpgradeState');
