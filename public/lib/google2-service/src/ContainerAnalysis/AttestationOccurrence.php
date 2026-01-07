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

class AttestationOccurrence extends \Google\Collection
{
  protected $collection_key = 'signatures';
  protected $jwtsType = Jwt::class;
  protected $jwtsDataType = 'array';
  /**
   * Required. The serialized payload that is verified by one or more
   * `signatures`.
   *
   * @var string
   */
  public $serializedPayload;
  protected $signaturesType = Signature::class;
  protected $signaturesDataType = 'array';

  /**
   * One or more JWTs encoding a self-contained attestation. Each JWT encodes
   * the payload that it verifies within the JWT itself. Verifier implementation
   * SHOULD ignore the `serialized_payload` field when verifying these JWTs. If
   * only JWTs are present on this AttestationOccurrence, then the
   * `serialized_payload` SHOULD be left empty. Each JWT SHOULD encode a claim
   * specific to the `resource_uri` of this Occurrence, but this is not
   * validated by Grafeas metadata API implementations. The JWT itself is opaque
   * to Grafeas.
   *
   * @param Jwt[] $jwts
   */
  public function setJwts($jwts)
  {
    $this->jwts = $jwts;
  }
  /**
   * @return Jwt[]
   */
  public function getJwts()
  {
    return $this->jwts;
  }
  /**
   * Required. The serialized payload that is verified by one or more
   * `signatures`.
   *
   * @param string $serializedPayload
   */
  public function setSerializedPayload($serializedPayload)
  {
    $this->serializedPayload = $serializedPayload;
  }
  /**
   * @return string
   */
  public function getSerializedPayload()
  {
    return $this->serializedPayload;
  }
  /**
   * One or more signatures over `serialized_payload`. Verifier implementations
   * should consider this attestation message verified if at least one
   * `signature` verifies `serialized_payload`. See `Signature` in common.proto
   * for more details on signature structure and verification.
   *
   * @param Signature[] $signatures
   */
  public function setSignatures($signatures)
  {
    $this->signatures = $signatures;
  }
  /**
   * @return Signature[]
   */
  public function getSignatures()
  {
    return $this->signatures;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AttestationOccurrence::class, 'Google_Service_ContainerAnalysis_AttestationOccurrence');
