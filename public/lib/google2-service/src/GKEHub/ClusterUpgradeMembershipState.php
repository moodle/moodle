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

class ClusterUpgradeMembershipState extends \Google\Collection
{
  protected $collection_key = 'upgrades';
  protected $ignoredType = ClusterUpgradeIgnoredMembership::class;
  protected $ignoredDataType = '';
  protected $upgradesType = ClusterUpgradeMembershipGKEUpgradeState::class;
  protected $upgradesDataType = 'array';

  /**
   * @param ClusterUpgradeIgnoredMembership
   */
  public function setIgnored(ClusterUpgradeIgnoredMembership $ignored)
  {
    $this->ignored = $ignored;
  }
  /**
   * @return ClusterUpgradeIgnoredMembership
   */
  public function getIgnored()
  {
    return $this->ignored;
  }
  /**
   * @param ClusterUpgradeMembershipGKEUpgradeState[]
   */
  public function setUpgrades($upgrades)
  {
    $this->upgrades = $upgrades;
  }
  /**
   * @return ClusterUpgradeMembershipGKEUpgradeState[]
   */
  public function getUpgrades()
  {
    return $this->upgrades;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ClusterUpgradeMembershipState::class, 'Google_Service_GKEHub_ClusterUpgradeMembershipState');
