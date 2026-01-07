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

namespace Google\Service\MyBusinessVerifications;

class GenerateInstantVerificationTokenResponse extends \Google\Model
{
  /**
   * Default value, will result in errors.
   */
  public const RESULT_RESULT_UNSPECIFIED = 'RESULT_UNSPECIFIED';
  /**
   * The instant verification token was generated successfully.
   */
  public const RESULT_SUCCEEDED = 'SUCCEEDED';
  /**
   * The instant verification token was not generated..
   */
  public const RESULT_FAILED = 'FAILED';
  /**
   * The generated instant verification token.
   *
   * @var string
   */
  public $instantVerificationToken;
  /**
   * Output only. The result of the instant verification token generation.
   *
   * @var string
   */
  public $result;

  /**
   * The generated instant verification token.
   *
   * @param string $instantVerificationToken
   */
  public function setInstantVerificationToken($instantVerificationToken)
  {
    $this->instantVerificationToken = $instantVerificationToken;
  }
  /**
   * @return string
   */
  public function getInstantVerificationToken()
  {
    return $this->instantVerificationToken;
  }
  /**
   * Output only. The result of the instant verification token generation.
   *
   * Accepted values: RESULT_UNSPECIFIED, SUCCEEDED, FAILED
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
class_alias(GenerateInstantVerificationTokenResponse::class, 'Google_Service_MyBusinessVerifications_GenerateInstantVerificationTokenResponse');
