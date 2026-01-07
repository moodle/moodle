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

class LoyaltyPoints extends \Google\Model
{
  protected $balanceType = LoyaltyPointsBalance::class;
  protected $balanceDataType = '';
  /**
   * The loyalty points label, such as "Points". Recommended maximum length is 9
   * characters.
   *
   * @var string
   */
  public $label;
  protected $localizedLabelType = LocalizedString::class;
  protected $localizedLabelDataType = '';

  /**
   * The account holder's loyalty point balance, such as "500" or "$10.00".
   * Recommended maximum length is 7 characters. This is a required field of
   * `loyaltyPoints` and `secondaryLoyaltyPoints`.
   *
   * @param LoyaltyPointsBalance $balance
   */
  public function setBalance(LoyaltyPointsBalance $balance)
  {
    $this->balance = $balance;
  }
  /**
   * @return LoyaltyPointsBalance
   */
  public function getBalance()
  {
    return $this->balance;
  }
  /**
   * The loyalty points label, such as "Points". Recommended maximum length is 9
   * characters.
   *
   * @param string $label
   */
  public function setLabel($label)
  {
    $this->label = $label;
  }
  /**
   * @return string
   */
  public function getLabel()
  {
    return $this->label;
  }
  /**
   * Translated strings for the label. Recommended maximum length is 9
   * characters.
   *
   * @param LocalizedString $localizedLabel
   */
  public function setLocalizedLabel(LocalizedString $localizedLabel)
  {
    $this->localizedLabel = $localizedLabel;
  }
  /**
   * @return LocalizedString
   */
  public function getLocalizedLabel()
  {
    return $this->localizedLabel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LoyaltyPoints::class, 'Google_Service_Walletobjects_LoyaltyPoints');
