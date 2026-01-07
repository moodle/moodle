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

class GoogleCloudDialogflowCxV3Tool extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const TOOL_TYPE_TOOL_TYPE_UNSPECIFIED = 'TOOL_TYPE_UNSPECIFIED';
  /**
   * Customer provided tool.
   */
  public const TOOL_TYPE_CUSTOMIZED_TOOL = 'CUSTOMIZED_TOOL';
  /**
   * First party built-in tool created by Dialogflow which cannot be modified.
   */
  public const TOOL_TYPE_BUILTIN_TOOL = 'BUILTIN_TOOL';
  protected $dataStoreSpecType = GoogleCloudDialogflowCxV3ToolDataStoreTool::class;
  protected $dataStoreSpecDataType = '';
  /**
   * Required. High level description of the Tool and its usage.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The human-readable name of the Tool, unique within an agent.
   *
   * @var string
   */
  public $displayName;
  protected $functionSpecType = GoogleCloudDialogflowCxV3ToolFunctionTool::class;
  protected $functionSpecDataType = '';
  /**
   * The unique identifier of the Tool. Format:
   * `projects//locations//agents//tools/`.
   *
   * @var string
   */
  public $name;
  protected $openApiSpecType = GoogleCloudDialogflowCxV3ToolOpenApiTool::class;
  protected $openApiSpecDataType = '';
  /**
   * Output only. The tool type.
   *
   * @var string
   */
  public $toolType;

  /**
   * Data store search tool specification.
   *
   * @param GoogleCloudDialogflowCxV3ToolDataStoreTool $dataStoreSpec
   */
  public function setDataStoreSpec(GoogleCloudDialogflowCxV3ToolDataStoreTool $dataStoreSpec)
  {
    $this->dataStoreSpec = $dataStoreSpec;
  }
  /**
   * @return GoogleCloudDialogflowCxV3ToolDataStoreTool
   */
  public function getDataStoreSpec()
  {
    return $this->dataStoreSpec;
  }
  /**
   * Required. High level description of the Tool and its usage.
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
   * Required. The human-readable name of the Tool, unique within an agent.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Client side executed function specification.
   *
   * @param GoogleCloudDialogflowCxV3ToolFunctionTool $functionSpec
   */
  public function setFunctionSpec(GoogleCloudDialogflowCxV3ToolFunctionTool $functionSpec)
  {
    $this->functionSpec = $functionSpec;
  }
  /**
   * @return GoogleCloudDialogflowCxV3ToolFunctionTool
   */
  public function getFunctionSpec()
  {
    return $this->functionSpec;
  }
  /**
   * The unique identifier of the Tool. Format:
   * `projects//locations//agents//tools/`.
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
   * OpenAPI specification of the Tool.
   *
   * @param GoogleCloudDialogflowCxV3ToolOpenApiTool $openApiSpec
   */
  public function setOpenApiSpec(GoogleCloudDialogflowCxV3ToolOpenApiTool $openApiSpec)
  {
    $this->openApiSpec = $openApiSpec;
  }
  /**
   * @return GoogleCloudDialogflowCxV3ToolOpenApiTool
   */
  public function getOpenApiSpec()
  {
    return $this->openApiSpec;
  }
  /**
   * Output only. The tool type.
   *
   * Accepted values: TOOL_TYPE_UNSPECIFIED, CUSTOMIZED_TOOL, BUILTIN_TOOL
   *
   * @param self::TOOL_TYPE_* $toolType
   */
  public function setToolType($toolType)
  {
    $this->toolType = $toolType;
  }
  /**
   * @return self::TOOL_TYPE_*
   */
  public function getToolType()
  {
    return $this->toolType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3Tool::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3Tool');
