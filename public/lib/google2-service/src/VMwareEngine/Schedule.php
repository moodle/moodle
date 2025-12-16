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

namespace Google\Service\VMwareEngine;

class Schedule extends \Google\Collection
{
  /**
   * The default value. This value should never be used.
   */
  public const LAST_EDITOR_EDITOR_UNSPECIFIED = 'EDITOR_UNSPECIFIED';
  /**
   * The upgrade is scheduled by the System or internal service.
   */
  public const LAST_EDITOR_SYSTEM = 'SYSTEM';
  /**
   * The upgrade is scheduled by the end user.
   */
  public const LAST_EDITOR_USER = 'USER';
  protected $collection_key = 'weeklyWindows';
  protected $constraintsType = Constraints::class;
  protected $constraintsDataType = '';
  protected $editWindowType = Interval::class;
  protected $editWindowDataType = '';
  /**
   * Output only. Output Only. Indicates who most recently edited the upgrade
   * schedule. The value is updated whenever the upgrade is rescheduled.
   *
   * @var string
   */
  public $lastEditor;
  /**
   * Required. The scheduled start time for the upgrade.
   *
   * @var string
   */
  public $startTime;
  protected $weeklyWindowsType = TimeWindow::class;
  protected $weeklyWindowsDataType = 'array';

  /**
   * Output only. Output Only. Constraints applied to the schedule. These
   * constraints should be applicable at the time of any rescheduling.
   *
   * @param Constraints $constraints
   */
  public function setConstraints(Constraints $constraints)
  {
    $this->constraints = $constraints;
  }
  /**
   * @return Constraints
   */
  public function getConstraints()
  {
    return $this->constraints;
  }
  /**
   * Output only. Output Only. The schedule is open for edits during this time
   * interval or window.
   *
   * @param Interval $editWindow
   */
  public function setEditWindow(Interval $editWindow)
  {
    $this->editWindow = $editWindow;
  }
  /**
   * @return Interval
   */
  public function getEditWindow()
  {
    return $this->editWindow;
  }
  /**
   * Output only. Output Only. Indicates who most recently edited the upgrade
   * schedule. The value is updated whenever the upgrade is rescheduled.
   *
   * Accepted values: EDITOR_UNSPECIFIED, SYSTEM, USER
   *
   * @param self::LAST_EDITOR_* $lastEditor
   */
  public function setLastEditor($lastEditor)
  {
    $this->lastEditor = $lastEditor;
  }
  /**
   * @return self::LAST_EDITOR_*
   */
  public function getLastEditor()
  {
    return $this->lastEditor;
  }
  /**
   * Required. The scheduled start time for the upgrade.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Required. Weekly time windows for upgrade activities. The server performs
   * upgrade activities during these time windows to minimize disruptions.
   *
   * @param TimeWindow[] $weeklyWindows
   */
  public function setWeeklyWindows($weeklyWindows)
  {
    $this->weeklyWindows = $weeklyWindows;
  }
  /**
   * @return TimeWindow[]
   */
  public function getWeeklyWindows()
  {
    return $this->weeklyWindows;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Schedule::class, 'Google_Service_VMwareEngine_Schedule');
