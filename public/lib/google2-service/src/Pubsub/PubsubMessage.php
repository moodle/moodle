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

namespace Google\Service\Pubsub;

class PubsubMessage extends \Google\Model
{
  /**
   * Optional. Attributes for this message. If this field is empty, the message
   * must contain non-empty data. This can be used to filter messages on the
   * subscription.
   *
   * @var string[]
   */
  public $attributes;
  /**
   * Optional. The message data field. If this field is empty, the message must
   * contain at least one attribute.
   *
   * @var string
   */
  public $data;
  /**
   * ID of this message, assigned by the server when the message is published.
   * Guaranteed to be unique within the topic. This value may be read by a
   * subscriber that receives a `PubsubMessage` via a `Pull` call or a push
   * delivery. It must not be populated by the publisher in a `Publish` call.
   *
   * @var string
   */
  public $messageId;
  /**
   * Optional. If non-empty, identifies related messages for which publish order
   * should be respected. If a `Subscription` has `enable_message_ordering` set
   * to `true`, messages published with the same non-empty `ordering_key` value
   * will be delivered to subscribers in the order in which they are received by
   * the Pub/Sub system. All `PubsubMessage`s published in a given
   * `PublishRequest` must specify the same `ordering_key` value. For more
   * information, see [ordering
   * messages](https://cloud.google.com/pubsub/docs/ordering).
   *
   * @var string
   */
  public $orderingKey;
  /**
   * The time at which the message was published, populated by the server when
   * it receives the `Publish` call. It must not be populated by the publisher
   * in a `Publish` call.
   *
   * @var string
   */
  public $publishTime;

  /**
   * Optional. Attributes for this message. If this field is empty, the message
   * must contain non-empty data. This can be used to filter messages on the
   * subscription.
   *
   * @param string[] $attributes
   */
  public function setAttributes($attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return string[]
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * Optional. The message data field. If this field is empty, the message must
   * contain at least one attribute.
   *
   * @param string $data
   */
  public function setData($data)
  {
    $this->data = $data;
  }
  /**
   * @return string
   */
  public function getData()
  {
    return $this->data;
  }
  /**
   * ID of this message, assigned by the server when the message is published.
   * Guaranteed to be unique within the topic. This value may be read by a
   * subscriber that receives a `PubsubMessage` via a `Pull` call or a push
   * delivery. It must not be populated by the publisher in a `Publish` call.
   *
   * @param string $messageId
   */
  public function setMessageId($messageId)
  {
    $this->messageId = $messageId;
  }
  /**
   * @return string
   */
  public function getMessageId()
  {
    return $this->messageId;
  }
  /**
   * Optional. If non-empty, identifies related messages for which publish order
   * should be respected. If a `Subscription` has `enable_message_ordering` set
   * to `true`, messages published with the same non-empty `ordering_key` value
   * will be delivered to subscribers in the order in which they are received by
   * the Pub/Sub system. All `PubsubMessage`s published in a given
   * `PublishRequest` must specify the same `ordering_key` value. For more
   * information, see [ordering
   * messages](https://cloud.google.com/pubsub/docs/ordering).
   *
   * @param string $orderingKey
   */
  public function setOrderingKey($orderingKey)
  {
    $this->orderingKey = $orderingKey;
  }
  /**
   * @return string
   */
  public function getOrderingKey()
  {
    return $this->orderingKey;
  }
  /**
   * The time at which the message was published, populated by the server when
   * it receives the `Publish` call. It must not be populated by the publisher
   * in a `Publish` call.
   *
   * @param string $publishTime
   */
  public function setPublishTime($publishTime)
  {
    $this->publishTime = $publishTime;
  }
  /**
   * @return string
   */
  public function getPublishTime()
  {
    return $this->publishTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PubsubMessage::class, 'Google_Service_Pubsub_PubsubMessage');
