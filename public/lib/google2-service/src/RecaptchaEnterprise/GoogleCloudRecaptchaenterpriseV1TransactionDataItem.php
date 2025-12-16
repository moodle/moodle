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

namespace Google\Service\RecaptchaEnterprise;

class GoogleCloudRecaptchaenterpriseV1TransactionDataItem extends \Google\Model
{
  /**
   * Optional. When a merchant is specified, its corresponding account_id.
   * Necessary to populate marketplace-style transactions.
   *
   * @var string
   */
  public $merchantAccountId;
  /**
   * Optional. The full name of the item.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The quantity of this item that is being purchased.
   *
   * @var string
   */
  public $quantity;
  /**
   * Optional. The value per item that the user is paying, in the transaction
   * currency, after discounts.
   *
   * @var 
   */
  public $value;

  /**
   * Optional. When a merchant is specified, its corresponding account_id.
   * Necessary to populate marketplace-style transactions.
   *
   * @param string $merchantAccountId
   */
  public function setMerchantAccountId($merchantAccountId)
  {
    $this->merchantAccountId = $merchantAccountId;
  }
  /**
   * @return string
   */
  public function getMerchantAccountId()
  {
    return $this->merchantAccountId;
  }
  /**
   * Optional. The full name of the item.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Optional. The quantity of this item that is being purchased.
   *
   * @param string $quantity
   */
  public function setQuantity($quantity)
  {
    $this->quantity = $quantity;
  }
  /**
   * @return string
   */
  public function getQuantity()
  {
    return $this->quantity;
  }
  public function setValue($value)
  {
    $this->value = $value;
  }
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1TransactionDataItem::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1TransactionDataItem');
