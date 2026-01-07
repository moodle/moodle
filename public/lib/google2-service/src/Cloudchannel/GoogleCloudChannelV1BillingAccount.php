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

namespace Google\Service\Cloudchannel;

class GoogleCloudChannelV1BillingAccount extends \Google\Model
{
  /**
   * Output only. The time when this billing account was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The 3-letter currency code defined in ISO 4217.
   *
   * @var string
   */
  public $currencyCode;
  /**
   * Display name of the billing account.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. Resource name of the billing account. Format:
   * accounts/{account_id}/billingAccounts/{billing_account_id}.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The CLDR region code.
   *
   * @var string
   */
  public $regionCode;

  /**
   * Output only. The time when this billing account was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. The 3-letter currency code defined in ISO 4217.
   *
   * @param string $currencyCode
   */
  public function setCurrencyCode($currencyCode)
  {
    $this->currencyCode = $currencyCode;
  }
  /**
   * @return string
   */
  public function getCurrencyCode()
  {
    return $this->currencyCode;
  }
  /**
   * Display name of the billing account.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. Resource name of the billing account. Format:
   * accounts/{account_id}/billingAccounts/{billing_account_id}.
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
   * Output only. The CLDR region code.
   *
   * @param string $regionCode
   */
  public function setRegionCode($regionCode)
  {
    $this->regionCode = $regionCode;
  }
  /**
   * @return string
   */
  public function getRegionCode()
  {
    return $this->regionCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1BillingAccount::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1BillingAccount');
