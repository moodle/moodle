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

namespace Google\Service\MyBusinessNotificationSettings;

class NotificationSetting extends \Google\Collection
{
  protected $collection_key = 'notificationTypes';
  /**
   * Required. The resource name this setting is for. This is of the form
   * `accounts/{account_id}/notificationSetting`.
   *
   * @var string
   */
  public $name;
  /**
   * The types of notifications that will be sent to the Pub/Sub topic. To stop
   * receiving notifications entirely, use
   * NotificationSettings.UpdateNotificationSetting with an empty
   * notification_types or set the pubsub_topic to an empty string.
   *
   * @var string[]
   */
  public $notificationTypes;
  /**
   * Optional. The Google Pub/Sub topic that will receive notifications when
   * locations managed by this account are updated. If unset, no notifications
   * will be posted. The account mybusiness-api-
   * pubsub@system.gserviceaccount.com must have at least Publish permissions on
   * the Pub/Sub topic.
   *
   * @var string
   */
  public $pubsubTopic;

  /**
   * Required. The resource name this setting is for. This is of the form
   * `accounts/{account_id}/notificationSetting`.
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
   * The types of notifications that will be sent to the Pub/Sub topic. To stop
   * receiving notifications entirely, use
   * NotificationSettings.UpdateNotificationSetting with an empty
   * notification_types or set the pubsub_topic to an empty string.
   *
   * @param string[] $notificationTypes
   */
  public function setNotificationTypes($notificationTypes)
  {
    $this->notificationTypes = $notificationTypes;
  }
  /**
   * @return string[]
   */
  public function getNotificationTypes()
  {
    return $this->notificationTypes;
  }
  /**
   * Optional. The Google Pub/Sub topic that will receive notifications when
   * locations managed by this account are updated. If unset, no notifications
   * will be posted. The account mybusiness-api-
   * pubsub@system.gserviceaccount.com must have at least Publish permissions on
   * the Pub/Sub topic.
   *
   * @param string $pubsubTopic
   */
  public function setPubsubTopic($pubsubTopic)
  {
    $this->pubsubTopic = $pubsubTopic;
  }
  /**
   * @return string
   */
  public function getPubsubTopic()
  {
    return $this->pubsubTopic;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NotificationSetting::class, 'Google_Service_MyBusinessNotificationSettings_NotificationSetting');
