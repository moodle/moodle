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

class GoogleFirebaseAppcheckV1GenerateAppAttestChallengeResponse extends \Google\Model
{
  /**
   * A one-time use challenge for the client to pass to the App Attest API.
   *
   * @var string
   */
  public $challenge;
  /**
   * The duration from the time this challenge is minted until its expiration.
   * This field is intended to ease client-side token management, since the
   * client may have clock skew, but is still able to accurately measure a
   * duration.
   *
   * @var string
   */
  public $ttl;

  /**
   * A one-time use challenge for the client to pass to the App Attest API.
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
   * The duration from the time this challenge is minted until its expiration.
   * This field is intended to ease client-side token management, since the
   * client may have clock skew, but is still able to accurately measure a
   * duration.
   *
   * @param string $ttl
   */
  public function setTtl($ttl)
  {
    $this->ttl = $ttl;
  }
  /**
   * @return string
   */
  public function getTtl()
  {
    return $this->ttl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirebaseAppcheckV1GenerateAppAttestChallengeResponse::class, 'Google_Service_Firebaseappcheck_GoogleFirebaseAppcheckV1GenerateAppAttestChallengeResponse');
