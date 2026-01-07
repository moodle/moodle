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

namespace Google\Service\AddressValidation;

class GoogleMapsAddressvalidationV1ValidationResult extends \Google\Model
{
  protected $addressType = GoogleMapsAddressvalidationV1Address::class;
  protected $addressDataType = '';
  protected $englishLatinAddressType = GoogleMapsAddressvalidationV1Address::class;
  protected $englishLatinAddressDataType = '';
  protected $geocodeType = GoogleMapsAddressvalidationV1Geocode::class;
  protected $geocodeDataType = '';
  protected $metadataType = GoogleMapsAddressvalidationV1AddressMetadata::class;
  protected $metadataDataType = '';
  protected $uspsDataType = GoogleMapsAddressvalidationV1UspsData::class;
  protected $uspsDataDataType = '';
  protected $verdictType = GoogleMapsAddressvalidationV1Verdict::class;
  protected $verdictDataType = '';

  /**
   * Information about the address itself as opposed to the geocode.
   *
   * @param GoogleMapsAddressvalidationV1Address $address
   */
  public function setAddress(GoogleMapsAddressvalidationV1Address $address)
  {
    $this->address = $address;
  }
  /**
   * @return GoogleMapsAddressvalidationV1Address
   */
  public function getAddress()
  {
    return $this->address;
  }
  /**
   * Preview: This feature is in Preview (pre-GA). Pre-GA products and features
   * might have limited support, and changes to pre-GA products and features
   * might not be compatible with other pre-GA versions. Pre-GA Offerings are
   * covered by the [Google Maps Platform Service Specific
   * Terms](https://cloud.google.com/maps-platform/terms/maps-service-terms).
   * For more information, see the [launch stage
   * descriptions](https://developers.google.com/maps/launch-stages). The
   * address translated to English. Translated addresses are not reusable as API
   * input. The service provides them so that the user can use their native
   * language to confirm or deny the validation of the originally-provided
   * address. If part of the address doesn't have an English translation, the
   * service returns that part in an alternate language that uses a Latin
   * script. See
   * [here](https://developers.google.com/maps/documentation/address-
   * validation/convert-addresses-english) for an explanation of how the
   * alternate language is selected. If part of the address doesn't have any
   * translations or transliterations in a language that uses a Latin script,
   * the service returns that part in the local language associated with the
   * address. Enable this output by using the [google.maps.addressvalidation.v1.
   * LanguageOptions.return_english_latin_address] flag. Note: the
   * [google.maps.addressvalidation.v1.Address.unconfirmed_component_types]
   * field in the `english_latin_address` and the
   * [google.maps.addressvalidation.v1.AddressComponent.confirmation_level]
   * fields in `english_latin_address.address_components` are not populated.
   *
   * @param GoogleMapsAddressvalidationV1Address $englishLatinAddress
   */
  public function setEnglishLatinAddress(GoogleMapsAddressvalidationV1Address $englishLatinAddress)
  {
    $this->englishLatinAddress = $englishLatinAddress;
  }
  /**
   * @return GoogleMapsAddressvalidationV1Address
   */
  public function getEnglishLatinAddress()
  {
    return $this->englishLatinAddress;
  }
  /**
   * Information about the location and place that the address geocoded to.
   *
   * @param GoogleMapsAddressvalidationV1Geocode $geocode
   */
  public function setGeocode(GoogleMapsAddressvalidationV1Geocode $geocode)
  {
    $this->geocode = $geocode;
  }
  /**
   * @return GoogleMapsAddressvalidationV1Geocode
   */
  public function getGeocode()
  {
    return $this->geocode;
  }
  /**
   * Other information relevant to deliverability. `metadata` is not guaranteed
   * to be fully populated for every address sent to the Address Validation API.
   *
   * @param GoogleMapsAddressvalidationV1AddressMetadata $metadata
   */
  public function setMetadata(GoogleMapsAddressvalidationV1AddressMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return GoogleMapsAddressvalidationV1AddressMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Extra deliverability flags provided by USPS. Only provided in region `US`
   * and `PR`.
   *
   * @param GoogleMapsAddressvalidationV1UspsData $uspsData
   */
  public function setUspsData(GoogleMapsAddressvalidationV1UspsData $uspsData)
  {
    $this->uspsData = $uspsData;
  }
  /**
   * @return GoogleMapsAddressvalidationV1UspsData
   */
  public function getUspsData()
  {
    return $this->uspsData;
  }
  /**
   * Overall verdict flags
   *
   * @param GoogleMapsAddressvalidationV1Verdict $verdict
   */
  public function setVerdict(GoogleMapsAddressvalidationV1Verdict $verdict)
  {
    $this->verdict = $verdict;
  }
  /**
   * @return GoogleMapsAddressvalidationV1Verdict
   */
  public function getVerdict()
  {
    return $this->verdict;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsAddressvalidationV1ValidationResult::class, 'Google_Service_AddressValidation_GoogleMapsAddressvalidationV1ValidationResult');
