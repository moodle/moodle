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

namespace Google\Service\SecurityCommandCenter;

class GoogleCloudSecuritycenterV2StaticMute extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const STATE_MUTE_UNSPECIFIED = 'MUTE_UNSPECIFIED';
  /**
   * Finding has been muted.
   */
  public const STATE_MUTED = 'MUTED';
  /**
   * Finding has been unmuted.
   */
  public const STATE_UNMUTED = 'UNMUTED';
  /**
   * Finding has never been muted/unmuted.
   */
  public const STATE_UNDEFINED = 'UNDEFINED';
  /**
   * When the static mute was applied.
   *
   * @var string
   */
  public $applyTime;
  /**
   * The static mute state. If the value is `MUTED` or `UNMUTED`, then the
   * finding's overall mute state will have the same value.
   *
   * @var string
   */
  public $state;

  /**
   * When the static mute was applied.
   *
   * @param string $applyTime
   */
  public function setApplyTime($applyTime)
  {
    $this->applyTime = $applyTime;
  }
  /**
   * @return string
   */
  public function getApplyTime()
  {
    return $this->applyTime;
  }
  /**
   * The static mute state. If the value is `MUTED` or `UNMUTED`, then the
   * finding's overall mute state will have the same value.
   *
   * Accepted values: MUTE_UNSPECIFIED, MUTED, UNMUTED, UNDEFINED
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV2StaticMute::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV2StaticMute');
