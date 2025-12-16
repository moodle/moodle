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

class SystemUpdate extends \Google\Collection
{
  /**
   * Follow the default update behavior for the device, which typically requires
   * the user to accept system updates.
   */
  public const TYPE_SYSTEM_UPDATE_TYPE_UNSPECIFIED = 'SYSTEM_UPDATE_TYPE_UNSPECIFIED';
  /**
   * Install automatically as soon as an update is available.
   */
  public const TYPE_AUTOMATIC = 'AUTOMATIC';
  /**
   * Install automatically within a daily maintenance window. This also
   * configures Play apps to be updated within the window. This is strongly
   * recommended for kiosk devices because this is the only way apps
   * persistently pinned to the foreground can be updated by Play.If
   * autoUpdateMode is set to AUTO_UPDATE_HIGH_PRIORITY for an app, then the
   * maintenance window is ignored for that app and it is updated as soon as
   * possible even outside of the maintenance window.
   */
  public const TYPE_WINDOWED = 'WINDOWED';
  /**
   * Postpone automatic install up to a maximum of 30 days. This policy does not
   * affect security updates (e.g. monthly security patches).
   */
  public const TYPE_POSTPONE = 'POSTPONE';
  protected $collection_key = 'freezePeriods';
  /**
   * If the type is WINDOWED, the end of the maintenance window, measured as the
   * number of minutes after midnight in device's local time. This value must be
   * between 0 and 1439, inclusive. If this value is less than start_minutes,
   * then the maintenance window spans midnight. If the maintenance window
   * specified is smaller than 30 minutes, the actual window is extended to 30
   * minutes beyond the start time.
   *
   * @var int
   */
  public $endMinutes;
  protected $freezePeriodsType = FreezePeriod::class;
  protected $freezePeriodsDataType = 'array';
  /**
   * If the type is WINDOWED, the start of the maintenance window, measured as
   * the number of minutes after midnight in the device's local time. This value
   * must be between 0 and 1439, inclusive.
   *
   * @var int
   */
  public $startMinutes;
  /**
   * The type of system update to configure.
   *
   * @var string
   */
  public $type;

  /**
   * If the type is WINDOWED, the end of the maintenance window, measured as the
   * number of minutes after midnight in device's local time. This value must be
   * between 0 and 1439, inclusive. If this value is less than start_minutes,
   * then the maintenance window spans midnight. If the maintenance window
   * specified is smaller than 30 minutes, the actual window is extended to 30
   * minutes beyond the start time.
   *
   * @param int $endMinutes
   */
  public function setEndMinutes($endMinutes)
  {
    $this->endMinutes = $endMinutes;
  }
  /**
   * @return int
   */
  public function getEndMinutes()
  {
    return $this->endMinutes;
  }
  /**
   * An annually repeating time period in which over-the-air (OTA) system
   * updates are postponed to freeze the OS version running on a device. To
   * prevent freezing the device indefinitely, each freeze period must be
   * separated by at least 60 days.
   *
   * @param FreezePeriod[] $freezePeriods
   */
  public function setFreezePeriods($freezePeriods)
  {
    $this->freezePeriods = $freezePeriods;
  }
  /**
   * @return FreezePeriod[]
   */
  public function getFreezePeriods()
  {
    return $this->freezePeriods;
  }
  /**
   * If the type is WINDOWED, the start of the maintenance window, measured as
   * the number of minutes after midnight in the device's local time. This value
   * must be between 0 and 1439, inclusive.
   *
   * @param int $startMinutes
   */
  public function setStartMinutes($startMinutes)
  {
    $this->startMinutes = $startMinutes;
  }
  /**
   * @return int
   */
  public function getStartMinutes()
  {
    return $this->startMinutes;
  }
  /**
   * The type of system update to configure.
   *
   * Accepted values: SYSTEM_UPDATE_TYPE_UNSPECIFIED, AUTOMATIC, WINDOWED,
   * POSTPONE
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
class_alias(SystemUpdate::class, 'Google_Service_AndroidManagement_SystemUpdate');
