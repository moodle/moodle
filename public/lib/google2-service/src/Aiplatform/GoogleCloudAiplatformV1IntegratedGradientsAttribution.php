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

class GoogleCloudAiplatformV1IntegratedGradientsAttribution extends \Google\Model
{
  protected $blurBaselineConfigType = GoogleCloudAiplatformV1BlurBaselineConfig::class;
  protected $blurBaselineConfigDataType = '';
  protected $smoothGradConfigType = GoogleCloudAiplatformV1SmoothGradConfig::class;
  protected $smoothGradConfigDataType = '';
  /**
   * Required. The number of steps for approximating the path integral. A good
   * value to start is 50 and gradually increase until the sum to diff property
   * is within the desired error range. Valid range of its value is [1, 100],
   * inclusively.
   *
   * @var int
   */
  public $stepCount;

  /**
   * Config for IG with blur baseline. When enabled, a linear path from the
   * maximally blurred image to the input image is created. Using a blurred
   * baseline instead of zero (black image) is motivated by the BlurIG approach
   * explained here: https://arxiv.org/abs/2004.03383
   *
   * @param GoogleCloudAiplatformV1BlurBaselineConfig $blurBaselineConfig
   */
  public function setBlurBaselineConfig(GoogleCloudAiplatformV1BlurBaselineConfig $blurBaselineConfig)
  {
    $this->blurBaselineConfig = $blurBaselineConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1BlurBaselineConfig
   */
  public function getBlurBaselineConfig()
  {
    return $this->blurBaselineConfig;
  }
  /**
   * Config for SmoothGrad approximation of gradients. When enabled, the
   * gradients are approximated by averaging the gradients from noisy samples in
   * the vicinity of the inputs. Adding noise can help improve the computed
   * gradients. Refer to this paper for more details:
   * https://arxiv.org/pdf/1706.03825.pdf
   *
   * @param GoogleCloudAiplatformV1SmoothGradConfig $smoothGradConfig
   */
  public function setSmoothGradConfig(GoogleCloudAiplatformV1SmoothGradConfig $smoothGradConfig)
  {
    $this->smoothGradConfig = $smoothGradConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1SmoothGradConfig
   */
  public function getSmoothGradConfig()
  {
    return $this->smoothGradConfig;
  }
  /**
   * Required. The number of steps for approximating the path integral. A good
   * value to start is 50 and gradually increase until the sum to diff property
   * is within the desired error range. Valid range of its value is [1, 100],
   * inclusively.
   *
   * @param int $stepCount
   */
  public function setStepCount($stepCount)
  {
    $this->stepCount = $stepCount;
  }
  /**
   * @return int
   */
  public function getStepCount()
  {
    return $this->stepCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1IntegratedGradientsAttribution::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1IntegratedGradientsAttribution');
