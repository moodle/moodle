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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1AnalyticsConfig extends \Google\Model
{
  /**
   * Default value.
   */
  public const STATE_ADDON_STATE_UNSPECIFIED = 'ADDON_STATE_UNSPECIFIED';
  /**
   * Add-on is in progress of enabling.
   */
  public const STATE_ENABLING = 'ENABLING';
  /**
   * Add-on is fully enabled and ready to use.
   */
  public const STATE_ENABLED = 'ENABLED';
  /**
   * Add-on is in progress of disabling.
   */
  public const STATE_DISABLING = 'DISABLING';
  /**
   * Add-on is fully disabled.
   */
  public const STATE_DISABLED = 'DISABLED';
  /**
   * Whether the Analytics add-on is enabled.
   *
   * @var bool
   */
  public $enabled;
  /**
   * Output only. Time at which the Analytics add-on expires in milliseconds
   * since epoch. If unspecified, the add-on will never expire.
   *
   * @var string
   */
  public $expireTimeMillis;
  /**
   * Output only. The state of the Analytics add-on.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The latest update time.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Whether the Analytics add-on is enabled.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * Output only. Time at which the Analytics add-on expires in milliseconds
   * since epoch. If unspecified, the add-on will never expire.
   *
   * @param string $expireTimeMillis
   */
  public function setExpireTimeMillis($expireTimeMillis)
  {
    $this->expireTimeMillis = $expireTimeMillis;
  }
  /**
   * @return string
   */
  public function getExpireTimeMillis()
  {
    return $this->expireTimeMillis;
  }
  /**
   * Output only. The state of the Analytics add-on.
   *
   * Accepted values: ADDON_STATE_UNSPECIFIED, ENABLING, ENABLED, DISABLING,
   * DISABLED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. The latest update time.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1AnalyticsConfig::class, 'Google_Service_Apigee_GoogleCloudApigeeV1AnalyticsConfig');
