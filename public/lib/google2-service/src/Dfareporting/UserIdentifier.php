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

namespace Google\Service\Dfareporting;

class UserIdentifier extends \Google\Model
{
  protected $addressInfoType = OfflineUserAddressInfo::class;
  protected $addressInfoDataType = '';
  /**
   * Hashed email address using SHA-256 hash function after normalization.
   *
   * @var string
   */
  public $hashedEmail;
  /**
   * Hashed phone number using SHA-256 hash function after normalization (E164
   * standard).
   *
   * @var string
   */
  public $hashedPhoneNumber;

  /**
   * Address information.
   *
   * @param OfflineUserAddressInfo $addressInfo
   */
  public function setAddressInfo(OfflineUserAddressInfo $addressInfo)
  {
    $this->addressInfo = $addressInfo;
  }
  /**
   * @return OfflineUserAddressInfo
   */
  public function getAddressInfo()
  {
    return $this->addressInfo;
  }
  /**
   * Hashed email address using SHA-256 hash function after normalization.
   *
   * @param string $hashedEmail
   */
  public function setHashedEmail($hashedEmail)
  {
    $this->hashedEmail = $hashedEmail;
  }
  /**
   * @return string
   */
  public function getHashedEmail()
  {
    return $this->hashedEmail;
  }
  /**
   * Hashed phone number using SHA-256 hash function after normalization (E164
   * standard).
   *
   * @param string $hashedPhoneNumber
   */
  public function setHashedPhoneNumber($hashedPhoneNumber)
  {
    $this->hashedPhoneNumber = $hashedPhoneNumber;
  }
  /**
   * @return string
   */
  public function getHashedPhoneNumber()
  {
    return $this->hashedPhoneNumber;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserIdentifier::class, 'Google_Service_Dfareporting_UserIdentifier');
