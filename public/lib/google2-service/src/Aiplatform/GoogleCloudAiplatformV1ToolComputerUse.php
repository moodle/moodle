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

class GoogleCloudAiplatformV1ToolComputerUse extends \Google\Collection
{
  /**
   * Defaults to browser.
   */
  public const ENVIRONMENT_ENVIRONMENT_UNSPECIFIED = 'ENVIRONMENT_UNSPECIFIED';
  /**
   * Operates in a web browser.
   */
  public const ENVIRONMENT_ENVIRONMENT_BROWSER = 'ENVIRONMENT_BROWSER';
  protected $collection_key = 'excludedPredefinedFunctions';
  /**
   * Required. The environment being operated.
   *
   * @var string
   */
  public $environment;
  /**
   * Optional. By default, [predefined
   * functions](https://cloud.google.com/vertex-ai/generative-ai/docs/computer-
   * use#supported-actions) are included in the final model call. Some of them
   * can be explicitly excluded from being automatically included. This can
   * serve two purposes: 1. Using a more restricted / different action space. 2.
   * Improving the definitions / instructions of predefined functions.
   *
   * @var string[]
   */
  public $excludedPredefinedFunctions;

  /**
   * Required. The environment being operated.
   *
   * Accepted values: ENVIRONMENT_UNSPECIFIED, ENVIRONMENT_BROWSER
   *
   * @param self::ENVIRONMENT_* $environment
   */
  public function setEnvironment($environment)
  {
    $this->environment = $environment;
  }
  /**
   * @return self::ENVIRONMENT_*
   */
  public function getEnvironment()
  {
    return $this->environment;
  }
  /**
   * Optional. By default, [predefined
   * functions](https://cloud.google.com/vertex-ai/generative-ai/docs/computer-
   * use#supported-actions) are included in the final model call. Some of them
   * can be explicitly excluded from being automatically included. This can
   * serve two purposes: 1. Using a more restricted / different action space. 2.
   * Improving the definitions / instructions of predefined functions.
   *
   * @param string[] $excludedPredefinedFunctions
   */
  public function setExcludedPredefinedFunctions($excludedPredefinedFunctions)
  {
    $this->excludedPredefinedFunctions = $excludedPredefinedFunctions;
  }
  /**
   * @return string[]
   */
  public function getExcludedPredefinedFunctions()
  {
    return $this->excludedPredefinedFunctions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ToolComputerUse::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ToolComputerUse');
