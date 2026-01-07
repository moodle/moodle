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

namespace Google\Service\Vault;

class HeldAccount extends \Google\Model
{
  /**
   * The account ID, as provided by the [Admin
   * SDK](https://developers.google.com/admin-sdk/).
   *
   * @var string
   */
  public $accountId;
  /**
   * The primary email address of the account. If used as an input, this takes
   * precedence over **accountId**.
   *
   * @var string
   */
  public $email;
  /**
   * Output only. The first name of the account holder.
   *
   * @var string
   */
  public $firstName;
  /**
   * Output only. When the account was put on hold.
   *
   * @var string
   */
  public $holdTime;
  /**
   * Output only. The last name of the account holder.
   *
   * @var string
   */
  public $lastName;

  /**
   * The account ID, as provided by the [Admin
   * SDK](https://developers.google.com/admin-sdk/).
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
   * The primary email address of the account. If used as an input, this takes
   * precedence over **accountId**.
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
   * Output only. The first name of the account holder.
   *
   * @param string $firstName
   */
  public function setFirstName($firstName)
  {
    $this->firstName = $firstName;
  }
  /**
   * @return string
   */
  public function getFirstName()
  {
    return $this->firstName;
  }
  /**
   * Output only. When the account was put on hold.
   *
   * @param string $holdTime
   */
  public function setHoldTime($holdTime)
  {
    $this->holdTime = $holdTime;
  }
  /**
   * @return string
   */
  public function getHoldTime()
  {
    return $this->holdTime;
  }
  /**
   * Output only. The last name of the account holder.
   *
   * @param string $lastName
   */
  public function setLastName($lastName)
  {
    $this->lastName = $lastName;
  }
  /**
   * @return string
   */
  public function getLastName()
  {
    return $this->lastName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HeldAccount::class, 'Google_Service_Vault_HeldAccount');
