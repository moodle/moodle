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

class GoogleFirebaseFcmDataV1beta1DeliveryPerformancePercents extends \Google\Model
{
  /**
   * The percentage of accepted messages that were delayed because the device
   * was in doze mode. Only [normal priority
   * messages](https://firebase.google.com/docs/cloud-messaging/concept-
   * options#setting-the-priority-of-a-message) should be delayed due to doze
   * mode.
   *
   * @var float
   */
  public $delayedDeviceDoze;
  /**
   * The percentage of accepted messages that were delayed because the target
   * device was not connected at the time of sending. These messages were
   * eventually delivered when the device reconnected.
   *
   * @var float
   */
  public $delayedDeviceOffline;
  /**
   * The percentage of accepted messages that were delayed due to message
   * throttling, such as [collapsible message
   * throttling](https://firebase.google.com/docs/cloud-messaging/concept-
   * options#collapsible_throttling) or [maximum message rate
   * throttling](https://firebase.google.com/docs/cloud-messaging/concept-
   * options#device_throttling).
   *
   * @var float
   */
  public $delayedMessageThrottled;
  /**
   * The percentage of accepted messages that were delayed because the intended
   * device user-profile was [stopped](https://firebase.google.com/docs/cloud-
   * messaging/android/receive#handling_messages) on the target device at the
   * time of the send. The messages were eventually delivered when the user-
   * profile was started again.
   *
   * @var float
   */
  public $delayedUserStopped;
  /**
   * The percentage of accepted messages that were delivered to the device
   * without delay from the FCM system.
   *
   * @var float
   */
  public $deliveredNoDelay;

  /**
   * The percentage of accepted messages that were delayed because the device
   * was in doze mode. Only [normal priority
   * messages](https://firebase.google.com/docs/cloud-messaging/concept-
   * options#setting-the-priority-of-a-message) should be delayed due to doze
   * mode.
   *
   * @param float $delayedDeviceDoze
   */
  public function setDelayedDeviceDoze($delayedDeviceDoze)
  {
    $this->delayedDeviceDoze = $delayedDeviceDoze;
  }
  /**
   * @return float
   */
  public function getDelayedDeviceDoze()
  {
    return $this->delayedDeviceDoze;
  }
  /**
   * The percentage of accepted messages that were delayed because the target
   * device was not connected at the time of sending. These messages were
   * eventually delivered when the device reconnected.
   *
   * @param float $delayedDeviceOffline
   */
  public function setDelayedDeviceOffline($delayedDeviceOffline)
  {
    $this->delayedDeviceOffline = $delayedDeviceOffline;
  }
  /**
   * @return float
   */
  public function getDelayedDeviceOffline()
  {
    return $this->delayedDeviceOffline;
  }
  /**
   * The percentage of accepted messages that were delayed due to message
   * throttling, such as [collapsible message
   * throttling](https://firebase.google.com/docs/cloud-messaging/concept-
   * options#collapsible_throttling) or [maximum message rate
   * throttling](https://firebase.google.com/docs/cloud-messaging/concept-
   * options#device_throttling).
   *
   * @param float $delayedMessageThrottled
   */
  public function setDelayedMessageThrottled($delayedMessageThrottled)
  {
    $this->delayedMessageThrottled = $delayedMessageThrottled;
  }
  /**
   * @return float
   */
  public function getDelayedMessageThrottled()
  {
    return $this->delayedMessageThrottled;
  }
  /**
   * The percentage of accepted messages that were delayed because the intended
   * device user-profile was [stopped](https://firebase.google.com/docs/cloud-
   * messaging/android/receive#handling_messages) on the target device at the
   * time of the send. The messages were eventually delivered when the user-
   * profile was started again.
   *
   * @param float $delayedUserStopped
   */
  public function setDelayedUserStopped($delayedUserStopped)
  {
    $this->delayedUserStopped = $delayedUserStopped;
  }
  /**
   * @return float
   */
  public function getDelayedUserStopped()
  {
    return $this->delayedUserStopped;
  }
  /**
   * The percentage of accepted messages that were delivered to the device
   * without delay from the FCM system.
   *
   * @param float $deliveredNoDelay
   */
  public function setDeliveredNoDelay($deliveredNoDelay)
  {
    $this->deliveredNoDelay = $deliveredNoDelay;
  }
  /**
   * @return float
   */
  public function getDeliveredNoDelay()
  {
    return $this->deliveredNoDelay;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirebaseFcmDataV1beta1DeliveryPerformancePercents::class, 'Google_Service_Fcmdata_GoogleFirebaseFcmDataV1beta1DeliveryPerformancePercents');
