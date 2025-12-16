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

namespace Google\Service\WorkspaceEvents;

class SendMessageConfiguration extends \Google\Collection
{
  protected $collection_key = 'acceptedOutputModes';
  /**
   * The output modes that the agent is expected to respond with.
   *
   * @var string[]
   */
  public $acceptedOutputModes;
  /**
   * If true, the message will be blocking until the task is completed. If
   * false, the message will be non-blocking and the task will be returned
   * immediately. It is the caller's responsibility to check for any task
   * updates.
   *
   * @var bool
   */
  public $blocking;
  /**
   * The maximum number of messages to include in the history. if 0, the history
   * will be unlimited.
   *
   * @var int
   */
  public $historyLength;
  protected $pushNotificationType = PushNotificationConfig::class;
  protected $pushNotificationDataType = '';

  /**
   * The output modes that the agent is expected to respond with.
   *
   * @param string[] $acceptedOutputModes
   */
  public function setAcceptedOutputModes($acceptedOutputModes)
  {
    $this->acceptedOutputModes = $acceptedOutputModes;
  }
  /**
   * @return string[]
   */
  public function getAcceptedOutputModes()
  {
    return $this->acceptedOutputModes;
  }
  /**
   * If true, the message will be blocking until the task is completed. If
   * false, the message will be non-blocking and the task will be returned
   * immediately. It is the caller's responsibility to check for any task
   * updates.
   *
   * @param bool $blocking
   */
  public function setBlocking($blocking)
  {
    $this->blocking = $blocking;
  }
  /**
   * @return bool
   */
  public function getBlocking()
  {
    return $this->blocking;
  }
  /**
   * The maximum number of messages to include in the history. if 0, the history
   * will be unlimited.
   *
   * @param int $historyLength
   */
  public function setHistoryLength($historyLength)
  {
    $this->historyLength = $historyLength;
  }
  /**
   * @return int
   */
  public function getHistoryLength()
  {
    return $this->historyLength;
  }
  /**
   * A configuration of a webhook that can be used to receive updates
   *
   * @param PushNotificationConfig $pushNotification
   */
  public function setPushNotification(PushNotificationConfig $pushNotification)
  {
    $this->pushNotification = $pushNotification;
  }
  /**
   * @return PushNotificationConfig
   */
  public function getPushNotification()
  {
    return $this->pushNotification;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SendMessageConfiguration::class, 'Google_Service_WorkspaceEvents_SendMessageConfiguration');
