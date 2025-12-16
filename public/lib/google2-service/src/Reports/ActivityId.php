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

namespace Google\Service\Reports;

class ActivityId extends \Google\Model
{
  /**
   * Application name to which the event belongs. For possible values see the
   * list of applications above in `applicationName`.
   *
   * @var string
   */
  public $applicationName;
  /**
   * The unique identifier for a Google Workspace account.
   *
   * @var string
   */
  public $customerId;
  /**
   * Time of occurrence of the activity. This is in UNIX epoch time in seconds.
   *
   * @var string
   */
  public $time;
  /**
   * Unique qualifier if multiple events have the same time.
   *
   * @var string
   */
  public $uniqueQualifier;

  /**
   * Application name to which the event belongs. For possible values see the
   * list of applications above in `applicationName`.
   *
   * @param string $applicationName
   */
  public function setApplicationName($applicationName)
  {
    $this->applicationName = $applicationName;
  }
  /**
   * @return string
   */
  public function getApplicationName()
  {
    return $this->applicationName;
  }
  /**
   * The unique identifier for a Google Workspace account.
   *
   * @param string $customerId
   */
  public function setCustomerId($customerId)
  {
    $this->customerId = $customerId;
  }
  /**
   * @return string
   */
  public function getCustomerId()
  {
    return $this->customerId;
  }
  /**
   * Time of occurrence of the activity. This is in UNIX epoch time in seconds.
   *
   * @param string $time
   */
  public function setTime($time)
  {
    $this->time = $time;
  }
  /**
   * @return string
   */
  public function getTime()
  {
    return $this->time;
  }
  /**
   * Unique qualifier if multiple events have the same time.
   *
   * @param string $uniqueQualifier
   */
  public function setUniqueQualifier($uniqueQualifier)
  {
    $this->uniqueQualifier = $uniqueQualifier;
  }
  /**
   * @return string
   */
  public function getUniqueQualifier()
  {
    return $this->uniqueQualifier;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ActivityId::class, 'Google_Service_Reports_ActivityId');
