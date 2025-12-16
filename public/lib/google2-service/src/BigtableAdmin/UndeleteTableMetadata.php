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

namespace Google\Service\BigtableAdmin;

class UndeleteTableMetadata extends \Google\Model
{
  /**
   * If set, the time at which this operation finished or was cancelled.
   * DEPRECATED: Use finish_time instead.
   *
   * @deprecated
   * @var string
   */
  public $endTime;
  /**
   * The time at which the operation failed or was completed successfully.
   *
   * @var string
   */
  public $finishTime;
  /**
   * The name of the table being restored.
   *
   * @var string
   */
  public $name;
  /**
   * The time at which the original request was received.
   *
   * @var string
   */
  public $requestTime;
  /**
   * The time at which this operation started. DEPRECATED: Use request_time
   * instead.
   *
   * @deprecated
   * @var string
   */
  public $startTime;

  /**
   * If set, the time at which this operation finished or was cancelled.
   * DEPRECATED: Use finish_time instead.
   *
   * @deprecated
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * The time at which the operation failed or was completed successfully.
   *
   * @param string $finishTime
   */
  public function setFinishTime($finishTime)
  {
    $this->finishTime = $finishTime;
  }
  /**
   * @return string
   */
  public function getFinishTime()
  {
    return $this->finishTime;
  }
  /**
   * The name of the table being restored.
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
   * The time at which the original request was received.
   *
   * @param string $requestTime
   */
  public function setRequestTime($requestTime)
  {
    $this->requestTime = $requestTime;
  }
  /**
   * @return string
   */
  public function getRequestTime()
  {
    return $this->requestTime;
  }
  /**
   * The time at which this operation started. DEPRECATED: Use request_time
   * instead.
   *
   * @deprecated
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UndeleteTableMetadata::class, 'Google_Service_BigtableAdmin_UndeleteTableMetadata');
