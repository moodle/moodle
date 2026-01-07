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

class GoogleCloudAiplatformV1FeatureNoiseSigma extends \Google\Collection
{
  protected $collection_key = 'noiseSigma';
  protected $noiseSigmaType = GoogleCloudAiplatformV1FeatureNoiseSigmaNoiseSigmaForFeature::class;
  protected $noiseSigmaDataType = 'array';

  /**
   * Noise sigma per feature. No noise is added to features that are not set.
   *
   * @param GoogleCloudAiplatformV1FeatureNoiseSigmaNoiseSigmaForFeature[] $noiseSigma
   */
  public function setNoiseSigma($noiseSigma)
  {
    $this->noiseSigma = $noiseSigma;
  }
  /**
   * @return GoogleCloudAiplatformV1FeatureNoiseSigmaNoiseSigmaForFeature[]
   */
  public function getNoiseSigma()
  {
    return $this->noiseSigma;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1FeatureNoiseSigma::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FeatureNoiseSigma');
