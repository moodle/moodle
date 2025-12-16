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

class GoogleCloudAiplatformV1CountTokensRequest extends \Google\Collection
{
  protected $collection_key = 'tools';
  protected $contentsType = GoogleCloudAiplatformV1Content::class;
  protected $contentsDataType = 'array';
  protected $generationConfigType = GoogleCloudAiplatformV1GenerationConfig::class;
  protected $generationConfigDataType = '';
  /**
   * Optional. The instances that are the input to token counting call. Schema
   * is identical to the prediction schema of the underlying model.
   *
   * @var array[]
   */
  public $instances;
  /**
   * Optional. The name of the publisher model requested to serve the
   * prediction. Format:
   * `projects/{project}/locations/{location}/publishers/models`
   *
   * @var string
   */
  public $model;
  protected $systemInstructionType = GoogleCloudAiplatformV1Content::class;
  protected $systemInstructionDataType = '';
  protected $toolsType = GoogleCloudAiplatformV1Tool::class;
  protected $toolsDataType = 'array';

  /**
   * Optional. Input content.
   *
   * @param GoogleCloudAiplatformV1Content[] $contents
   */
  public function setContents($contents)
  {
    $this->contents = $contents;
  }
  /**
   * @return GoogleCloudAiplatformV1Content[]
   */
  public function getContents()
  {
    return $this->contents;
  }
  /**
   * Optional. Generation config that the model will use to generate the
   * response.
   *
   * @param GoogleCloudAiplatformV1GenerationConfig $generationConfig
   */
  public function setGenerationConfig(GoogleCloudAiplatformV1GenerationConfig $generationConfig)
  {
    $this->generationConfig = $generationConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1GenerationConfig
   */
  public function getGenerationConfig()
  {
    return $this->generationConfig;
  }
  /**
   * Optional. The instances that are the input to token counting call. Schema
   * is identical to the prediction schema of the underlying model.
   *
   * @param array[] $instances
   */
  public function setInstances($instances)
  {
    $this->instances = $instances;
  }
  /**
   * @return array[]
   */
  public function getInstances()
  {
    return $this->instances;
  }
  /**
   * Optional. The name of the publisher model requested to serve the
   * prediction. Format:
   * `projects/{project}/locations/{location}/publishers/models`
   *
   * @param string $model
   */
  public function setModel($model)
  {
    $this->model = $model;
  }
  /**
   * @return string
   */
  public function getModel()
  {
    return $this->model;
  }
  /**
   * Optional. The user provided system instructions for the model. Note: only
   * text should be used in parts and content in each part will be in a separate
   * paragraph.
   *
   * @param GoogleCloudAiplatformV1Content $systemInstruction
   */
  public function setSystemInstruction(GoogleCloudAiplatformV1Content $systemInstruction)
  {
    $this->systemInstruction = $systemInstruction;
  }
  /**
   * @return GoogleCloudAiplatformV1Content
   */
  public function getSystemInstruction()
  {
    return $this->systemInstruction;
  }
  /**
   * Optional. A list of `Tools` the model may use to generate the next
   * response. A `Tool` is a piece of code that enables the system to interact
   * with external systems to perform an action, or set of actions, outside of
   * knowledge and scope of the model.
   *
   * @param GoogleCloudAiplatformV1Tool[] $tools
   */
  public function setTools($tools)
  {
    $this->tools = $tools;
  }
  /**
   * @return GoogleCloudAiplatformV1Tool[]
   */
  public function getTools()
  {
    return $this->tools;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1CountTokensRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1CountTokensRequest');
