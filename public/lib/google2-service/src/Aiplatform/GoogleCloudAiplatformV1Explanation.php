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

class GoogleCloudAiplatformV1Explanation extends \Google\Collection
{
  protected $collection_key = 'neighbors';
  protected $attributionsType = GoogleCloudAiplatformV1Attribution::class;
  protected $attributionsDataType = 'array';
  protected $neighborsType = GoogleCloudAiplatformV1Neighbor::class;
  protected $neighborsDataType = 'array';

  /**
   * Output only. Feature attributions grouped by predicted outputs. For Models
   * that predict only one output, such as regression Models that predict only
   * one score, there is only one attibution that explains the predicted output.
   * For Models that predict multiple outputs, such as multiclass Models that
   * predict multiple classes, each element explains one specific item.
   * Attribution.output_index can be used to identify which output this
   * attribution is explaining. By default, we provide Shapley values for the
   * predicted class. However, you can configure the explanation request to
   * generate Shapley values for any other classes too. For example, if a model
   * predicts a probability of `0.4` for approving a loan application, the
   * model's decision is to reject the application since `p(reject) = 0.6 >
   * p(approve) = 0.4`, and the default Shapley values would be computed for
   * rejection decision and not approval, even though the latter might be the
   * positive class. If users set ExplanationParameters.top_k, the attributions
   * are sorted by instance_output_value in descending order. If
   * ExplanationParameters.output_indices is specified, the attributions are
   * stored by Attribution.output_index in the same order as they appear in the
   * output_indices.
   *
   * @param GoogleCloudAiplatformV1Attribution[] $attributions
   */
  public function setAttributions($attributions)
  {
    $this->attributions = $attributions;
  }
  /**
   * @return GoogleCloudAiplatformV1Attribution[]
   */
  public function getAttributions()
  {
    return $this->attributions;
  }
  /**
   * Output only. List of the nearest neighbors for example-based explanations.
   * For models deployed with the examples explanations feature enabled, the
   * attributions field is empty and instead the neighbors field is populated.
   *
   * @param GoogleCloudAiplatformV1Neighbor[] $neighbors
   */
  public function setNeighbors($neighbors)
  {
    $this->neighbors = $neighbors;
  }
  /**
   * @return GoogleCloudAiplatformV1Neighbor[]
   */
  public function getNeighbors()
  {
    return $this->neighbors;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1Explanation::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Explanation');
