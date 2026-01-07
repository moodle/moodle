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

namespace Google\Service\DisplayVideo;

class CustomBiddingAlgorithmRulesError extends \Google\Model
{
  /**
   * The error is not specified or is unknown in this version.
   */
  public const ERROR_CODE_ERROR_CODE_UNSPECIFIED = 'ERROR_CODE_UNSPECIFIED';
  /**
   * The rules have a syntax error.
   */
  public const ERROR_CODE_SYNTAX_ERROR = 'SYNTAX_ERROR';
  /**
   * The rules have a constraint violation error.
   */
  public const ERROR_CODE_CONSTRAINT_VIOLATION_ERROR = 'CONSTRAINT_VIOLATION_ERROR';
  /**
   * Internal errors were thrown while processing the rules.
   */
  public const ERROR_CODE_INTERNAL_ERROR = 'INTERNAL_ERROR';
  /**
   * The type of error.
   *
   * @var string
   */
  public $errorCode;

  /**
   * The type of error.
   *
   * Accepted values: ERROR_CODE_UNSPECIFIED, SYNTAX_ERROR,
   * CONSTRAINT_VIOLATION_ERROR, INTERNAL_ERROR
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomBiddingAlgorithmRulesError::class, 'Google_Service_DisplayVideo_CustomBiddingAlgorithmRulesError');
