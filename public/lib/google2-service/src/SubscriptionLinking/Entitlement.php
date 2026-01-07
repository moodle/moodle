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

namespace Google\Service\SubscriptionLinking;

class Entitlement extends \Google\Model
{
  /**
   * The detail field can carry a description of the SKU that corresponds to
   * what the user has been granted access to. This description, which is opaque
   * to Google, can be displayed in the Google user subscription console for
   * users who linked the subscription to a Google Account. Max 80 character
   * limit.
   *
   * @var string
   */
  public $detail;
  /**
   * Required. Expiration time of the entitlement. Entitlements that have
   * expired over 30 days will be purged. The max expire_time is 398 days from
   * now().
   *
   * @var string
   */
  public $expireTime;
  /**
   * Required. The publication's product ID that the user has access to. This is
   * the same product ID as can be found in Schema.org markup
   * (http://schema.org/productID). E.g. "dailybugle.com:basic"
   *
   * @var string
   */
  public $productId;
  /**
   * A source-specific subscription token. This is an opaque string that the
   * publisher provides to Google. This token is opaque and has no meaning to
   * Google.
   *
   * @var string
   */
  public $subscriptionToken;

  /**
   * The detail field can carry a description of the SKU that corresponds to
   * what the user has been granted access to. This description, which is opaque
   * to Google, can be displayed in the Google user subscription console for
   * users who linked the subscription to a Google Account. Max 80 character
   * limit.
   *
   * @param string $detail
   */
  public function setDetail($detail)
  {
    $this->detail = $detail;
  }
  /**
   * @return string
   */
  public function getDetail()
  {
    return $this->detail;
  }
  /**
   * Required. Expiration time of the entitlement. Entitlements that have
   * expired over 30 days will be purged. The max expire_time is 398 days from
   * now().
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * Required. The publication's product ID that the user has access to. This is
   * the same product ID as can be found in Schema.org markup
   * (http://schema.org/productID). E.g. "dailybugle.com:basic"
   *
   * @param string $productId
   */
  public function setProductId($productId)
  {
    $this->productId = $productId;
  }
  /**
   * @return string
   */
  public function getProductId()
  {
    return $this->productId;
  }
  /**
   * A source-specific subscription token. This is an opaque string that the
   * publisher provides to Google. This token is opaque and has no meaning to
   * Google.
   *
   * @param string $subscriptionToken
   */
  public function setSubscriptionToken($subscriptionToken)
  {
    $this->subscriptionToken = $subscriptionToken;
  }
  /**
   * @return string
   */
  public function getSubscriptionToken()
  {
    return $this->subscriptionToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Entitlement::class, 'Google_Service_SubscriptionLinking_Entitlement');
