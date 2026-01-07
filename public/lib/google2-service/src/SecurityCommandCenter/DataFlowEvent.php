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

class DataFlowEvent extends \Google\Model
{
  /**
   * The operation is unspecified.
   */
  public const OPERATION_OPERATION_UNSPECIFIED = 'OPERATION_UNSPECIFIED';
  /**
   * Represents a read operation.
   */
  public const OPERATION_READ = 'READ';
  /**
   * Represents a move operation.
   */
  public const OPERATION_MOVE = 'MOVE';
  /**
   * Represents a copy operation.
   */
  public const OPERATION_COPY = 'COPY';
  /**
   * Unique identifier for data flow event.
   *
   * @var string
   */
  public $eventId;
  /**
   * Timestamp of data flow event.
   *
   * @var string
   */
  public $eventTime;
  /**
   * The operation performed by the principal for the data flow event.
   *
   * @var string
   */
  public $operation;
  /**
   * The email address of the principal that initiated the data flow event. The
   * principal could be a user account, service account, Google group, or other.
   *
   * @var string
   */
  public $principalEmail;
  /**
   * Non-compliant location of the principal or the data destination.
   *
   * @var string
   */
  public $violatedLocation;

  /**
   * Unique identifier for data flow event.
   *
   * @param string $eventId
   */
  public function setEventId($eventId)
  {
    $this->eventId = $eventId;
  }
  /**
   * @return string
   */
  public function getEventId()
  {
    return $this->eventId;
  }
  /**
   * Timestamp of data flow event.
   *
   * @param string $eventTime
   */
  public function setEventTime($eventTime)
  {
    $this->eventTime = $eventTime;
  }
  /**
   * @return string
   */
  public function getEventTime()
  {
    return $this->eventTime;
  }
  /**
   * The operation performed by the principal for the data flow event.
   *
   * Accepted values: OPERATION_UNSPECIFIED, READ, MOVE, COPY
   *
   * @param self::OPERATION_* $operation
   */
  public function setOperation($operation)
  {
    $this->operation = $operation;
  }
  /**
   * @return self::OPERATION_*
   */
  public function getOperation()
  {
    return $this->operation;
  }
  /**
   * The email address of the principal that initiated the data flow event. The
   * principal could be a user account, service account, Google group, or other.
   *
   * @param string $principalEmail
   */
  public function setPrincipalEmail($principalEmail)
  {
    $this->principalEmail = $principalEmail;
  }
  /**
   * @return string
   */
  public function getPrincipalEmail()
  {
    return $this->principalEmail;
  }
  /**
   * Non-compliant location of the principal or the data destination.
   *
   * @param string $violatedLocation
   */
  public function setViolatedLocation($violatedLocation)
  {
    $this->violatedLocation = $violatedLocation;
  }
  /**
   * @return string
   */
  public function getViolatedLocation()
  {
    return $this->violatedLocation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataFlowEvent::class, 'Google_Service_SecurityCommandCenter_DataFlowEvent');
