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

namespace Google\Service\ContainerAnalysis;

class SBOMReferenceOccurrence extends \Google\Collection
{
  protected $collection_key = 'signatures';
  protected $payloadDataType = '';
  /**
   * The kind of payload that SbomReferenceIntotoPayload takes. Since it's in
   * the intoto format, this value is expected to be 'application/vnd.in-
   * toto+json'.
   *
   * @var string
   */
  public $payloadType;
  protected $signaturesType = EnvelopeSignature::class;
  protected $signaturesDataType = 'array';

  /**
   * The actual payload that contains the SBOM reference data.
   *
   * @param SbomReferenceIntotoPayload $payload
   */
  public function setPayload(SbomReferenceIntotoPayload $payload)
  {
    $this->payload = $payload;
  }
  /**
   * @return SbomReferenceIntotoPayload
   */
  public function getPayload()
  {
    return $this->payload;
  }
  /**
   * The kind of payload that SbomReferenceIntotoPayload takes. Since it's in
   * the intoto format, this value is expected to be 'application/vnd.in-
   * toto+json'.
   *
   * @param string $payloadType
   */
  public function setPayloadType($payloadType)
  {
    $this->payloadType = $payloadType;
  }
  /**
   * @return string
   */
  public function getPayloadType()
  {
    return $this->payloadType;
  }
  /**
   * The signatures over the payload.
   *
   * @param EnvelopeSignature[] $signatures
   */
  public function setSignatures($signatures)
  {
    $this->signatures = $signatures;
  }
  /**
   * @return EnvelopeSignature[]
   */
  public function getSignatures()
  {
    return $this->signatures;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SBOMReferenceOccurrence::class, 'Google_Service_ContainerAnalysis_SBOMReferenceOccurrence');
