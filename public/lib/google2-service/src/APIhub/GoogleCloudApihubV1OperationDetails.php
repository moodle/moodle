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

class GoogleCloudApihubV1OperationDetails extends \Google\Model
{
  /**
   * Optional. For OpenAPI spec, this will be set if `operation.deprecated`is
   * marked as `true` in the spec.
   *
   * @var bool
   */
  public $deprecated;
  /**
   * Optional. Description of the operation behavior. For OpenAPI spec, this
   * will map to `operation.description` in the spec, in case description is
   * empty, `operation.summary` will be used.
   *
   * @var string
   */
  public $description;
  protected $documentationType = GoogleCloudApihubV1Documentation::class;
  protected $documentationDataType = '';
  protected $httpOperationType = GoogleCloudApihubV1HttpOperation::class;
  protected $httpOperationDataType = '';
  protected $mcpToolType = GoogleCloudApihubV1McpTool::class;
  protected $mcpToolDataType = '';

  /**
   * Optional. For OpenAPI spec, this will be set if `operation.deprecated`is
   * marked as `true` in the spec.
   *
   * @param bool $deprecated
   */
  public function setDeprecated($deprecated)
  {
    $this->deprecated = $deprecated;
  }
  /**
   * @return bool
   */
  public function getDeprecated()
  {
    return $this->deprecated;
  }
  /**
   * Optional. Description of the operation behavior. For OpenAPI spec, this
   * will map to `operation.description` in the spec, in case description is
   * empty, `operation.summary` will be used.
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
   * Optional. Additional external documentation for this operation. For OpenAPI
   * spec, this will map to `operation.documentation` in the spec.
   *
   * @param GoogleCloudApihubV1Documentation $documentation
   */
  public function setDocumentation(GoogleCloudApihubV1Documentation $documentation)
  {
    $this->documentation = $documentation;
  }
  /**
   * @return GoogleCloudApihubV1Documentation
   */
  public function getDocumentation()
  {
    return $this->documentation;
  }
  /**
   * The HTTP Operation.
   *
   * @param GoogleCloudApihubV1HttpOperation $httpOperation
   */
  public function setHttpOperation(GoogleCloudApihubV1HttpOperation $httpOperation)
  {
    $this->httpOperation = $httpOperation;
  }
  /**
   * @return GoogleCloudApihubV1HttpOperation
   */
  public function getHttpOperation()
  {
    return $this->httpOperation;
  }
  /**
   * The MCP Tool Operation.
   *
   * @param GoogleCloudApihubV1McpTool $mcpTool
   */
  public function setMcpTool(GoogleCloudApihubV1McpTool $mcpTool)
  {
    $this->mcpTool = $mcpTool;
  }
  /**
   * @return GoogleCloudApihubV1McpTool
   */
  public function getMcpTool()
  {
    return $this->mcpTool;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1OperationDetails::class, 'Google_Service_APIhub_GoogleCloudApihubV1OperationDetails');
