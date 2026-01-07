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

class EnterpriseCrmEventbusProtoBaseFunction extends \Google\Model
{
  public const FUNCTION_NAME_UNSPECIFIED = 'UNSPECIFIED';
  public const FUNCTION_NAME_NOW_IN_MILLIS = 'NOW_IN_MILLIS';
  public const FUNCTION_NAME_INT_LIST = 'INT_LIST';
  public const FUNCTION_NAME_ENVIRONMENT = 'ENVIRONMENT';
  public const FUNCTION_NAME_GET_EXECUTION_ID = 'GET_EXECUTION_ID';
  public const FUNCTION_NAME_GET_INTEGRATION_NAME = 'GET_INTEGRATION_NAME';
  public const FUNCTION_NAME_GET_REGION = 'GET_REGION';
  public const FUNCTION_NAME_GET_UUID = 'GET_UUID';
  public const FUNCTION_NAME_GET_PROJECT_ID = 'GET_PROJECT_ID';
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
class_alias(EnterpriseCrmEventbusProtoBaseFunction::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoBaseFunction');
