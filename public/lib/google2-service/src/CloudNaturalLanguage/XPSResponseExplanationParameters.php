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

namespace Google\Service\CloudNaturalLanguage;

class XPSResponseExplanationParameters extends \Google\Model
{
  protected $integratedGradientsAttributionType = XPSIntegratedGradientsAttribution::class;
  protected $integratedGradientsAttributionDataType = '';
  protected $xraiAttributionType = XPSXraiAttribution::class;
  protected $xraiAttributionDataType = '';

  /**
   * An attribution method that computes Aumann-Shapley values taking advantage
   * of the model's fully differentiable structure. Refer to this paper for more
   * details: https://arxiv.org/abs/1703.01365
   *
   * @param XPSIntegratedGradientsAttribution $integratedGradientsAttribution
   */
  public function setIntegratedGradientsAttribution(XPSIntegratedGradientsAttribution $integratedGradientsAttribution)
  {
    $this->integratedGradientsAttribution = $integratedGradientsAttribution;
  }
  /**
   * @return XPSIntegratedGradientsAttribution
   */
  public function getIntegratedGradientsAttribution()
  {
    return $this->integratedGradientsAttribution;
  }
  /**
   * An attribution method that redistributes Integrated Gradients attribution
   * to segmented regions, taking advantage of the model's fully differentiable
   * structure. Refer to this paper for more details:
   * https://arxiv.org/abs/1906.02825 XRAI currently performs better on natural
   * images, like a picture of a house or an animal. If the images are taken in
   * artificial environments, like a lab or manufacturing line, or from
   * diagnostic equipment, like x-rays or quality-control cameras, use
   * Integrated Gradients instead.
   *
   * @param XPSXraiAttribution $xraiAttribution
   */
  public function setXraiAttribution(XPSXraiAttribution $xraiAttribution)
  {
    $this->xraiAttribution = $xraiAttribution;
  }
  /**
   * @return XPSXraiAttribution
   */
  public function getXraiAttribution()
  {
    return $this->xraiAttribution;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSResponseExplanationParameters::class, 'Google_Service_CloudNaturalLanguage_XPSResponseExplanationParameters');
