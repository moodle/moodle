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

namespace Google\Service\DisplayVideo;

class ContactInfo extends \Google\Collection
{
  protected $collection_key = 'zipCodes';
  /**
   * Country code of the member. Must also be set with the following fields: *
   * hashed_first_name * hashed_last_name * zip_codes
   *
   * @var string
   */
  public $countryCode;
  /**
   * A list of SHA256 hashed email of the member. Before hashing, remove all
   * whitespace and make sure the string is all lowercase.
   *
   * @var string[]
   */
  public $hashedEmails;
  /**
   * SHA256 hashed first name of the member. Before hashing, remove all
   * whitespace and make sure the string is all lowercase. Must also be set with
   * the following fields: * country_code * hashed_last_name * zip_codes
   *
   * @var string
   */
  public $hashedFirstName;
  /**
   * SHA256 hashed last name of the member. Before hashing, remove all
   * whitespace and make sure the string is all lowercase. Must also be set with
   * the following fields: * country_code * hashed_first_name * zip_codes
   *
   * @var string
   */
  public $hashedLastName;
  /**
   * A list of SHA256 hashed phone numbers of the member. Before hashing, all
   * phone numbers must be formatted using the [E.164
   * format](//en.wikipedia.org/wiki/E.164) and include the country calling
   * code.
   *
   * @var string[]
   */
  public $hashedPhoneNumbers;
  /**
   * A list of zip codes of the member. Must also be set with the following
   * fields: * country_code * hashed_first_name * hashed_last_name
   *
   * @var string[]
   */
  public $zipCodes;

  /**
   * Country code of the member. Must also be set with the following fields: *
   * hashed_first_name * hashed_last_name * zip_codes
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
   * A list of SHA256 hashed email of the member. Before hashing, remove all
   * whitespace and make sure the string is all lowercase.
   *
   * @param string[] $hashedEmails
   */
  public function setHashedEmails($hashedEmails)
  {
    $this->hashedEmails = $hashedEmails;
  }
  /**
   * @return string[]
   */
  public function getHashedEmails()
  {
    return $this->hashedEmails;
  }
  /**
   * SHA256 hashed first name of the member. Before hashing, remove all
   * whitespace and make sure the string is all lowercase. Must also be set with
   * the following fields: * country_code * hashed_last_name * zip_codes
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
   * SHA256 hashed last name of the member. Before hashing, remove all
   * whitespace and make sure the string is all lowercase. Must also be set with
   * the following fields: * country_code * hashed_first_name * zip_codes
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
   * A list of SHA256 hashed phone numbers of the member. Before hashing, all
   * phone numbers must be formatted using the [E.164
   * format](//en.wikipedia.org/wiki/E.164) and include the country calling
   * code.
   *
   * @param string[] $hashedPhoneNumbers
   */
  public function setHashedPhoneNumbers($hashedPhoneNumbers)
  {
    $this->hashedPhoneNumbers = $hashedPhoneNumbers;
  }
  /**
   * @return string[]
   */
  public function getHashedPhoneNumbers()
  {
    return $this->hashedPhoneNumbers;
  }
  /**
   * A list of zip codes of the member. Must also be set with the following
   * fields: * country_code * hashed_first_name * hashed_last_name
   *
   * @param string[] $zipCodes
   */
  public function setZipCodes($zipCodes)
  {
    $this->zipCodes = $zipCodes;
  }
  /**
   * @return string[]
   */
  public function getZipCodes()
  {
    return $this->zipCodes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContactInfo::class, 'Google_Service_DisplayVideo_ContactInfo');
