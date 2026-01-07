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

namespace Google\Service\AndroidPublisher;

class RegionalSubscriptionOfferConfig extends \Google\Model
{
  /**
   * Whether the subscription offer in the specified region is available for new
   * subscribers. Existing subscribers will not have their subscription
   * cancelled if this value is set to false. If not specified, this will
   * default to false.
   *
   * @var bool
   */
  public $newSubscriberAvailability;
  /**
   * Required. Immutable. Region code this configuration applies to, as defined
   * by ISO 3166-2, e.g. "US".
   *
   * @var string
   */
  public $regionCode;

  /**
   * Whether the subscription offer in the specified region is available for new
   * subscribers. Existing subscribers will not have their subscription
   * cancelled if this value is set to false. If not specified, this will
   * default to false.
   *
   * @param bool $newSubscriberAvailability
   */
  public function setNewSubscriberAvailability($newSubscriberAvailability)
  {
    $this->newSubscriberAvailability = $newSubscriberAvailability;
  }
  /**
   * @return bool
   */
  public function getNewSubscriberAvailability()
  {
    return $this->newSubscriberAvailability;
  }
  /**
   * Required. Immutable. Region code this configuration applies to, as defined
   * by ISO 3166-2, e.g. "US".
   *
   * @param string $regionCode
   */
  public function setRegionCode($regionCode)
  {
    $this->regionCode = $regionCode;
  }
  /**
   * @return string
   */
  public function getRegionCode()
  {
    return $this->regionCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RegionalSubscriptionOfferConfig::class, 'Google_Service_AndroidPublisher_RegionalSubscriptionOfferConfig');
