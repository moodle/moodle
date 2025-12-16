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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3ToolFunctionTool extends \Google\Model
{
  /**
   * Optional. The JSON schema is encapsulated in a google.protobuf.Struct to
   * describe the input of the function. This input is a JSON object that
   * contains the function's parameters as properties of the object.
   *
   * @var array[]
   */
  public $inputSchema;
  /**
   * Optional. The JSON schema is encapsulated in a google.protobuf.Struct to
   * describe the output of the function. This output is a JSON object that
   * contains the function's parameters as properties of the object.
   *
   * @var array[]
   */
  public $outputSchema;

  /**
   * Optional. The JSON schema is encapsulated in a google.protobuf.Struct to
   * describe the input of the function. This input is a JSON object that
   * contains the function's parameters as properties of the object.
   *
   * @param array[] $inputSchema
   */
  public function setInputSchema($inputSchema)
  {
    $this->inputSchema = $inputSchema;
  }
  /**
   * @return array[]
   */
  public function getInputSchema()
  {
    return $this->inputSchema;
  }
  /**
   * Optional. The JSON schema is encapsulated in a google.protobuf.Struct to
   * describe the output of the function. This output is a JSON object that
   * contains the function's parameters as properties of the object.
   *
   * @param array[] $outputSchema
   */
  public function setOutputSchema($outputSchema)
  {
    $this->outputSchema = $outputSchema;
  }
  /**
   * @return array[]
   */
  public function getOutputSchema()
  {
    return $this->outputSchema;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3ToolFunctionTool::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3ToolFunctionTool');
