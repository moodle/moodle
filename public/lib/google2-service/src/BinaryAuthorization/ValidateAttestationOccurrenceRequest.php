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

class ValidateAttestationOccurrenceRequest extends \Google\Model
{
  protected $attestationType = AttestationOccurrence::class;
  protected $attestationDataType = '';
  /**
   * Required. The resource name of the Note to which the containing Occurrence
   * is associated.
   *
   * @var string
   */
  public $occurrenceNote;
  /**
   * Required. The URI of the artifact (e.g. container image) that is the
   * subject of the containing Occurrence.
   *
   * @var string
   */
  public $occurrenceResourceUri;

  /**
   * Required. An AttestationOccurrence to be checked that it can be verified by
   * the `Attestor`. It does not have to be an existing entity in Container
   * Analysis. It must otherwise be a valid `AttestationOccurrence`.
   *
   * @param AttestationOccurrence $attestation
   */
  public function setAttestation(AttestationOccurrence $attestation)
  {
    $this->attestation = $attestation;
  }
  /**
   * @return AttestationOccurrence
   */
  public function getAttestation()
  {
    return $this->attestation;
  }
  /**
   * Required. The resource name of the Note to which the containing Occurrence
   * is associated.
   *
   * @param string $occurrenceNote
   */
  public function setOccurrenceNote($occurrenceNote)
  {
    $this->occurrenceNote = $occurrenceNote;
  }
  /**
   * @return string
   */
  public function getOccurrenceNote()
  {
    return $this->occurrenceNote;
  }
  /**
   * Required. The URI of the artifact (e.g. container image) that is the
   * subject of the containing Occurrence.
   *
   * @param string $occurrenceResourceUri
   */
  public function setOccurrenceResourceUri($occurrenceResourceUri)
  {
    $this->occurrenceResourceUri = $occurrenceResourceUri;
  }
  /**
   * @return string
   */
  public function getOccurrenceResourceUri()
  {
    return $this->occurrenceResourceUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ValidateAttestationOccurrenceRequest::class, 'Google_Service_BinaryAuthorization_ValidateAttestationOccurrenceRequest');
