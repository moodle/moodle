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

class AndroidConfig extends \Google\Model
{
  /**
   * Default priority for data messages. Normal priority messages won't open
   * network connections on a sleeping device, and their delivery may be delayed
   * to conserve the battery. For less time-sensitive messages, such as
   * notifications of new email or other data to sync, choose normal delivery
   * priority.
   */
  public const PRIORITY_NORMAL = 'NORMAL';
  /**
   * Default priority for notification messages. FCM attempts to deliver high
   * priority messages immediately, allowing the FCM service to wake a sleeping
   * device when possible and open a network connection to your app server. Apps
   * with instant messaging, chat, or voice call alerts, for example, generally
   * need to open a network connection and make sure FCM delivers the message to
   * the device without delay. Set high priority if the message is time-critical
   * and requires the user's immediate interaction, but beware that setting your
   * messages to high priority contributes more to battery drain compared with
   * normal priority messages.
   */
  public const PRIORITY_HIGH = 'HIGH';
  /**
   * Optional. If set to true, messages will be allowed to be delivered to the
   * app while the device is in bandwidth constrained mode. This should only be
   * enabled when the app has been tested to properly handle messages in
   * bandwidth constrained mode.
   *
   * @var bool
   */
  public $bandwidthConstrainedOk;
  /**
   * An identifier of a group of messages that can be collapsed, so that only
   * the last message gets sent when delivery can be resumed. A maximum of 4
   * different collapse keys is allowed at any given time.
   *
   * @var string
   */
  public $collapseKey;
  /**
   * Arbitrary key/value payload. If present, it will override
   * google.firebase.fcm.v1.Message.data.
   *
   * @var string[]
   */
  public $data;
  /**
   * Optional. If set to true, messages will be allowed to be delivered to the
   * app while the device is in direct boot mode. See [Support Direct Boot
   * mode](https://developer.android.com/training/articles/direct-boot).
   *
   * @var bool
   */
  public $directBootOk;
  protected $fcmOptionsType = AndroidFcmOptions::class;
  protected $fcmOptionsDataType = '';
  protected $notificationType = AndroidNotification::class;
  protected $notificationDataType = '';
  /**
   * Message priority. Can take "normal" and "high" values. For more
   * information, see [Setting the priority of a
   * message](https://goo.gl/GjONJv).
   *
   * @var string
   */
  public $priority;
  /**
   * Package name of the application where the registration token must match in
   * order to receive the message.
   *
   * @var string
   */
  public $restrictedPackageName;
  /**
   * Optional. If set to true, messages will be allowed to be delivered to the
   * app while the device is connected over a restricted satellite network. This
   * should only be enabled for messages that can be handled over a restricted
   * satellite network and only for apps that are enabled to work over a
   * restricted satellite network. Note that the ability of the app to connect
   * to a restricted satellite network is dependent on the carrier's settings
   * and the device model.
   *
   * @var bool
   */
  public $restrictedSatelliteOk;
  /**
   * How long (in seconds) the message should be kept in FCM storage if the
   * device is offline. The maximum time to live supported is 4 weeks, and the
   * default value is 4 weeks if not set. Set it to 0 if want to send the
   * message immediately. In JSON format, the Duration type is encoded as a
   * string rather than an object, where the string ends in the suffix "s"
   * (indicating seconds) and is preceded by the number of seconds, with
   * nanoseconds expressed as fractional seconds. For example, 3 seconds with 0
   * nanoseconds should be encoded in JSON format as "3s", while 3 seconds and 1
   * nanosecond should be expressed in JSON format as "3.000000001s". The ttl
   * will be rounded down to the nearest second.
   *
   * @var string
   */
  public $ttl;

  /**
   * Optional. If set to true, messages will be allowed to be delivered to the
   * app while the device is in bandwidth constrained mode. This should only be
   * enabled when the app has been tested to properly handle messages in
   * bandwidth constrained mode.
   *
   * @param bool $bandwidthConstrainedOk
   */
  public function setBandwidthConstrainedOk($bandwidthConstrainedOk)
  {
    $this->bandwidthConstrainedOk = $bandwidthConstrainedOk;
  }
  /**
   * @return bool
   */
  public function getBandwidthConstrainedOk()
  {
    return $this->bandwidthConstrainedOk;
  }
  /**
   * An identifier of a group of messages that can be collapsed, so that only
   * the last message gets sent when delivery can be resumed. A maximum of 4
   * different collapse keys is allowed at any given time.
   *
   * @param string $collapseKey
   */
  public function setCollapseKey($collapseKey)
  {
    $this->collapseKey = $collapseKey;
  }
  /**
   * @return string
   */
  public function getCollapseKey()
  {
    return $this->collapseKey;
  }
  /**
   * Arbitrary key/value payload. If present, it will override
   * google.firebase.fcm.v1.Message.data.
   *
   * @param string[] $data
   */
  public function setData($data)
  {
    $this->data = $data;
  }
  /**
   * @return string[]
   */
  public function getData()
  {
    return $this->data;
  }
  /**
   * Optional. If set to true, messages will be allowed to be delivered to the
   * app while the device is in direct boot mode. See [Support Direct Boot
   * mode](https://developer.android.com/training/articles/direct-boot).
   *
   * @param bool $directBootOk
   */
  public function setDirectBootOk($directBootOk)
  {
    $this->directBootOk = $directBootOk;
  }
  /**
   * @return bool
   */
  public function getDirectBootOk()
  {
    return $this->directBootOk;
  }
  /**
   * Options for features provided by the FCM SDK for Android.
   *
   * @param AndroidFcmOptions $fcmOptions
   */
  public function setFcmOptions(AndroidFcmOptions $fcmOptions)
  {
    $this->fcmOptions = $fcmOptions;
  }
  /**
   * @return AndroidFcmOptions
   */
  public function getFcmOptions()
  {
    return $this->fcmOptions;
  }
  /**
   * Notification to send to android devices.
   *
   * @param AndroidNotification $notification
   */
  public function setNotification(AndroidNotification $notification)
  {
    $this->notification = $notification;
  }
  /**
   * @return AndroidNotification
   */
  public function getNotification()
  {
    return $this->notification;
  }
  /**
   * Message priority. Can take "normal" and "high" values. For more
   * information, see [Setting the priority of a
   * message](https://goo.gl/GjONJv).
   *
   * Accepted values: NORMAL, HIGH
   *
   * @param self::PRIORITY_* $priority
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;
  }
  /**
   * @return self::PRIORITY_*
   */
  public function getPriority()
  {
    return $this->priority;
  }
  /**
   * Package name of the application where the registration token must match in
   * order to receive the message.
   *
   * @param string $restrictedPackageName
   */
  public function setRestrictedPackageName($restrictedPackageName)
  {
    $this->restrictedPackageName = $restrictedPackageName;
  }
  /**
   * @return string
   */
  public function getRestrictedPackageName()
  {
    return $this->restrictedPackageName;
  }
  /**
   * Optional. If set to true, messages will be allowed to be delivered to the
   * app while the device is connected over a restricted satellite network. This
   * should only be enabled for messages that can be handled over a restricted
   * satellite network and only for apps that are enabled to work over a
   * restricted satellite network. Note that the ability of the app to connect
   * to a restricted satellite network is dependent on the carrier's settings
   * and the device model.
   *
   * @param bool $restrictedSatelliteOk
   */
  public function setRestrictedSatelliteOk($restrictedSatelliteOk)
  {
    $this->restrictedSatelliteOk = $restrictedSatelliteOk;
  }
  /**
   * @return bool
   */
  public function getRestrictedSatelliteOk()
  {
    return $this->restrictedSatelliteOk;
  }
  /**
   * How long (in seconds) the message should be kept in FCM storage if the
   * device is offline. The maximum time to live supported is 4 weeks, and the
   * default value is 4 weeks if not set. Set it to 0 if want to send the
   * message immediately. In JSON format, the Duration type is encoded as a
   * string rather than an object, where the string ends in the suffix "s"
   * (indicating seconds) and is preceded by the number of seconds, with
   * nanoseconds expressed as fractional seconds. For example, 3 seconds with 0
   * nanoseconds should be encoded in JSON format as "3s", while 3 seconds and 1
   * nanosecond should be expressed in JSON format as "3.000000001s". The ttl
   * will be rounded down to the nearest second.
   *
   * @param string $ttl
   */
  public function setTtl($ttl)
  {
    $this->ttl = $ttl;
  }
  /**
   * @return string
   */
  public function getTtl()
  {
    return $this->ttl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AndroidConfig::class, 'Google_Service_FirebaseCloudMessaging_AndroidConfig');
