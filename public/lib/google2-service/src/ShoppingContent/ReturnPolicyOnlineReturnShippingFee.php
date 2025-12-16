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

class ReturnPolicyOnlineReturnShippingFee extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * The return shipping fee is a fixed value.
   */
  public const TYPE_FIXED = 'FIXED';
  /**
   * Customer will pay the actual return shipping fee.
   */
  public const TYPE_CUSTOMER_PAYING_ACTUAL_FEE = 'CUSTOMER_PAYING_ACTUAL_FEE';
  protected $fixedFeeType = PriceAmount::class;
  protected $fixedFeeDataType = '';
  /**
   * Type of return shipping fee.
   *
   * @var string
   */
  public $type;

  /**
   * Fixed return shipping fee amount. This value is only applicable when type
   * is FIXED. We will treat the return shipping fee as free if type is FIXED
   * and this value is not set.
   *
   * @param PriceAmount $fixedFee
   */
  public function setFixedFee(PriceAmount $fixedFee)
  {
    $this->fixedFee = $fixedFee;
  }
  /**
   * @return PriceAmount
   */
  public function getFixedFee()
  {
    return $this->fixedFee;
  }
  /**
   * Type of return shipping fee.
   *
   * Accepted values: TYPE_UNSPECIFIED, FIXED, CUSTOMER_PAYING_ACTUAL_FEE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReturnPolicyOnlineReturnShippingFee::class, 'Google_Service_ShoppingContent_ReturnPolicyOnlineReturnShippingFee');
