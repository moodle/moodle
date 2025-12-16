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

class OfflineUserAddressInfo extends \Google\Model
{
  /**
   * City of the address.
   *
   * @var string
   */
  public $city;
  /**
   * 2-letter country code in ISO-3166-1 alpha-2 of the user's address.
   *
   * @var string
   */
  public $countryCode;
  /**
   * First name of the user, which is hashed as SHA-256 after normalized
   * (Lowercase all characters; Remove any extra spaces before, after, and in
   * between).
   *
   * @var string
   */
  public $hashedFirstName;
  /**
   * Last name of the user, which is hashed as SHA-256 after normalized (lower
   * case only and no punctuation).
   *
   * @var string
   */
  public $hashedLastName;
  /**
   * The street address of the user hashed using SHA-256 hash function after
   * normalization (lower case only).
   *
   * @var string
   */
  public $hashedStreetAddress;
  /**
   * Postal code of the user's address.
   *
   * @var string
   */
  public $postalCode;
  /**
   * State code of the address.
   *
   * @var string
   */
  public $state;

  /**
   * City of the address.
   *
   * @param string $city
   */
  public function setCity($city)
  {
    $this->city = $city;
  }
  /**
   * @return string
   */
  public function getCity()
  {
    return $this->city;
  }
  /**
   * 2-letter country code in ISO-3166-1 alpha-2 of the user's address.
   *
   * @param string $countryCode
   */
  public function setCountryCode($countryCode)
  {
    $this->countryCode = $countryCode;
  }
  /**
   * @return string
   */
  public function getCountryCode()
  {
    return $this->countryCode;
  }
  /**
   * First name of the user, which is hashed as SHA-256 after normalized
   * (Lowercase all characters; Remove any extra spaces before, after, and in
   * between).
   *
   * @param string $hashedFirstName
   */
  public function setHashedFirstName($hashedFirstName)
  {
    $this->hashedFirstName = $hashedFirstName;
  }
  /**
   * @return string
   */
  public function getHashedFirstName()
  {
    return $this->hashedFirstName;
  }
  /**
   * Last name of the user, which is hashed as SHA-256 after normalized (lower
   * case only and no punctuation).
   *
   * @param string $hashedLastName
   */
  public function setHashedLastName($hashedLastName)
  {
    $this->hashedLastName = $hashedLastName;
  }
  /**
   * @return string
   */
  public function getHashedLastName()
  {
    return $this->hashedLastName;
  }
  /**
   * The street address of the user hashed using SHA-256 hash function after
   * normalization (lower case only).
   *
   * @param string $hashedStreetAddress
   */
  public function setHashedStreetAddress($hashedStreetAddress)
  {
    $this->hashedStreetAddress = $hashedStreetAddress;
  }
  /**
   * @return string
   */
  public function getHashedStreetAddress()
  {
    return $this->hashedStreetAddress;
  }
  /**
   * Postal code of the user's address.
   *
   * @param string $postalCode
   */
  public function setPostalCode($postalCode)
  {
    $this->postalCode = $postalCode;
  }
  /**
   * @return string
   */
  public function getPostalCode()
  {
    return $this->postalCode;
  }
  /**
   * State code of the address.
   *
   * @param string $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return string
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OfflineUserAddressInfo::class, 'Google_Service_Dfareporting_OfflineUserAddressInfo');
