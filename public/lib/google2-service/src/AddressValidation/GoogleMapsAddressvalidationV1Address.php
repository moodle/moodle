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

class GoogleMapsAddressvalidationV1Address extends \Google\Collection
{
  protected $collection_key = 'unresolvedTokens';
  protected $addressComponentsType = GoogleMapsAddressvalidationV1AddressComponent::class;
  protected $addressComponentsDataType = 'array';
  /**
   * The post-processed address, formatted as a single-line address following
   * the address formatting rules of the region where the address is located.
   * Note: the format of this address may not match the format of the address in
   * the `postal_address` field. For example, the `postal_address` always
   * represents the country as a 2 letter `region_code`, such as "US" or "NZ".
   * By contrast, this field uses a longer form of the country name, such as
   * "USA" or "New Zealand".
   *
   * @var string
   */
  public $formattedAddress;
  /**
   * The types of components that were expected to be present in a correctly
   * formatted mailing address but were not found in the input AND could not be
   * inferred. An example might be `['street_number', 'route']` for an input
   * like "Boulder, Colorado, 80301, USA". The list of possible types can be
   * found
   * [here](https://developers.google.com/maps/documentation/geocoding/requests-
   * geocoding#Types). **Note: you might see a missing component type when you
   * think you've already supplied the missing component.** For example, this
   * can happen when the input address contains the building name, but not the
   * premise number. In the address "渋谷区渋谷３丁目　Shibuya Stream", the building name
   * "Shibuya Stream" has the component type `premise`, but the premise number
   * is missing, so `missing_component_types` will contain `premise`.
   *
   * @var string[]
   */
  public $missingComponentTypes;
  protected $postalAddressType = GoogleTypePostalAddress::class;
  protected $postalAddressDataType = '';
  /**
   * The types of the components that are present in the `address_components`
   * but could not be confirmed to be correct. This field is provided for the
   * sake of convenience: its contents are equivalent to iterating through the
   * `address_components` to find the types of all the components where the
   * confirmation_level is not CONFIRMED or the inferred flag is not set to
   * `true`. The list of possible types can be found
   * [here](https://developers.google.com/maps/documentation/geocoding/requests-
   * geocoding#Types).
   *
   * @var string[]
   */
  public $unconfirmedComponentTypes;
  /**
   * Any tokens in the input that could not be resolved. This might be an input
   * that was not recognized as a valid part of an address. For example, for an
   * input such as "Parcel 0000123123 & 0000456456 Str # Guthrie Center IA 50115
   * US", the unresolved tokens might look like `["Parcel", "0000123123", "&",
   * "0000456456"]`.
   *
   * @var string[]
   */
  public $unresolvedTokens;

  /**
   * Unordered list. The individual address components of the formatted and
   * corrected address, along with validation information. This provides
   * information on the validation status of the individual components. Address
   * components are not ordered in a particular way. Do not make any assumptions
   * on the ordering of the address components in the list.
   *
   * @param GoogleMapsAddressvalidationV1AddressComponent[] $addressComponents
   */
  public function setAddressComponents($addressComponents)
  {
    $this->addressComponents = $addressComponents;
  }
  /**
   * @return GoogleMapsAddressvalidationV1AddressComponent[]
   */
  public function getAddressComponents()
  {
    return $this->addressComponents;
  }
  /**
   * The post-processed address, formatted as a single-line address following
   * the address formatting rules of the region where the address is located.
   * Note: the format of this address may not match the format of the address in
   * the `postal_address` field. For example, the `postal_address` always
   * represents the country as a 2 letter `region_code`, such as "US" or "NZ".
   * By contrast, this field uses a longer form of the country name, such as
   * "USA" or "New Zealand".
   *
   * @param string $formattedAddress
   */
  public function setFormattedAddress($formattedAddress)
  {
    $this->formattedAddress = $formattedAddress;
  }
  /**
   * @return string
   */
  public function getFormattedAddress()
  {
    return $this->formattedAddress;
  }
  /**
   * The types of components that were expected to be present in a correctly
   * formatted mailing address but were not found in the input AND could not be
   * inferred. An example might be `['street_number', 'route']` for an input
   * like "Boulder, Colorado, 80301, USA". The list of possible types can be
   * found
   * [here](https://developers.google.com/maps/documentation/geocoding/requests-
   * geocoding#Types). **Note: you might see a missing component type when you
   * think you've already supplied the missing component.** For example, this
   * can happen when the input address contains the building name, but not the
   * premise number. In the address "渋谷区渋谷３丁目　Shibuya Stream", the building name
   * "Shibuya Stream" has the component type `premise`, but the premise number
   * is missing, so `missing_component_types` will contain `premise`.
   *
   * @param string[] $missingComponentTypes
   */
  public function setMissingComponentTypes($missingComponentTypes)
  {
    $this->missingComponentTypes = $missingComponentTypes;
  }
  /**
   * @return string[]
   */
  public function getMissingComponentTypes()
  {
    return $this->missingComponentTypes;
  }
  /**
   * The post-processed address represented as a postal address.
   *
   * @param GoogleTypePostalAddress $postalAddress
   */
  public function setPostalAddress(GoogleTypePostalAddress $postalAddress)
  {
    $this->postalAddress = $postalAddress;
  }
  /**
   * @return GoogleTypePostalAddress
   */
  public function getPostalAddress()
  {
    return $this->postalAddress;
  }
  /**
   * The types of the components that are present in the `address_components`
   * but could not be confirmed to be correct. This field is provided for the
   * sake of convenience: its contents are equivalent to iterating through the
   * `address_components` to find the types of all the components where the
   * confirmation_level is not CONFIRMED or the inferred flag is not set to
   * `true`. The list of possible types can be found
   * [here](https://developers.google.com/maps/documentation/geocoding/requests-
   * geocoding#Types).
   *
   * @param string[] $unconfirmedComponentTypes
   */
  public function setUnconfirmedComponentTypes($unconfirmedComponentTypes)
  {
    $this->unconfirmedComponentTypes = $unconfirmedComponentTypes;
  }
  /**
   * @return string[]
   */
  public function getUnconfirmedComponentTypes()
  {
    return $this->unconfirmedComponentTypes;
  }
  /**
   * Any tokens in the input that could not be resolved. This might be an input
   * that was not recognized as a valid part of an address. For example, for an
   * input such as "Parcel 0000123123 & 0000456456 Str # Guthrie Center IA 50115
   * US", the unresolved tokens might look like `["Parcel", "0000123123", "&",
   * "0000456456"]`.
   *
   * @param string[] $unresolvedTokens
   */
  public function setUnresolvedTokens($unresolvedTokens)
  {
    $this->unresolvedTokens = $unresolvedTokens;
  }
  /**
   * @return string[]
   */
  public function getUnresolvedTokens()
  {
    return $this->unresolvedTokens;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsAddressvalidationV1Address::class, 'Google_Service_AddressValidation_GoogleMapsAddressvalidationV1Address');
