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

namespace Google\Service\Fitness;

class Session extends \Google\Model
{
  /**
   * Session active time. While start_time_millis and end_time_millis define the
   * full session time, the active time can be shorter and specified by
   * active_time_millis. If the inactive time during the session is known, it
   * should also be inserted via a com.google.activity.segment data point with a
   * STILL activity value
   *
   * @var string
   */
  public $activeTimeMillis;
  /**
   * The type of activity this session represents.
   *
   * @var int
   */
  public $activityType;
  protected $applicationType = Application::class;
  protected $applicationDataType = '';
  /**
   * A description for this session.
   *
   * @var string
   */
  public $description;
  /**
   * An end time, in milliseconds since epoch, inclusive.
   *
   * @var string
   */
  public $endTimeMillis;
  /**
   * A client-generated identifier that is unique across all sessions owned by
   * this particular user.
   *
   * @var string
   */
  public $id;
  /**
   * A timestamp that indicates when the session was last modified.
   *
   * @var string
   */
  public $modifiedTimeMillis;
  /**
   * A human readable name of the session.
   *
   * @var string
   */
  public $name;
  /**
   * A start time, in milliseconds since epoch, inclusive.
   *
   * @var string
   */
  public $startTimeMillis;

  /**
   * Session active time. While start_time_millis and end_time_millis define the
   * full session time, the active time can be shorter and specified by
   * active_time_millis. If the inactive time during the session is known, it
   * should also be inserted via a com.google.activity.segment data point with a
   * STILL activity value
   *
   * @param string $activeTimeMillis
   */
  public function setActiveTimeMillis($activeTimeMillis)
  {
    $this->activeTimeMillis = $activeTimeMillis;
  }
  /**
   * @return string
   */
  public function getActiveTimeMillis()
  {
    return $this->activeTimeMillis;
  }
  /**
   * The type of activity this session represents.
   *
   * @param int $activityType
   */
  public function setActivityType($activityType)
  {
    $this->activityType = $activityType;
  }
  /**
   * @return int
   */
  public function getActivityType()
  {
    return $this->activityType;
  }
  /**
   * The application that created the session.
   *
   * @param Application $application
   */
  public function setApplication(Application $application)
  {
    $this->application = $application;
  }
  /**
   * @return Application
   */
  public function getApplication()
  {
    return $this->application;
  }
  /**
   * A description for this session.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * An end time, in milliseconds since epoch, inclusive.
   *
   * @param string $endTimeMillis
   */
  public function setEndTimeMillis($endTimeMillis)
  {
    $this->endTimeMillis = $endTimeMillis;
  }
  /**
   * @return string
   */
  public function getEndTimeMillis()
  {
    return $this->endTimeMillis;
  }
  /**
   * A client-generated identifier that is unique across all sessions owned by
   * this particular user.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * A timestamp that indicates when the session was last modified.
   *
   * @param string $modifiedTimeMillis
   */
  public function setModifiedTimeMillis($modifiedTimeMillis)
  {
    $this->modifiedTimeMillis = $modifiedTimeMillis;
  }
  /**
   * @return string
   */
  public function getModifiedTimeMillis()
  {
    return $this->modifiedTimeMillis;
  }
  /**
   * A human readable name of the session.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * A start time, in milliseconds since epoch, inclusive.
   *
   * @param string $startTimeMillis
   */
  public function setStartTimeMillis($startTimeMillis)
  {
    $this->startTimeMillis = $startTimeMillis;
  }
  /**
   * @return string
   */
  public function getStartTimeMillis()
  {
    return $this->startTimeMillis;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Session::class, 'Google_Service_Fitness_Session');
