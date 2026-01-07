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

namespace Google\Service\DriveActivity;

class Action extends \Google\Model
{
  protected $actorType = Actor::class;
  protected $actorDataType = '';
  protected $detailType = ActionDetail::class;
  protected $detailDataType = '';
  protected $targetType = Target::class;
  protected $targetDataType = '';
  protected $timeRangeType = TimeRange::class;
  protected $timeRangeDataType = '';
  /**
   * The action occurred at this specific time.
   *
   * @var string
   */
  public $timestamp;

  /**
   * The actor responsible for this action (or empty if all actors are
   * responsible).
   *
   * @param Actor $actor
   */
  public function setActor(Actor $actor)
  {
    $this->actor = $actor;
  }
  /**
   * @return Actor
   */
  public function getActor()
  {
    return $this->actor;
  }
  /**
   * The type and detailed information about the action.
   *
   * @param ActionDetail $detail
   */
  public function setDetail(ActionDetail $detail)
  {
    $this->detail = $detail;
  }
  /**
   * @return ActionDetail
   */
  public function getDetail()
  {
    return $this->detail;
  }
  /**
   * The target this action affects (or empty if affecting all targets). This
   * represents the state of the target immediately after this action occurred.
   *
   * @param Target $target
   */
  public function setTarget(Target $target)
  {
    $this->target = $target;
  }
  /**
   * @return Target
   */
  public function getTarget()
  {
    return $this->target;
  }
  /**
   * The action occurred over this time range.
   *
   * @param TimeRange $timeRange
   */
  public function setTimeRange(TimeRange $timeRange)
  {
    $this->timeRange = $timeRange;
  }
  /**
   * @return TimeRange
   */
  public function getTimeRange()
  {
    return $this->timeRange;
  }
  /**
   * The action occurred at this specific time.
   *
   * @param string $timestamp
   */
  public function setTimestamp($timestamp)
  {
    $this->timestamp = $timestamp;
  }
  /**
   * @return string
   */
  public function getTimestamp()
  {
    return $this->timestamp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Action::class, 'Google_Service_DriveActivity_Action');
