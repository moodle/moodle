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

class EnterpriseCrmEventbusProtoStringFunction extends \Google\Model
{
  public const FUNCTION_NAME_UNSPECIFIED = 'UNSPECIFIED';
  public const FUNCTION_NAME_CONCAT = 'CONCAT';
  public const FUNCTION_NAME_TO_UPPERCASE = 'TO_UPPERCASE';
  public const FUNCTION_NAME_TO_LOWERCASE = 'TO_LOWERCASE';
  public const FUNCTION_NAME_CONTAINS = 'CONTAINS';
  public const FUNCTION_NAME_SPLIT = 'SPLIT';
  public const FUNCTION_NAME_LENGTH = 'LENGTH';
  public const FUNCTION_NAME_EQUALS = 'EQUALS';
  public const FUNCTION_NAME_TO_INT = 'TO_INT';
  public const FUNCTION_NAME_TO_DOUBLE = 'TO_DOUBLE';
  public const FUNCTION_NAME_TO_BOOLEAN = 'TO_BOOLEAN';
  public const FUNCTION_NAME_TO_BASE_64 = 'TO_BASE_64';
  public const FUNCTION_NAME_TO_JSON = 'TO_JSON';
  public const FUNCTION_NAME_EQUALS_IGNORE_CASE = 'EQUALS_IGNORE_CASE';
  public const FUNCTION_NAME_REPLACE_ALL = 'REPLACE_ALL';
  public const FUNCTION_NAME_SUBSTRING = 'SUBSTRING';
  public const FUNCTION_NAME_RESOLVE_TEMPLATE = 'RESOLVE_TEMPLATE';
  public const FUNCTION_NAME_DECODE_BASE64_STRING = 'DECODE_BASE64_STRING';
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
class_alias(EnterpriseCrmEventbusProtoStringFunction::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoStringFunction');
