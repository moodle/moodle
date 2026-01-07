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

namespace Google\Service\CloudMachineLearningEngine;

class GoogleCloudMlV1ExplanationConfig extends \Google\Model
{
  protected $integratedGradientsAttributionType = GoogleCloudMlV1IntegratedGradientsAttribution::class;
  protected $integratedGradientsAttributionDataType = '';
  protected $sampledShapleyAttributionType = GoogleCloudMlV1SampledShapleyAttribution::class;
  protected $sampledShapleyAttributionDataType = '';
  protected $xraiAttributionType = GoogleCloudMlV1XraiAttribution::class;
  protected $xraiAttributionDataType = '';

  /**
   * Attributes credit by computing the Aumann-Shapley value taking advantage of
   * the model's fully differentiable structure. Refer to this paper for more
   * details: https://arxiv.org/abs/1703.01365
   *
   * @param GoogleCloudMlV1IntegratedGradientsAttribution $integratedGradientsAttribution
   */
  public function setIntegratedGradientsAttribution(GoogleCloudMlV1IntegratedGradientsAttribution $integratedGradientsAttribution)
  {
    $this->integratedGradientsAttribution = $integratedGradientsAttribution;
  }
  /**
   * @return GoogleCloudMlV1IntegratedGradientsAttribution
   */
  public function getIntegratedGradientsAttribution()
  {
    return $this->integratedGradientsAttribution;
  }
  /**
   * An attribution method that approximates Shapley values for features that
   * contribute to the label being predicted. A sampling strategy is used to
   * approximate the value rather than considering all subsets of features.
   *
   * @param GoogleCloudMlV1SampledShapleyAttribution $sampledShapleyAttribution
   */
  public function setSampledShapleyAttribution(GoogleCloudMlV1SampledShapleyAttribution $sampledShapleyAttribution)
  {
    $this->sampledShapleyAttribution = $sampledShapleyAttribution;
  }
  /**
   * @return GoogleCloudMlV1SampledShapleyAttribution
   */
  public function getSampledShapleyAttribution()
  {
    return $this->sampledShapleyAttribution;
  }
  /**
   * Attributes credit by computing the XRAI taking advantage of the model's
   * fully differentiable structure. Refer to this paper for more details:
   * https://arxiv.org/abs/1906.02825 Currently only implemented for models with
   * natural image inputs.
   *
   * @param GoogleCloudMlV1XraiAttribution $xraiAttribution
   */
  public function setXraiAttribution(GoogleCloudMlV1XraiAttribution $xraiAttribution)
  {
    $this->xraiAttribution = $xraiAttribution;
  }
  /**
   * @return GoogleCloudMlV1XraiAttribution
   */
  public function getXraiAttribution()
  {
    return $this->xraiAttribution;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudMlV1ExplanationConfig::class, 'Google_Service_CloudMachineLearningEngine_GoogleCloudMlV1ExplanationConfig');
