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

class GoogleMapsAddressvalidationV1ValidateAddressResponse extends \Google\Model
{
  /**
   * The UUID that identifies this response. If the address needs to be re-
   * validated, this UUID *must* accompany the new request.
   *
   * @var string
   */
  public $responseId;
  protected $resultType = GoogleMapsAddressvalidationV1ValidationResult::class;
  protected $resultDataType = '';

  /**
   * The UUID that identifies this response. If the address needs to be re-
   * validated, this UUID *must* accompany the new request.
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
  /**
   * The result of the address validation.
   *
   * @param GoogleMapsAddressvalidationV1ValidationResult $result
   */
  public function setResult(GoogleMapsAddressvalidationV1ValidationResult $result)
  {
    $this->result = $result;
  }
  /**
   * @return GoogleMapsAddressvalidationV1ValidationResult
   */
  public function getResult()
  {
    return $this->result;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsAddressvalidationV1ValidateAddressResponse::class, 'Google_Service_AddressValidation_GoogleMapsAddressvalidationV1ValidateAddressResponse');
