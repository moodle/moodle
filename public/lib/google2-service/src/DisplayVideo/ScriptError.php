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

class ScriptError extends \Google\Model
{
  /**
   * The script error is not specified or is unknown in this version.
   */
  public const ERROR_CODE_ERROR_CODE_UNSPECIFIED = 'ERROR_CODE_UNSPECIFIED';
  /**
   * The script has a syntax error.
   */
  public const ERROR_CODE_SYNTAX_ERROR = 'SYNTAX_ERROR';
  /**
   * The script uses deprecated syntax.
   */
  public const ERROR_CODE_DEPRECATED_SYNTAX = 'DEPRECATED_SYNTAX';
  /**
   * Internal errors were thrown while processing the script.
   */
  public const ERROR_CODE_INTERNAL_ERROR = 'INTERNAL_ERROR';
  /**
   * The column number in the script where the error was thrown.
   *
   * @var string
   */
  public $column;
  /**
   * The type of error.
   *
   * @var string
   */
  public $errorCode;
  /**
   * The detailed error message.
   *
   * @var string
   */
  public $errorMessage;
  /**
   * The line number in the script where the error was thrown.
   *
   * @var string
   */
  public $line;

  /**
   * The column number in the script where the error was thrown.
   *
   * @param string $column
   */
  public function setColumn($column)
  {
    $this->column = $column;
  }
  /**
   * @return string
   */
  public function getColumn()
  {
    return $this->column;
  }
  /**
   * The type of error.
   *
   * Accepted values: ERROR_CODE_UNSPECIFIED, SYNTAX_ERROR, DEPRECATED_SYNTAX,
   * INTERNAL_ERROR
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
   * The detailed error message.
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
  /**
   * The line number in the script where the error was thrown.
   *
   * @param string $line
   */
  public function setLine($line)
  {
    $this->line = $line;
  }
  /**
   * @return string
   */
  public function getLine()
  {
    return $this->line;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ScriptError::class, 'Google_Service_DisplayVideo_ScriptError');
