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

namespace Google\Service\GKEOnPrem;

class ResourceCondition extends \Google\Model
{
  /**
   * Not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Resource is in the condition.
   */
  public const STATE_STATE_TRUE = 'STATE_TRUE';
  /**
   * Resource is not in the condition.
   */
  public const STATE_STATE_FALSE = 'STATE_FALSE';
  /**
   * Kubernetes controller can't decide if the resource is in the condition or
   * not.
   */
  public const STATE_STATE_UNKNOWN = 'STATE_UNKNOWN';
  /**
   * Last time the condition transit from one status to another.
   *
   * @var string
   */
  public $lastTransitionTime;
  /**
   * Human-readable message indicating details about last transition.
   *
   * @var string
   */
  public $message;
  /**
   * Machine-readable message indicating details about last transition.
   *
   * @var string
   */
  public $reason;
  /**
   * state of the condition.
   *
   * @var string
   */
  public $state;
  /**
   * Type of the condition. (e.g., ClusterRunning, NodePoolRunning or
   * ServerSidePreflightReady)
   *
   * @var string
   */
  public $type;

  /**
   * Last time the condition transit from one status to another.
   *
   * @param string $lastTransitionTime
   */
  public function setLastTransitionTime($lastTransitionTime)
  {
    $this->lastTransitionTime = $lastTransitionTime;
  }
  /**
   * @return string
   */
  public function getLastTransitionTime()
  {
    return $this->lastTransitionTime;
  }
  /**
   * Human-readable message indicating details about last transition.
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * Machine-readable message indicating details about last transition.
   *
   * @param string $reason
   */
  public function setReason($reason)
  {
    $this->reason = $reason;
  }
  /**
   * @return string
   */
  public function getReason()
  {
    return $this->reason;
  }
  /**
   * state of the condition.
   *
   * Accepted values: STATE_UNSPECIFIED, STATE_TRUE, STATE_FALSE, STATE_UNKNOWN
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
   * Type of the condition. (e.g., ClusterRunning, NodePoolRunning or
   * ServerSidePreflightReady)
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResourceCondition::class, 'Google_Service_GKEOnPrem_ResourceCondition');
