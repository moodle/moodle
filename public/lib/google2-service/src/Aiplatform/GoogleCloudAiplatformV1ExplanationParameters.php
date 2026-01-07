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

class GoogleCloudAiplatformV1ExplanationParameters extends \Google\Collection
{
  protected $collection_key = 'outputIndices';
  protected $examplesType = GoogleCloudAiplatformV1Examples::class;
  protected $examplesDataType = '';
  protected $integratedGradientsAttributionType = GoogleCloudAiplatformV1IntegratedGradientsAttribution::class;
  protected $integratedGradientsAttributionDataType = '';
  /**
   * If populated, only returns attributions that have output_index contained in
   * output_indices. It must be an ndarray of integers, with the same shape of
   * the output it's explaining. If not populated, returns attributions for
   * top_k indices of outputs. If neither top_k nor output_indices is populated,
   * returns the argmax index of the outputs. Only applicable to Models that
   * predict multiple outputs (e,g, multi-class Models that predict multiple
   * classes).
   *
   * @var array[]
   */
  public $outputIndices;
  protected $sampledShapleyAttributionType = GoogleCloudAiplatformV1SampledShapleyAttribution::class;
  protected $sampledShapleyAttributionDataType = '';
  /**
   * If populated, returns attributions for top K indices of outputs (defaults
   * to 1). Only applies to Models that predicts more than one outputs (e,g,
   * multi-class Models). When set to -1, returns explanations for all outputs.
   *
   * @var int
   */
  public $topK;
  protected $xraiAttributionType = GoogleCloudAiplatformV1XraiAttribution::class;
  protected $xraiAttributionDataType = '';

  /**
   * Example-based explanations that returns the nearest neighbors from the
   * provided dataset.
   *
   * @param GoogleCloudAiplatformV1Examples $examples
   */
  public function setExamples(GoogleCloudAiplatformV1Examples $examples)
  {
    $this->examples = $examples;
  }
  /**
   * @return GoogleCloudAiplatformV1Examples
   */
  public function getExamples()
  {
    return $this->examples;
  }
  /**
   * An attribution method that computes Aumann-Shapley values taking advantage
   * of the model's fully differentiable structure. Refer to this paper for more
   * details: https://arxiv.org/abs/1703.01365
   *
   * @param GoogleCloudAiplatformV1IntegratedGradientsAttribution $integratedGradientsAttribution
   */
  public function setIntegratedGradientsAttribution(GoogleCloudAiplatformV1IntegratedGradientsAttribution $integratedGradientsAttribution)
  {
    $this->integratedGradientsAttribution = $integratedGradientsAttribution;
  }
  /**
   * @return GoogleCloudAiplatformV1IntegratedGradientsAttribution
   */
  public function getIntegratedGradientsAttribution()
  {
    return $this->integratedGradientsAttribution;
  }
  /**
   * If populated, only returns attributions that have output_index contained in
   * output_indices. It must be an ndarray of integers, with the same shape of
   * the output it's explaining. If not populated, returns attributions for
   * top_k indices of outputs. If neither top_k nor output_indices is populated,
   * returns the argmax index of the outputs. Only applicable to Models that
   * predict multiple outputs (e,g, multi-class Models that predict multiple
   * classes).
   *
   * @param array[] $outputIndices
   */
  public function setOutputIndices($outputIndices)
  {
    $this->outputIndices = $outputIndices;
  }
  /**
   * @return array[]
   */
  public function getOutputIndices()
  {
    return $this->outputIndices;
  }
  /**
   * An attribution method that approximates Shapley values for features that
   * contribute to the label being predicted. A sampling strategy is used to
   * approximate the value rather than considering all subsets of features.
   * Refer to this paper for model details: https://arxiv.org/abs/1306.4265.
   *
   * @param GoogleCloudAiplatformV1SampledShapleyAttribution $sampledShapleyAttribution
   */
  public function setSampledShapleyAttribution(GoogleCloudAiplatformV1SampledShapleyAttribution $sampledShapleyAttribution)
  {
    $this->sampledShapleyAttribution = $sampledShapleyAttribution;
  }
  /**
   * @return GoogleCloudAiplatformV1SampledShapleyAttribution
   */
  public function getSampledShapleyAttribution()
  {
    return $this->sampledShapleyAttribution;
  }
  /**
   * If populated, returns attributions for top K indices of outputs (defaults
   * to 1). Only applies to Models that predicts more than one outputs (e,g,
   * multi-class Models). When set to -1, returns explanations for all outputs.
   *
   * @param int $topK
   */
  public function setTopK($topK)
  {
    $this->topK = $topK;
  }
  /**
   * @return int
   */
  public function getTopK()
  {
    return $this->topK;
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
   * @param GoogleCloudAiplatformV1XraiAttribution $xraiAttribution
   */
  public function setXraiAttribution(GoogleCloudAiplatformV1XraiAttribution $xraiAttribution)
  {
    $this->xraiAttribution = $xraiAttribution;
  }
  /**
   * @return GoogleCloudAiplatformV1XraiAttribution
   */
  public function getXraiAttribution()
  {
    return $this->xraiAttribution;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ExplanationParameters::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ExplanationParameters');
