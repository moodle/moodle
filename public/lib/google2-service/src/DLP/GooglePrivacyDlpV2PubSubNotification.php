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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2PubSubNotification extends \Google\Model
{
  /**
   * Unused.
   */
  public const DETAIL_OF_MESSAGE_DETAIL_LEVEL_UNSPECIFIED = 'DETAIL_LEVEL_UNSPECIFIED';
  /**
   * The full table data profile.
   */
  public const DETAIL_OF_MESSAGE_TABLE_PROFILE = 'TABLE_PROFILE';
  /**
   * The name of the profiled resource.
   */
  public const DETAIL_OF_MESSAGE_RESOURCE_NAME = 'RESOURCE_NAME';
  /**
   * The full file store data profile.
   */
  public const DETAIL_OF_MESSAGE_FILE_STORE_PROFILE = 'FILE_STORE_PROFILE';
  /**
   * Unused.
   */
  public const EVENT_EVENT_TYPE_UNSPECIFIED = 'EVENT_TYPE_UNSPECIFIED';
  /**
   * New profile (not a re-profile).
   */
  public const EVENT_NEW_PROFILE = 'NEW_PROFILE';
  /**
   * One of the following profile metrics changed: Data risk score, Sensitivity
   * score, Resource visibility, Encryption type, Predicted infoTypes, Other
   * infoTypes
   */
  public const EVENT_CHANGED_PROFILE = 'CHANGED_PROFILE';
  /**
   * Table data risk score or sensitivity score increased.
   */
  public const EVENT_SCORE_INCREASED = 'SCORE_INCREASED';
  /**
   * A user (non-internal) error occurred.
   */
  public const EVENT_ERROR_CHANGED = 'ERROR_CHANGED';
  /**
   * How much data to include in the Pub/Sub message. If the user wishes to
   * limit the size of the message, they can use resource_name and fetch the
   * profile fields they wish to. Per table profile (not per column).
   *
   * @var string
   */
  public $detailOfMessage;
  /**
   * The type of event that triggers a Pub/Sub. At most one `PubSubNotification`
   * per EventType is permitted.
   *
   * @var string
   */
  public $event;
  protected $pubsubConditionType = GooglePrivacyDlpV2DataProfilePubSubCondition::class;
  protected $pubsubConditionDataType = '';
  /**
   * Cloud Pub/Sub topic to send notifications to. Format is
   * projects/{project}/topics/{topic}.
   *
   * @var string
   */
  public $topic;

  /**
   * How much data to include in the Pub/Sub message. If the user wishes to
   * limit the size of the message, they can use resource_name and fetch the
   * profile fields they wish to. Per table profile (not per column).
   *
   * Accepted values: DETAIL_LEVEL_UNSPECIFIED, TABLE_PROFILE, RESOURCE_NAME,
   * FILE_STORE_PROFILE
   *
   * @param self::DETAIL_OF_MESSAGE_* $detailOfMessage
   */
  public function setDetailOfMessage($detailOfMessage)
  {
    $this->detailOfMessage = $detailOfMessage;
  }
  /**
   * @return self::DETAIL_OF_MESSAGE_*
   */
  public function getDetailOfMessage()
  {
    return $this->detailOfMessage;
  }
  /**
   * The type of event that triggers a Pub/Sub. At most one `PubSubNotification`
   * per EventType is permitted.
   *
   * Accepted values: EVENT_TYPE_UNSPECIFIED, NEW_PROFILE, CHANGED_PROFILE,
   * SCORE_INCREASED, ERROR_CHANGED
   *
   * @param self::EVENT_* $event
   */
  public function setEvent($event)
  {
    $this->event = $event;
  }
  /**
   * @return self::EVENT_*
   */
  public function getEvent()
  {
    return $this->event;
  }
  /**
   * Conditions (e.g., data risk or sensitivity level) for triggering a Pub/Sub.
   *
   * @param GooglePrivacyDlpV2DataProfilePubSubCondition $pubsubCondition
   */
  public function setPubsubCondition(GooglePrivacyDlpV2DataProfilePubSubCondition $pubsubCondition)
  {
    $this->pubsubCondition = $pubsubCondition;
  }
  /**
   * @return GooglePrivacyDlpV2DataProfilePubSubCondition
   */
  public function getPubsubCondition()
  {
    return $this->pubsubCondition;
  }
  /**
   * Cloud Pub/Sub topic to send notifications to. Format is
   * projects/{project}/topics/{topic}.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2PubSubNotification::class, 'Google_Service_DLP_GooglePrivacyDlpV2PubSubNotification');
