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

namespace Google\Service\Sheets;

class ErrorValue extends \Google\Model
{
  /**
   * The default error type, do not use this.
   */
  public const TYPE_ERROR_TYPE_UNSPECIFIED = 'ERROR_TYPE_UNSPECIFIED';
  /**
   * Corresponds to the `#ERROR!` error.
   */
  public const TYPE_ERROR = 'ERROR';
  /**
   * Corresponds to the `#NULL!` error.
   */
  public const TYPE_NULL_VALUE = 'NULL_VALUE';
  /**
   * Corresponds to the `#DIV/0` error.
   */
  public const TYPE_DIVIDE_BY_ZERO = 'DIVIDE_BY_ZERO';
  /**
   * Corresponds to the `#VALUE!` error.
   */
  public const TYPE_VALUE = 'VALUE';
  /**
   * Corresponds to the `#REF!` error.
   */
  public const TYPE_REF = 'REF';
  /**
   * Corresponds to the `#NAME?` error.
   */
  public const TYPE_NAME = 'NAME';
  /**
   * Corresponds to the `#NUM!` error.
   */
  public const TYPE_NUM = 'NUM';
  /**
   * Corresponds to the `#N/A` error.
   */
  public const TYPE_N_A = 'N_A';
  /**
   * Corresponds to the `Loading...` state.
   */
  public const TYPE_LOADING = 'LOADING';
  /**
   * A message with more information about the error (in the spreadsheet's
   * locale).
   *
   * @var string
   */
  public $message;
  /**
   * The type of error.
   *
   * @var string
   */
  public $type;

  /**
   * A message with more information about the error (in the spreadsheet's
   * locale).
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
  /**
   * The type of error.
   *
   * Accepted values: ERROR_TYPE_UNSPECIFIED, ERROR, NULL_VALUE, DIVIDE_BY_ZERO,
   * VALUE, REF, NAME, NUM, N_A, LOADING
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ErrorValue::class, 'Google_Service_Sheets_ErrorValue');
