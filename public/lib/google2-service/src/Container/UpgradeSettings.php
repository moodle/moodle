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

class UpgradeSettings extends \Google\Model
{
  /**
   * Default value if unset. GKE internally defaults the update strategy to
   * SURGE for unspecified strategies.
   */
  public const STRATEGY_NODE_POOL_UPDATE_STRATEGY_UNSPECIFIED = 'NODE_POOL_UPDATE_STRATEGY_UNSPECIFIED';
  /**
   * blue-green upgrade.
   */
  public const STRATEGY_BLUE_GREEN = 'BLUE_GREEN';
  /**
   * SURGE is the traditional way of upgrade a node pool. max_surge and
   * max_unavailable determines the level of upgrade parallelism.
   */
  public const STRATEGY_SURGE = 'SURGE';
  protected $blueGreenSettingsType = BlueGreenSettings::class;
  protected $blueGreenSettingsDataType = '';
  /**
   * The maximum number of nodes that can be created beyond the current size of
   * the node pool during the upgrade process.
   *
   * @var int
   */
  public $maxSurge;
  /**
   * The maximum number of nodes that can be simultaneously unavailable during
   * the upgrade process. A node is considered available if its status is Ready.
   *
   * @var int
   */
  public $maxUnavailable;
  /**
   * Update strategy of the node pool.
   *
   * @var string
   */
  public $strategy;

  /**
   * Settings for blue-green upgrade strategy.
   *
   * @param BlueGreenSettings $blueGreenSettings
   */
  public function setBlueGreenSettings(BlueGreenSettings $blueGreenSettings)
  {
    $this->blueGreenSettings = $blueGreenSettings;
  }
  /**
   * @return BlueGreenSettings
   */
  public function getBlueGreenSettings()
  {
    return $this->blueGreenSettings;
  }
  /**
   * The maximum number of nodes that can be created beyond the current size of
   * the node pool during the upgrade process.
   *
   * @param int $maxSurge
   */
  public function setMaxSurge($maxSurge)
  {
    $this->maxSurge = $maxSurge;
  }
  /**
   * @return int
   */
  public function getMaxSurge()
  {
    return $this->maxSurge;
  }
  /**
   * The maximum number of nodes that can be simultaneously unavailable during
   * the upgrade process. A node is considered available if its status is Ready.
   *
   * @param int $maxUnavailable
   */
  public function setMaxUnavailable($maxUnavailable)
  {
    $this->maxUnavailable = $maxUnavailable;
  }
  /**
   * @return int
   */
  public function getMaxUnavailable()
  {
    return $this->maxUnavailable;
  }
  /**
   * Update strategy of the node pool.
   *
   * Accepted values: NODE_POOL_UPDATE_STRATEGY_UNSPECIFIED, BLUE_GREEN, SURGE
   *
   * @param self::STRATEGY_* $strategy
   */
  public function setStrategy($strategy)
  {
    $this->strategy = $strategy;
  }
  /**
   * @return self::STRATEGY_*
   */
  public function getStrategy()
  {
    return $this->strategy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpgradeSettings::class, 'Google_Service_Container_UpgradeSettings');
