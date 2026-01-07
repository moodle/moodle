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

class GoogleCloudAiplatformV1SchemaPromptSpecAppBuilderDataLinkedResource extends \Google\Model
{
  /**
   * A user-friendly name for the data source shown in the UI.
   *
   * @var string
   */
  public $displayName;
  /**
   * The unique resource name of the data source. The format is determined by
   * the 'type' field. For type "SAVED_PROMPT":
   * projects/{project}/locations/{location}/datasets/{dataset} For type
   * "AI_AGENT": projects/{project}/locations/{location}/agents/{agent}
   *
   * @var string
   */
  public $name;
  /**
   * The type of the linked resource. e.g., "SAVED_PROMPT", "AI_AGENT" This
   * string corresponds to the name of the LinkedResourceType enum member. See:
   * google3/cloud/console/web/ai/platform/llm/prompts/build/services/specs_repo
   * sitory_service/linked_resources/linked_resource.ts
   *
   * @var string
   */
  public $type;

  /**
   * A user-friendly name for the data source shown in the UI.
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
   * The unique resource name of the data source. The format is determined by
   * the 'type' field. For type "SAVED_PROMPT":
   * projects/{project}/locations/{location}/datasets/{dataset} For type
   * "AI_AGENT": projects/{project}/locations/{location}/agents/{agent}
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
   * The type of the linked resource. e.g., "SAVED_PROMPT", "AI_AGENT" This
   * string corresponds to the name of the LinkedResourceType enum member. See:
   * google3/cloud/console/web/ai/platform/llm/prompts/build/services/specs_repo
   * sitory_service/linked_resources/linked_resource.ts
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaPromptSpecAppBuilderDataLinkedResource::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaPromptSpecAppBuilderDataLinkedResource');
