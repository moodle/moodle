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

class GoogleCloudRecaptchaenterpriseV1TransactionDataUser extends \Google\Model
{
  /**
   * Optional. Unique account identifier for this user. If using account
   * defender, this should match the hashed_account_id field. Otherwise, a
   * unique and persistent identifier for this account.
   *
   * @var string
   */
  public $accountId;
  /**
   * Optional. The epoch milliseconds of the user's account creation.
   *
   * @var string
   */
  public $creationMs;
  /**
   * Optional. The email address of the user.
   *
   * @var string
   */
  public $email;
  /**
   * Optional. Whether the email has been verified to be accessible by the user
   * (OTP or similar).
   *
   * @var bool
   */
  public $emailVerified;
  /**
   * Optional. The phone number of the user, with country code.
   *
   * @var string
   */
  public $phoneNumber;
  /**
   * Optional. Whether the phone number has been verified to be accessible by
   * the user (OTP or similar).
   *
   * @var bool
   */
  public $phoneVerified;

  /**
   * Optional. Unique account identifier for this user. If using account
   * defender, this should match the hashed_account_id field. Otherwise, a
   * unique and persistent identifier for this account.
   *
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * Optional. The epoch milliseconds of the user's account creation.
   *
   * @param string $creationMs
   */
  public function setCreationMs($creationMs)
  {
    $this->creationMs = $creationMs;
  }
  /**
   * @return string
   */
  public function getCreationMs()
  {
    return $this->creationMs;
  }
  /**
   * Optional. The email address of the user.
   *
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }
  /**
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }
  /**
   * Optional. Whether the email has been verified to be accessible by the user
   * (OTP or similar).
   *
   * @param bool $emailVerified
   */
  public function setEmailVerified($emailVerified)
  {
    $this->emailVerified = $emailVerified;
  }
  /**
   * @return bool
   */
  public function getEmailVerified()
  {
    return $this->emailVerified;
  }
  /**
   * Optional. The phone number of the user, with country code.
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
   * Optional. Whether the phone number has been verified to be accessible by
   * the user (OTP or similar).
   *
   * @param bool $phoneVerified
   */
  public function setPhoneVerified($phoneVerified)
  {
    $this->phoneVerified = $phoneVerified;
  }
  /**
   * @return bool
   */
  public function getPhoneVerified()
  {
    return $this->phoneVerified;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1TransactionDataUser::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1TransactionDataUser');
