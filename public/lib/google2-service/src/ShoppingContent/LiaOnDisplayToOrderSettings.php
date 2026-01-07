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

namespace Google\Service\ShoppingContent;

class LiaOnDisplayToOrderSettings extends \Google\Model
{
  /**
   * Shipping cost and policy URL.
   *
   * @var string
   */
  public $shippingCostPolicyUrl;
  /**
   * The status of the ?On display to order? feature. Acceptable values are: -
   * "`active`" - "`inactive`" - "`pending`"
   *
   * @var string
   */
  public $status;

  /**
   * Shipping cost and policy URL.
   *
   * @param string $shippingCostPolicyUrl
   */
  public function setShippingCostPolicyUrl($shippingCostPolicyUrl)
  {
    $this->shippingCostPolicyUrl = $shippingCostPolicyUrl;
  }
  /**
   * @return string
   */
  public function getShippingCostPolicyUrl()
  {
    return $this->shippingCostPolicyUrl;
  }
  /**
   * The status of the ?On display to order? feature. Acceptable values are: -
   * "`active`" - "`inactive`" - "`pending`"
   *
   * @param string $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return string
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LiaOnDisplayToOrderSettings::class, 'Google_Service_ShoppingContent_LiaOnDisplayToOrderSettings');
