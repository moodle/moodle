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

namespace Google\Service\Dfareporting;

class ConversionError extends \Google\Model
{
  public const CODE_INVALID_ARGUMENT = 'INVALID_ARGUMENT';
  public const CODE_INTERNAL = 'INTERNAL';
  public const CODE_PERMISSION_DENIED = 'PERMISSION_DENIED';
  public const CODE_NOT_FOUND = 'NOT_FOUND';
  /**
   * The error code.
   *
   * @var string
   */
  public $code;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#conversionError".
   *
   * @var string
   */
  public $kind;
  /**
   * A description of the error.
   *
   * @var string
   */
  public $message;

  /**
   * The error code.
   *
   * Accepted values: INVALID_ARGUMENT, INTERNAL, PERMISSION_DENIED, NOT_FOUND
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
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#conversionError".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * A description of the error.
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
class_alias(ConversionError::class, 'Google_Service_Dfareporting_ConversionError');
