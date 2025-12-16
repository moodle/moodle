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

class GoogleCloudAiplatformV1ExplanationMetadataInputMetadataFeatureValueDomain extends \Google\Model
{
  /**
   * The maximum permissible value for this feature.
   *
   * @var float
   */
  public $maxValue;
  /**
   * The minimum permissible value for this feature.
   *
   * @var float
   */
  public $minValue;
  /**
   * If this input feature has been normalized to a mean value of 0, the
   * original_mean specifies the mean value of the domain prior to
   * normalization.
   *
   * @var float
   */
  public $originalMean;
  /**
   * If this input feature has been normalized to a standard deviation of 1.0,
   * the original_stddev specifies the standard deviation of the domain prior to
   * normalization.
   *
   * @var float
   */
  public $originalStddev;

  /**
   * The maximum permissible value for this feature.
   *
   * @param float $maxValue
   */
  public function setMaxValue($maxValue)
  {
    $this->maxValue = $maxValue;
  }
  /**
   * @return float
   */
  public function getMaxValue()
  {
    return $this->maxValue;
  }
  /**
   * The minimum permissible value for this feature.
   *
   * @param float $minValue
   */
  public function setMinValue($minValue)
  {
    $this->minValue = $minValue;
  }
  /**
   * @return float
   */
  public function getMinValue()
  {
    return $this->minValue;
  }
  /**
   * If this input feature has been normalized to a mean value of 0, the
   * original_mean specifies the mean value of the domain prior to
   * normalization.
   *
   * @param float $originalMean
   */
  public function setOriginalMean($originalMean)
  {
    $this->originalMean = $originalMean;
  }
  /**
   * @return float
   */
  public function getOriginalMean()
  {
    return $this->originalMean;
  }
  /**
   * If this input feature has been normalized to a standard deviation of 1.0,
   * the original_stddev specifies the standard deviation of the domain prior to
   * normalization.
   *
   * @param float $originalStddev
   */
  public function setOriginalStddev($originalStddev)
  {
    $this->originalStddev = $originalStddev;
  }
  /**
   * @return float
   */
  public function getOriginalStddev()
  {
    return $this->originalStddev;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ExplanationMetadataInputMetadataFeatureValueDomain::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ExplanationMetadataInputMetadataFeatureValueDomain');
