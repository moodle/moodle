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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1McpTool extends \Google\Model
{
  protected $annotationsType = GoogleCloudApihubV1ToolAnnotations::class;
  protected $annotationsDataType = '';
  /**
   * Optional. Description of what the tool does.
   *
   * @var string
   */
  public $description;
  protected $inputSchemaType = GoogleCloudApihubV1OperationSchema::class;
  protected $inputSchemaDataType = '';
  /**
   * Required. The name of the tool, unique within its parent scope (version).
   *
   * @var string
   */
  public $name;
  protected $outputSchemaType = GoogleCloudApihubV1OperationSchema::class;
  protected $outputSchemaDataType = '';
  /**
   * Optional. Optional title for the tool.
   *
   * @var string
   */
  public $title;

  /**
   * Optional. Optional annotations for the tool.
   *
   * @param GoogleCloudApihubV1ToolAnnotations $annotations
   */
  public function setAnnotations(GoogleCloudApihubV1ToolAnnotations $annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return GoogleCloudApihubV1ToolAnnotations
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * Optional. Description of what the tool does.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Optional. Input schema for the operation. This can be parsed only from MCP
   * schema type.
   *
   * @param GoogleCloudApihubV1OperationSchema $inputSchema
   */
  public function setInputSchema(GoogleCloudApihubV1OperationSchema $inputSchema)
  {
    $this->inputSchema = $inputSchema;
  }
  /**
   * @return GoogleCloudApihubV1OperationSchema
   */
  public function getInputSchema()
  {
    return $this->inputSchema;
  }
  /**
   * Required. The name of the tool, unique within its parent scope (version).
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Optional. Output schema for the operation. This can be parsed only from MCP
   * schema type.
   *
   * @param GoogleCloudApihubV1OperationSchema $outputSchema
   */
  public function setOutputSchema(GoogleCloudApihubV1OperationSchema $outputSchema)
  {
    $this->outputSchema = $outputSchema;
  }
  /**
   * @return GoogleCloudApihubV1OperationSchema
   */
  public function getOutputSchema()
  {
    return $this->outputSchema;
  }
  /**
   * Optional. Optional title for the tool.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1McpTool::class, 'Google_Service_APIhub_GoogleCloudApihubV1McpTool');
