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

namespace Google\Service\Verifiedaccess;

class VerifyChallengeResponseRequest extends \Google\Model
{
  /**
   * Required. The generated response to the challenge, the bytes representation
   * of SignedData.
   *
   * @var string
   */
  public $challengeResponse;
  /**
   * Optional. Service can optionally provide identity information about the
   * device or user associated with the key. For an EMK, this value is the
   * enrolled domain. For an EUK, this value is the user's email address. If
   * present, this value will be checked against contents of the response, and
   * verification will fail if there is no match.
   *
   * @var string
   */
  public $expectedIdentity;

  /**
   * Required. The generated response to the challenge, the bytes representation
   * of SignedData.
   *
   * @param string $challengeResponse
   */
  public function setChallengeResponse($challengeResponse)
  {
    $this->challengeResponse = $challengeResponse;
  }
  /**
   * @return string
   */
  public function getChallengeResponse()
  {
    return $this->challengeResponse;
  }
  /**
   * Optional. Service can optionally provide identity information about the
   * device or user associated with the key. For an EMK, this value is the
   * enrolled domain. For an EUK, this value is the user's email address. If
   * present, this value will be checked against contents of the response, and
   * verification will fail if there is no match.
   *
   * @param string $expectedIdentity
   */
  public function setExpectedIdentity($expectedIdentity)
  {
    $this->expectedIdentity = $expectedIdentity;
  }
  /**
   * @return string
   */
  public function getExpectedIdentity()
  {
    return $this->expectedIdentity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VerifyChallengeResponseRequest::class, 'Google_Service_Verifiedaccess_VerifyChallengeResponseRequest');
