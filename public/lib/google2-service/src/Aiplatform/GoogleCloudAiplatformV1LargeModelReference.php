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

class GoogleCloudAiplatformV1LargeModelReference extends \Google\Model
{
  /**
   * Required. The unique name of the large Foundation or pre-built model. Like
   * "chat-bison", "text-bison". Or model name with version ID, like "chat-
   * bison@001", "text-bison@005", etc.
   *
   * @var string
   */
  public $name;

  /**
   * Required. The unique name of the large Foundation or pre-built model. Like
   * "chat-bison", "text-bison". Or model name with version ID, like "chat-
   * bison@001", "text-bison@005", etc.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1LargeModelReference::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1LargeModelReference');
