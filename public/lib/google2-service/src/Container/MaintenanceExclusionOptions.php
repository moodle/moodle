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

namespace Google\Service\Container;

class MaintenanceExclusionOptions extends \Google\Model
{
  /**
   * END_TIME_BEHAVIOR_UNSPECIFIED is the default behavior, which is fixed end
   * time.
   */
  public const END_TIME_BEHAVIOR_END_TIME_BEHAVIOR_UNSPECIFIED = 'END_TIME_BEHAVIOR_UNSPECIFIED';
  /**
   * UNTIL_END_OF_SUPPORT means the exclusion will be in effect until the end of
   * the support of the cluster's current version.
   */
  public const END_TIME_BEHAVIOR_UNTIL_END_OF_SUPPORT = 'UNTIL_END_OF_SUPPORT';
  /**
   * NO_UPGRADES excludes all upgrades, including patch upgrades and minor
   * upgrades across control planes and nodes. This is the default exclusion
   * behavior.
   */
  public const SCOPE_NO_UPGRADES = 'NO_UPGRADES';
  /**
   * NO_MINOR_UPGRADES excludes all minor upgrades for the cluster, only patches
   * are allowed.
   */
  public const SCOPE_NO_MINOR_UPGRADES = 'NO_MINOR_UPGRADES';
  /**
   * NO_MINOR_OR_NODE_UPGRADES excludes all minor upgrades for the cluster, and
   * also exclude all node pool upgrades. Only control plane patches are
   * allowed.
   */
  public const SCOPE_NO_MINOR_OR_NODE_UPGRADES = 'NO_MINOR_OR_NODE_UPGRADES';
  /**
   * EndTimeBehavior specifies the behavior of the exclusion end time.
   *
   * @var string
   */
  public $endTimeBehavior;
  /**
   * Scope specifies the upgrade scope which upgrades are blocked by the
   * exclusion.
   *
   * @var string
   */
  public $scope;

  /**
   * EndTimeBehavior specifies the behavior of the exclusion end time.
   *
   * Accepted values: END_TIME_BEHAVIOR_UNSPECIFIED, UNTIL_END_OF_SUPPORT
   *
   * @param self::END_TIME_BEHAVIOR_* $endTimeBehavior
   */
  public function setEndTimeBehavior($endTimeBehavior)
  {
    $this->endTimeBehavior = $endTimeBehavior;
  }
  /**
   * @return self::END_TIME_BEHAVIOR_*
   */
  public function getEndTimeBehavior()
  {
    return $this->endTimeBehavior;
  }
  /**
   * Scope specifies the upgrade scope which upgrades are blocked by the
   * exclusion.
   *
   * Accepted values: NO_UPGRADES, NO_MINOR_UPGRADES, NO_MINOR_OR_NODE_UPGRADES
   *
   * @param self::SCOPE_* $scope
   */
  public function setScope($scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return self::SCOPE_*
   */
  public function getScope()
  {
    return $this->scope;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MaintenanceExclusionOptions::class, 'Google_Service_Container_MaintenanceExclusionOptions');
