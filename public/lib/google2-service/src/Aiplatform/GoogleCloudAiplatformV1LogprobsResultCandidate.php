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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1LogprobsResultCandidate extends \Google\Model
{
  /**
   * The log probability of this token. A higher value indicates that the model
   * was more confident in this token. The log probability can be used to assess
   * the relative likelihood of different tokens and to identify when the model
   * is uncertain.
   *
   * @var float
   */
  public $logProbability;
  /**
   * The token's string representation.
   *
   * @var string
   */
  public $token;
  /**
   * The token's numerical ID. While the `token` field provides the string
   * representation of the token, the `token_id` is the numerical representation
   * that the model uses internally. This can be useful for developers who want
   * to build custom logic based on the model's vocabulary.
   *
   * @var int
   */
  public $tokenId;

  /**
   * The log probability of this token. A higher value indicates that the model
   * was more confident in this token. The log probability can be used to assess
   * the relative likelihood of different tokens and to identify when the model
   * is uncertain.
   *
   * @param float $logProbability
   */
  public function setLogProbability($logProbability)
  {
    $this->logProbability = $logProbability;
  }
  /**
   * @return float
   */
  public function getLogProbability()
  {
    return $this->logProbability;
  }
  /**
   * The token's string representation.
   *
   * @param string $token
   */
  public function setToken($token)
  {
    $this->token = $token;
  }
  /**
   * @return string
   */
  public function getToken()
  {
    return $this->token;
  }
  /**
   * The token's numerical ID. While the `token` field provides the string
   * representation of the token, the `token_id` is the numerical representation
   * that the model uses internally. This can be useful for developers who want
   * to build custom logic based on the model's vocabulary.
   *
   * @param int $tokenId
   */
  public function setTokenId($tokenId)
  {
    $this->tokenId = $tokenId;
  }
  /**
   * @return int
   */
  public function getTokenId()
  {
    return $this->tokenId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1LogprobsResultCandidate::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1LogprobsResultCandidate');
