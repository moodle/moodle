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

namespace Google\Service\StorageBatchOperations;

class ObjectRetention extends \Google\Model
{
  /**
   * If set and retain_until_time is empty, clears the retention.
   */
  public const RETENTION_MODE_RETENTION_MODE_UNSPECIFIED = 'RETENTION_MODE_UNSPECIFIED';
  /**
   * Sets the retention mode to locked.
   */
  public const RETENTION_MODE_LOCKED = 'LOCKED';
  /**
   * Sets the retention mode to unlocked.
   */
  public const RETENTION_MODE_UNLOCKED = 'UNLOCKED';
  /**
   * Required. The time when the object will be retained until. UNSET will clear
   * the retention. Must be specified in RFC 3339 format e.g. YYYY-MM-
   * DD'T'HH:MM:SS.SS'Z' or YYYY-MM-DD'T'HH:MM:SS'Z'.
   *
   * @var string
   */
  public $retainUntilTime;
  /**
   * Required. The retention mode of the object.
   *
   * @var string
   */
  public $retentionMode;

  /**
   * Required. The time when the object will be retained until. UNSET will clear
   * the retention. Must be specified in RFC 3339 format e.g. YYYY-MM-
   * DD'T'HH:MM:SS.SS'Z' or YYYY-MM-DD'T'HH:MM:SS'Z'.
   *
   * @param string $retainUntilTime
   */
  public function setRetainUntilTime($retainUntilTime)
  {
    $this->retainUntilTime = $retainUntilTime;
  }
  /**
   * @return string
   */
  public function getRetainUntilTime()
  {
    return $this->retainUntilTime;
  }
  /**
   * Required. The retention mode of the object.
   *
   * Accepted values: RETENTION_MODE_UNSPECIFIED, LOCKED, UNLOCKED
   *
   * @param self::RETENTION_MODE_* $retentionMode
   */
  public function setRetentionMode($retentionMode)
  {
    $this->retentionMode = $retentionMode;
  }
  /**
   * @return self::RETENTION_MODE_*
   */
  public function getRetentionMode()
  {
    return $this->retentionMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ObjectRetention::class, 'Google_Service_StorageBatchOperations_ObjectRetention');
