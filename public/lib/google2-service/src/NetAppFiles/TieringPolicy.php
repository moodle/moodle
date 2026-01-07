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

namespace Google\Service\NetAppFiles;

class TieringPolicy extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const TIER_ACTION_TIER_ACTION_UNSPECIFIED = 'TIER_ACTION_UNSPECIFIED';
  /**
   * When tiering is enabled, new cold data will be tiered.
   */
  public const TIER_ACTION_ENABLED = 'ENABLED';
  /**
   * When paused, tiering won't be performed on new data. Existing data stays
   * tiered until accessed.
   */
  public const TIER_ACTION_PAUSED = 'PAUSED';
  /**
   * Optional. Time in days to mark the volume's data block as cold and make it
   * eligible for tiering, can be range from 2-183. Default is 31.
   *
   * @var int
   */
  public $coolingThresholdDays;
  /**
   * Optional. Flag indicating that the hot tier bypass mode is enabled. Default
   * is false. This is only applicable to Flex service level.
   *
   * @var bool
   */
  public $hotTierBypassModeEnabled;
  /**
   * Optional. Flag indicating if the volume has tiering policy enable/pause.
   * Default is PAUSED.
   *
   * @var string
   */
  public $tierAction;

  /**
   * Optional. Time in days to mark the volume's data block as cold and make it
   * eligible for tiering, can be range from 2-183. Default is 31.
   *
   * @param int $coolingThresholdDays
   */
  public function setCoolingThresholdDays($coolingThresholdDays)
  {
    $this->coolingThresholdDays = $coolingThresholdDays;
  }
  /**
   * @return int
   */
  public function getCoolingThresholdDays()
  {
    return $this->coolingThresholdDays;
  }
  /**
   * Optional. Flag indicating that the hot tier bypass mode is enabled. Default
   * is false. This is only applicable to Flex service level.
   *
   * @param bool $hotTierBypassModeEnabled
   */
  public function setHotTierBypassModeEnabled($hotTierBypassModeEnabled)
  {
    $this->hotTierBypassModeEnabled = $hotTierBypassModeEnabled;
  }
  /**
   * @return bool
   */
  public function getHotTierBypassModeEnabled()
  {
    return $this->hotTierBypassModeEnabled;
  }
  /**
   * Optional. Flag indicating if the volume has tiering policy enable/pause.
   * Default is PAUSED.
   *
   * Accepted values: TIER_ACTION_UNSPECIFIED, ENABLED, PAUSED
   *
   * @param self::TIER_ACTION_* $tierAction
   */
  public function setTierAction($tierAction)
  {
    $this->tierAction = $tierAction;
  }
  /**
   * @return self::TIER_ACTION_*
   */
  public function getTierAction()
  {
    return $this->tierAction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TieringPolicy::class, 'Google_Service_NetAppFiles_TieringPolicy');
