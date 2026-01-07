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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1EncryptionConfigFailureDetails extends \Google\Model
{
  /**
   * The error code is not specified
   */
  public const ERROR_CODE_UNKNOWN = 'UNKNOWN';
  /**
   * Error because of internal server error, will be retried automatically.
   */
  public const ERROR_CODE_INTERNAL_ERROR = 'INTERNAL_ERROR';
  /**
   * User action is required to resolve the error.
   */
  public const ERROR_CODE_REQUIRE_USER_ACTION = 'REQUIRE_USER_ACTION';
  /**
   * Output only. The error code for the failure.
   *
   * @var string
   */
  public $errorCode;
  /**
   * Output only. The error message will be shown to the user. Set only if the
   * error code is REQUIRE_USER_ACTION.
   *
   * @var string
   */
  public $errorMessage;

  /**
   * Output only. The error code for the failure.
   *
   * Accepted values: UNKNOWN, INTERNAL_ERROR, REQUIRE_USER_ACTION
   *
   * @param self::ERROR_CODE_* $errorCode
   */
  public function setErrorCode($errorCode)
  {
    $this->errorCode = $errorCode;
  }
  /**
   * @return self::ERROR_CODE_*
   */
  public function getErrorCode()
  {
    return $this->errorCode;
  }
  /**
   * Output only. The error message will be shown to the user. Set only if the
   * error code is REQUIRE_USER_ACTION.
   *
   * @param string $errorMessage
   */
  public function setErrorMessage($errorMessage)
  {
    $this->errorMessage = $errorMessage;
  }
  /**
   * @return string
   */
  public function getErrorMessage()
  {
    return $this->errorMessage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1EncryptionConfigFailureDetails::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1EncryptionConfigFailureDetails');
