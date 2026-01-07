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

namespace Google\Service\Storagetransfer;

class NotificationConfig extends \Google\Collection
{
  /**
   * Illegal value, to avoid allowing a default.
   */
  public const PAYLOAD_FORMAT_PAYLOAD_FORMAT_UNSPECIFIED = 'PAYLOAD_FORMAT_UNSPECIFIED';
  /**
   * No payload is included with the notification.
   */
  public const PAYLOAD_FORMAT_NONE = 'NONE';
  /**
   * `TransferOperation` is [formatted as a JSON
   * response](https://developers.google.com/protocol-buffers/docs/proto3#json),
   * in application/json.
   */
  public const PAYLOAD_FORMAT_JSON = 'JSON';
  protected $collection_key = 'eventTypes';
  /**
   * Event types for which a notification is desired. If empty, send
   * notifications for all event types.
   *
   * @var string[]
   */
  public $eventTypes;
  /**
   * Required. The desired format of the notification message payloads.
   *
   * @var string
   */
  public $payloadFormat;
  /**
   * Required. The `Topic.name` of the Pub/Sub topic to which to publish
   * notifications. Must be of the format: `projects/{project}/topics/{topic}`.
   * Not matching this format results in an INVALID_ARGUMENT error.
   *
   * @var string
   */
  public $pubsubTopic;

  /**
   * Event types for which a notification is desired. If empty, send
   * notifications for all event types.
   *
   * @param string[] $eventTypes
   */
  public function setEventTypes($eventTypes)
  {
    $this->eventTypes = $eventTypes;
  }
  /**
   * @return string[]
   */
  public function getEventTypes()
  {
    return $this->eventTypes;
  }
  /**
   * Required. The desired format of the notification message payloads.
   *
   * Accepted values: PAYLOAD_FORMAT_UNSPECIFIED, NONE, JSON
   *
   * @param self::PAYLOAD_FORMAT_* $payloadFormat
   */
  public function setPayloadFormat($payloadFormat)
  {
    $this->payloadFormat = $payloadFormat;
  }
  /**
   * @return self::PAYLOAD_FORMAT_*
   */
  public function getPayloadFormat()
  {
    return $this->payloadFormat;
  }
  /**
   * Required. The `Topic.name` of the Pub/Sub topic to which to publish
   * notifications. Must be of the format: `projects/{project}/topics/{topic}`.
   * Not matching this format results in an INVALID_ARGUMENT error.
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
class_alias(NotificationConfig::class, 'Google_Service_Storagetransfer_NotificationConfig');
