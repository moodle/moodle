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

class SetFindingStateRequest extends \Google\Model
{
  /**
   * Unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The finding requires attention and has not been addressed yet.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The finding has been fixed, triaged as a non-issue or otherwise addressed
   * and is no longer active.
   */
  public const STATE_INACTIVE = 'INACTIVE';
  /**
   * Optional. The time at which the updated state takes effect. If unset,
   * defaults to the request time.
   *
   * @deprecated
   * @var string
   */
  public $startTime;
  /**
   * Required. The desired State of the finding.
   *
   * @var string
   */
  public $state;

  /**
   * Optional. The time at which the updated state takes effect. If unset,
   * defaults to the request time.
   *
   * @deprecated
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Required. The desired State of the finding.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, INACTIVE
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
class_alias(SetFindingStateRequest::class, 'Google_Service_SecurityCommandCenter_SetFindingStateRequest');
