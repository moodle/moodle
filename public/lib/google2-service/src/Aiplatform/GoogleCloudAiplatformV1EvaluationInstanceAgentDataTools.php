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

class GoogleCloudAiplatformV1EvaluationInstanceAgentDataTools extends \Google\Collection
{
  protected $collection_key = 'tool';
  protected $toolType = GoogleCloudAiplatformV1Tool::class;
  protected $toolDataType = 'array';

  /**
   * Optional. List of tools: each tool can have multiple function declarations.
   *
   * @deprecated
   * @param GoogleCloudAiplatformV1Tool[] $tool
   */
  public function setTool($tool)
  {
    $this->tool = $tool;
  }
  /**
   * @deprecated
   * @return GoogleCloudAiplatformV1Tool[]
   */
  public function getTool()
  {
    return $this->tool;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1EvaluationInstanceAgentDataTools::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1EvaluationInstanceAgentDataTools');
