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

class GoogleCloudAiplatformV1SchemaPromptSpecAppBuilderData extends \Google\Collection
{
  protected $collection_key = 'linkedResources';
  /**
   * Serialized state of the code repository. This string will typically contain
   * a JSON representation of the UI's CodeRepositoryService state (files,
   * folders, content, and any metadata). The UI is responsible for
   * serialization and deserialization.
   *
   * @var string
   */
  public $codeRepositoryState;
  protected $linkedResourcesType = GoogleCloudAiplatformV1SchemaPromptSpecAppBuilderDataLinkedResource::class;
  protected $linkedResourcesDataType = 'array';

  /**
   * Serialized state of the code repository. This string will typically contain
   * a JSON representation of the UI's CodeRepositoryService state (files,
   * folders, content, and any metadata). The UI is responsible for
   * serialization and deserialization.
   *
   * @param string $codeRepositoryState
   */
  public function setCodeRepositoryState($codeRepositoryState)
  {
    $this->codeRepositoryState = $codeRepositoryState;
  }
  /**
   * @return string
   */
  public function getCodeRepositoryState()
  {
    return $this->codeRepositoryState;
  }
  /**
   * Linked resources attached to the application by the user.
   *
   * @param GoogleCloudAiplatformV1SchemaPromptSpecAppBuilderDataLinkedResource[] $linkedResources
   */
  public function setLinkedResources($linkedResources)
  {
    $this->linkedResources = $linkedResources;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaPromptSpecAppBuilderDataLinkedResource[]
   */
  public function getLinkedResources()
  {
    return $this->linkedResources;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaPromptSpecAppBuilderData::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaPromptSpecAppBuilderData');
