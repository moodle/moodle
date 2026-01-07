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

class GoogleCloudAiplatformV1SmoothGradConfig extends \Google\Model
{
  protected $featureNoiseSigmaType = GoogleCloudAiplatformV1FeatureNoiseSigma::class;
  protected $featureNoiseSigmaDataType = '';
  /**
   * This is a single float value and will be used to add noise to all the
   * features. Use this field when all features are normalized to have the same
   * distribution: scale to range [0, 1], [-1, 1] or z-scoring, where features
   * are normalized to have 0-mean and 1-variance. Learn more about
   * [normalization](https://developers.google.com/machine-learning/data-
   * prep/transform/normalization). For best results the recommended value is
   * about 10% - 20% of the standard deviation of the input feature. Refer to
   * section 3.2 of the SmoothGrad paper: https://arxiv.org/pdf/1706.03825.pdf.
   * Defaults to 0.1. If the distribution is different per feature, set
   * feature_noise_sigma instead for each feature.
   *
   * @var float
   */
  public $noiseSigma;
  /**
   * The number of gradient samples to use for approximation. The higher this
   * number, the more accurate the gradient is, but the runtime complexity
   * increases by this factor as well. Valid range of its value is [1, 50].
   * Defaults to 3.
   *
   * @var int
   */
  public $noisySampleCount;

  /**
   * This is similar to noise_sigma, but provides additional flexibility. A
   * separate noise sigma can be provided for each feature, which is useful if
   * their distributions are different. No noise is added to features that are
   * not set. If this field is unset, noise_sigma will be used for all features.
   *
   * @param GoogleCloudAiplatformV1FeatureNoiseSigma $featureNoiseSigma
   */
  public function setFeatureNoiseSigma(GoogleCloudAiplatformV1FeatureNoiseSigma $featureNoiseSigma)
  {
    $this->featureNoiseSigma = $featureNoiseSigma;
  }
  /**
   * @return GoogleCloudAiplatformV1FeatureNoiseSigma
   */
  public function getFeatureNoiseSigma()
  {
    return $this->featureNoiseSigma;
  }
  /**
   * This is a single float value and will be used to add noise to all the
   * features. Use this field when all features are normalized to have the same
   * distribution: scale to range [0, 1], [-1, 1] or z-scoring, where features
   * are normalized to have 0-mean and 1-variance. Learn more about
   * [normalization](https://developers.google.com/machine-learning/data-
   * prep/transform/normalization). For best results the recommended value is
   * about 10% - 20% of the standard deviation of the input feature. Refer to
   * section 3.2 of the SmoothGrad paper: https://arxiv.org/pdf/1706.03825.pdf.
   * Defaults to 0.1. If the distribution is different per feature, set
   * feature_noise_sigma instead for each feature.
   *
   * @param float $noiseSigma
   */
  public function setNoiseSigma($noiseSigma)
  {
    $this->noiseSigma = $noiseSigma;
  }
  /**
   * @return float
   */
  public function getNoiseSigma()
  {
    return $this->noiseSigma;
  }
  /**
   * The number of gradient samples to use for approximation. The higher this
   * number, the more accurate the gradient is, but the runtime complexity
   * increases by this factor as well. Valid range of its value is [1, 50].
   * Defaults to 3.
   *
   * @param int $noisySampleCount
   */
  public function setNoisySampleCount($noisySampleCount)
  {
    $this->noisySampleCount = $noisySampleCount;
  }
  /**
   * @return int
   */
  public function getNoisySampleCount()
  {
    return $this->noisySampleCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SmoothGradConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SmoothGradConfig');
