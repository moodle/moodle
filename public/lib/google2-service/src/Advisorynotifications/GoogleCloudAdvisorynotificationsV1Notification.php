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

namespace Google\Service\Advisorynotifications;

class GoogleCloudAdvisorynotificationsV1Notification extends \Google\Collection
{
  /**
   * Default type
   */
  public const NOTIFICATION_TYPE_NOTIFICATION_TYPE_UNSPECIFIED = 'NOTIFICATION_TYPE_UNSPECIFIED';
  /**
   * Security and privacy advisory notifications
   */
  public const NOTIFICATION_TYPE_NOTIFICATION_TYPE_SECURITY_PRIVACY_ADVISORY = 'NOTIFICATION_TYPE_SECURITY_PRIVACY_ADVISORY';
  /**
   * Sensitive action notifications
   */
  public const NOTIFICATION_TYPE_NOTIFICATION_TYPE_SENSITIVE_ACTIONS = 'NOTIFICATION_TYPE_SENSITIVE_ACTIONS';
  /**
   * General security MSA
   */
  public const NOTIFICATION_TYPE_NOTIFICATION_TYPE_SECURITY_MSA = 'NOTIFICATION_TYPE_SECURITY_MSA';
  /**
   * Threat horizons MSA
   */
  public const NOTIFICATION_TYPE_NOTIFICATION_TYPE_THREAT_HORIZONS = 'NOTIFICATION_TYPE_THREAT_HORIZONS';
  protected $collection_key = 'messages';
  /**
   * Output only. Time the notification was created.
   *
   * @var string
   */
  public $createTime;
  protected $messagesType = GoogleCloudAdvisorynotificationsV1Message::class;
  protected $messagesDataType = 'array';
  /**
   * The resource name of the notification. Format: organizations/{organization}
   * /locations/{location}/notifications/{notification} or
   * projects/{project}/locations/{location}/notifications/{notification}.
   *
   * @var string
   */
  public $name;
  /**
   * Type of notification
   *
   * @var string
   */
  public $notificationType;
  protected $subjectType = GoogleCloudAdvisorynotificationsV1Subject::class;
  protected $subjectDataType = '';

  /**
   * Output only. Time the notification was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * A list of messages in the notification.
   *
   * @param GoogleCloudAdvisorynotificationsV1Message[] $messages
   */
  public function setMessages($messages)
  {
    $this->messages = $messages;
  }
  /**
   * @return GoogleCloudAdvisorynotificationsV1Message[]
   */
  public function getMessages()
  {
    return $this->messages;
  }
  /**
   * The resource name of the notification. Format: organizations/{organization}
   * /locations/{location}/notifications/{notification} or
   * projects/{project}/locations/{location}/notifications/{notification}.
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
   * Type of notification
   *
   * Accepted values: NOTIFICATION_TYPE_UNSPECIFIED,
   * NOTIFICATION_TYPE_SECURITY_PRIVACY_ADVISORY,
   * NOTIFICATION_TYPE_SENSITIVE_ACTIONS, NOTIFICATION_TYPE_SECURITY_MSA,
   * NOTIFICATION_TYPE_THREAT_HORIZONS
   *
   * @param self::NOTIFICATION_TYPE_* $notificationType
   */
  public function setNotificationType($notificationType)
  {
    $this->notificationType = $notificationType;
  }
  /**
   * @return self::NOTIFICATION_TYPE_*
   */
  public function getNotificationType()
  {
    return $this->notificationType;
  }
  /**
   * The subject line of the notification.
   *
   * @param GoogleCloudAdvisorynotificationsV1Subject $subject
   */
  public function setSubject(GoogleCloudAdvisorynotificationsV1Subject $subject)
  {
    $this->subject = $subject;
  }
  /**
   * @return GoogleCloudAdvisorynotificationsV1Subject
   */
  public function getSubject()
  {
    return $this->subject;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAdvisorynotificationsV1Notification::class, 'Google_Service_Advisorynotifications_GoogleCloudAdvisorynotificationsV1Notification');
