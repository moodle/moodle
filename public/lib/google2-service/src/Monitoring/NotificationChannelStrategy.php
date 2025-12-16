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

namespace Google\Service\Monitoring;

class NotificationChannelStrategy extends \Google\Collection
{
  protected $collection_key = 'notificationChannelNames';
  /**
   * The full REST resource name for the notification channels that these
   * settings apply to. Each of these correspond to the name field in one of the
   * NotificationChannel objects referenced in the notification_channels field
   * of this AlertPolicy. The format is:
   * projects/[PROJECT_ID_OR_NUMBER]/notificationChannels/[CHANNEL_ID]
   *
   * @var string[]
   */
  public $notificationChannelNames;
  /**
   * The frequency at which to send reminder notifications for open incidents.
   *
   * @var string
   */
  public $renotifyInterval;

  /**
   * The full REST resource name for the notification channels that these
   * settings apply to. Each of these correspond to the name field in one of the
   * NotificationChannel objects referenced in the notification_channels field
   * of this AlertPolicy. The format is:
   * projects/[PROJECT_ID_OR_NUMBER]/notificationChannels/[CHANNEL_ID]
   *
   * @param string[] $notificationChannelNames
   */
  public function setNotificationChannelNames($notificationChannelNames)
  {
    $this->notificationChannelNames = $notificationChannelNames;
  }
  /**
   * @return string[]
   */
  public function getNotificationChannelNames()
  {
    return $this->notificationChannelNames;
  }
  /**
   * The frequency at which to send reminder notifications for open incidents.
   *
   * @param string $renotifyInterval
   */
  public function setRenotifyInterval($renotifyInterval)
  {
    $this->renotifyInterval = $renotifyInterval;
  }
  /**
   * @return string
   */
  public function getRenotifyInterval()
  {
    return $this->renotifyInterval;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NotificationChannelStrategy::class, 'Google_Service_Monitoring_NotificationChannelStrategy');
