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

class AccountBusinessInformation extends \Google\Model
{
  protected $addressType = AccountAddress::class;
  protected $addressDataType = '';
  protected $customerServiceType = AccountCustomerService::class;
  protected $customerServiceDataType = '';
  /**
   * The 10-digit [Korean business registration
   * number](https://support.google.com/merchants/answer/9037766) separated with
   * dashes in the format: XXX-XX-XXXXX. This field will only be updated if
   * explicitly set.
   *
   * @var string
   */
  public $koreanBusinessRegistrationNumber;
  /**
   * The phone number of the business in
   * [E.164](https://en.wikipedia.org/wiki/E.164) format. This can only be
   * updated if a verified phone number is not already set. To replace a
   * verified phone number use the `Accounts.requestphoneverification` and
   * `Accounts.verifyphonenumber`.
   *
   * @var string
   */
  public $phoneNumber;
  /**
   * Verification status of the phone number of the business. This status is
   * read only and can be updated only by successful phone verification.
   * Acceptable values are: - "`verified`" - "`unverified`"
   *
   * @var string
   */
  public $phoneVerificationStatus;

  /**
   * The address of the business. Use `\n` to add a second address line.
   *
   * @param AccountAddress $address
   */
  public function setAddress(AccountAddress $address)
  {
    $this->address = $address;
  }
  /**
   * @return AccountAddress
   */
  public function getAddress()
  {
    return $this->address;
  }
  /**
   * The customer service information of the business.
   *
   * @param AccountCustomerService $customerService
   */
  public function setCustomerService(AccountCustomerService $customerService)
  {
    $this->customerService = $customerService;
  }
  /**
   * @return AccountCustomerService
   */
  public function getCustomerService()
  {
    return $this->customerService;
  }
  /**
   * The 10-digit [Korean business registration
   * number](https://support.google.com/merchants/answer/9037766) separated with
   * dashes in the format: XXX-XX-XXXXX. This field will only be updated if
   * explicitly set.
   *
   * @param string $koreanBusinessRegistrationNumber
   */
  public function setKoreanBusinessRegistrationNumber($koreanBusinessRegistrationNumber)
  {
    $this->koreanBusinessRegistrationNumber = $koreanBusinessRegistrationNumber;
  }
  /**
   * @return string
   */
  public function getKoreanBusinessRegistrationNumber()
  {
    return $this->koreanBusinessRegistrationNumber;
  }
  /**
   * The phone number of the business in
   * [E.164](https://en.wikipedia.org/wiki/E.164) format. This can only be
   * updated if a verified phone number is not already set. To replace a
   * verified phone number use the `Accounts.requestphoneverification` and
   * `Accounts.verifyphonenumber`.
   *
   * @param string $phoneNumber
   */
  public function setPhoneNumber($phoneNumber)
  {
    $this->phoneNumber = $phoneNumber;
  }
  /**
   * @return string
   */
  public function getPhoneNumber()
  {
    return $this->phoneNumber;
  }
  /**
   * Verification status of the phone number of the business. This status is
   * read only and can be updated only by successful phone verification.
   * Acceptable values are: - "`verified`" - "`unverified`"
   *
   * @param string $phoneVerificationStatus
   */
  public function setPhoneVerificationStatus($phoneVerificationStatus)
  {
    $this->phoneVerificationStatus = $phoneVerificationStatus;
  }
  /**
   * @return string
   */
  public function getPhoneVerificationStatus()
  {
    return $this->phoneVerificationStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountBusinessInformation::class, 'Google_Service_ShoppingContent_AccountBusinessInformation');
