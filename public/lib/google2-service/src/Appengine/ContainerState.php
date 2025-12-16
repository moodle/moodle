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

namespace Google\Service\Appengine;

class ContainerState extends \Google\Model
{
  /**
   * A container should never be in an unknown state. Receipt of a container
   * with this state is an error.
   */
  public const STATE_UNKNOWN_STATE = 'UNKNOWN_STATE';
  /**
   * CCFE considers the container to be serving or transitioning into serving.
   */
  public const STATE_ON = 'ON';
  /**
   * CCFE considers the container to be in an OFF state. This could occur due to
   * various factors. The state could be triggered by Google-internal audits
   * (ex. abuse suspension, billing closed) or cleanups trigged by compliance
   * systems (ex. data governance hide). User-initiated events such as service
   * management deactivation trigger a container to an OFF state.CLHs might
   * choose to do nothing in this case or to turn off costly resources. CLHs
   * need to consider the customer experience if an ON/OFF/ON sequence of state
   * transitions occurs vs. the cost of deleting resources, keeping metadata
   * about resources, or even keeping resources live for a period of time.CCFE
   * will not send any new customer requests to the CLH when the container is in
   * an OFF state. However, CCFE will allow all previous customer requests
   * relayed to CLH to complete.
   */
  public const STATE_OFF = 'OFF';
  /**
   * This state indicates that the container has been (or is being) completely
   * removed. This is often due to a data governance purge request and therefore
   * resources should be deleted when this state is reached.
   */
  public const STATE_DELETED = 'DELETED';
  protected $currentReasonsType = Reasons::class;
  protected $currentReasonsDataType = '';
  protected $previousReasonsType = Reasons::class;
  protected $previousReasonsDataType = '';
  /**
   * The current state of the container. This state is the culmination of all of
   * the opinions from external systems that CCFE knows about of the container.
   *
   * @var string
   */
  public $state;

  /**
   * @param Reasons $currentReasons
   */
  public function setCurrentReasons(Reasons $currentReasons)
  {
    $this->currentReasons = $currentReasons;
  }
  /**
   * @return Reasons
   */
  public function getCurrentReasons()
  {
    return $this->currentReasons;
  }
  /**
   * The previous and current reasons for a container state will be sent for a
   * container event. CLHs that need to know the signal that caused the
   * container event to trigger (edges) as opposed to just knowing the state can
   * act upon differences in the previous and current reasons.Reasons will be
   * provided for every system: service management, data governance, abuse, and
   * billing.If this is a CCFE-triggered event used for reconciliation then the
   * current reasons will be set to their *_CONTROL_PLANE_SYNC state. The
   * previous reasons will contain the last known set of non-unknown non-
   * control_plane_sync reasons for the state.
   *
   * @param Reasons $previousReasons
   */
  public function setPreviousReasons(Reasons $previousReasons)
  {
    $this->previousReasons = $previousReasons;
  }
  /**
   * @return Reasons
   */
  public function getPreviousReasons()
  {
    return $this->previousReasons;
  }
  /**
   * The current state of the container. This state is the culmination of all of
   * the opinions from external systems that CCFE knows about of the container.
   *
   * Accepted values: UNKNOWN_STATE, ON, OFF, DELETED
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
class_alias(ContainerState::class, 'Google_Service_Appengine_ContainerState');
