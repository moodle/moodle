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

class ClusterUpgradeGKEUpgradeFeatureState extends \Google\Collection
{
  protected $collection_key = 'upgradeState';
  protected $conditionsType = ClusterUpgradeGKEUpgradeFeatureCondition::class;
  protected $conditionsDataType = 'array';
  protected $upgradeStateType = ClusterUpgradeGKEUpgradeState::class;
  protected $upgradeStateDataType = 'array';

  /**
   * @param ClusterUpgradeGKEUpgradeFeatureCondition[]
   */
  public function setConditions($conditions)
  {
    $this->conditions = $conditions;
  }
  /**
   * @return ClusterUpgradeGKEUpgradeFeatureCondition[]
   */
  public function getConditions()
  {
    return $this->conditions;
  }
  /**
   * @param ClusterUpgradeGKEUpgradeState[]
   */
  public function setUpgradeState($upgradeState)
  {
    $this->upgradeState = $upgradeState;
  }
  /**
   * @return ClusterUpgradeGKEUpgradeState[]
   */
  public function getUpgradeState()
  {
    return $this->upgradeState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ClusterUpgradeGKEUpgradeFeatureState::class, 'Google_Service_GKEHub_ClusterUpgradeGKEUpgradeFeatureState');
