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

class GoogleCloudAiplatformV1QueryReasoningEngineRequest extends \Google\Model
{
  /**
   * Optional. Class method to be used for the query. It is optional and
   * defaults to "query" if unspecified.
   *
   * @var string
   */
  public $classMethod;
  /**
   * Optional. Input content provided by users in JSON object format. Examples
   * include text query, function calling parameters, media bytes, etc.
   *
   * @var array[]
   */
  public $input;

  /**
   * Optional. Class method to be used for the query. It is optional and
   * defaults to "query" if unspecified.
   *
   * @param string $classMethod
   */
  public function setClassMethod($classMethod)
  {
    $this->classMethod = $classMethod;
  }
  /**
   * @return string
   */
  public function getClassMethod()
  {
    return $this->classMethod;
  }
  /**
   * Optional. Input content provided by users in JSON object format. Examples
   * include text query, function calling parameters, media bytes, etc.
   *
   * @param array[] $input
   */
  public function setInput($input)
  {
    $this->input = $input;
  }
  /**
   * @return array[]
   */
  public function getInput()
  {
    return $this->input;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1QueryReasoningEngineRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1QueryReasoningEngineRequest');
