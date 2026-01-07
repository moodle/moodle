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

class EnterpriseCrmEventbusProtoBooleanArrayFunction extends \Google\Model
{
  public const FUNCTION_NAME_UNSPECIFIED = 'UNSPECIFIED';
  public const FUNCTION_NAME_GET = 'GET';
  public const FUNCTION_NAME_APPEND = 'APPEND';
  public const FUNCTION_NAME_SIZE = 'SIZE';
  public const FUNCTION_NAME_TO_SET = 'TO_SET';
  public const FUNCTION_NAME_APPEND_ALL = 'APPEND_ALL';
  public const FUNCTION_NAME_TO_JSON = 'TO_JSON';
  public const FUNCTION_NAME_SET = 'SET';
  public const FUNCTION_NAME_REMOVE = 'REMOVE';
  public const FUNCTION_NAME_REMOVE_AT = 'REMOVE_AT';
  public const FUNCTION_NAME_CONTAINS = 'CONTAINS';
  public const FUNCTION_NAME_FOR_EACH = 'FOR_EACH';
  public const FUNCTION_NAME_FILTER = 'FILTER';
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
class_alias(EnterpriseCrmEventbusProtoBooleanArrayFunction::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoBooleanArrayFunction');
