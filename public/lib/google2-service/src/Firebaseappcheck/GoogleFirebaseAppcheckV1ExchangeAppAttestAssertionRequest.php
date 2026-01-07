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

namespace Google\Service\Firebaseappcheck;

class GoogleFirebaseAppcheckV1ExchangeAppAttestAssertionRequest extends \Google\Model
{
  /**
   * Required. The artifact returned by a previous call to
   * ExchangeAppAttestAttestation.
   *
   * @var string
   */
  public $artifact;
  /**
   * Required. The CBOR-encoded assertion returned by the client-side App Attest
   * API.
   *
   * @var string
   */
  public $assertion;
  /**
   * Required. A one-time challenge returned by an immediately prior call to
   * GenerateAppAttestChallenge.
   *
   * @var string
   */
  public $challenge;
  /**
   * Specifies whether this attestation is for use in a *limited use* (`true`)
   * or *session based* (`false`) context. To enable this attestation to be used
   * with the *replay protection* feature, set this to `true`. The default value
   * is `false`.
   *
   * @var bool
   */
  public $limitedUse;

  /**
   * Required. The artifact returned by a previous call to
   * ExchangeAppAttestAttestation.
   *
   * @param string $artifact
   */
  public function setArtifact($artifact)
  {
    $this->artifact = $artifact;
  }
  /**
   * @return string
   */
  public function getArtifact()
  {
    return $this->artifact;
  }
  /**
   * Required. The CBOR-encoded assertion returned by the client-side App Attest
   * API.
   *
   * @param string $assertion
   */
  public function setAssertion($assertion)
  {
    $this->assertion = $assertion;
  }
  /**
   * @return string
   */
  public function getAssertion()
  {
    return $this->assertion;
  }
  /**
   * Required. A one-time challenge returned by an immediately prior call to
   * GenerateAppAttestChallenge.
   *
   * @param string $challenge
   */
  public function setChallenge($challenge)
  {
    $this->challenge = $challenge;
  }
  /**
   * @return string
   */
  public function getChallenge()
  {
    return $this->challenge;
  }
  /**
   * Specifies whether this attestation is for use in a *limited use* (`true`)
   * or *session based* (`false`) context. To enable this attestation to be used
   * with the *replay protection* feature, set this to `true`. The default value
   * is `false`.
   *
   * @param bool $limitedUse
   */
  public function setLimitedUse($limitedUse)
  {
    $this->limitedUse = $limitedUse;
  }
  /**
   * @return bool
   */
  public function getLimitedUse()
  {
    return $this->limitedUse;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirebaseAppcheckV1ExchangeAppAttestAssertionRequest::class, 'Google_Service_Firebaseappcheck_GoogleFirebaseAppcheckV1ExchangeAppAttestAssertionRequest');
