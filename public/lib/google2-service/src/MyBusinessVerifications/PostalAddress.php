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

namespace Google\Service\MyBusinessVerifications;

class PostalAddress extends \Google\Collection
{
  protected $collection_key = 'recipients';
  /**
   * Unstructured address lines describing the lower levels of an address.
   * Because values in `address_lines` do not have type information and may
   * sometimes contain multiple values in a single field (for example, "Austin,
   * TX"), it is important that the line order is clear. The order of address
   * lines should be "envelope order" for the country or region of the address.
   * In places where this can vary (for example, Japan), `address_language` is
   * used to make it explicit (for example, "ja" for large-to-small ordering and
   * "ja-Latn" or "en" for small-to-large). In this way, the most specific line
   * of an address can be selected based on the language. The minimum permitted
   * structural representation of an address consists of a `region_code` with
   * all remaining information placed in the `address_lines`. It would be
   * possible to format such an address very approximately without geocoding,
   * but no semantic reasoning could be made about any of the address components
   * until it was at least partially resolved. Creating an address only
   * containing a `region_code` and `address_lines` and then geocoding is the
   * recommended way to handle completely unstructured addresses (as opposed to
   * guessing which parts of the address should be localities or administrative
   * areas).
   *
   * @var string[]
   */
  public $addressLines;
  /**
   * Optional. Highest administrative subdivision which is used for postal
   * addresses of a country or region. For example, this can be a state, a
   * province, an oblast, or a prefecture. For Spain, this is the province and
   * not the autonomous community (for example, "Barcelona" and not
   * "Catalonia"). Many countries don't use an administrative area in postal
   * addresses. For example, in Switzerland, this should be left unpopulated.
   *
   * @var string
   */
  public $administrativeArea;
  /**
   * Optional. BCP-47 language code of the contents of this address (if known).
   * This is often the UI language of the input form or is expected to match one
   * of the languages used in the address' country/region, or their
   * transliterated equivalents. This can affect formatting in certain
   * countries, but is not critical to the correctness of the data and will
   * never affect any validation or other non-formatting related operations. If
   * this value is not known, it should be omitted (rather than specifying a
   * possibly incorrect default). Examples: "zh-Hant", "ja", "ja-Latn", "en".
   *
   * @var string
   */
  public $languageCode;
  /**
   * Optional. Generally refers to the city or town portion of the address.
   * Examples: US city, IT comune, UK post town. In regions of the world where
   * localities are not well defined or do not fit into this structure well,
   * leave `locality` empty and use `address_lines`.
   *
   * @var string
   */
  public $locality;
  /**
   * Optional. The name of the organization at the address.
   *
   * @var string
   */
  public $organization;
  /**
   * Optional. Postal code of the address. Not all countries use or require
   * postal codes to be present, but where they are used, they may trigger
   * additional validation with other parts of the address (for example, state
   * or zip code validation in the United States).
   *
   * @var string
   */
  public $postalCode;
  /**
   * Optional. The recipient at the address. This field may, under certain
   * circumstances, contain multiline information. For example, it might contain
   * "care of" information.
   *
   * @var string[]
   */
  public $recipients;
  /**
   * Required. CLDR region code of the country/region of the address. This is
   * never inferred and it is up to the user to ensure the value is correct. See
   * https://cldr.unicode.org/ and https://www.unicode.org/cldr/charts/30/supple
   * mental/territory_information.html for details. Example: "CH" for
   * Switzerland.
   *
   * @var string
   */
  public $regionCode;
  /**
   * The schema revision of the `PostalAddress`. This must be set to 0, which is
   * the latest revision. All new revisions **must** be backward compatible with
   * old revisions.
   *
   * @var int
   */
  public $revision;
  /**
   * Optional. Additional, country-specific, sorting code. This is not used in
   * most regions. Where it is used, the value is either a string like "CEDEX",
   * optionally followed by a number (for example, "CEDEX 7"), or just a number
   * alone, representing the "sector code" (Jamaica), "delivery area indicator"
   * (Malawi) or "post office indicator" (Côte d'Ivoire).
   *
   * @var string
   */
  public $sortingCode;
  /**
   * Optional. Sublocality of the address. For example, this can be a
   * neighborhood, borough, or district.
   *
   * @var string
   */
  public $sublocality;

  /**
   * Unstructured address lines describing the lower levels of an address.
   * Because values in `address_lines` do not have type information and may
   * sometimes contain multiple values in a single field (for example, "Austin,
   * TX"), it is important that the line order is clear. The order of address
   * lines should be "envelope order" for the country or region of the address.
   * In places where this can vary (for example, Japan), `address_language` is
   * used to make it explicit (for example, "ja" for large-to-small ordering and
   * "ja-Latn" or "en" for small-to-large). In this way, the most specific line
   * of an address can be selected based on the language. The minimum permitted
   * structural representation of an address consists of a `region_code` with
   * all remaining information placed in the `address_lines`. It would be
   * possible to format such an address very approximately without geocoding,
   * but no semantic reasoning could be made about any of the address components
   * until it was at least partially resolved. Creating an address only
   * containing a `region_code` and `address_lines` and then geocoding is the
   * recommended way to handle completely unstructured addresses (as opposed to
   * guessing which parts of the address should be localities or administrative
   * areas).
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
   * addresses of a country or region. For example, this can be a state, a
   * province, an oblast, or a prefecture. For Spain, this is the province and
   * not the autonomous community (for example, "Barcelona" and not
   * "Catalonia"). Many countries don't use an administrative area in postal
   * addresses. For example, in Switzerland, this should be left unpopulated.
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
   * This is often the UI language of the input form or is expected to match one
   * of the languages used in the address' country/region, or their
   * transliterated equivalents. This can affect formatting in certain
   * countries, but is not critical to the correctness of the data and will
   * never affect any validation or other non-formatting related operations. If
   * this value is not known, it should be omitted (rather than specifying a
   * possibly incorrect default). Examples: "zh-Hant", "ja", "ja-Latn", "en".
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
   * Optional. Generally refers to the city or town portion of the address.
   * Examples: US city, IT comune, UK post town. In regions of the world where
   * localities are not well defined or do not fit into this structure well,
   * leave `locality` empty and use `address_lines`.
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
   * Optional. The name of the organization at the address.
   *
   * @param string $organization
   */
  public function setOrganization($organization)
  {
    $this->organization = $organization;
  }
  /**
   * @return string
   */
  public function getOrganization()
  {
    return $this->organization;
  }
  /**
   * Optional. Postal code of the address. Not all countries use or require
   * postal codes to be present, but where they are used, they may trigger
   * additional validation with other parts of the address (for example, state
   * or zip code validation in the United States).
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
   * Optional. The recipient at the address. This field may, under certain
   * circumstances, contain multiline information. For example, it might contain
   * "care of" information.
   *
   * @param string[] $recipients
   */
  public function setRecipients($recipients)
  {
    $this->recipients = $recipients;
  }
  /**
   * @return string[]
   */
  public function getRecipients()
  {
    return $this->recipients;
  }
  /**
   * Required. CLDR region code of the country/region of the address. This is
   * never inferred and it is up to the user to ensure the value is correct. See
   * https://cldr.unicode.org/ and https://www.unicode.org/cldr/charts/30/supple
   * mental/territory_information.html for details. Example: "CH" for
   * Switzerland.
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
   * The schema revision of the `PostalAddress`. This must be set to 0, which is
   * the latest revision. All new revisions **must** be backward compatible with
   * old revisions.
   *
   * @param int $revision
   */
  public function setRevision($revision)
  {
    $this->revision = $revision;
  }
  /**
   * @return int
   */
  public function getRevision()
  {
    return $this->revision;
  }
  /**
   * Optional. Additional, country-specific, sorting code. This is not used in
   * most regions. Where it is used, the value is either a string like "CEDEX",
   * optionally followed by a number (for example, "CEDEX 7"), or just a number
   * alone, representing the "sector code" (Jamaica), "delivery area indicator"
   * (Malawi) or "post office indicator" (Côte d'Ivoire).
   *
   * @param string $sortingCode
   */
  public function setSortingCode($sortingCode)
  {
    $this->sortingCode = $sortingCode;
  }
  /**
   * @return string
   */
  public function getSortingCode()
  {
    return $this->sortingCode;
  }
  /**
   * Optional. Sublocality of the address. For example, this can be a
   * neighborhood, borough, or district.
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
class_alias(PostalAddress::class, 'Google_Service_MyBusinessVerifications_PostalAddress');
