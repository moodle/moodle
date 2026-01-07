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

class GoogleMapsAddressvalidationV1ProvideValidationFeedbackRequest extends \Google\Model
{
  /**
   * This value is unused. If the `ProvideValidationFeedbackRequest.conclusion`
   * field is set to `VALIDATION_CONCLUSION_UNSPECIFIED`, an `INVALID_ARGUMENT`
   * error will be returned.
   */
  public const CONCLUSION_VALIDATION_CONCLUSION_UNSPECIFIED = 'VALIDATION_CONCLUSION_UNSPECIFIED';
  /**
   * The version of the address returned by the Address Validation API was used
   * for the transaction.
   */
  public const CONCLUSION_VALIDATED_VERSION_USED = 'VALIDATED_VERSION_USED';
  /**
   * The version of the address provided by the user was used for the
   * transaction
   */
  public const CONCLUSION_USER_VERSION_USED = 'USER_VERSION_USED';
  /**
   * A version of the address that was entered after the last validation attempt
   * but that was not re-validated was used for the transaction.
   */
  public const CONCLUSION_UNVALIDATED_VERSION_USED = 'UNVALIDATED_VERSION_USED';
  /**
   * The transaction was abandoned and the address was not used.
   */
  public const CONCLUSION_UNUSED = 'UNUSED';
  /**
   * Required. The outcome of the sequence of validation attempts. If this field
   * is set to `VALIDATION_CONCLUSION_UNSPECIFIED`, an `INVALID_ARGUMENT` error
   * will be returned.
   *
   * @var string
   */
  public $conclusion;
  /**
   * Required. The ID of the response that this feedback is for. This should be
   * the response_id from the first response in a series of address validation
   * attempts.
   *
   * @var string
   */
  public $responseId;

  /**
   * Required. The outcome of the sequence of validation attempts. If this field
   * is set to `VALIDATION_CONCLUSION_UNSPECIFIED`, an `INVALID_ARGUMENT` error
   * will be returned.
   *
   * Accepted values: VALIDATION_CONCLUSION_UNSPECIFIED, VALIDATED_VERSION_USED,
   * USER_VERSION_USED, UNVALIDATED_VERSION_USED, UNUSED
   *
   * @param self::CONCLUSION_* $conclusion
   */
  public function setConclusion($conclusion)
  {
    $this->conclusion = $conclusion;
  }
  /**
   * @return self::CONCLUSION_*
   */
  public function getConclusion()
  {
    return $this->conclusion;
  }
  /**
   * Required. The ID of the response that this feedback is for. This should be
   * the response_id from the first response in a series of address validation
   * attempts.
   *
   * @param string $responseId
   */
  public function setResponseId($responseId)
  {
    $this->responseId = $responseId;
  }
  /**
   * @return string
   */
  public function getResponseId()
  {
    return $this->responseId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsAddressvalidationV1ProvideValidationFeedbackRequest::class, 'Google_Service_AddressValidation_GoogleMapsAddressvalidationV1ProvideValidationFeedbackRequest');
