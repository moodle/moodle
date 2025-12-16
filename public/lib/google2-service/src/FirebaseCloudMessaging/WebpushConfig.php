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

class WebpushConfig extends \Google\Model
{
  /**
   * Arbitrary key/value payload. If present, it will override
   * google.firebase.fcm.v1.Message.data.
   *
   * @var string[]
   */
  public $data;
  protected $fcmOptionsType = WebpushFcmOptions::class;
  protected $fcmOptionsDataType = '';
  /**
   * HTTP headers defined in webpush protocol. Refer to [Webpush
   * protocol](https://tools.ietf.org/html/rfc8030#section-5) for supported
   * headers, e.g. "TTL": "15".
   *
   * @var string[]
   */
  public $headers;
  /**
   * Web Notification options as a JSON object. Supports Notification instance
   * properties as defined in [Web Notification
   * API](https://developer.mozilla.org/en-US/docs/Web/API/Notification). If
   * present, "title" and "body" fields override
   * [google.firebase.fcm.v1.Notification.title] and
   * [google.firebase.fcm.v1.Notification.body].
   *
   * @var array[]
   */
  public $notification;

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
   * Options for features provided by the FCM SDK for Web.
   *
   * @param WebpushFcmOptions $fcmOptions
   */
  public function setFcmOptions(WebpushFcmOptions $fcmOptions)
  {
    $this->fcmOptions = $fcmOptions;
  }
  /**
   * @return WebpushFcmOptions
   */
  public function getFcmOptions()
  {
    return $this->fcmOptions;
  }
  /**
   * HTTP headers defined in webpush protocol. Refer to [Webpush
   * protocol](https://tools.ietf.org/html/rfc8030#section-5) for supported
   * headers, e.g. "TTL": "15".
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
   * Web Notification options as a JSON object. Supports Notification instance
   * properties as defined in [Web Notification
   * API](https://developer.mozilla.org/en-US/docs/Web/API/Notification). If
   * present, "title" and "body" fields override
   * [google.firebase.fcm.v1.Notification.title] and
   * [google.firebase.fcm.v1.Notification.body].
   *
   * @param array[] $notification
   */
  public function setNotification($notification)
  {
    $this->notification = $notification;
  }
  /**
   * @return array[]
   */
  public function getNotification()
  {
    return $this->notification;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WebpushConfig::class, 'Google_Service_FirebaseCloudMessaging_WebpushConfig');
