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

class EnterpriseCrmEventbusProtoJsonFunction extends \Google\Model
{
  public const FUNCTION_NAME_UNSPECIFIED = 'UNSPECIFIED';
  public const FUNCTION_NAME_GET_PROPERTY = 'GET_PROPERTY';
  public const FUNCTION_NAME_GET_ELEMENT = 'GET_ELEMENT';
  public const FUNCTION_NAME_APPEND_ELEMENT = 'APPEND_ELEMENT';
  public const FUNCTION_NAME_SIZE = 'SIZE';
  public const FUNCTION_NAME_SET_PROPERTY = 'SET_PROPERTY';
  public const FUNCTION_NAME_FLATTEN = 'FLATTEN';
  public const FUNCTION_NAME_FLATTEN_ONCE = 'FLATTEN_ONCE';
  public const FUNCTION_NAME_MERGE = 'MERGE';
  public const FUNCTION_NAME_TO_STRING = 'TO_STRING';
  public const FUNCTION_NAME_TO_INT = 'TO_INT';
  public const FUNCTION_NAME_TO_DOUBLE = 'TO_DOUBLE';
  public const FUNCTION_NAME_TO_BOOLEAN = 'TO_BOOLEAN';
  public const FUNCTION_NAME_TO_PROTO = 'TO_PROTO';
  public const FUNCTION_NAME_TO_STRING_ARRAY = 'TO_STRING_ARRAY';
  public const FUNCTION_NAME_TO_INT_ARRAY = 'TO_INT_ARRAY';
  public const FUNCTION_NAME_TO_DOUBLE_ARRAY = 'TO_DOUBLE_ARRAY';
  public const FUNCTION_NAME_TO_PROTO_ARRAY = 'TO_PROTO_ARRAY';
  public const FUNCTION_NAME_TO_BOOLEAN_ARRAY = 'TO_BOOLEAN_ARRAY';
  public const FUNCTION_NAME_REMOVE_PROPERTY = 'REMOVE_PROPERTY';
  public const FUNCTION_NAME_RESOLVE_TEMPLATE = 'RESOLVE_TEMPLATE';
  public const FUNCTION_NAME_EQUALS = 'EQUALS';
  public const FUNCTION_NAME_FOR_EACH = 'FOR_EACH';
  public const FUNCTION_NAME_FILTER_ELEMENTS = 'FILTER_ELEMENTS';
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
class_alias(EnterpriseCrmEventbusProtoJsonFunction::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoJsonFunction');
