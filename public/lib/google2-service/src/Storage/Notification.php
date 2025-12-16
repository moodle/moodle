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

namespace Google\Service\Storage;

class Notification extends \Google\Collection
{
  protected $collection_key = 'event_types';
  protected $internal_gapi_mappings = [
        "customAttributes" => "custom_attributes",
        "eventTypes" => "event_types",
        "objectNamePrefix" => "object_name_prefix",
        "payloadFormat" => "payload_format",
  ];
  /**
   * An optional list of additional attributes to attach to each Cloud PubSub
   * message published for this notification subscription.
   *
   * @var string[]
   */
  public $customAttributes;
  /**
   * HTTP 1.1 Entity tag for this subscription notification.
   *
   * @var string
   */
  public $etag;
  /**
   * If present, only send notifications about listed event types. If empty,
   * sent notifications for all event types.
   *
   * @var string[]
   */
  public $eventTypes;
  /**
   * The ID of the notification.
   *
   * @var string
   */
  public $id;
  /**
   * The kind of item this is. For notifications, this is always
   * storage#notification.
   *
   * @var string
   */
  public $kind;
  /**
   * If present, only apply this notification configuration to object names that
   * begin with this prefix.
   *
   * @var string
   */
  public $objectNamePrefix;
  /**
   * The desired content of the Payload.
   *
   * @var string
   */
  public $payloadFormat;
  /**
   * The canonical URL of this notification.
   *
   * @var string
   */
  public $selfLink;
  /**
   * The Cloud PubSub topic to which this subscription publishes. Formatted as:
   * '//pubsub.googleapis.com/projects/{project-identifier}/topics/{my-topic}'
   *
   * @var string
   */
  public $topic;

  /**
   * An optional list of additional attributes to attach to each Cloud PubSub
   * message published for this notification subscription.
   *
   * @param string[] $customAttributes
   */
  public function setCustomAttributes($customAttributes)
  {
    $this->customAttributes = $customAttributes;
  }
  /**
   * @return string[]
   */
  public function getCustomAttributes()
  {
    return $this->customAttributes;
  }
  /**
   * HTTP 1.1 Entity tag for this subscription notification.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * If present, only send notifications about listed event types. If empty,
   * sent notifications for all event types.
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
   * The ID of the notification.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * The kind of item this is. For notifications, this is always
   * storage#notification.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * If present, only apply this notification configuration to object names that
   * begin with this prefix.
   *
   * @param string $objectNamePrefix
   */
  public function setObjectNamePrefix($objectNamePrefix)
  {
    $this->objectNamePrefix = $objectNamePrefix;
  }
  /**
   * @return string
   */
  public function getObjectNamePrefix()
  {
    return $this->objectNamePrefix;
  }
  /**
   * The desired content of the Payload.
   *
   * @param string $payloadFormat
   */
  public function setPayloadFormat($payloadFormat)
  {
    $this->payloadFormat = $payloadFormat;
  }
  /**
   * @return string
   */
  public function getPayloadFormat()
  {
    return $this->payloadFormat;
  }
  /**
   * The canonical URL of this notification.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * The Cloud PubSub topic to which this subscription publishes. Formatted as:
   * '//pubsub.googleapis.com/projects/{project-identifier}/topics/{my-topic}'
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
class_alias(Notification::class, 'Google_Service_Storage_Notification');
