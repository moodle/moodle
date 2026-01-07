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

class ApnsConfig extends \Google\Model
{
  protected $fcmOptionsType = ApnsFcmOptions::class;
  protected $fcmOptionsDataType = '';
  /**
   * HTTP request headers defined in Apple Push Notification Service. Refer to
   * [APNs request headers](https://developer.apple.com/documentation/usernotifi
   * cations/setting_up_a_remote_notification_server/sending_notification_reques
   * ts_to_apns) for supported headers such as `apns-expiration` and `apns-
   * priority`. The backend sets a default value for `apns-expiration` of 30
   * days and a default value for `apns-priority` of 10 if not explicitly set.
   *
   * @var string[]
   */
  public $headers;
  /**
   * Optional. [Apple Live Activity](https://developer.apple.com/design/human-
   * interface-guidelines/live-activities) token to send updates to. This token
   * can either be a push token or [push-to-start](https://developer.apple.com/d
   * ocumentation/activitykit/activity/pushtostarttoken) token from Apple. To
   * start, update, or end a live activity remotely using FCM, construct an
   * [`aps
   * payload`](https://developer.apple.com/documentation/activitykit/starting-
   * and-updating-live-activities-with-activitykit-push-notifications#Construct-
   * the-payload-that-starts-a-Live-Activity) and put it in the [`apns.payload`]
   * (https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#A
   * pnsConfig) field.
   *
   * @var string
   */
  public $liveActivityToken;
  /**
   * APNs payload as a JSON object, including both `aps` dictionary and custom
   * payload. See [Payload Key Reference](https://developer.apple.com/documentat
   * ion/usernotifications/setting_up_a_remote_notification_server/generating_a_
   * remote_notification). If present, it overrides
   * google.firebase.fcm.v1.Notification.title and
   * google.firebase.fcm.v1.Notification.body.
   *
   * @var array[]
   */
  public $payload;

  /**
   * Options for features provided by the FCM SDK for iOS.
   *
   * @param ApnsFcmOptions $fcmOptions
   */
  public function setFcmOptions(ApnsFcmOptions $fcmOptions)
  {
    $this->fcmOptions = $fcmOptions;
  }
  /**
   * @return ApnsFcmOptions
   */
  public function getFcmOptions()
  {
    return $this->fcmOptions;
  }
  /**
   * HTTP request headers defined in Apple Push Notification Service. Refer to
   * [APNs request headers](https://developer.apple.com/documentation/usernotifi
   * cations/setting_up_a_remote_notification_server/sending_notification_reques
   * ts_to_apns) for supported headers such as `apns-expiration` and `apns-
   * priority`. The backend sets a default value for `apns-expiration` of 30
   * days and a default value for `apns-priority` of 10 if not explicitly set.
   *
   * @param string[] $headers
   */
  public function setHeaders($headers)
  {
    $this->headers = $headers;
  }
  /**
   * @return string[]
   */
  public function getHeaders()
  {
    return $this->headers;
  }
  /**
   * Optional. [Apple Live Activity](https://developer.apple.com/design/human-
   * interface-guidelines/live-activities) token to send updates to. This token
   * can either be a push token or [push-to-start](https://developer.apple.com/d
   * ocumentation/activitykit/activity/pushtostarttoken) token from Apple. To
   * start, update, or end a live activity remotely using FCM, construct an
   * [`aps
   * payload`](https://developer.apple.com/documentation/activitykit/starting-
   * and-updating-live-activities-with-activitykit-push-notifications#Construct-
   * the-payload-that-starts-a-Live-Activity) and put it in the [`apns.payload`]
   * (https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#A
   * pnsConfig) field.
   *
   * @param string $liveActivityToken
   */
  public function setLiveActivityToken($liveActivityToken)
  {
    $this->liveActivityToken = $liveActivityToken;
  }
  /**
   * @return string
   */
  public function getLiveActivityToken()
  {
    return $this->liveActivityToken;
  }
  /**
   * APNs payload as a JSON object, including both `aps` dictionary and custom
   * payload. See [Payload Key Reference](https://developer.apple.com/documentat
   * ion/usernotifications/setting_up_a_remote_notification_server/generating_a_
   * remote_notification). If present, it overrides
   * google.firebase.fcm.v1.Notification.title and
   * google.firebase.fcm.v1.Notification.body.
   *
   * @param array[] $payload
   */
  public function setPayload($payload)
  {
    $this->payload = $payload;
  }
  /**
   * @return array[]
   */
  public function getPayload()
  {
    return $this->payload;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ApnsConfig::class, 'Google_Service_FirebaseCloudMessaging_ApnsConfig');
