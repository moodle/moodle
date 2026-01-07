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

class GoogleCloudAiplatformV1RagFileParsingConfigLlmParser extends \Google\Model
{
  /**
   * The prompt to use for parsing. If not specified, a default prompt will be
   * used.
   *
   * @var string
   */
  public $customParsingPrompt;
  /**
   * The maximum number of requests the job is allowed to make to the LLM model
   * per minute. Consult https://cloud.google.com/vertex-ai/generative-
   * ai/docs/quotas and your document size to set an appropriate value here. If
   * unspecified, a default value of 5000 QPM would be used.
   *
   * @var int
   */
  public $maxParsingRequestsPerMin;
  /**
   * The name of a LLM model used for parsing. Format: * `projects/{project_id}/
   * locations/{location}/publishers/{publisher}/models/{model}`
   *
   * @var string
   */
  public $modelName;

  /**
   * The prompt to use for parsing. If not specified, a default prompt will be
   * used.
   *
   * @param string $customParsingPrompt
   */
  public function setCustomParsingPrompt($customParsingPrompt)
  {
    $this->customParsingPrompt = $customParsingPrompt;
  }
  /**
   * @return string
   */
  public function getCustomParsingPrompt()
  {
    return $this->customParsingPrompt;
  }
  /**
   * The maximum number of requests the job is allowed to make to the LLM model
   * per minute. Consult https://cloud.google.com/vertex-ai/generative-
   * ai/docs/quotas and your document size to set an appropriate value here. If
   * unspecified, a default value of 5000 QPM would be used.
   *
   * @param int $maxParsingRequestsPerMin
   */
  public function setMaxParsingRequestsPerMin($maxParsingRequestsPerMin)
  {
    $this->maxParsingRequestsPerMin = $maxParsingRequestsPerMin;
  }
  /**
   * @return int
   */
  public function getMaxParsingRequestsPerMin()
  {
    return $this->maxParsingRequestsPerMin;
  }
  /**
   * The name of a LLM model used for parsing. Format: * `projects/{project_id}/
   * locations/{location}/publishers/{publisher}/models/{model}`
   *
   * @param string $modelName
   */
  public function setModelName($modelName)
  {
    $this->modelName = $modelName;
  }
  /**
   * @return string
   */
  public function getModelName()
  {
    return $this->modelName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1RagFileParsingConfigLlmParser::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1RagFileParsingConfigLlmParser');
