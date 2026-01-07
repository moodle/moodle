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

class ReceivedMessage extends \Google\Model
{
  /**
   * Optional. This ID can be used to acknowledge the received message.
   *
   * @var string
   */
  public $ackId;
  /**
   * Optional. The approximate number of times that Pub/Sub has attempted to
   * deliver the associated message to a subscriber. More precisely, this is 1 +
   * (number of NACKs) + (number of ack_deadline exceeds) for this message. A
   * NACK is any call to ModifyAckDeadline with a 0 deadline. An ack_deadline
   * exceeds event is whenever a message is not acknowledged within
   * ack_deadline. Note that ack_deadline is initially
   * Subscription.ackDeadlineSeconds, but may get extended automatically by the
   * client library. Upon the first delivery of a given message,
   * `delivery_attempt` will have a value of 1. The value is calculated at best
   * effort and is approximate. If a DeadLetterPolicy is not set on the
   * subscription, this will be 0.
   *
   * @var int
   */
  public $deliveryAttempt;
  protected $messageType = PubsubMessage::class;
  protected $messageDataType = '';

  /**
   * Optional. This ID can be used to acknowledge the received message.
   *
   * @param string $ackId
   */
  public function setAckId($ackId)
  {
    $this->ackId = $ackId;
  }
  /**
   * @return string
   */
  public function getAckId()
  {
    return $this->ackId;
  }
  /**
   * Optional. The approximate number of times that Pub/Sub has attempted to
   * deliver the associated message to a subscriber. More precisely, this is 1 +
   * (number of NACKs) + (number of ack_deadline exceeds) for this message. A
   * NACK is any call to ModifyAckDeadline with a 0 deadline. An ack_deadline
   * exceeds event is whenever a message is not acknowledged within
   * ack_deadline. Note that ack_deadline is initially
   * Subscription.ackDeadlineSeconds, but may get extended automatically by the
   * client library. Upon the first delivery of a given message,
   * `delivery_attempt` will have a value of 1. The value is calculated at best
   * effort and is approximate. If a DeadLetterPolicy is not set on the
   * subscription, this will be 0.
   *
   * @param int $deliveryAttempt
   */
  public function setDeliveryAttempt($deliveryAttempt)
  {
    $this->deliveryAttempt = $deliveryAttempt;
  }
  /**
   * @return int
   */
  public function getDeliveryAttempt()
  {
    return $this->deliveryAttempt;
  }
  /**
   * Optional. The message.
   *
   * @param PubsubMessage $message
   */
  public function setMessage(PubsubMessage $message)
  {
    $this->message = $message;
  }
  /**
   * @return PubsubMessage
   */
  public function getMessage()
  {
    return $this->message;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReceivedMessage::class, 'Google_Service_Pubsub_ReceivedMessage');
