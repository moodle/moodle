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

class ExternalSubscription extends \Google\Model
{
  /**
   * Unspecified, do not use.
   */
  public const SUBSCRIPTION_TYPE_SUBSCRIPTION_TYPE_UNSPECIFIED = 'SUBSCRIPTION_TYPE_UNSPECIFIED';
  /**
   * This is a recurring subscription where the user is charged every billing
   * cycle.
   */
  public const SUBSCRIPTION_TYPE_RECURRING = 'RECURRING';
  /**
   * This is a prepaid subscription where the user pays up front.
   */
  public const SUBSCRIPTION_TYPE_PREPAID = 'PREPAID';
  /**
   * Required. The type of the external subscription.
   *
   * @var string
   */
  public $subscriptionType;

  /**
   * Required. The type of the external subscription.
   *
   * Accepted values: SUBSCRIPTION_TYPE_UNSPECIFIED, RECURRING, PREPAID
   *
   * @param self::SUBSCRIPTION_TYPE_* $subscriptionType
   */
  public function setSubscriptionType($subscriptionType)
  {
    $this->subscriptionType = $subscriptionType;
  }
  /**
   * @return self::SUBSCRIPTION_TYPE_*
   */
  public function getSubscriptionType()
  {
    return $this->subscriptionType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExternalSubscription::class, 'Google_Service_AndroidPublisher_ExternalSubscription');
