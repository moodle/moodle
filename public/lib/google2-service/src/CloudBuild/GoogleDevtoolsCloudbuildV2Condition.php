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

namespace Google\Service\CloudBuild;

class GoogleDevtoolsCloudbuildV2Condition extends \Google\Model
{
  /**
   * Default enum type; should not be used.
   */
  public const SEVERITY_SEVERITY_UNSPECIFIED = 'SEVERITY_UNSPECIFIED';
  /**
   * Severity is warning.
   */
  public const SEVERITY_WARNING = 'WARNING';
  /**
   * Severity is informational only.
   */
  public const SEVERITY_INFO = 'INFO';
  /**
   * Default enum type indicating execution is still ongoing.
   */
  public const STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * Success
   */
  public const STATUS_TRUE = 'TRUE';
  /**
   * Failure
   */
  public const STATUS_FALSE = 'FALSE';
  /**
   * LastTransitionTime is the last time the condition transitioned from one
   * status to another.
   *
   * @var string
   */
  public $lastTransitionTime;
  /**
   * A human readable message indicating details about the transition.
   *
   * @var string
   */
  public $message;
  /**
   * The reason for the condition's last transition.
   *
   * @var string
   */
  public $reason;
  /**
   * Severity with which to treat failures of this type of condition.
   *
   * @var string
   */
  public $severity;
  /**
   * Status of the condition.
   *
   * @var string
   */
  public $status;
  /**
   * Type of condition.
   *
   * @var string
   */
  public $type;

  /**
   * LastTransitionTime is the last time the condition transitioned from one
   * status to another.
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
   * A human readable message indicating details about the transition.
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
   * The reason for the condition's last transition.
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
   * Severity with which to treat failures of this type of condition.
   *
   * Accepted values: SEVERITY_UNSPECIFIED, WARNING, INFO
   *
   * @param self::SEVERITY_* $severity
   */
  public function setSeverity($severity)
  {
    $this->severity = $severity;
  }
  /**
   * @return self::SEVERITY_*
   */
  public function getSeverity()
  {
    return $this->severity;
  }
  /**
   * Status of the condition.
   *
   * Accepted values: UNKNOWN, TRUE, FALSE
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Type of condition.
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
class_alias(GoogleDevtoolsCloudbuildV2Condition::class, 'Google_Service_CloudBuild_GoogleDevtoolsCloudbuildV2Condition');
