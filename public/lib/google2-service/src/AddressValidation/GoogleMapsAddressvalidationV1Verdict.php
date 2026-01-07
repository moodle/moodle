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

class GoogleMapsAddressvalidationV1Verdict extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const GEOCODE_GRANULARITY_GRANULARITY_UNSPECIFIED = 'GRANULARITY_UNSPECIFIED';
  /**
   * Below-building level result, such as an apartment.
   */
  public const GEOCODE_GRANULARITY_SUB_PREMISE = 'SUB_PREMISE';
  /**
   * Building-level result.
   */
  public const GEOCODE_GRANULARITY_PREMISE = 'PREMISE';
  /**
   * A geocode that approximates the building-level location of the address.
   */
  public const GEOCODE_GRANULARITY_PREMISE_PROXIMITY = 'PREMISE_PROXIMITY';
  /**
   * The address or geocode indicates a block. Only used in regions which have
   * block-level addressing, such as Japan.
   */
  public const GEOCODE_GRANULARITY_BLOCK = 'BLOCK';
  /**
   * The geocode or address is granular to route, such as a street, road, or
   * highway.
   */
  public const GEOCODE_GRANULARITY_ROUTE = 'ROUTE';
  /**
   * All other granularities, which are bucketed together since they are not
   * deliverable.
   */
  public const GEOCODE_GRANULARITY_OTHER = 'OTHER';
  /**
   * Default value. This value is unused.
   */
  public const INPUT_GRANULARITY_GRANULARITY_UNSPECIFIED = 'GRANULARITY_UNSPECIFIED';
  /**
   * Below-building level result, such as an apartment.
   */
  public const INPUT_GRANULARITY_SUB_PREMISE = 'SUB_PREMISE';
  /**
   * Building-level result.
   */
  public const INPUT_GRANULARITY_PREMISE = 'PREMISE';
  /**
   * A geocode that approximates the building-level location of the address.
   */
  public const INPUT_GRANULARITY_PREMISE_PROXIMITY = 'PREMISE_PROXIMITY';
  /**
   * The address or geocode indicates a block. Only used in regions which have
   * block-level addressing, such as Japan.
   */
  public const INPUT_GRANULARITY_BLOCK = 'BLOCK';
  /**
   * The geocode or address is granular to route, such as a street, road, or
   * highway.
   */
  public const INPUT_GRANULARITY_ROUTE = 'ROUTE';
  /**
   * All other granularities, which are bucketed together since they are not
   * deliverable.
   */
  public const INPUT_GRANULARITY_OTHER = 'OTHER';
  /**
   * Default value. This value is unused.
   */
  public const POSSIBLE_NEXT_ACTION_POSSIBLE_NEXT_ACTION_UNSPECIFIED = 'POSSIBLE_NEXT_ACTION_UNSPECIFIED';
  /**
   * One or more fields of the API response indicate a potential issue with the
   * post-processed address, for example the `verdict.validation_granularity` is
   * `OTHER`. Prompting your customer to edit the address could help improve the
   * quality of the address.
   */
  public const POSSIBLE_NEXT_ACTION_FIX = 'FIX';
  /**
   * The API response indicates the post-processed address might be missing a
   * subpremises. Prompting your customer to review the address and consider
   * adding a unit number could help improve the quality of the address. The
   * post-processed address might also have other minor issues. Note: this enum
   * value can only be returned for US addresses.
   */
  public const POSSIBLE_NEXT_ACTION_CONFIRM_ADD_SUBPREMISES = 'CONFIRM_ADD_SUBPREMISES';
  /**
   * One or more fields of the API response indicate potential minor issues with
   * the post-processed address, for example the `postal_code` address component
   * was `replaced`. Prompting your customer to review the address could help
   * improve the quality of the address.
   */
  public const POSSIBLE_NEXT_ACTION_CONFIRM = 'CONFIRM';
  /**
   * The API response does not contain signals that warrant one of the other
   * PossibleNextAction values. You might consider using the post-processed
   * address without further prompting your customer, though this does not
   * guarantee the address is valid, and the address might still contain
   * corrections. It is your responsibility to determine if and how to prompt
   * your customer, depending on your own risk assessment.
   */
  public const POSSIBLE_NEXT_ACTION_ACCEPT = 'ACCEPT';
  /**
   * Default value. This value is unused.
   */
  public const VALIDATION_GRANULARITY_GRANULARITY_UNSPECIFIED = 'GRANULARITY_UNSPECIFIED';
  /**
   * Below-building level result, such as an apartment.
   */
  public const VALIDATION_GRANULARITY_SUB_PREMISE = 'SUB_PREMISE';
  /**
   * Building-level result.
   */
  public const VALIDATION_GRANULARITY_PREMISE = 'PREMISE';
  /**
   * A geocode that approximates the building-level location of the address.
   */
  public const VALIDATION_GRANULARITY_PREMISE_PROXIMITY = 'PREMISE_PROXIMITY';
  /**
   * The address or geocode indicates a block. Only used in regions which have
   * block-level addressing, such as Japan.
   */
  public const VALIDATION_GRANULARITY_BLOCK = 'BLOCK';
  /**
   * The geocode or address is granular to route, such as a street, road, or
   * highway.
   */
  public const VALIDATION_GRANULARITY_ROUTE = 'ROUTE';
  /**
   * All other granularities, which are bucketed together since they are not
   * deliverable.
   */
  public const VALIDATION_GRANULARITY_OTHER = 'OTHER';
  /**
   * The post-processed address is considered complete if there are no
   * unresolved tokens, no unexpected or missing address components. If unset,
   * indicates that the value is `false`. See `missing_component_types`,
   * `unresolved_tokens` or `unexpected` fields for more details.
   *
   * @var bool
   */
  public $addressComplete;
  /**
   * Information about the granularity of the `geocode`. This can be understood
   * as the semantic meaning of how coarse or fine the geocoded location is.
   * This can differ from the `validation_granularity` above occasionally. For
   * example, our database might record the existence of an apartment number but
   * do not have a precise location for the apartment within a big apartment
   * complex. In that case, the `validation_granularity` will be `SUB_PREMISE`
   * but the `geocode_granularity` will be `PREMISE`.
   *
   * @var string
   */
  public $geocodeGranularity;
  /**
   * At least one address component was inferred (added) that wasn't in the
   * input, see [google.maps.addressvalidation.v1.Address.address_components]
   * for details.
   *
   * @var bool
   */
  public $hasInferredComponents;
  /**
   * At least one address component was replaced, see
   * [google.maps.addressvalidation.v1.Address.address_components] for details.
   *
   * @var bool
   */
  public $hasReplacedComponents;
  /**
   * At least one address component was spell-corrected, see
   * [google.maps.addressvalidation.v1.Address.address_components] for details.
   *
   * @var bool
   */
  public $hasSpellCorrectedComponents;
  /**
   * At least one address component cannot be categorized or validated, see
   * [google.maps.addressvalidation.v1.Address.address_components] for details.
   *
   * @var bool
   */
  public $hasUnconfirmedComponents;
  /**
   * The granularity of the **input** address. This is the result of parsing the
   * input address and does not give any validation signals. For validation
   * signals, refer to `validation_granularity` below. For example, if the input
   * address includes a specific apartment number, then the `input_granularity`
   * here will be `SUB_PREMISE`. If the address validation service cannot match
   * the apartment number in the databases or the apartment number is invalid,
   * the `validation_granularity` will likely be `PREMISE` or more coarse.
   *
   * @var string
   */
  public $inputGranularity;
  /**
   * Preview: This feature is in Preview (pre-GA). Pre-GA products and features
   * might have limited support, and changes to pre-GA products and features
   * might not be compatible with other pre-GA versions. Pre-GA Offerings are
   * covered by the [Google Maps Platform Service Specific
   * Terms](https://cloud.google.com/maps-platform/terms/maps-service-terms).
   * For more information, see the [launch stage
   * descriptions](https://developers.google.com/maps/launch-stages). Offers an
   * interpretive summary of the API response, intended to assist in determining
   * a potential subsequent action to take. This field is derived from other
   * fields in the API response and should not be considered as a guarantee of
   * address accuracy or deliverability. See [Build your validation
   * logic](https://developers.google.com/maps/documentation/address-
   * validation/build-validation-logic) for more details.
   *
   * @var string
   */
  public $possibleNextAction;
  /**
   * The level of granularity for the post-processed address that the API can
   * fully validate. For example, a `validation_granularity` of `PREMISE`
   * indicates all address components at the level of `PREMISE` or more coarse
   * can be validated. Per address component validation result can be found in
   * [google.maps.addressvalidation.v1.Address.address_components].
   *
   * @var string
   */
  public $validationGranularity;

  /**
   * The post-processed address is considered complete if there are no
   * unresolved tokens, no unexpected or missing address components. If unset,
   * indicates that the value is `false`. See `missing_component_types`,
   * `unresolved_tokens` or `unexpected` fields for more details.
   *
   * @param bool $addressComplete
   */
  public function setAddressComplete($addressComplete)
  {
    $this->addressComplete = $addressComplete;
  }
  /**
   * @return bool
   */
  public function getAddressComplete()
  {
    return $this->addressComplete;
  }
  /**
   * Information about the granularity of the `geocode`. This can be understood
   * as the semantic meaning of how coarse or fine the geocoded location is.
   * This can differ from the `validation_granularity` above occasionally. For
   * example, our database might record the existence of an apartment number but
   * do not have a precise location for the apartment within a big apartment
   * complex. In that case, the `validation_granularity` will be `SUB_PREMISE`
   * but the `geocode_granularity` will be `PREMISE`.
   *
   * Accepted values: GRANULARITY_UNSPECIFIED, SUB_PREMISE, PREMISE,
   * PREMISE_PROXIMITY, BLOCK, ROUTE, OTHER
   *
   * @param self::GEOCODE_GRANULARITY_* $geocodeGranularity
   */
  public function setGeocodeGranularity($geocodeGranularity)
  {
    $this->geocodeGranularity = $geocodeGranularity;
  }
  /**
   * @return self::GEOCODE_GRANULARITY_*
   */
  public function getGeocodeGranularity()
  {
    return $this->geocodeGranularity;
  }
  /**
   * At least one address component was inferred (added) that wasn't in the
   * input, see [google.maps.addressvalidation.v1.Address.address_components]
   * for details.
   *
   * @param bool $hasInferredComponents
   */
  public function setHasInferredComponents($hasInferredComponents)
  {
    $this->hasInferredComponents = $hasInferredComponents;
  }
  /**
   * @return bool
   */
  public function getHasInferredComponents()
  {
    return $this->hasInferredComponents;
  }
  /**
   * At least one address component was replaced, see
   * [google.maps.addressvalidation.v1.Address.address_components] for details.
   *
   * @param bool $hasReplacedComponents
   */
  public function setHasReplacedComponents($hasReplacedComponents)
  {
    $this->hasReplacedComponents = $hasReplacedComponents;
  }
  /**
   * @return bool
   */
  public function getHasReplacedComponents()
  {
    return $this->hasReplacedComponents;
  }
  /**
   * At least one address component was spell-corrected, see
   * [google.maps.addressvalidation.v1.Address.address_components] for details.
   *
   * @param bool $hasSpellCorrectedComponents
   */
  public function setHasSpellCorrectedComponents($hasSpellCorrectedComponents)
  {
    $this->hasSpellCorrectedComponents = $hasSpellCorrectedComponents;
  }
  /**
   * @return bool
   */
  public function getHasSpellCorrectedComponents()
  {
    return $this->hasSpellCorrectedComponents;
  }
  /**
   * At least one address component cannot be categorized or validated, see
   * [google.maps.addressvalidation.v1.Address.address_components] for details.
   *
   * @param bool $hasUnconfirmedComponents
   */
  public function setHasUnconfirmedComponents($hasUnconfirmedComponents)
  {
    $this->hasUnconfirmedComponents = $hasUnconfirmedComponents;
  }
  /**
   * @return bool
   */
  public function getHasUnconfirmedComponents()
  {
    return $this->hasUnconfirmedComponents;
  }
  /**
   * The granularity of the **input** address. This is the result of parsing the
   * input address and does not give any validation signals. For validation
   * signals, refer to `validation_granularity` below. For example, if the input
   * address includes a specific apartment number, then the `input_granularity`
   * here will be `SUB_PREMISE`. If the address validation service cannot match
   * the apartment number in the databases or the apartment number is invalid,
   * the `validation_granularity` will likely be `PREMISE` or more coarse.
   *
   * Accepted values: GRANULARITY_UNSPECIFIED, SUB_PREMISE, PREMISE,
   * PREMISE_PROXIMITY, BLOCK, ROUTE, OTHER
   *
   * @param self::INPUT_GRANULARITY_* $inputGranularity
   */
  public function setInputGranularity($inputGranularity)
  {
    $this->inputGranularity = $inputGranularity;
  }
  /**
   * @return self::INPUT_GRANULARITY_*
   */
  public function getInputGranularity()
  {
    return $this->inputGranularity;
  }
  /**
   * Preview: This feature is in Preview (pre-GA). Pre-GA products and features
   * might have limited support, and changes to pre-GA products and features
   * might not be compatible with other pre-GA versions. Pre-GA Offerings are
   * covered by the [Google Maps Platform Service Specific
   * Terms](https://cloud.google.com/maps-platform/terms/maps-service-terms).
   * For more information, see the [launch stage
   * descriptions](https://developers.google.com/maps/launch-stages). Offers an
   * interpretive summary of the API response, intended to assist in determining
   * a potential subsequent action to take. This field is derived from other
   * fields in the API response and should not be considered as a guarantee of
   * address accuracy or deliverability. See [Build your validation
   * logic](https://developers.google.com/maps/documentation/address-
   * validation/build-validation-logic) for more details.
   *
   * Accepted values: POSSIBLE_NEXT_ACTION_UNSPECIFIED, FIX,
   * CONFIRM_ADD_SUBPREMISES, CONFIRM, ACCEPT
   *
   * @param self::POSSIBLE_NEXT_ACTION_* $possibleNextAction
   */
  public function setPossibleNextAction($possibleNextAction)
  {
    $this->possibleNextAction = $possibleNextAction;
  }
  /**
   * @return self::POSSIBLE_NEXT_ACTION_*
   */
  public function getPossibleNextAction()
  {
    return $this->possibleNextAction;
  }
  /**
   * The level of granularity for the post-processed address that the API can
   * fully validate. For example, a `validation_granularity` of `PREMISE`
   * indicates all address components at the level of `PREMISE` or more coarse
   * can be validated. Per address component validation result can be found in
   * [google.maps.addressvalidation.v1.Address.address_components].
   *
   * Accepted values: GRANULARITY_UNSPECIFIED, SUB_PREMISE, PREMISE,
   * PREMISE_PROXIMITY, BLOCK, ROUTE, OTHER
   *
   * @param self::VALIDATION_GRANULARITY_* $validationGranularity
   */
  public function setValidationGranularity($validationGranularity)
  {
    $this->validationGranularity = $validationGranularity;
  }
  /**
   * @return self::VALIDATION_GRANULARITY_*
   */
  public function getValidationGranularity()
  {
    return $this->validationGranularity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsAddressvalidationV1Verdict::class, 'Google_Service_AddressValidation_GoogleMapsAddressvalidationV1Verdict');
