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

class PowerManagementEvent extends \Google\Model
{
  /**
   * Unspecified. No events have this type.
   */
  public const EVENT_TYPE_POWER_MANAGEMENT_EVENT_TYPE_UNSPECIFIED = 'POWER_MANAGEMENT_EVENT_TYPE_UNSPECIFIED';
  /**
   * Battery level was measured.
   */
  public const EVENT_TYPE_BATTERY_LEVEL_COLLECTED = 'BATTERY_LEVEL_COLLECTED';
  /**
   * The device started charging.
   */
  public const EVENT_TYPE_POWER_CONNECTED = 'POWER_CONNECTED';
  /**
   * The device stopped charging.
   */
  public const EVENT_TYPE_POWER_DISCONNECTED = 'POWER_DISCONNECTED';
  /**
   * The device entered low-power mode.
   */
  public const EVENT_TYPE_BATTERY_LOW = 'BATTERY_LOW';
  /**
   * The device exited low-power mode.
   */
  public const EVENT_TYPE_BATTERY_OKAY = 'BATTERY_OKAY';
  /**
   * The device booted.
   */
  public const EVENT_TYPE_BOOT_COMPLETED = 'BOOT_COMPLETED';
  /**
   * The device shut down.
   */
  public const EVENT_TYPE_SHUTDOWN = 'SHUTDOWN';
  /**
   * For BATTERY_LEVEL_COLLECTED events, the battery level as a percentage.
   *
   * @var float
   */
  public $batteryLevel;
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
   * For BATTERY_LEVEL_COLLECTED events, the battery level as a percentage.
   *
   * @param float $batteryLevel
   */
  public function setBatteryLevel($batteryLevel)
  {
    $this->batteryLevel = $batteryLevel;
  }
  /**
   * @return float
   */
  public function getBatteryLevel()
  {
    return $this->batteryLevel;
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
   * Accepted values: POWER_MANAGEMENT_EVENT_TYPE_UNSPECIFIED,
   * BATTERY_LEVEL_COLLECTED, POWER_CONNECTED, POWER_DISCONNECTED, BATTERY_LOW,
   * BATTERY_OKAY, BOOT_COMPLETED, SHUTDOWN
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
class_alias(PowerManagementEvent::class, 'Google_Service_AndroidManagement_PowerManagementEvent');
