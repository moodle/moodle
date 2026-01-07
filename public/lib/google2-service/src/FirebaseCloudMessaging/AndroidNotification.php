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

namespace Google\Service\FirebaseCloudMessaging;

class AndroidNotification extends \Google\Collection
{
  /**
   * If priority is unspecified, notification priority is set to
   * `PRIORITY_DEFAULT`.
   */
  public const NOTIFICATION_PRIORITY_PRIORITY_UNSPECIFIED = 'PRIORITY_UNSPECIFIED';
  /**
   * Lowest notification priority. Notifications with this `PRIORITY_MIN` might
   * not be shown to the user except under special circumstances, such as
   * detailed notification logs.
   */
  public const NOTIFICATION_PRIORITY_PRIORITY_MIN = 'PRIORITY_MIN';
  /**
   * Lower notification priority. The UI may choose to show the notifications
   * smaller, or at a different position in the list, compared with
   * notifications with `PRIORITY_DEFAULT`.
   */
  public const NOTIFICATION_PRIORITY_PRIORITY_LOW = 'PRIORITY_LOW';
  /**
   * Default notification priority. If the application does not prioritize its
   * own notifications, use this value for all notifications.
   */
  public const NOTIFICATION_PRIORITY_PRIORITY_DEFAULT = 'PRIORITY_DEFAULT';
  /**
   * Higher notification priority. Use this for more important notifications or
   * alerts. The UI may choose to show these notifications larger, or at a
   * different position in the notification lists, compared with notifications
   * with `PRIORITY_DEFAULT`.
   */
  public const NOTIFICATION_PRIORITY_PRIORITY_HIGH = 'PRIORITY_HIGH';
  /**
   * Highest notification priority. Use this for the application's most
   * important items that require the user's prompt attention or input.
   */
  public const NOTIFICATION_PRIORITY_PRIORITY_MAX = 'PRIORITY_MAX';
  /**
   * If unspecified, default to `Proxy.IF_PRIORITY_LOWERED`.
   */
  public const PROXY_PROXY_UNSPECIFIED = 'PROXY_UNSPECIFIED';
  /**
   * Try to proxy this notification.
   */
  public const PROXY_ALLOW = 'ALLOW';
  /**
   * Do not proxy this notification.
   */
  public const PROXY_DENY = 'DENY';
  /**
   * Only try to proxy this notification if its `AndroidMessagePriority` was
   * lowered from `HIGH` to `NORMAL` on the device.
   */
  public const PROXY_IF_PRIORITY_LOWERED = 'IF_PRIORITY_LOWERED';
  /**
   * If unspecified, default to `Visibility.PRIVATE`.
   */
  public const VISIBILITY_VISIBILITY_UNSPECIFIED = 'VISIBILITY_UNSPECIFIED';
  /**
   * Show this notification on all lockscreens, but conceal sensitive or private
   * information on secure lockscreens.
   */
  public const VISIBILITY_PRIVATE = 'PRIVATE';
  /**
   * Show this notification in its entirety on all lockscreens.
   */
  public const VISIBILITY_PUBLIC = 'PUBLIC';
  /**
   * Do not reveal any part of this notification on a secure lockscreen.
   */
  public const VISIBILITY_SECRET = 'SECRET';
  protected $collection_key = 'vibrateTimings';
  /**
   * The notification's body text. If present, it will override
   * google.firebase.fcm.v1.Notification.body.
   *
   * @var string
   */
  public $body;
  /**
   * Variable string values to be used in place of the format specifiers in
   * body_loc_key to use to localize the body text to the user's current
   * localization. See [Formatting and Styling](https://goo.gl/MalYE3) for more
   * information.
   *
   * @var string[]
   */
  public $bodyLocArgs;
  /**
   * The key to the body string in the app's string resources to use to localize
   * the body text to the user's current localization. See [String
   * Resources](https://goo.gl/NdFZGI) for more information.
   *
   * @var string
   */
  public $bodyLocKey;
  /**
   * If set, display notifications delivered to the device will be handled by
   * the app instead of the proxy.
   *
   * @deprecated
   * @var bool
   */
  public $bypassProxyNotification;
  /**
   * The [notification's channel id](https://developer.android.com/guide/topics/
   * ui/notifiers/notifications#ManageChannels) (new in Android O). The app must
   * create a channel with this channel ID before any notification with this
   * channel ID is received. If you don't send this channel ID in the request,
   * or if the channel ID provided has not yet been created by the app, FCM uses
   * the channel ID specified in the app manifest.
   *
   * @var string
   */
  public $channelId;
  /**
   * The action associated with a user click on the notification. If specified,
   * an activity with a matching intent filter is launched when a user clicks on
   * the notification.
   *
   * @var string
   */
  public $clickAction;
  /**
   * The notification's icon color, expressed in #rrggbb format.
   *
   * @var string
   */
  public $color;
  /**
   * If set to true, use the Android framework's default LED light settings for
   * the notification. Default values are specified in [config.xml](https://andr
   * oid.googlesource.com/platform/frameworks/base/+/master/core/res/res/values/
   * config.xml). If `default_light_settings` is set to true and
   * `light_settings` is also set, the user-specified `light_settings` is used
   * instead of the default value.
   *
   * @var bool
   */
  public $defaultLightSettings;
  /**
   * If set to true, use the Android framework's default sound for the
   * notification. Default values are specified in [config.xml](https://android.
   * googlesource.com/platform/frameworks/base/+/master/core/res/res/values/conf
   * ig.xml).
   *
   * @var bool
   */
  public $defaultSound;
  /**
   * If set to true, use the Android framework's default vibrate pattern for the
   * notification. Default values are specified in [config.xml](https://android.
   * googlesource.com/platform/frameworks/base/+/master/core/res/res/values/conf
   * ig.xml). If `default_vibrate_timings` is set to true and `vibrate_timings`
   * is also set, the default value is used instead of the user-specified
   * `vibrate_timings`.
   *
   * @var bool
   */
  public $defaultVibrateTimings;
  /**
   * Set the time that the event in the notification occurred. Notifications in
   * the panel are sorted by this time. A point in time is represented using
   * [protobuf.Timestamp](https://developers.google.com/protocol-
   * buffers/docs/reference/java/com/google/protobuf/Timestamp).
   *
   * @var string
   */
  public $eventTime;
  /**
   * The notification's icon. Sets the notification icon to myicon for drawable
   * resource myicon. If you don't send this key in the request, FCM displays
   * the launcher icon specified in your app manifest.
   *
   * @var string
   */
  public $icon;
  /**
   * Contains the URL of an image that is going to be displayed in a
   * notification. If present, it will override
   * google.firebase.fcm.v1.Notification.image.
   *
   * @var string
   */
  public $image;
  protected $lightSettingsType = LightSettings::class;
  protected $lightSettingsDataType = '';
  /**
   * Set whether or not this notification is relevant only to the current
   * device. Some notifications can be bridged to other devices for remote
   * display, such as a Wear OS watch. This hint can be set to recommend this
   * notification not be bridged. See [Wear OS guides](https://developer.android
   * .com/training/wearables/notifications/bridger#existing-method-of-
   * preventing-bridging)
   *
   * @var bool
   */
  public $localOnly;
  /**
   * Sets the number of items this notification represents. May be displayed as
   * a badge count for launchers that support badging.See [Notification
   * Badge](https://developer.android.com/training/notify-user/badges). For
   * example, this might be useful if you're using just one notification to
   * represent multiple new messages but you want the count here to represent
   * the number of total new messages. If zero or unspecified, systems that
   * support badging use the default, which is to increment a number displayed
   * on the long-press menu each time a new notification arrives.
   *
   * @var int
   */
  public $notificationCount;
  /**
   * Set the relative priority for this notification. Priority is an indication
   * of how much of the user's attention should be consumed by this
   * notification. Low-priority notifications may be hidden from the user in
   * certain situations, while the user might be interrupted for a higher-
   * priority notification. The effect of setting the same priorities may differ
   * slightly on different platforms. Note this priority differs from
   * `AndroidMessagePriority`. This priority is processed by the client after
   * the message has been delivered, whereas [AndroidMessagePriority](https://fi
   * rebase.google.com/docs/reference/fcm/rest/v1/projects.messages#androidmessa
   * gepriority) is an FCM concept that controls when the message is delivered.
   *
   * @var string
   */
  public $notificationPriority;
  /**
   * Setting to control when a notification may be proxied.
   *
   * @var string
   */
  public $proxy;
  /**
   * The sound to play when the device receives the notification. Supports
   * "default" or the filename of a sound resource bundled in the app. Sound
   * files must reside in /res/raw/.
   *
   * @var string
   */
  public $sound;
  /**
   * When set to false or unset, the notification is automatically dismissed
   * when the user clicks it in the panel. When set to true, the notification
   * persists even when the user clicks it.
   *
   * @var bool
   */
  public $sticky;
  /**
   * Identifier used to replace existing notifications in the notification
   * drawer. If not specified, each request creates a new notification. If
   * specified and a notification with the same tag is already being shown, the
   * new notification replaces the existing one in the notification drawer.
   *
   * @var string
   */
  public $tag;
  /**
   * Sets the "ticker" text, which is sent to accessibility services. Prior to
   * API level 21 (`Lollipop`), sets the text that is displayed in the status
   * bar when the notification first arrives.
   *
   * @var string
   */
  public $ticker;
  /**
   * The notification's title. If present, it will override
   * google.firebase.fcm.v1.Notification.title.
   *
   * @var string
   */
  public $title;
  /**
   * Variable string values to be used in place of the format specifiers in
   * title_loc_key to use to localize the title text to the user's current
   * localization. See [Formatting and Styling](https://goo.gl/MalYE3) for more
   * information.
   *
   * @var string[]
   */
  public $titleLocArgs;
  /**
   * The key to the title string in the app's string resources to use to
   * localize the title text to the user's current localization. See [String
   * Resources](https://goo.gl/NdFZGI) for more information.
   *
   * @var string
   */
  public $titleLocKey;
  /**
   * Set the vibration pattern to use. Pass in an array of
   * [protobuf.Duration](https://developers.google.com/protocol-
   * buffers/docs/reference/google.protobuf#google.protobuf.Duration) to turn on
   * or off the vibrator. The first value indicates the `Duration` to wait
   * before turning the vibrator on. The next value indicates the `Duration` to
   * keep the vibrator on. Subsequent values alternate between `Duration` to
   * turn the vibrator off and to turn the vibrator on. If `vibrate_timings` is
   * set and `default_vibrate_timings` is set to `true`, the default value is
   * used instead of the user-specified `vibrate_timings`.
   *
   * @var string[]
   */
  public $vibrateTimings;
  /**
   * Set the [Notification.visibility](https://developer.android.com/reference/a
   * ndroid/app/Notification.html#visibility) of the notification.
   *
   * @var string
   */
  public $visibility;

  /**
   * The notification's body text. If present, it will override
   * google.firebase.fcm.v1.Notification.body.
   *
   * @param string $body
   */
  public function setBody($body)
  {
    $this->body = $body;
  }
  /**
   * @return string
   */
  public function getBody()
  {
    return $this->body;
  }
  /**
   * Variable string values to be used in place of the format specifiers in
   * body_loc_key to use to localize the body text to the user's current
   * localization. See [Formatting and Styling](https://goo.gl/MalYE3) for more
   * information.
   *
   * @param string[] $bodyLocArgs
   */
  public function setBodyLocArgs($bodyLocArgs)
  {
    $this->bodyLocArgs = $bodyLocArgs;
  }
  /**
   * @return string[]
   */
  public function getBodyLocArgs()
  {
    return $this->bodyLocArgs;
  }
  /**
   * The key to the body string in the app's string resources to use to localize
   * the body text to the user's current localization. See [String
   * Resources](https://goo.gl/NdFZGI) for more information.
   *
   * @param string $bodyLocKey
   */
  public function setBodyLocKey($bodyLocKey)
  {
    $this->bodyLocKey = $bodyLocKey;
  }
  /**
   * @return string
   */
  public function getBodyLocKey()
  {
    return $this->bodyLocKey;
  }
  /**
   * If set, display notifications delivered to the device will be handled by
   * the app instead of the proxy.
   *
   * @deprecated
   * @param bool $bypassProxyNotification
   */
  public function setBypassProxyNotification($bypassProxyNotification)
  {
    $this->bypassProxyNotification = $bypassProxyNotification;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getBypassProxyNotification()
  {
    return $this->bypassProxyNotification;
  }
  /**
   * The [notification's channel id](https://developer.android.com/guide/topics/
   * ui/notifiers/notifications#ManageChannels) (new in Android O). The app must
   * create a channel with this channel ID before any notification with this
   * channel ID is received. If you don't send this channel ID in the request,
   * or if the channel ID provided has not yet been created by the app, FCM uses
   * the channel ID specified in the app manifest.
   *
   * @param string $channelId
   */
  public function setChannelId($channelId)
  {
    $this->channelId = $channelId;
  }
  /**
   * @return string
   */
  public function getChannelId()
  {
    return $this->channelId;
  }
  /**
   * The action associated with a user click on the notification. If specified,
   * an activity with a matching intent filter is launched when a user clicks on
   * the notification.
   *
   * @param string $clickAction
   */
  public function setClickAction($clickAction)
  {
    $this->clickAction = $clickAction;
  }
  /**
   * @return string
   */
  public function getClickAction()
  {
    return $this->clickAction;
  }
  /**
   * The notification's icon color, expressed in #rrggbb format.
   *
   * @param string $color
   */
  public function setColor($color)
  {
    $this->color = $color;
  }
  /**
   * @return string
   */
  public function getColor()
  {
    return $this->color;
  }
  /**
   * If set to true, use the Android framework's default LED light settings for
   * the notification. Default values are specified in [config.xml](https://andr
   * oid.googlesource.com/platform/frameworks/base/+/master/core/res/res/values/
   * config.xml). If `default_light_settings` is set to true and
   * `light_settings` is also set, the user-specified `light_settings` is used
   * instead of the default value.
   *
   * @param bool $defaultLightSettings
   */
  public function setDefaultLightSettings($defaultLightSettings)
  {
    $this->defaultLightSettings = $defaultLightSettings;
  }
  /**
   * @return bool
   */
  public function getDefaultLightSettings()
  {
    return $this->defaultLightSettings;
  }
  /**
   * If set to true, use the Android framework's default sound for the
   * notification. Default values are specified in [config.xml](https://android.
   * googlesource.com/platform/frameworks/base/+/master/core/res/res/values/conf
   * ig.xml).
   *
   * @param bool $defaultSound
   */
  public function setDefaultSound($defaultSound)
  {
    $this->defaultSound = $defaultSound;
  }
  /**
   * @return bool
   */
  public function getDefaultSound()
  {
    return $this->defaultSound;
  }
  /**
   * If set to true, use the Android framework's default vibrate pattern for the
   * notification. Default values are specified in [config.xml](https://android.
   * googlesource.com/platform/frameworks/base/+/master/core/res/res/values/conf
   * ig.xml). If `default_vibrate_timings` is set to true and `vibrate_timings`
   * is also set, the default value is used instead of the user-specified
   * `vibrate_timings`.
   *
   * @param bool $defaultVibrateTimings
   */
  public function setDefaultVibrateTimings($defaultVibrateTimings)
  {
    $this->defaultVibrateTimings = $defaultVibrateTimings;
  }
  /**
   * @return bool
   */
  public function getDefaultVibrateTimings()
  {
    return $this->defaultVibrateTimings;
  }
  /**
   * Set the time that the event in the notification occurred. Notifications in
   * the panel are sorted by this time. A point in time is represented using
   * [protobuf.Timestamp](https://developers.google.com/protocol-
   * buffers/docs/reference/java/com/google/protobuf/Timestamp).
   *
   * @param string $eventTime
   */
  public function setEventTime($eventTime)
  {
    $this->eventTime = $eventTime;
  }
  /**
   * @return string
   */
  public function getEventTime()
  {
    return $this->eventTime;
  }
  /**
   * The notification's icon. Sets the notification icon to myicon for drawable
   * resource myicon. If you don't send this key in the request, FCM displays
   * the launcher icon specified in your app manifest.
   *
   * @param string $icon
   */
  public function setIcon($icon)
  {
    $this->icon = $icon;
  }
  /**
   * @return string
   */
  public function getIcon()
  {
    return $this->icon;
  }
  /**
   * Contains the URL of an image that is going to be displayed in a
   * notification. If present, it will override
   * google.firebase.fcm.v1.Notification.image.
   *
   * @param string $image
   */
  public function setImage($image)
  {
    $this->image = $image;
  }
  /**
   * @return string
   */
  public function getImage()
  {
    return $this->image;
  }
  /**
   * Settings to control the notification's LED blinking rate and color if LED
   * is available on the device. The total blinking time is controlled by the
   * OS.
   *
   * @param LightSettings $lightSettings
   */
  public function setLightSettings(LightSettings $lightSettings)
  {
    $this->lightSettings = $lightSettings;
  }
  /**
   * @return LightSettings
   */
  public function getLightSettings()
  {
    return $this->lightSettings;
  }
  /**
   * Set whether or not this notification is relevant only to the current
   * device. Some notifications can be bridged to other devices for remote
   * display, such as a Wear OS watch. This hint can be set to recommend this
   * notification not be bridged. See [Wear OS guides](https://developer.android
   * .com/training/wearables/notifications/bridger#existing-method-of-
   * preventing-bridging)
   *
   * @param bool $localOnly
   */
  public function setLocalOnly($localOnly)
  {
    $this->localOnly = $localOnly;
  }
  /**
   * @return bool
   */
  public function getLocalOnly()
  {
    return $this->localOnly;
  }
  /**
   * Sets the number of items this notification represents. May be displayed as
   * a badge count for launchers that support badging.See [Notification
   * Badge](https://developer.android.com/training/notify-user/badges). For
   * example, this might be useful if you're using just one notification to
   * represent multiple new messages but you want the count here to represent
   * the number of total new messages. If zero or unspecified, systems that
   * support badging use the default, which is to increment a number displayed
   * on the long-press menu each time a new notification arrives.
   *
   * @param int $notificationCount
   */
  public function setNotificationCount($notificationCount)
  {
    $this->notificationCount = $notificationCount;
  }
  /**
   * @return int
   */
  public function getNotificationCount()
  {
    return $this->notificationCount;
  }
  /**
   * Set the relative priority for this notification. Priority is an indication
   * of how much of the user's attention should be consumed by this
   * notification. Low-priority notifications may be hidden from the user in
   * certain situations, while the user might be interrupted for a higher-
   * priority notification. The effect of setting the same priorities may differ
   * slightly on different platforms. Note this priority differs from
   * `AndroidMessagePriority`. This priority is processed by the client after
   * the message has been delivered, whereas [AndroidMessagePriority](https://fi
   * rebase.google.com/docs/reference/fcm/rest/v1/projects.messages#androidmessa
   * gepriority) is an FCM concept that controls when the message is delivered.
   *
   * Accepted values: PRIORITY_UNSPECIFIED, PRIORITY_MIN, PRIORITY_LOW,
   * PRIORITY_DEFAULT, PRIORITY_HIGH, PRIORITY_MAX
   *
   * @param self::NOTIFICATION_PRIORITY_* $notificationPriority
   */
  public function setNotificationPriority($notificationPriority)
  {
    $this->notificationPriority = $notificationPriority;
  }
  /**
   * @return self::NOTIFICATION_PRIORITY_*
   */
  public function getNotificationPriority()
  {
    return $this->notificationPriority;
  }
  /**
   * Setting to control when a notification may be proxied.
   *
   * Accepted values: PROXY_UNSPECIFIED, ALLOW, DENY, IF_PRIORITY_LOWERED
   *
   * @param self::PROXY_* $proxy
   */
  public function setProxy($proxy)
  {
    $this->proxy = $proxy;
  }
  /**
   * @return self::PROXY_*
   */
  public function getProxy()
  {
    return $this->proxy;
  }
  /**
   * The sound to play when the device receives the notification. Supports
   * "default" or the filename of a sound resource bundled in the app. Sound
   * files must reside in /res/raw/.
   *
   * @param string $sound
   */
  public function setSound($sound)
  {
    $this->sound = $sound;
  }
  /**
   * @return string
   */
  public function getSound()
  {
    return $this->sound;
  }
  /**
   * When set to false or unset, the notification is automatically dismissed
   * when the user clicks it in the panel. When set to true, the notification
   * persists even when the user clicks it.
   *
   * @param bool $sticky
   */
  public function setSticky($sticky)
  {
    $this->sticky = $sticky;
  }
  /**
   * @return bool
   */
  public function getSticky()
  {
    return $this->sticky;
  }
  /**
   * Identifier used to replace existing notifications in the notification
   * drawer. If not specified, each request creates a new notification. If
   * specified and a notification with the same tag is already being shown, the
   * new notification replaces the existing one in the notification drawer.
   *
   * @param string $tag
   */
  public function setTag($tag)
  {
    $this->tag = $tag;
  }
  /**
   * @return string
   */
  public function getTag()
  {
    return $this->tag;
  }
  /**
   * Sets the "ticker" text, which is sent to accessibility services. Prior to
   * API level 21 (`Lollipop`), sets the text that is displayed in the status
   * bar when the notification first arrives.
   *
   * @param string $ticker
   */
  public function setTicker($ticker)
  {
    $this->ticker = $ticker;
  }
  /**
   * @return string
   */
  public function getTicker()
  {
    return $this->ticker;
  }
  /**
   * The notification's title. If present, it will override
   * google.firebase.fcm.v1.Notification.title.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * Variable string values to be used in place of the format specifiers in
   * title_loc_key to use to localize the title text to the user's current
   * localization. See [Formatting and Styling](https://goo.gl/MalYE3) for more
   * information.
   *
   * @param string[] $titleLocArgs
   */
  public function setTitleLocArgs($titleLocArgs)
  {
    $this->titleLocArgs = $titleLocArgs;
  }
  /**
   * @return string[]
   */
  public function getTitleLocArgs()
  {
    return $this->titleLocArgs;
  }
  /**
   * The key to the title string in the app's string resources to use to
   * localize the title text to the user's current localization. See [String
   * Resources](https://goo.gl/NdFZGI) for more information.
   *
   * @param string $titleLocKey
   */
  public function setTitleLocKey($titleLocKey)
  {
    $this->titleLocKey = $titleLocKey;
  }
  /**
   * @return string
   */
  public function getTitleLocKey()
  {
    return $this->titleLocKey;
  }
  /**
   * Set the vibration pattern to use. Pass in an array of
   * [protobuf.Duration](https://developers.google.com/protocol-
   * buffers/docs/reference/google.protobuf#google.protobuf.Duration) to turn on
   * or off the vibrator. The first value indicates the `Duration` to wait
   * before turning the vibrator on. The next value indicates the `Duration` to
   * keep the vibrator on. Subsequent values alternate between `Duration` to
   * turn the vibrator off and to turn the vibrator on. If `vibrate_timings` is
   * set and `default_vibrate_timings` is set to `true`, the default value is
   * used instead of the user-specified `vibrate_timings`.
   *
   * @param string[] $vibrateTimings
   */
  public function setVibrateTimings($vibrateTimings)
  {
    $this->vibrateTimings = $vibrateTimings;
  }
  /**
   * @return string[]
   */
  public function getVibrateTimings()
  {
    return $this->vibrateTimings;
  }
  /**
   * Set the [Notification.visibility](https://developer.android.com/reference/a
   * ndroid/app/Notification.html#visibility) of the notification.
   *
   * Accepted values: VISIBILITY_UNSPECIFIED, PRIVATE, PUBLIC, SECRET
   *
   * @param self::VISIBILITY_* $visibility
   */
  public function setVisibility($visibility)
  {
    $this->visibility = $visibility;
  }
  /**
   * @return self::VISIBILITY_*
   */
  public function getVisibility()
  {
    return $this->visibility;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AndroidNotification::class, 'Google_Service_FirebaseCloudMessaging_AndroidNotification');
