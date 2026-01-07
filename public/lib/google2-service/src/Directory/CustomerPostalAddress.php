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

namespace Google\Service\Directory;

class CustomerPostalAddress extends \Google\Model
{
  /**
   * A customer's physical address. The address can be composed of one to three
   * lines.
   *
   * @var string
   */
  public $addressLine1;
  /**
   * Address line 2 of the address.
   *
   * @var string
   */
  public $addressLine2;
  /**
   * Address line 3 of the address.
   *
   * @var string
   */
  public $addressLine3;
  /**
   * The customer contact's name.
   *
   * @var string
   */
  public $contactName;
  /**
   * This is a required property. For `countryCode` information see the [ISO
   * 3166 country code elements](https://www.iso.org/iso/country_codes.htm).
   *
   * @var string
   */
  public $countryCode;
  /**
   * Name of the locality. An example of a locality value is the city of `San
   * Francisco`.
   *
   * @var string
   */
  public $locality;
  /**
   * The company or company division name.
   *
   * @var string
   */
  public $organizationName;
  /**
   * The postal code. A postalCode example is a postal zip code such as `10009`.
   * This is in accordance with - http: //portablecontacts.net/draft-
   * spec.html#address_element.
   *
   * @var string
   */
  public $postalCode;
  /**
   * Name of the region. An example of a region value is `NY` for the state of
   * New York.
   *
   * @var string
   */
  public $region;

  /**
   * A customer's physical address. The address can be composed of one to three
   * lines.
   *
   * @param string $addressLine1
   */
  public function setAddressLine1($addressLine1)
  {
    $this->addressLine1 = $addressLine1;
  }
  /**
   * @return string
   */
  public function getAddressLine1()
  {
    return $this->addressLine1;
  }
  /**
   * Address line 2 of the address.
   *
   * @param string $addressLine2
   */
  public function setAddressLine2($addressLine2)
  {
    $this->addressLine2 = $addressLine2;
  }
  /**
   * @return string
   */
  public function getAddressLine2()
  {
    return $this->addressLine2;
  }
  /**
   * Address line 3 of the address.
   *
   * @param string $addressLine3
   */
  public function setAddressLine3($addressLine3)
  {
    $this->addressLine3 = $addressLine3;
  }
  /**
   * @return string
   */
  public function getAddressLine3()
  {
    return $this->addressLine3;
  }
  /**
   * The customer contact's name.
   *
   * @param string $contactName
   */
  public function setContactName($contactName)
  {
    $this->contactName = $contactName;
  }
  /**
   * @return string
   */
  public function getContactName()
  {
    return $this->contactName;
  }
  /**
   * This is a required property. For `countryCode` information see the [ISO
   * 3166 country code elements](https://www.iso.org/iso/country_codes.htm).
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
   * Name of the locality. An example of a locality value is the city of `San
   * Francisco`.
   *
   * @param string $locality
   */
  public function setLocality($locality)
  {
    $this->locality = $locality;
  }
  /**
   * @return string
   */
  public function getLocality()
  {
    return $this->locality;
  }
  /**
   * The company or company division name.
   *
   * @param string $organizationName
   */
  public function setOrganizationName($organizationName)
  {
    $this->organizationName = $organizationName;
  }
  /**
   * @return string
   */
  public function getOrganizationName()
  {
    return $this->organizationName;
  }
  /**
   * The postal code. A postalCode example is a postal zip code such as `10009`.
   * This is in accordance with - http: //portablecontacts.net/draft-
   * spec.html#address_element.
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
   * Name of the region. An example of a region value is `NY` for the state of
   * New York.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomerPostalAddress::class, 'Google_Service_Directory_CustomerPostalAddress');
