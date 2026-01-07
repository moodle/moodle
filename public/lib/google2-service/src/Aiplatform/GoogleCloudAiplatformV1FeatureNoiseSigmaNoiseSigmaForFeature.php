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

class GoogleCloudAiplatformV1FeatureNoiseSigmaNoiseSigmaForFeature extends \Google\Model
{
  /**
   * The name of the input feature for which noise sigma is provided. The
   * features are defined in explanation metadata inputs.
   *
   * @var string
   */
  public $name;
  /**
   * This represents the standard deviation of the Gaussian kernel that will be
   * used to add noise to the feature prior to computing gradients. Similar to
   * noise_sigma but represents the noise added to the current feature. Defaults
   * to 0.1.
   *
   * @var float
   */
  public $sigma;

  /**
   * The name of the input feature for which noise sigma is provided. The
   * features are defined in explanation metadata inputs.
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
   * This represents the standard deviation of the Gaussian kernel that will be
   * used to add noise to the feature prior to computing gradients. Similar to
   * noise_sigma but represents the noise added to the current feature. Defaults
   * to 0.1.
   *
   * @param float $sigma
   */
  public function setSigma($sigma)
  {
    $this->sigma = $sigma;
  }
  /**
   * @return float
   */
  public function getSigma()
  {
    return $this->sigma;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1FeatureNoiseSigmaNoiseSigmaForFeature::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FeatureNoiseSigmaNoiseSigmaForFeature');
