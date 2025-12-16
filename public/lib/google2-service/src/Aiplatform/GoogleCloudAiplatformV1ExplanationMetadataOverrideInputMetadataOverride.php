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

class GoogleCloudAiplatformV1ExplanationMetadataOverrideInputMetadataOverride extends \Google\Collection
{
  protected $collection_key = 'inputBaselines';
  /**
   * Baseline inputs for this feature. This overrides the `input_baseline` field
   * of the ExplanationMetadata.InputMetadata object of the corresponding
   * feature's input metadata. If it's not specified, the original baselines are
   * not overridden.
   *
   * @var array[]
   */
  public $inputBaselines;

  /**
   * Baseline inputs for this feature. This overrides the `input_baseline` field
   * of the ExplanationMetadata.InputMetadata object of the corresponding
   * feature's input metadata. If it's not specified, the original baselines are
   * not overridden.
   *
   * @param array[] $inputBaselines
   */
  public function setInputBaselines($inputBaselines)
  {
    $this->inputBaselines = $inputBaselines;
  }
  /**
   * @return array[]
   */
  public function getInputBaselines()
  {
    return $this->inputBaselines;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ExplanationMetadataOverrideInputMetadataOverride::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ExplanationMetadataOverrideInputMetadataOverride');
