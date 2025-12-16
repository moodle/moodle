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

class GoogleCloudRecaptchaenterpriseV1UserId extends \Google\Model
{
  /**
   * Optional. An email address.
   *
   * @var string
   */
  public $email;
  /**
   * Optional. A phone number. Should use the E.164 format.
   *
   * @var string
   */
  public $phoneNumber;
  /**
   * Optional. A unique username, if different from all the other identifiers
   * and `account_id` that are provided. Can be a unique login handle or display
   * name for a user.
   *
   * @var string
   */
  public $username;

  /**
   * Optional. An email address.
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
   * Optional. A phone number. Should use the E.164 format.
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
   * Optional. A unique username, if different from all the other identifiers
   * and `account_id` that are provided. Can be a unique login handle or display
   * name for a user.
   *
   * @param string $username
   */
  public function setUsername($username)
  {
    $this->username = $username;
  }
  /**
   * @return string
   */
  public function getUsername()
  {
    return $this->username;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1UserId::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1UserId');
