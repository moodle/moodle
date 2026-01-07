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

namespace Google\Service\Reseller;

class Address extends \Google\Model
{
  /**
   * A customer's physical address. An address can be composed of one to three
   * lines. The `addressline2` and `addressLine3` are optional.
   *
   * @var string
   */
  public $addressLine1;
  /**
   * Line 2 of the address.
   *
   * @var string
   */
  public $addressLine2;
  /**
   * Line 3 of the address.
   *
   * @var string
   */
  public $addressLine3;
  /**
   * The customer contact's name. This is required.
   *
   * @var string
   */
  public $contactName;
  /**
   * For `countryCode` information, see the ISO 3166 country code elements.
   * Verify that country is approved for resale of Google products. This
   * property is required when creating a new customer.
   *
   * @var string
   */
  public $countryCode;
  /**
   * Identifies the resource as a customer address. Value: `customers#address`
   *
   * @var string
   */
  public $kind;
  /**
   * An example of a `locality` value is the city of `San Francisco`.
   *
   * @var string
   */
  public $locality;
  /**
   * The company or company division name. This is required.
   *
   * @var string
   */
  public $organizationName;
  /**
   * A `postalCode` example is a postal zip code such as `94043`. This property
   * is required when creating a new customer.
   *
   * @var string
   */
  public $postalCode;
  /**
   * An example of a `region` value is `CA` for the state of California.
   *
   * @var string
   */
  public $region;

  /**
   * A customer's physical address. An address can be composed of one to three
   * lines. The `addressline2` and `addressLine3` are optional.
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
   * Line 2 of the address.
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
   * Line 3 of the address.
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
   * The customer contact's name. This is required.
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
   * For `countryCode` information, see the ISO 3166 country code elements.
   * Verify that country is approved for resale of Google products. This
   * property is required when creating a new customer.
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
   * Identifies the resource as a customer address. Value: `customers#address`
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * An example of a `locality` value is the city of `San Francisco`.
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
   * The company or company division name. This is required.
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
   * A `postalCode` example is a postal zip code such as `94043`. This property
   * is required when creating a new customer.
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
   * An example of a `region` value is `CA` for the state of California.
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
class_alias(Address::class, 'Google_Service_Reseller_Address');
