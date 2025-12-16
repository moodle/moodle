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

namespace Google\Service\BinaryAuthorization;

class ValidateAttestationOccurrenceResponse extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const RESULT_RESULT_UNSPECIFIED = 'RESULT_UNSPECIFIED';
  /**
   * The Attestation was able to verified by the Attestor.
   */
  public const RESULT_VERIFIED = 'VERIFIED';
  /**
   * The Attestation was not able to verified by the Attestor.
   */
  public const RESULT_ATTESTATION_NOT_VERIFIABLE = 'ATTESTATION_NOT_VERIFIABLE';
  /**
   * The reason for denial if the Attestation couldn't be validated.
   *
   * @var string
   */
  public $denialReason;
  /**
   * The result of the Attestation validation.
   *
   * @var string
   */
  public $result;

  /**
   * The reason for denial if the Attestation couldn't be validated.
   *
   * @param string $denialReason
   */
  public function setDenialReason($denialReason)
  {
    $this->denialReason = $denialReason;
  }
  /**
   * @return string
   */
  public function getDenialReason()
  {
    return $this->denialReason;
  }
  /**
   * The result of the Attestation validation.
   *
   * Accepted values: RESULT_UNSPECIFIED, VERIFIED, ATTESTATION_NOT_VERIFIABLE
   *
   * @param self::RESULT_* $result
   */
  public function setResult($result)
  {
    $this->result = $result;
  }
  /**
   * @return self::RESULT_*
   */
  public function getResult()
  {
    return $this->result;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ValidateAttestationOccurrenceResponse::class, 'Google_Service_BinaryAuthorization_ValidateAttestationOccurrenceResponse');
