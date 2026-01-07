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

namespace Google\Service\Bigquery;

class BiEngineReason extends \Google\Model
{
  /**
   * BiEngineReason not specified.
   */
  public const CODE_CODE_UNSPECIFIED = 'CODE_UNSPECIFIED';
  /**
   * No reservation available for BI Engine acceleration.
   */
  public const CODE_NO_RESERVATION = 'NO_RESERVATION';
  /**
   * Not enough memory available for BI Engine acceleration.
   */
  public const CODE_INSUFFICIENT_RESERVATION = 'INSUFFICIENT_RESERVATION';
  /**
   * This particular SQL text is not supported for acceleration by BI Engine.
   */
  public const CODE_UNSUPPORTED_SQL_TEXT = 'UNSUPPORTED_SQL_TEXT';
  /**
   * Input too large for acceleration by BI Engine.
   */
  public const CODE_INPUT_TOO_LARGE = 'INPUT_TOO_LARGE';
  /**
   * Catch-all code for all other cases for partial or disabled acceleration.
   */
  public const CODE_OTHER_REASON = 'OTHER_REASON';
  /**
   * One or more tables were not eligible for BI Engine acceleration.
   */
  public const CODE_TABLE_EXCLUDED = 'TABLE_EXCLUDED';
  /**
   * Output only. High-level BI Engine reason for partial or disabled
   * acceleration
   *
   * @var string
   */
  public $code;
  /**
   * Output only. Free form human-readable reason for partial or disabled
   * acceleration.
   *
   * @var string
   */
  public $message;

  /**
   * Output only. High-level BI Engine reason for partial or disabled
   * acceleration
   *
   * Accepted values: CODE_UNSPECIFIED, NO_RESERVATION,
   * INSUFFICIENT_RESERVATION, UNSUPPORTED_SQL_TEXT, INPUT_TOO_LARGE,
   * OTHER_REASON, TABLE_EXCLUDED
   *
   * @param self::CODE_* $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return self::CODE_*
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * Output only. Free form human-readable reason for partial or disabled
   * acceleration.
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BiEngineReason::class, 'Google_Service_Bigquery_BiEngineReason');
