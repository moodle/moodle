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

namespace Google\Service\Fcmdata;

class GoogleFirebaseFcmDataV1beta1MessageOutcomePercents extends \Google\Model
{
  /**
   * The percentage of accepted messages that were
   * [collapsed](https://firebase.google.com/docs/cloud-messaging/concept-
   * options#collapsible_and_non-collapsible_messages) by another message.
   *
   * @var float
   */
  public $collapsed;
  /**
   * The percentage of all accepted messages that were successfully delivered to
   * the device.
   *
   * @var float
   */
  public $delivered;
  /**
   * The percentage of accepted messages that were dropped because the
   * application was force stopped on the device at the time of delivery and
   * retries were unsuccessful.
   *
   * @var float
   */
  public $droppedAppForceStopped;
  /**
   * The percentage of accepted messages that were dropped because the target
   * device is inactive. FCM will drop messages if the target device is deemed
   * inactive by our servers. If a device does reconnect, we call
   * [OnDeletedMessages()](https://firebase.google.com/docs/cloud-
   * messaging/android/receive#override-ondeletedmessages) in our SDK instead of
   * delivering the messages.
   *
   * @var float
   */
  public $droppedDeviceInactive;
  /**
   * The percentage of accepted messages that were dropped due to [too many
   * undelivered non-collapsible
   * messages](https://firebase.google.com/docs/cloud-messaging/concept-
   * options#collapsible_and_non-collapsible_messages). Specifically, each app
   * instance can only have 100 pending messages stored on our servers for a
   * device which is disconnected. When that device reconnects, those messages
   * are delivered. When there are more than the maximum pending messages, we
   * call [OnDeletedMessages()](https://firebase.google.com/docs/cloud-
   * messaging/android/receive#override-ondeletedmessages) in our SDK instead of
   * delivering the messages.
   *
   * @var float
   */
  public $droppedTooManyPendingMessages;
  /**
   * The percentage of accepted messages that expired because [Time To Live
   * (TTL)](https://firebase.google.com/docs/cloud-messaging/concept-
   * options#ttl) elapsed before the target device reconnected.
   *
   * @var float
   */
  public $droppedTtlExpired;
  /**
   * The percentage of messages accepted on this day that were not dropped and
   * not delivered, due to the device being disconnected (as of the end of the
   * America/Los_Angeles day when the message was sent to FCM). A portion of
   * these messages will be delivered the next day when the device connects but
   * others may be destined to devices that ultimately never reconnect.
   *
   * @var float
   */
  public $pending;

  /**
   * The percentage of accepted messages that were
   * [collapsed](https://firebase.google.com/docs/cloud-messaging/concept-
   * options#collapsible_and_non-collapsible_messages) by another message.
   *
   * @param float $collapsed
   */
  public function setCollapsed($collapsed)
  {
    $this->collapsed = $collapsed;
  }
  /**
   * @return float
   */
  public function getCollapsed()
  {
    return $this->collapsed;
  }
  /**
   * The percentage of all accepted messages that were successfully delivered to
   * the device.
   *
   * @param float $delivered
   */
  public function setDelivered($delivered)
  {
    $this->delivered = $delivered;
  }
  /**
   * @return float
   */
  public function getDelivered()
  {
    return $this->delivered;
  }
  /**
   * The percentage of accepted messages that were dropped because the
   * application was force stopped on the device at the time of delivery and
   * retries were unsuccessful.
   *
   * @param float $droppedAppForceStopped
   */
  public function setDroppedAppForceStopped($droppedAppForceStopped)
  {
    $this->droppedAppForceStopped = $droppedAppForceStopped;
  }
  /**
   * @return float
   */
  public function getDroppedAppForceStopped()
  {
    return $this->droppedAppForceStopped;
  }
  /**
   * The percentage of accepted messages that were dropped because the target
   * device is inactive. FCM will drop messages if the target device is deemed
   * inactive by our servers. If a device does reconnect, we call
   * [OnDeletedMessages()](https://firebase.google.com/docs/cloud-
   * messaging/android/receive#override-ondeletedmessages) in our SDK instead of
   * delivering the messages.
   *
   * @param float $droppedDeviceInactive
   */
  public function setDroppedDeviceInactive($droppedDeviceInactive)
  {
    $this->droppedDeviceInactive = $droppedDeviceInactive;
  }
  /**
   * @return float
   */
  public function getDroppedDeviceInactive()
  {
    return $this->droppedDeviceInactive;
  }
  /**
   * The percentage of accepted messages that were dropped due to [too many
   * undelivered non-collapsible
   * messages](https://firebase.google.com/docs/cloud-messaging/concept-
   * options#collapsible_and_non-collapsible_messages). Specifically, each app
   * instance can only have 100 pending messages stored on our servers for a
   * device which is disconnected. When that device reconnects, those messages
   * are delivered. When there are more than the maximum pending messages, we
   * call [OnDeletedMessages()](https://firebase.google.com/docs/cloud-
   * messaging/android/receive#override-ondeletedmessages) in our SDK instead of
   * delivering the messages.
   *
   * @param float $droppedTooManyPendingMessages
   */
  public function setDroppedTooManyPendingMessages($droppedTooManyPendingMessages)
  {
    $this->droppedTooManyPendingMessages = $droppedTooManyPendingMessages;
  }
  /**
   * @return float
   */
  public function getDroppedTooManyPendingMessages()
  {
    return $this->droppedTooManyPendingMessages;
  }
  /**
   * The percentage of accepted messages that expired because [Time To Live
   * (TTL)](https://firebase.google.com/docs/cloud-messaging/concept-
   * options#ttl) elapsed before the target device reconnected.
   *
   * @param float $droppedTtlExpired
   */
  public function setDroppedTtlExpired($droppedTtlExpired)
  {
    $this->droppedTtlExpired = $droppedTtlExpired;
  }
  /**
   * @return float
   */
  public function getDroppedTtlExpired()
  {
    return $this->droppedTtlExpired;
  }
  /**
   * The percentage of messages accepted on this day that were not dropped and
   * not delivered, due to the device being disconnected (as of the end of the
   * America/Los_Angeles day when the message was sent to FCM). A portion of
   * these messages will be delivered the next day when the device connects but
   * others may be destined to devices that ultimately never reconnect.
   *
   * @param float $pending
   */
  public function setPending($pending)
  {
    $this->pending = $pending;
  }
  /**
   * @return float
   */
  public function getPending()
  {
    return $this->pending;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirebaseFcmDataV1beta1MessageOutcomePercents::class, 'Google_Service_Fcmdata_GoogleFirebaseFcmDataV1beta1MessageOutcomePercents');
