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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1FunctionCallingConfig extends \Google\Collection
{
  /**
   * Unspecified function calling mode. This value should not be used.
   */
  public const MODE_MODE_UNSPECIFIED = 'MODE_UNSPECIFIED';
  /**
   * Default model behavior, model decides to predict either function calls or
   * natural language response.
   */
  public const MODE_AUTO = 'AUTO';
  /**
   * Model is constrained to always predicting function calls only. If
   * "allowed_function_names" are set, the predicted function calls will be
   * limited to any one of "allowed_function_names", else the predicted function
   * calls will be any one of the provided "function_declarations".
   */
  public const MODE_ANY = 'ANY';
  /**
   * Model will not predict any function calls. Model behavior is same as when
   * not passing any function declarations.
   */
  public const MODE_NONE = 'NONE';
  protected $collection_key = 'allowedFunctionNames';
  /**
   * Optional. Function names to call. Only set when the Mode is ANY. Function
   * names should match [FunctionDeclaration.name]. With mode set to ANY, model
   * will predict a function call from the set of function names provided.
   *
   * @var string[]
   */
  public $allowedFunctionNames;
  /**
   * Optional. Function calling mode.
   *
   * @var string
   */
  public $mode;
  /**
   * Optional. When set to true, arguments of a single function call will be
   * streamed out in multiple parts/contents/responses. Partial parameter
   * results will be returned in the [FunctionCall.partial_args] field.
   *
   * @var bool
   */
  public $streamFunctionCallArguments;

  /**
   * Optional. Function names to call. Only set when the Mode is ANY. Function
   * names should match [FunctionDeclaration.name]. With mode set to ANY, model
   * will predict a function call from the set of function names provided.
   *
   * @param string[] $allowedFunctionNames
   */
  public function setAllowedFunctionNames($allowedFunctionNames)
  {
    $this->allowedFunctionNames = $allowedFunctionNames;
  }
  /**
   * @return string[]
   */
  public function getAllowedFunctionNames()
  {
    return $this->allowedFunctionNames;
  }
  /**
   * Optional. Function calling mode.
   *
   * Accepted values: MODE_UNSPECIFIED, AUTO, ANY, NONE
   *
   * @param self::MODE_* $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return self::MODE_*
   */
  public function getMode()
  {
    return $this->mode;
  }
  /**
   * Optional. When set to true, arguments of a single function call will be
   * streamed out in multiple parts/contents/responses. Partial parameter
   * results will be returned in the [FunctionCall.partial_args] field.
   *
   * @param bool $streamFunctionCallArguments
   */
  public function setStreamFunctionCallArguments($streamFunctionCallArguments)
  {
    $this->streamFunctionCallArguments = $streamFunctionCallArguments;
  }
  /**
   * @return bool
   */
  public function getStreamFunctionCallArguments()
  {
    return $this->streamFunctionCallArguments;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1FunctionCallingConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FunctionCallingConfig');
