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

class ReturnPolicyOnlinePolicy extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Number of days after a return is delivered.
   */
  public const TYPE_NUMBER_OF_DAYS_AFTER_DELIVERY = 'NUMBER_OF_DAYS_AFTER_DELIVERY';
  /**
   * No returns.
   */
  public const TYPE_NO_RETURNS = 'NO_RETURNS';
  /**
   * Life time returns.
   */
  public const TYPE_LIFETIME_RETURNS = 'LIFETIME_RETURNS';
  /**
   * The number of days items can be returned after delivery, where one day is
   * defined to be 24 hours after the delivery timestamp. Required for
   * `numberOfDaysAfterDelivery` returns.
   *
   * @var string
   */
  public $days;
  /**
   * Policy type.
   *
   * @var string
   */
  public $type;

  /**
   * The number of days items can be returned after delivery, where one day is
   * defined to be 24 hours after the delivery timestamp. Required for
   * `numberOfDaysAfterDelivery` returns.
   *
   * @param string $days
   */
  public function setDays($days)
  {
    $this->days = $days;
  }
  /**
   * @return string
   */
  public function getDays()
  {
    return $this->days;
  }
  /**
   * Policy type.
   *
   * Accepted values: TYPE_UNSPECIFIED, NUMBER_OF_DAYS_AFTER_DELIVERY,
   * NO_RETURNS, LIFETIME_RETURNS
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
class_alias(ReturnPolicyOnlinePolicy::class, 'Google_Service_ShoppingContent_ReturnPolicyOnlinePolicy');
