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

namespace Google\Service\DataManager;

class UserIdentifier extends \Google\Model
{
  protected $addressType = AddressInfo::class;
  protected $addressDataType = '';
  /**
   * Hashed email address using SHA-256 hash function after normalization.
   *
   * @var string
   */
  public $emailAddress;
  /**
   * Hashed phone number using SHA-256 hash function after normalization (E164
   * standard).
   *
   * @var string
   */
  public $phoneNumber;

  /**
   * The known components of a user's address. Holds a grouping of identifiers
   * that are matched all at once.
   *
   * @param AddressInfo $address
   */
  public function setAddress(AddressInfo $address)
  {
    $this->address = $address;
  }
  /**
   * @return AddressInfo
   */
  public function getAddress()
  {
    return $this->address;
  }
  /**
   * Hashed email address using SHA-256 hash function after normalization.
   *
   * @param string $emailAddress
   */
  public function setEmailAddress($emailAddress)
  {
    $this->emailAddress = $emailAddress;
  }
  /**
   * @return string
   */
  public function getEmailAddress()
  {
    return $this->emailAddress;
  }
  /**
   * Hashed phone number using SHA-256 hash function after normalization (E164
   * standard).
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserIdentifier::class, 'Google_Service_DataManager_UserIdentifier');
