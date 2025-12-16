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

class BuildingAddress extends \Google\Collection
{
  protected $collection_key = 'addressLines';
  /**
   * Unstructured address lines describing the lower levels of an address.
   *
   * @var string[]
   */
  public $addressLines;
  /**
   * Optional. Highest administrative subdivision which is used for postal
   * addresses of a country or region.
   *
   * @var string
   */
  public $administrativeArea;
  /**
   * Optional. BCP-47 language code of the contents of this address (if known).
   *
   * @var string
   */
  public $languageCode;
  /**
   * Optional. Generally refers to the city/town portion of the address.
   * Examples: US city, IT comune, UK post town. In regions of the world where
   * localities are not well defined or do not fit into this structure well,
   * leave locality empty and use addressLines.
   *
   * @var string
   */
  public $locality;
  /**
   * Optional. Postal code of the address.
   *
   * @var string
   */
  public $postalCode;
  /**
   * Required. CLDR region code of the country/region of the address.
   *
   * @var string
   */
  public $regionCode;
  /**
   * Optional. Sublocality of the address.
   *
   * @var string
   */
  public $sublocality;

  /**
   * Unstructured address lines describing the lower levels of an address.
   *
   * @param string[] $addressLines
   */
  public function setAddressLines($addressLines)
  {
    $this->addressLines = $addressLines;
  }
  /**
   * @return string[]
   */
  public function getAddressLines()
  {
    return $this->addressLines;
  }
  /**
   * Optional. Highest administrative subdivision which is used for postal
   * addresses of a country or region.
   *
   * @param string $administrativeArea
   */
  public function setAdministrativeArea($administrativeArea)
  {
    $this->administrativeArea = $administrativeArea;
  }
  /**
   * @return string
   */
  public function getAdministrativeArea()
  {
    return $this->administrativeArea;
  }
  /**
   * Optional. BCP-47 language code of the contents of this address (if known).
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * Optional. Generally refers to the city/town portion of the address.
   * Examples: US city, IT comune, UK post town. In regions of the world where
   * localities are not well defined or do not fit into this structure well,
   * leave locality empty and use addressLines.
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
   * Optional. Postal code of the address.
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
   * Required. CLDR region code of the country/region of the address.
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
  /**
   * Optional. Sublocality of the address.
   *
   * @param string $sublocality
   */
  public function setSublocality($sublocality)
  {
    $this->sublocality = $sublocality;
  }
  /**
   * @return string
   */
  public function getSublocality()
  {
    return $this->sublocality;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BuildingAddress::class, 'Google_Service_Directory_BuildingAddress');
