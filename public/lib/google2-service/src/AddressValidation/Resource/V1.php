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

namespace Google\Service\AddressValidation\Resource;

use Google\Service\AddressValidation\GoogleMapsAddressvalidationV1ProvideValidationFeedbackRequest;
use Google\Service\AddressValidation\GoogleMapsAddressvalidationV1ProvideValidationFeedbackResponse;
use Google\Service\AddressValidation\GoogleMapsAddressvalidationV1ValidateAddressRequest;
use Google\Service\AddressValidation\GoogleMapsAddressvalidationV1ValidateAddressResponse;

/**
 * The "v1" collection of methods.
 * Typical usage is:
 *  <code>
 *   $addressvalidationService = new Google\Service\AddressValidation(...);
 *   $v1 = $addressvalidationService->v1;
 *  </code>
 */
class V1 extends \Google\Service\Resource
{
  /**
   * Feedback about the outcome of the sequence of validation attempts. This
   * should be the last call made after a sequence of validation calls for the
   * same address, and should be called once the transaction is concluded. This
   * should only be sent once for the sequence of `ValidateAddress` requests
   * needed to validate an address fully. (v1.provideValidationFeedback)
   *
   * @param GoogleMapsAddressvalidationV1ProvideValidationFeedbackRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleMapsAddressvalidationV1ProvideValidationFeedbackResponse
   * @throws \Google\Service\Exception
   */
  public function provideValidationFeedback(GoogleMapsAddressvalidationV1ProvideValidationFeedbackRequest $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('provideValidationFeedback', [$params], GoogleMapsAddressvalidationV1ProvideValidationFeedbackResponse::class);
  }
  /**
   * Validates an address. (v1.validateAddress)
   *
   * @param GoogleMapsAddressvalidationV1ValidateAddressRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleMapsAddressvalidationV1ValidateAddressResponse
   * @throws \Google\Service\Exception
   */
  public function validateAddress(GoogleMapsAddressvalidationV1ValidateAddressRequest $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('validateAddress', [$params], GoogleMapsAddressvalidationV1ValidateAddressResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(V1::class, 'Google_Service_AddressValidation_Resource_V1');
