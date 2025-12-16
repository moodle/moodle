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

namespace Google\Service\AndroidManagement;

class MemoryEvent extends \Google\Model
{
  /**
   * Unspecified. No events have this type.
   */
  public const EVENT_TYPE_MEMORY_EVENT_TYPE_UNSPECIFIED = 'MEMORY_EVENT_TYPE_UNSPECIFIED';
  /**
   * Free space in RAM was measured.
   */
  public const EVENT_TYPE_RAM_MEASURED = 'RAM_MEASURED';
  /**
   * Free space in internal storage was measured.
   */
  public const EVENT_TYPE_INTERNAL_STORAGE_MEASURED = 'INTERNAL_STORAGE_MEASURED';
  /**
   * A new external storage medium was detected. The reported byte count is the
   * total capacity of the storage medium.
   */
  public const EVENT_TYPE_EXTERNAL_STORAGE_DETECTED = 'EXTERNAL_STORAGE_DETECTED';
  /**
   * An external storage medium was removed. The reported byte count is zero.
   */
  public const EVENT_TYPE_EXTERNAL_STORAGE_REMOVED = 'EXTERNAL_STORAGE_REMOVED';
  /**
   * Free space in an external storage medium was measured.
   */
  public const EVENT_TYPE_EXTERNAL_STORAGE_MEASURED = 'EXTERNAL_STORAGE_MEASURED';
  /**
   * The number of free bytes in the medium, or for EXTERNAL_STORAGE_DETECTED,
   * the total capacity in bytes of the storage medium.
   *
   * @var string
   */
  public $byteCount;
  /**
   * The creation time of the event.
   *
   * @var string
   */
  public $createTime;
  /**
   * Event type.
   *
   * @var string
   */
  public $eventType;

  /**
   * The number of free bytes in the medium, or for EXTERNAL_STORAGE_DETECTED,
   * the total capacity in bytes of the storage medium.
   *
   * @param string $byteCount
   */
  public function setByteCount($byteCount)
  {
    $this->byteCount = $byteCount;
  }
  /**
   * @return string
   */
  public function getByteCount()
  {
    return $this->byteCount;
  }
  /**
   * The creation time of the event.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Event type.
   *
   * Accepted values: MEMORY_EVENT_TYPE_UNSPECIFIED, RAM_MEASURED,
   * INTERNAL_STORAGE_MEASURED, EXTERNAL_STORAGE_DETECTED,
   * EXTERNAL_STORAGE_REMOVED, EXTERNAL_STORAGE_MEASURED
   *
   * @param self::EVENT_TYPE_* $eventType
   */
  public function setEventType($eventType)
  {
    $this->eventType = $eventType;
  }
  /**
   * @return self::EVENT_TYPE_*
   */
  public function getEventType()
  {
    return $this->eventType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MemoryEvent::class, 'Google_Service_AndroidManagement_MemoryEvent');
