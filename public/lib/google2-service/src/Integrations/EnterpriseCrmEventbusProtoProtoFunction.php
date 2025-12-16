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

class EnterpriseCrmEventbusProtoProtoFunction extends \Google\Model
{
  public const FUNCTION_NAME_UNSPECIFIED = 'UNSPECIFIED';
  public const FUNCTION_NAME_GET_STRING_SUBFIELD = 'GET_STRING_SUBFIELD';
  public const FUNCTION_NAME_GET_INT_SUBFIELD = 'GET_INT_SUBFIELD';
  public const FUNCTION_NAME_GET_DOUBLE_SUBFIELD = 'GET_DOUBLE_SUBFIELD';
  public const FUNCTION_NAME_GET_BOOLEAN_SUBFIELD = 'GET_BOOLEAN_SUBFIELD';
  public const FUNCTION_NAME_GET_STRING_ARRAY_SUBFIELD = 'GET_STRING_ARRAY_SUBFIELD';
  public const FUNCTION_NAME_GET_INT_ARRAY_SUBFIELD = 'GET_INT_ARRAY_SUBFIELD';
  public const FUNCTION_NAME_GET_DOUBLE_ARRAY_SUBFIELD = 'GET_DOUBLE_ARRAY_SUBFIELD';
  public const FUNCTION_NAME_GET_BOOLEAN_ARRAY_SUBFIELD = 'GET_BOOLEAN_ARRAY_SUBFIELD';
  public const FUNCTION_NAME_GET_PROTO_ARRAY_SUBFIELD = 'GET_PROTO_ARRAY_SUBFIELD';
  public const FUNCTION_NAME_GET_PROTO_SUBFIELD = 'GET_PROTO_SUBFIELD';
  public const FUNCTION_NAME_TO_JSON = 'TO_JSON';
  public const FUNCTION_NAME_GET_BYTES_SUBFIELD_AS_UTF_8_STRING = 'GET_BYTES_SUBFIELD_AS_UTF_8_STRING';
  public const FUNCTION_NAME_GET_BYTES_SUBFIELD_AS_PROTO = 'GET_BYTES_SUBFIELD_AS_PROTO';
  public const FUNCTION_NAME_EQUALS = 'EQUALS';
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
class_alias(EnterpriseCrmEventbusProtoProtoFunction::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoProtoFunction');
