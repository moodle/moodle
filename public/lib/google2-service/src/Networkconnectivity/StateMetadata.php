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

namespace Google\Service\Networkconnectivity;

class StateMetadata extends \Google\Model
{
  /**
   * An invalid state, which is the default case.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The resource is being added.
   */
  public const STATE_ADDING = 'ADDING';
  /**
   * The resource is in use.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The resource is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The resource is being suspended.
   */
  public const STATE_SUSPENDING = 'SUSPENDING';
  /**
   * The resource is suspended and not in use.
   */
  public const STATE_SUSPENDED = 'SUSPENDED';
  /**
   * Output only. Accompanies only the transient states, which include `ADDING`,
   * `DELETING`, and `SUSPENDING`, to denote the time until which the transient
   * state of the resource will be effective. For instance, if the state is
   * `ADDING`, this field shows the time when the resource state transitions to
   * `ACTIVE`.
   *
   * @var string
   */
  public $effectiveTime;
  /**
   * Output only. The state of the resource.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. Accompanies only the transient states, which include `ADDING`,
   * `DELETING`, and `SUSPENDING`, to denote the time until which the transient
   * state of the resource will be effective. For instance, if the state is
   * `ADDING`, this field shows the time when the resource state transitions to
   * `ACTIVE`.
   *
   * @param string $effectiveTime
   */
  public function setEffectiveTime($effectiveTime)
  {
    $this->effectiveTime = $effectiveTime;
  }
  /**
   * @return string
   */
  public function getEffectiveTime()
  {
    return $this->effectiveTime;
  }
  /**
   * Output only. The state of the resource.
   *
   * Accepted values: STATE_UNSPECIFIED, ADDING, ACTIVE, DELETING, SUSPENDING,
   * SUSPENDED
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
class_alias(StateMetadata::class, 'Google_Service_Networkconnectivity_StateMetadata');
