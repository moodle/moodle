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

class GoogleCloudDialogflowCxV3TypeSchemaSchemaReference extends \Google\Model
{
  /**
   * The name of the schema.
   *
   * @var string
   */
  public $schema;
  /**
   * The tool that contains this schema definition. Format:
   * `projects//locations//agents//tools/`.
   *
   * @var string
   */
  public $tool;

  /**
   * The name of the schema.
   *
   * @param string $schema
   */
  public function setSchema($schema)
  {
    $this->schema = $schema;
  }
  /**
   * @return string
   */
  public function getSchema()
  {
    return $this->schema;
  }
  /**
   * The tool that contains this schema definition. Format:
   * `projects//locations//agents//tools/`.
   *
   * @param string $tool
   */
  public function setTool($tool)
  {
    $this->tool = $tool;
  }
  /**
   * @return string
   */
  public function getTool()
  {
    return $this->tool;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3TypeSchemaSchemaReference::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3TypeSchemaSchemaReference');
