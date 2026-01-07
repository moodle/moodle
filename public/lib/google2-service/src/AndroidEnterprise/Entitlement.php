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

namespace Google\Service\AndroidEnterprise;

class Entitlement extends \Google\Model
{
  public const REASON_free = 'free';
  public const REASON_groupLicense = 'groupLicense';
  public const REASON_userPurchase = 'userPurchase';
  /**
   * The ID of the product that the entitlement is for. For example,
   * "app:com.google.android.gm".
   *
   * @var string
   */
  public $productId;
  /**
   * The reason for the entitlement. For example, "free" for free apps. This
   * property is temporary: it will be replaced by the acquisition kind field of
   * group licenses.
   *
   * @var string
   */
  public $reason;

  /**
   * The ID of the product that the entitlement is for. For example,
   * "app:com.google.android.gm".
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
   * The reason for the entitlement. For example, "free" for free apps. This
   * property is temporary: it will be replaced by the acquisition kind field of
   * group licenses.
   *
   * Accepted values: free, groupLicense, userPurchase
   *
   * @param self::REASON_* $reason
   */
  public function setReason($reason)
  {
    $this->reason = $reason;
  }
  /**
   * @return self::REASON_*
   */
  public function getReason()
  {
    return $this->reason;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Entitlement::class, 'Google_Service_AndroidEnterprise_Entitlement');
