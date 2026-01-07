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

namespace Google\Service\AndroidEnterprise;

class NotificationSet extends \Google\Collection
{
  protected $collection_key = 'notification';
  protected $notificationType = Notification::class;
  protected $notificationDataType = 'array';
  /**
   * The notification set ID, required to mark the notification as received with
   * the Enterprises.AcknowledgeNotification API. This will be omitted if no
   * notifications are present.
   *
   * @var string
   */
  public $notificationSetId;

  /**
   * The notifications received, or empty if no notifications are present.
   *
   * @param Notification[] $notification
   */
  public function setNotification($notification)
  {
    $this->notification = $notification;
  }
  /**
   * @return Notification[]
   */
  public function getNotification()
  {
    return $this->notification;
  }
  /**
   * The notification set ID, required to mark the notification as received with
   * the Enterprises.AcknowledgeNotification API. This will be omitted if no
   * notifications are present.
   *
   * @param string $notificationSetId
   */
  public function setNotificationSetId($notificationSetId)
  {
    $this->notificationSetId = $notificationSetId;
  }
  /**
   * @return string
   */
  public function getNotificationSetId()
  {
    return $this->notificationSetId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NotificationSet::class, 'Google_Service_AndroidEnterprise_NotificationSet');
