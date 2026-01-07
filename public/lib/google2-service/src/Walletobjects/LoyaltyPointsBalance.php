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

namespace Google\Service\Walletobjects;

class LoyaltyPointsBalance extends \Google\Model
{
  /**
   * The double form of a balance. Only one of these subtypes (string, int,
   * double, money) should be populated.
   *
   * @var 
   */
  public $double;
  /**
   * The integer form of a balance. Only one of these subtypes (string, int,
   * double, money) should be populated.
   *
   * @var int
   */
  public $int;
  protected $moneyType = Money::class;
  protected $moneyDataType = '';
  /**
   * The string form of a balance. Only one of these subtypes (string, int,
   * double, money) should be populated.
   *
   * @var string
   */
  public $string;

  public function setDouble($double)
  {
    $this->double = $double;
  }
  public function getDouble()
  {
    return $this->double;
  }
  /**
   * The integer form of a balance. Only one of these subtypes (string, int,
   * double, money) should be populated.
   *
   * @param int $int
   */
  public function setInt($int)
  {
    $this->int = $int;
  }
  /**
   * @return int
   */
  public function getInt()
  {
    return $this->int;
  }
  /**
   * The money form of a balance. Only one of these subtypes (string, int,
   * double, money) should be populated.
   *
   * @param Money $money
   */
  public function setMoney(Money $money)
  {
    $this->money = $money;
  }
  /**
   * @return Money
   */
  public function getMoney()
  {
    return $this->money;
  }
  /**
   * The string form of a balance. Only one of these subtypes (string, int,
   * double, money) should be populated.
   *
   * @param string $string
   */
  public function setString($string)
  {
    $this->string = $string;
  }
  /**
   * @return string
   */
  public function getString()
  {
    return $this->string;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LoyaltyPointsBalance::class, 'Google_Service_Walletobjects_LoyaltyPointsBalance');
