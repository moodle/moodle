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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1SearchRequestContentSearchSpecSummarySpecModelSpec extends \Google\Model
{
  /**
   * The model version used to generate the summary. Supported values are: *
   * `stable`: string. Default value when no value is specified. Uses a
   * generally available, fine-tuned model. For more information, see [Answer
   * generation model versions and
   * lifecycle](https://cloud.google.com/generative-ai-app-builder/docs/answer-
   * generation-models). * `preview`: string. (Public preview) Uses a preview
   * model. For more information, see [Answer generation model versions and
   * lifecycle](https://cloud.google.com/generative-ai-app-builder/docs/answer-
   * generation-models).
   *
   * @var string
   */
  public $version;

  /**
   * The model version used to generate the summary. Supported values are: *
   * `stable`: string. Default value when no value is specified. Uses a
   * generally available, fine-tuned model. For more information, see [Answer
   * generation model versions and
   * lifecycle](https://cloud.google.com/generative-ai-app-builder/docs/answer-
   * generation-models). * `preview`: string. (Public preview) Uses a preview
   * model. For more information, see [Answer generation model versions and
   * lifecycle](https://cloud.google.com/generative-ai-app-builder/docs/answer-
   * generation-models).
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1SearchRequestContentSearchSpecSummarySpecModelSpec::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1SearchRequestContentSearchSpecSummarySpecModelSpec');
