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

class AddressInfo extends \Google\Model
{
  /**
   * Required. Family (last) name of the user, all lowercase, with no
   * punctuation, no leading or trailing whitespace, and hashed as SHA-256.
   *
   * @var string
   */
  public $familyName;
  /**
   * Required. Given (first) name of the user, all lowercase, with no
   * punctuation, no leading or trailing whitespace, and hashed as SHA-256.
   *
   * @var string
   */
  public $givenName;
  /**
   * Required. The postal code of the user's address.
   *
   * @var string
   */
  public $postalCode;
  /**
   * Required. The 2-letter region code in ISO-3166-1 alpha-2 of the user's
   * address.
   *
   * @var string
   */
  public $regionCode;

  /**
   * Required. Family (last) name of the user, all lowercase, with no
   * punctuation, no leading or trailing whitespace, and hashed as SHA-256.
   *
   * @param string $familyName
   */
  public function setFamilyName($familyName)
  {
    $this->familyName = $familyName;
  }
  /**
   * @return string
   */
  public function getFamilyName()
  {
    return $this->familyName;
  }
  /**
   * Required. Given (first) name of the user, all lowercase, with no
   * punctuation, no leading or trailing whitespace, and hashed as SHA-256.
   *
   * @param string $givenName
   */
  public function setGivenName($givenName)
  {
    $this->givenName = $givenName;
  }
  /**
   * @return string
   */
  public function getGivenName()
  {
    return $this->givenName;
  }
  /**
   * Required. The postal code of the user's address.
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
   * Required. The 2-letter region code in ISO-3166-1 alpha-2 of the user's
   * address.
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
class_alias(AddressInfo::class, 'Google_Service_DataManager_AddressInfo');
