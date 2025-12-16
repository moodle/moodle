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

class GoogleMapsAddressvalidationV1ValidateAddressRequest extends \Google\Model
{
  protected $addressType = GoogleTypePostalAddress::class;
  protected $addressDataType = '';
  /**
   * Enables USPS CASS compatible mode. This affects _only_ the
   * [google.maps.addressvalidation.v1.ValidationResult.usps_data] field of
   * [google.maps.addressvalidation.v1.ValidationResult]. Note: for USPS CASS
   * enabled requests for addresses in Puerto Rico, a
   * [google.type.PostalAddress.region_code] of the `address` must be provided
   * as "PR", or an [google.type.PostalAddress.administrative_area] of the
   * `address` must be provided as "Puerto Rico" (case-insensitive) or "PR".
   * It's recommended to use a componentized `address`, or alternatively specify
   * at least two [google.type.PostalAddress.address_lines] where the first line
   * contains the street number and name and the second line contains the city,
   * state, and zip code.
   *
   * @var bool
   */
  public $enableUspsCass;
  protected $languageOptionsType = GoogleMapsAddressvalidationV1LanguageOptions::class;
  protected $languageOptionsDataType = '';
  /**
   * This field must be empty for the first address validation request. If more
   * requests are necessary to fully validate a single address (for example if
   * the changes the user makes after the initial validation need to be re-
   * validated), then each followup request must populate this field with the
   * response_id from the very first response in the validation sequence.
   *
   * @var string
   */
  public $previousResponseId;
  /**
   * Optional. A string which identifies an Autocomplete session for billing
   * purposes. Must be a URL and filename safe base64 string with at most 36
   * ASCII characters in length. Otherwise an INVALID_ARGUMENT error is
   * returned. The session begins when the user makes an Autocomplete query, and
   * concludes when they select a place and a call to Place Details or Address
   * Validation is made. Each session can have multiple Autocomplete queries,
   * followed by one Place Details or Address Validation request. The
   * credentials used for each request within a session must belong to the same
   * Google Cloud Console project. Once a session has concluded, the token is no
   * longer valid; your app must generate a fresh token for each session. If the
   * `sessionToken` parameter is omitted, or if you reuse a session token, the
   * session is charged as if no session token was provided (each request is
   * billed separately). Note: Address Validation can only be used in sessions
   * with the Autocomplete (New) API, not the Autocomplete API. See
   * https://developers.google.com/maps/documentation/places/web-
   * service/session-pricing for more details.
   *
   * @var string
   */
  public $sessionToken;

  /**
   * Required. The address being validated. Unformatted addresses should be
   * submitted via `address_lines`. The total length of the fields in this input
   * must not exceed 280 characters. Supported regions can be found
   * [here](https://developers.google.com/maps/documentation/address-
   * validation/coverage). The language_code value in the input address is
   * reserved for future uses and is ignored today. The validated address result
   * will be populated based on the preferred language for the given address, as
   * identified by the system. The Address Validation API ignores the values in
   * recipients and organization. Any values in those fields will be discarded
   * and not returned. Please do not set them.
   *
   * @param GoogleTypePostalAddress $address
   */
  public function setAddress(GoogleTypePostalAddress $address)
  {
    $this->address = $address;
  }
  /**
   * @return GoogleTypePostalAddress
   */
  public function getAddress()
  {
    return $this->address;
  }
  /**
   * Enables USPS CASS compatible mode. This affects _only_ the
   * [google.maps.addressvalidation.v1.ValidationResult.usps_data] field of
   * [google.maps.addressvalidation.v1.ValidationResult]. Note: for USPS CASS
   * enabled requests for addresses in Puerto Rico, a
   * [google.type.PostalAddress.region_code] of the `address` must be provided
   * as "PR", or an [google.type.PostalAddress.administrative_area] of the
   * `address` must be provided as "Puerto Rico" (case-insensitive) or "PR".
   * It's recommended to use a componentized `address`, or alternatively specify
   * at least two [google.type.PostalAddress.address_lines] where the first line
   * contains the street number and name and the second line contains the city,
   * state, and zip code.
   *
   * @param bool $enableUspsCass
   */
  public function setEnableUspsCass($enableUspsCass)
  {
    $this->enableUspsCass = $enableUspsCass;
  }
  /**
   * @return bool
   */
  public function getEnableUspsCass()
  {
    return $this->enableUspsCass;
  }
  /**
   * Optional. Preview: This feature is in Preview (pre-GA). Pre-GA products and
   * features might have limited support, and changes to pre-GA products and
   * features might not be compatible with other pre-GA versions. Pre-GA
   * Offerings are covered by the [Google Maps Platform Service Specific
   * Terms](https://cloud.google.com/maps-platform/terms/maps-service-terms).
   * For more information, see the [launch stage
   * descriptions](https://developers.google.com/maps/launch-stages). Enables
   * the Address Validation API to include additional information in the
   * response.
   *
   * @param GoogleMapsAddressvalidationV1LanguageOptions $languageOptions
   */
  public function setLanguageOptions(GoogleMapsAddressvalidationV1LanguageOptions $languageOptions)
  {
    $this->languageOptions = $languageOptions;
  }
  /**
   * @return GoogleMapsAddressvalidationV1LanguageOptions
   */
  public function getLanguageOptions()
  {
    return $this->languageOptions;
  }
  /**
   * This field must be empty for the first address validation request. If more
   * requests are necessary to fully validate a single address (for example if
   * the changes the user makes after the initial validation need to be re-
   * validated), then each followup request must populate this field with the
   * response_id from the very first response in the validation sequence.
   *
   * @param string $previousResponseId
   */
  public function setPreviousResponseId($previousResponseId)
  {
    $this->previousResponseId = $previousResponseId;
  }
  /**
   * @return string
   */
  public function getPreviousResponseId()
  {
    return $this->previousResponseId;
  }
  /**
   * Optional. A string which identifies an Autocomplete session for billing
   * purposes. Must be a URL and filename safe base64 string with at most 36
   * ASCII characters in length. Otherwise an INVALID_ARGUMENT error is
   * returned. The session begins when the user makes an Autocomplete query, and
   * concludes when they select a place and a call to Place Details or Address
   * Validation is made. Each session can have multiple Autocomplete queries,
   * followed by one Place Details or Address Validation request. The
   * credentials used for each request within a session must belong to the same
   * Google Cloud Console project. Once a session has concluded, the token is no
   * longer valid; your app must generate a fresh token for each session. If the
   * `sessionToken` parameter is omitted, or if you reuse a session token, the
   * session is charged as if no session token was provided (each request is
   * billed separately). Note: Address Validation can only be used in sessions
   * with the Autocomplete (New) API, not the Autocomplete API. See
   * https://developers.google.com/maps/documentation/places/web-
   * service/session-pricing for more details.
   *
   * @param string $sessionToken
   */
  public function setSessionToken($sessionToken)
  {
    $this->sessionToken = $sessionToken;
  }
  /**
   * @return string
   */
  public function getSessionToken()
  {
    return $this->sessionToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsAddressvalidationV1ValidateAddressRequest::class, 'Google_Service_AddressValidation_GoogleMapsAddressvalidationV1ValidateAddressRequest');
