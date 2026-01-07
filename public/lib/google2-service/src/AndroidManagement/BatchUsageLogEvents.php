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

class BatchUsageLogEvents extends \Google\Collection
{
  protected $collection_key = 'usageLogEvents';
  /**
   * If present, the name of the device in the form
   * ‘enterprises/{enterpriseId}/devices/{deviceId}’
   *
   * @var string
   */
  public $device;
  /**
   * The device timestamp when the batch of events were collected from the
   * device.
   *
   * @var string
   */
  public $retrievalTime;
  protected $usageLogEventsType = UsageLogEvent::class;
  protected $usageLogEventsDataType = 'array';
  /**
   * If present, the resource name of the user that owns this device in the form
   * ‘enterprises/{enterpriseId}/users/{userId}’.
   *
   * @var string
   */
  public $user;

  /**
   * If present, the name of the device in the form
   * ‘enterprises/{enterpriseId}/devices/{deviceId}’
   *
   * @param string $device
   */
  public function setDevice($device)
  {
    $this->device = $device;
  }
  /**
   * @return string
   */
  public function getDevice()
  {
    return $this->device;
  }
  /**
   * The device timestamp when the batch of events were collected from the
   * device.
   *
   * @param string $retrievalTime
   */
  public function setRetrievalTime($retrievalTime)
  {
    $this->retrievalTime = $retrievalTime;
  }
  /**
   * @return string
   */
  public function getRetrievalTime()
  {
    return $this->retrievalTime;
  }
  /**
   * The list of UsageLogEvent that were reported by the device, sorted
   * chronologically by the event time.
   *
   * @param UsageLogEvent[] $usageLogEvents
   */
  public function setUsageLogEvents($usageLogEvents)
  {
    $this->usageLogEvents = $usageLogEvents;
  }
  /**
   * @return UsageLogEvent[]
   */
  public function getUsageLogEvents()
  {
    return $this->usageLogEvents;
  }
  /**
   * If present, the resource name of the user that owns this device in the form
   * ‘enterprises/{enterpriseId}/users/{userId}’.
   *
   * @param string $user
   */
  public function setUser($user)
  {
    $this->user = $user;
  }
  /**
   * @return string
   */
  public function getUser()
  {
    return $this->user;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BatchUsageLogEvents::class, 'Google_Service_AndroidManagement_BatchUsageLogEvents');
