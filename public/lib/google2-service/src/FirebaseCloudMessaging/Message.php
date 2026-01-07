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

class Message extends \Google\Model
{
  protected $androidType = AndroidConfig::class;
  protected $androidDataType = '';
  protected $apnsType = ApnsConfig::class;
  protected $apnsDataType = '';
  /**
   * Condition to send a message to, e.g. "'foo' in topics && 'bar' in topics".
   *
   * @var string
   */
  public $condition;
  /**
   * Input only. Arbitrary key/value payload, which must be UTF-8 encoded. The
   * key should not be a reserved word ("from", "message_type", or any word
   * starting with "google." or "gcm.notification."). When sending payloads
   * containing only data fields to iOS devices, only normal priority (`"apns-
   * priority": "5"`) is allowed in
   * [`ApnsConfig`](/docs/reference/fcm/rest/v1/projects.messages#apnsconfig).
   *
   * @var string[]
   */
  public $data;
  protected $fcmOptionsType = FcmOptions::class;
  protected $fcmOptionsDataType = '';
  /**
   * Output Only. The identifier of the message sent, in the format of
   * `projects/messages/{message_id}`.
   *
   * @var string
   */
  public $name;
  protected $notificationType = Notification::class;
  protected $notificationDataType = '';
  /**
   * Registration token to send a message to.
   *
   * @var string
   */
  public $token;
  /**
   * Topic name to send a message to, e.g. "weather". Note: "/topics/" prefix
   * should not be provided.
   *
   * @var string
   */
  public $topic;
  protected $webpushType = WebpushConfig::class;
  protected $webpushDataType = '';

  /**
   * Input only. Android specific options for messages sent through [FCM
   * connection server](https://goo.gl/4GLdUl).
   *
   * @param AndroidConfig $android
   */
  public function setAndroid(AndroidConfig $android)
  {
    $this->android = $android;
  }
  /**
   * @return AndroidConfig
   */
  public function getAndroid()
  {
    return $this->android;
  }
  /**
   * Input only. [Apple Push Notification Service](https://goo.gl/MXRTPa)
   * specific options.
   *
   * @param ApnsConfig $apns
   */
  public function setApns(ApnsConfig $apns)
  {
    $this->apns = $apns;
  }
  /**
   * @return ApnsConfig
   */
  public function getApns()
  {
    return $this->apns;
  }
  /**
   * Condition to send a message to, e.g. "'foo' in topics && 'bar' in topics".
   *
   * @param string $condition
   */
  public function setCondition($condition)
  {
    $this->condition = $condition;
  }
  /**
   * @return string
   */
  public function getCondition()
  {
    return $this->condition;
  }
  /**
   * Input only. Arbitrary key/value payload, which must be UTF-8 encoded. The
   * key should not be a reserved word ("from", "message_type", or any word
   * starting with "google." or "gcm.notification."). When sending payloads
   * containing only data fields to iOS devices, only normal priority (`"apns-
   * priority": "5"`) is allowed in
   * [`ApnsConfig`](/docs/reference/fcm/rest/v1/projects.messages#apnsconfig).
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
   * Input only. Template for FCM SDK feature options to use across all
   * platforms.
   *
   * @param FcmOptions $fcmOptions
   */
  public function setFcmOptions(FcmOptions $fcmOptions)
  {
    $this->fcmOptions = $fcmOptions;
  }
  /**
   * @return FcmOptions
   */
  public function getFcmOptions()
  {
    return $this->fcmOptions;
  }
  /**
   * Output Only. The identifier of the message sent, in the format of
   * `projects/messages/{message_id}`.
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
   * Input only. Basic notification template to use across all platforms.
   *
   * @param Notification $notification
   */
  public function setNotification(Notification $notification)
  {
    $this->notification = $notification;
  }
  /**
   * @return Notification
   */
  public function getNotification()
  {
    return $this->notification;
  }
  /**
   * Registration token to send a message to.
   *
   * @param string $token
   */
  public function setToken($token)
  {
    $this->token = $token;
  }
  /**
   * @return string
   */
  public function getToken()
  {
    return $this->token;
  }
  /**
   * Topic name to send a message to, e.g. "weather". Note: "/topics/" prefix
   * should not be provided.
   *
   * @param string $topic
   */
  public function setTopic($topic)
  {
    $this->topic = $topic;
  }
  /**
   * @return string
   */
  public function getTopic()
  {
    return $this->topic;
  }
  /**
   * Input only. [Webpush protocol](https://tools.ietf.org/html/rfc8030)
   * options.
   *
   * @param WebpushConfig $webpush
   */
  public function setWebpush(WebpushConfig $webpush)
  {
    $this->webpush = $webpush;
  }
  /**
   * @return WebpushConfig
   */
  public function getWebpush()
  {
    return $this->webpush;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Message::class, 'Google_Service_FirebaseCloudMessaging_Message');
