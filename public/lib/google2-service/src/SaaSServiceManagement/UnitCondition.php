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

namespace Google\Service\SaaSServiceManagement;

class UnitCondition extends \Google\Model
{
  /**
   * Condition status is unspecified.
   */
  public const STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * Condition is unknown.
   */
  public const STATUS_STATUS_UNKNOWN = 'STATUS_UNKNOWN';
  /**
   * Condition is true.
   */
  public const STATUS_STATUS_TRUE = 'STATUS_TRUE';
  /**
   * Condition is false.
   */
  public const STATUS_STATUS_FALSE = 'STATUS_FALSE';
  /**
   * Condition type is unspecified.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Condition type is ready.
   */
  public const TYPE_TYPE_READY = 'TYPE_READY';
  /**
   * Condition type is updating.
   */
  public const TYPE_TYPE_UPDATING = 'TYPE_UPDATING';
  /**
   * Condition type is provisioned.
   */
  public const TYPE_TYPE_PROVISIONED = 'TYPE_PROVISIONED';
  /**
   * Condition type is operationError. True when the last unit operation fails
   * with a non-ignorable error.
   */
  public const TYPE_TYPE_OPERATION_ERROR = 'TYPE_OPERATION_ERROR';
  /**
   * Required. Last time the condition transited from one status to another.
   *
   * @var string
   */
  public $lastTransitionTime;
  /**
   * Required. Human readable message indicating details about the last
   * transition.
   *
   * @var string
   */
  public $message;
  /**
   * Required. Brief reason for the condition's last transition.
   *
   * @var string
   */
  public $reason;
  /**
   * Required. Status of the condition.
   *
   * @var string
   */
  public $status;
  /**
   * Required. Type of the condition.
   *
   * @var string
   */
  public $type;

  /**
   * Required. Last time the condition transited from one status to another.
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
   * Required. Human readable message indicating details about the last
   * transition.
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
   * Required. Brief reason for the condition's last transition.
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
   * Required. Status of the condition.
   *
   * Accepted values: STATUS_UNSPECIFIED, STATUS_UNKNOWN, STATUS_TRUE,
   * STATUS_FALSE
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
   * Required. Type of the condition.
   *
   * Accepted values: TYPE_UNSPECIFIED, TYPE_READY, TYPE_UPDATING,
   * TYPE_PROVISIONED, TYPE_OPERATION_ERROR
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UnitCondition::class, 'Google_Service_SaaSServiceManagement_UnitCondition');
