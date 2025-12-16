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

class GoogleCloudAiplatformV1ExplanationSpec extends \Google\Model
{
  protected $metadataType = GoogleCloudAiplatformV1ExplanationMetadata::class;
  protected $metadataDataType = '';
  protected $parametersType = GoogleCloudAiplatformV1ExplanationParameters::class;
  protected $parametersDataType = '';

  /**
   * Optional. Metadata describing the Model's input and output for explanation.
   *
   * @param GoogleCloudAiplatformV1ExplanationMetadata $metadata
   */
  public function setMetadata(GoogleCloudAiplatformV1ExplanationMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return GoogleCloudAiplatformV1ExplanationMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Required. Parameters that configure explaining of the Model's predictions.
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
class_alias(GoogleCloudAiplatformV1ExplanationSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ExplanationSpec');
