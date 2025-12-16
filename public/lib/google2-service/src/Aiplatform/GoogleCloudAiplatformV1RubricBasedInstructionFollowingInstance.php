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

class GoogleCloudAiplatformV1RubricBasedInstructionFollowingInstance extends \Google\Model
{
  /**
   * Required. Instance specified as a json string. String key-value pairs are
   * expected in the json_instance to render RubricBasedInstructionFollowing
   * prompt templates.
   *
   * @var string
   */
  public $jsonInstance;

  /**
   * Required. Instance specified as a json string. String key-value pairs are
   * expected in the json_instance to render RubricBasedInstructionFollowing
   * prompt templates.
   *
   * @param string $jsonInstance
   */
  public function setJsonInstance($jsonInstance)
  {
    $this->jsonInstance = $jsonInstance;
  }
  /**
   * @return string
   */
  public function getJsonInstance()
  {
    return $this->jsonInstance;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1RubricBasedInstructionFollowingInstance::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1RubricBasedInstructionFollowingInstance');
