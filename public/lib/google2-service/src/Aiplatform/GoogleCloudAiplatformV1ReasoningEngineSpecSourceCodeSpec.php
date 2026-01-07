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

class GoogleCloudAiplatformV1ReasoningEngineSpecSourceCodeSpec extends \Google\Model
{
  protected $developerConnectSourceType = GoogleCloudAiplatformV1ReasoningEngineSpecSourceCodeSpecDeveloperConnectSource::class;
  protected $developerConnectSourceDataType = '';
  protected $inlineSourceType = GoogleCloudAiplatformV1ReasoningEngineSpecSourceCodeSpecInlineSource::class;
  protected $inlineSourceDataType = '';
  protected $pythonSpecType = GoogleCloudAiplatformV1ReasoningEngineSpecSourceCodeSpecPythonSpec::class;
  protected $pythonSpecDataType = '';

  /**
   * Source code is in a Git repository managed by Developer Connect.
   *
   * @param GoogleCloudAiplatformV1ReasoningEngineSpecSourceCodeSpecDeveloperConnectSource $developerConnectSource
   */
  public function setDeveloperConnectSource(GoogleCloudAiplatformV1ReasoningEngineSpecSourceCodeSpecDeveloperConnectSource $developerConnectSource)
  {
    $this->developerConnectSource = $developerConnectSource;
  }
  /**
   * @return GoogleCloudAiplatformV1ReasoningEngineSpecSourceCodeSpecDeveloperConnectSource
   */
  public function getDeveloperConnectSource()
  {
    return $this->developerConnectSource;
  }
  /**
   * Source code is provided directly in the request.
   *
   * @param GoogleCloudAiplatformV1ReasoningEngineSpecSourceCodeSpecInlineSource $inlineSource
   */
  public function setInlineSource(GoogleCloudAiplatformV1ReasoningEngineSpecSourceCodeSpecInlineSource $inlineSource)
  {
    $this->inlineSource = $inlineSource;
  }
  /**
   * @return GoogleCloudAiplatformV1ReasoningEngineSpecSourceCodeSpecInlineSource
   */
  public function getInlineSource()
  {
    return $this->inlineSource;
  }
  /**
   * Configuration for a Python application.
   *
   * @param GoogleCloudAiplatformV1ReasoningEngineSpecSourceCodeSpecPythonSpec $pythonSpec
   */
  public function setPythonSpec(GoogleCloudAiplatformV1ReasoningEngineSpecSourceCodeSpecPythonSpec $pythonSpec)
  {
    $this->pythonSpec = $pythonSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1ReasoningEngineSpecSourceCodeSpecPythonSpec
   */
  public function getPythonSpec()
  {
    return $this->pythonSpec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ReasoningEngineSpecSourceCodeSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ReasoningEngineSpecSourceCodeSpec');
