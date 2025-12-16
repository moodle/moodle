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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1DeveloperBalanceWallet extends \Google\Model
{
  protected $balanceType = GoogleTypeMoney::class;
  protected $balanceDataType = '';
  /**
   * Output only. Time at which the developer last added credit to the account
   * in milliseconds since epoch.
   *
   * @var string
   */
  public $lastCreditTime;

  /**
   * Current remaining balance of the developer for a particular currency.
   *
   * @param GoogleTypeMoney $balance
   */
  public function setBalance(GoogleTypeMoney $balance)
  {
    $this->balance = $balance;
  }
  /**
   * @return GoogleTypeMoney
   */
  public function getBalance()
  {
    return $this->balance;
  }
  /**
   * Output only. Time at which the developer last added credit to the account
   * in milliseconds since epoch.
   *
   * @param string $lastCreditTime
   */
  public function setLastCreditTime($lastCreditTime)
  {
    $this->lastCreditTime = $lastCreditTime;
  }
  /**
   * @return string
   */
  public function getLastCreditTime()
  {
    return $this->lastCreditTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1DeveloperBalanceWallet::class, 'Google_Service_Apigee_GoogleCloudApigeeV1DeveloperBalanceWallet');
