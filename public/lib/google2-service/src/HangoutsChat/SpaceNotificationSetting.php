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

namespace Google\Service\HangoutsChat;

class SpaceNotificationSetting extends \Google\Model
{
  /**
   * Reserved.
   */
  public const MUTE_SETTING_MUTE_SETTING_UNSPECIFIED = 'MUTE_SETTING_UNSPECIFIED';
  /**
   * The user will receive notifications for the space based on the notification
   * setting.
   */
  public const MUTE_SETTING_UNMUTED = 'UNMUTED';
  /**
   * The user will not receive any notifications for the space, regardless of
   * the notification setting.
   */
  public const MUTE_SETTING_MUTED = 'MUTED';
  /**
   * Reserved.
   */
  public const NOTIFICATION_SETTING_NOTIFICATION_SETTING_UNSPECIFIED = 'NOTIFICATION_SETTING_UNSPECIFIED';
  /**
   * Notifications are triggered by @mentions, followed threads, first message
   * of new threads. All new threads are automatically followed, unless manually
   * unfollowed by the user.
   */
  public const NOTIFICATION_SETTING_ALL = 'ALL';
  /**
   * The notification is triggered by @mentions, followed threads, first message
   * of new threads. Not available for 1:1 direct messages.
   */
  public const NOTIFICATION_SETTING_MAIN_CONVERSATIONS = 'MAIN_CONVERSATIONS';
  /**
   * The notification is triggered by @mentions, followed threads. Not available
   * for 1:1 direct messages.
   */
  public const NOTIFICATION_SETTING_FOR_YOU = 'FOR_YOU';
  /**
   * Notification is off.
   */
  public const NOTIFICATION_SETTING_OFF = 'OFF';
  /**
   * The space notification mute setting.
   *
   * @var string
   */
  public $muteSetting;
  /**
   * Identifier. The resource name of the space notification setting. Format:
   * `users/{user}/spaces/{space}/spaceNotificationSetting`.
   *
   * @var string
   */
  public $name;
  /**
   * The notification setting.
   *
   * @var string
   */
  public $notificationSetting;

  /**
   * The space notification mute setting.
   *
   * Accepted values: MUTE_SETTING_UNSPECIFIED, UNMUTED, MUTED
   *
   * @param self::MUTE_SETTING_* $muteSetting
   */
  public function setMuteSetting($muteSetting)
  {
    $this->muteSetting = $muteSetting;
  }
  /**
   * @return self::MUTE_SETTING_*
   */
  public function getMuteSetting()
  {
    return $this->muteSetting;
  }
  /**
   * Identifier. The resource name of the space notification setting. Format:
   * `users/{user}/spaces/{space}/spaceNotificationSetting`.
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
   * The notification setting.
   *
   * Accepted values: NOTIFICATION_SETTING_UNSPECIFIED, ALL, MAIN_CONVERSATIONS,
   * FOR_YOU, OFF
   *
   * @param self::NOTIFICATION_SETTING_* $notificationSetting
   */
  public function setNotificationSetting($notificationSetting)
  {
    $this->notificationSetting = $notificationSetting;
  }
  /**
   * @return self::NOTIFICATION_SETTING_*
   */
  public function getNotificationSetting()
  {
    return $this->notificationSetting;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SpaceNotificationSetting::class, 'Google_Service_HangoutsChat_SpaceNotificationSetting');
