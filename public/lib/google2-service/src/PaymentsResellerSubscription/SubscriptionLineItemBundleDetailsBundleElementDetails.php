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

class SubscriptionLineItemBundleDetailsBundleElementDetails extends \Google\Model
{
  /**
   * Output only. Product resource name that identifies the bundle element. The
   * format is 'partners/{partner_id}/products/{product_id}'.
   *
   * @var string
   */
  public $product;
  /**
   * Output only. The time when this product is linked to an end user.
   *
   * @var string
   */
  public $userAccountLinkedTime;

  /**
   * Output only. Product resource name that identifies the bundle element. The
   * format is 'partners/{partner_id}/products/{product_id}'.
   *
   * @param string $product
   */
  public function setProduct($product)
  {
    $this->product = $product;
  }
  /**
   * @return string
   */
  public function getProduct()
  {
    return $this->product;
  }
  /**
   * Output only. The time when this product is linked to an end user.
   *
   * @param string $userAccountLinkedTime
   */
  public function setUserAccountLinkedTime($userAccountLinkedTime)
  {
    $this->userAccountLinkedTime = $userAccountLinkedTime;
  }
  /**
   * @return string
   */
  public function getUserAccountLinkedTime()
  {
    return $this->userAccountLinkedTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SubscriptionLineItemBundleDetailsBundleElementDetails::class, 'Google_Service_PaymentsResellerSubscription_SubscriptionLineItemBundleDetailsBundleElementDetails');
