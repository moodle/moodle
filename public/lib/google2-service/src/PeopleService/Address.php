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

namespace Google\Service\PeopleService;

class Address extends \Google\Model
{
  /**
   * The city of the address.
   *
   * @var string
   */
  public $city;
  /**
   * The country of the address.
   *
   * @var string
   */
  public $country;
  /**
   * The [ISO 3166-1 alpha-2](http://www.iso.org/iso/country_codes.htm) country
   * code of the address.
   *
   * @var string
   */
  public $countryCode;
  /**
   * The extended address of the address; for example, the apartment number.
   *
   * @var string
   */
  public $extendedAddress;
  /**
   * Output only. The type of the address translated and formatted in the
   * viewer's account locale or the `Accept-Language` HTTP header locale.
   *
   * @var string
   */
  public $formattedType;
  /**
   * The unstructured value of the address. If this is not set by the user it
   * will be automatically constructed from structured values.
   *
   * @var string
   */
  public $formattedValue;
  protected $metadataType = FieldMetadata::class;
  protected $metadataDataType = '';
  /**
   * The P.O. box of the address.
   *
   * @var string
   */
  public $poBox;
  /**
   * The postal code of the address.
   *
   * @var string
   */
  public $postalCode;
  /**
   * The region of the address; for example, the state or province.
   *
   * @var string
   */
  public $region;
  /**
   * The street address.
   *
   * @var string
   */
  public $streetAddress;
  /**
   * The type of the address. The type can be custom or one of these predefined
   * values: * `home` * `work` * `other`
   *
   * @var string
   */
  public $type;

  /**
   * The city of the address.
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
   * The country of the address.
   *
   * @param string $country
   */
  public function setCountry($country)
  {
    $this->country = $country;
  }
  /**
   * @return string
   */
  public function getCountry()
  {
    return $this->country;
  }
  /**
   * The [ISO 3166-1 alpha-2](http://www.iso.org/iso/country_codes.htm) country
   * code of the address.
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
   * The extended address of the address; for example, the apartment number.
   *
   * @param string $extendedAddress
   */
  public function setExtendedAddress($extendedAddress)
  {
    $this->extendedAddress = $extendedAddress;
  }
  /**
   * @return string
   */
  public function getExtendedAddress()
  {
    return $this->extendedAddress;
  }
  /**
   * Output only. The type of the address translated and formatted in the
   * viewer's account locale or the `Accept-Language` HTTP header locale.
   *
   * @param string $formattedType
   */
  public function setFormattedType($formattedType)
  {
    $this->formattedType = $formattedType;
  }
  /**
   * @return string
   */
  public function getFormattedType()
  {
    return $this->formattedType;
  }
  /**
   * The unstructured value of the address. If this is not set by the user it
   * will be automatically constructed from structured values.
   *
   * @param string $formattedValue
   */
  public function setFormattedValue($formattedValue)
  {
    $this->formattedValue = $formattedValue;
  }
  /**
   * @return string
   */
  public function getFormattedValue()
  {
    return $this->formattedValue;
  }
  /**
   * Metadata about the address.
   *
   * @param FieldMetadata $metadata
   */
  public function setMetadata(FieldMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return FieldMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * The P.O. box of the address.
   *
   * @param string $poBox
   */
  public function setPoBox($poBox)
  {
    $this->poBox = $poBox;
  }
  /**
   * @return string
   */
  public function getPoBox()
  {
    return $this->poBox;
  }
  /**
   * The postal code of the address.
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
   * The region of the address; for example, the state or province.
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
  /**
   * The street address.
   *
   * @param string $streetAddress
   */
  public function setStreetAddress($streetAddress)
  {
    $this->streetAddress = $streetAddress;
  }
  /**
   * @return string
   */
  public function getStreetAddress()
  {
    return $this->streetAddress;
  }
  /**
   * The type of the address. The type can be custom or one of these predefined
   * values: * `home` * `work` * `other`
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Address::class, 'Google_Service_PeopleService_Address');
