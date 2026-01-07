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

class GoogleCloudIntegrationsV1alphaRuntimeActionSchema extends \Google\Model
{
  /**
   * Name of the action.
   *
   * @var string
   */
  public $action;
  /**
   * Input parameter schema for the action.
   *
   * @var string
   */
  public $inputSchema;
  /**
   * Output parameter schema for the action.
   *
   * @var string
   */
  public $outputSchema;

  /**
   * Name of the action.
   *
   * @param string $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }
  /**
   * @return string
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * Input parameter schema for the action.
   *
   * @param string $inputSchema
   */
  public function setInputSchema($inputSchema)
  {
    $this->inputSchema = $inputSchema;
  }
  /**
   * @return string
   */
  public function getInputSchema()
  {
    return $this->inputSchema;
  }
  /**
   * Output parameter schema for the action.
   *
   * @param string $outputSchema
   */
  public function setOutputSchema($outputSchema)
  {
    $this->outputSchema = $outputSchema;
  }
  /**
   * @return string
   */
  public function getOutputSchema()
  {
    return $this->outputSchema;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaRuntimeActionSchema::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaRuntimeActionSchema');
