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

namespace Google\Service\PubsubLite;

class DeliveryConfig extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const DELIVERY_REQUIREMENT_DELIVERY_REQUIREMENT_UNSPECIFIED = 'DELIVERY_REQUIREMENT_UNSPECIFIED';
  /**
   * The server does not wait for a published message to be successfully written
   * to storage before delivering it to subscribers.
   */
  public const DELIVERY_REQUIREMENT_DELIVER_IMMEDIATELY = 'DELIVER_IMMEDIATELY';
  /**
   * The server will not deliver a published message to subscribers until the
   * message has been successfully written to storage. This will result in
   * higher end-to-end latency, but consistent delivery.
   */
  public const DELIVERY_REQUIREMENT_DELIVER_AFTER_STORED = 'DELIVER_AFTER_STORED';
  /**
   * The DeliveryRequirement for this subscription.
   *
   * @var string
   */
  public $deliveryRequirement;

  /**
   * The DeliveryRequirement for this subscription.
   *
   * Accepted values: DELIVERY_REQUIREMENT_UNSPECIFIED, DELIVER_IMMEDIATELY,
   * DELIVER_AFTER_STORED
   *
   * @param self::DELIVERY_REQUIREMENT_* $deliveryRequirement
   */
  public function setDeliveryRequirement($deliveryRequirement)
  {
    $this->deliveryRequirement = $deliveryRequirement;
  }
  /**
   * @return self::DELIVERY_REQUIREMENT_*
   */
  public function getDeliveryRequirement()
  {
    return $this->deliveryRequirement;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeliveryConfig::class, 'Google_Service_PubsubLite_DeliveryConfig');
