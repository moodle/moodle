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

namespace Google\Service\PaymentsResellerSubscription;

class CreateSubscriptionIntent extends \Google\Model
{
  protected $cycleOptionsType = CycleOptions::class;
  protected $cycleOptionsDataType = '';
  /**
   * Required. The parent resource name, which is the identifier of the partner.
   *
   * @var string
   */
  public $parent;
  protected $subscriptionType = Subscription::class;
  protected $subscriptionDataType = '';
  /**
   * Required. Identifies the subscription resource on the Partner side. The
   * value is restricted to 63 ASCII characters at the maximum. If a
   * subscription was previously created with the same subscription_id, we will
   * directly return that one.
   *
   * @var string
   */
  public $subscriptionId;

  /**
   * Optional. The cycle options for the subscription.
   *
   * @param CycleOptions $cycleOptions
   */
  public function setCycleOptions(CycleOptions $cycleOptions)
  {
    $this->cycleOptions = $cycleOptions;
  }
  /**
   * @return CycleOptions
   */
  public function getCycleOptions()
  {
    return $this->cycleOptions;
  }
  /**
   * Required. The parent resource name, which is the identifier of the partner.
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
  /**
   * Required. The Subscription to be created.
   *
   * @param Subscription $subscription
   */
  public function setSubscription(Subscription $subscription)
  {
    $this->subscription = $subscription;
  }
  /**
   * @return Subscription
   */
  public function getSubscription()
  {
    return $this->subscription;
  }
  /**
   * Required. Identifies the subscription resource on the Partner side. The
   * value is restricted to 63 ASCII characters at the maximum. If a
   * subscription was previously created with the same subscription_id, we will
   * directly return that one.
   *
   * @param string $subscriptionId
   */
  public function setSubscriptionId($subscriptionId)
  {
    $this->subscriptionId = $subscriptionId;
  }
  /**
   * @return string
   */
  public function getSubscriptionId()
  {
    return $this->subscriptionId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateSubscriptionIntent::class, 'Google_Service_PaymentsResellerSubscription_CreateSubscriptionIntent');
