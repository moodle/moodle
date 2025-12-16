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

namespace Google\Service\Integrations;

class EnterpriseCrmEventbusProtoDoubleFunction extends \Google\Model
{
  public const FUNCTION_NAME_UNSPECIFIED = 'UNSPECIFIED';
  public const FUNCTION_NAME_TO_JSON = 'TO_JSON';
  public const FUNCTION_NAME_TO_STRING = 'TO_STRING';
  public const FUNCTION_NAME_ADD = 'ADD';
  public const FUNCTION_NAME_SUBTRACT = 'SUBTRACT';
  public const FUNCTION_NAME_MULTIPLY = 'MULTIPLY';
  public const FUNCTION_NAME_DIVIDE = 'DIVIDE';
  public const FUNCTION_NAME_EXPONENT = 'EXPONENT';
  public const FUNCTION_NAME_ROUND = 'ROUND';
  public const FUNCTION_NAME_FLOOR = 'FLOOR';
  public const FUNCTION_NAME_CEIL = 'CEIL';
  public const FUNCTION_NAME_GREATER_THAN = 'GREATER_THAN';
  public const FUNCTION_NAME_LESS_THAN = 'LESS_THAN';
  public const FUNCTION_NAME_EQUALS = 'EQUALS';
  public const FUNCTION_NAME_GREATER_THAN_EQUALS = 'GREATER_THAN_EQUALS';
  public const FUNCTION_NAME_LESS_THAN_EQUALS = 'LESS_THAN_EQUALS';
  public const FUNCTION_NAME_MOD = 'MOD';
  /**
   * @var string
   */
  public $functionName;

  /**
   * @param self::FUNCTION_NAME_* $functionName
   */
  public function setFunctionName($functionName)
  {
    $this->functionName = $functionName;
  }
  /**
   * @return self::FUNCTION_NAME_*
   */
  public function getFunctionName()
  {
    return $this->functionName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmEventbusProtoDoubleFunction::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoDoubleFunction');
