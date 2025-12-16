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

namespace Google\Service\Adsense;

class Payment extends \Google\Model
{
  /**
   * Output only. The amount of unpaid or paid earnings, as a formatted string,
   * including the currency. E.g. "¥1,235 JPY", "$1,234.57", "£87.65".
   *
   * @var string
   */
  public $amount;
  protected $dateType = Date::class;
  protected $dateDataType = '';
  /**
   * Output only. Resource name of the payment. Format: -
   * accounts/{account}/payments/unpaid for unpaid (current) AdSense earnings. -
   * accounts/{account}/payments/youtube-unpaid for unpaid (current) YouTube
   * earnings. - accounts/{account}/payments/yyyy-MM-dd for paid AdSense
   * earnings. - accounts/{account}/payments/youtube-yyyy-MM-dd for paid YouTube
   * earnings.
   *
   * @var string
   */
  public $name;

  /**
   * Output only. The amount of unpaid or paid earnings, as a formatted string,
   * including the currency. E.g. "¥1,235 JPY", "$1,234.57", "£87.65".
   *
   * @param string $amount
   */
  public function setAmount($amount)
  {
    $this->amount = $amount;
  }
  /**
   * @return string
   */
  public function getAmount()
  {
    return $this->amount;
  }
  /**
   * Output only. For paid earnings, the date that the payment was credited. For
   * unpaid earnings, this field is empty. Payment dates are always returned in
   * the billing timezone (America/Los_Angeles).
   *
   * @param Date $date
   */
  public function setDate(Date $date)
  {
    $this->date = $date;
  }
  /**
   * @return Date
   */
  public function getDate()
  {
    return $this->date;
  }
  /**
   * Output only. Resource name of the payment. Format: -
   * accounts/{account}/payments/unpaid for unpaid (current) AdSense earnings. -
   * accounts/{account}/payments/youtube-unpaid for unpaid (current) YouTube
   * earnings. - accounts/{account}/payments/yyyy-MM-dd for paid AdSense
   * earnings. - accounts/{account}/payments/youtube-yyyy-MM-dd for paid YouTube
   * earnings.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Payment::class, 'Google_Service_Adsense_Payment');
