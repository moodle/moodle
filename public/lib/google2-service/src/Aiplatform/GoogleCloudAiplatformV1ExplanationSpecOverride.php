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

class GoogleCloudAiplatformV1ExplanationSpecOverride extends \Google\Model
{
  protected $examplesOverrideType = GoogleCloudAiplatformV1ExamplesOverride::class;
  protected $examplesOverrideDataType = '';
  protected $metadataType = GoogleCloudAiplatformV1ExplanationMetadataOverride::class;
  protected $metadataDataType = '';
  protected $parametersType = GoogleCloudAiplatformV1ExplanationParameters::class;
  protected $parametersDataType = '';

  /**
   * The example-based explanations parameter overrides.
   *
   * @param GoogleCloudAiplatformV1ExamplesOverride $examplesOverride
   */
  public function setExamplesOverride(GoogleCloudAiplatformV1ExamplesOverride $examplesOverride)
  {
    $this->examplesOverride = $examplesOverride;
  }
  /**
   * @return GoogleCloudAiplatformV1ExamplesOverride
   */
  public function getExamplesOverride()
  {
    return $this->examplesOverride;
  }
  /**
   * The metadata to be overridden. If not specified, no metadata is overridden.
   *
   * @param GoogleCloudAiplatformV1ExplanationMetadataOverride $metadata
   */
  public function setMetadata(GoogleCloudAiplatformV1ExplanationMetadataOverride $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return GoogleCloudAiplatformV1ExplanationMetadataOverride
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * The parameters to be overridden. Note that the attribution method cannot be
   * changed. If not specified, no parameter is overridden.
   *
   * @param GoogleCloudAiplatformV1ExplanationParameters $parameters
   */
  public function setParameters(GoogleCloudAiplatformV1ExplanationParameters $parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return GoogleCloudAiplatformV1ExplanationParameters
   */
  public function getParameters()
  {
    return $this->parameters;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ExplanationSpecOverride::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ExplanationSpecOverride');
