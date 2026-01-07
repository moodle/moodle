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

class GoogleCloudAiplatformV1ToolCall extends \Google\Model
{
  /**
   * Optional. Spec for tool input
   *
   * @var string
   */
  public $toolInput;
  /**
   * Required. Spec for tool name
   *
   * @var string
   */
  public $toolName;

  /**
   * Optional. Spec for tool input
   *
   * @param string $toolInput
   */
  public function setToolInput($toolInput)
  {
    $this->toolInput = $toolInput;
  }
  /**
   * @return string
   */
  public function getToolInput()
  {
    return $this->toolInput;
  }
  /**
   * Required. Spec for tool name
   *
   * @param string $toolName
   */
  public function setToolName($toolName)
  {
    $this->toolName = $toolName;
  }
  /**
   * @return string
   */
  public function getToolName()
  {
    return $this->toolName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ToolCall::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ToolCall');
