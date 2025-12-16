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

class GoogleCloudAiplatformV1ExplanationMetadataOverride extends \Google\Model
{
  protected $inputsType = GoogleCloudAiplatformV1ExplanationMetadataOverrideInputMetadataOverride::class;
  protected $inputsDataType = 'map';

  /**
   * Required. Overrides the input metadata of the features. The key is the name
   * of the feature to be overridden. The keys specified here must exist in the
   * input metadata to be overridden. If a feature is not specified here, the
   * corresponding feature's input metadata is not overridden.
   *
   * @param GoogleCloudAiplatformV1ExplanationMetadataOverrideInputMetadataOverride[] $inputs
   */
  public function setInputs($inputs)
  {
    $this->inputs = $inputs;
  }
  /**
   * @return GoogleCloudAiplatformV1ExplanationMetadataOverrideInputMetadataOverride[]
   */
  public function getInputs()
  {
    return $this->inputs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ExplanationMetadataOverride::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ExplanationMetadataOverride');
