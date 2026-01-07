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

class GoogleCloudAiplatformV1ModelExplanation extends \Google\Collection
{
  protected $collection_key = 'meanAttributions';
  protected $meanAttributionsType = GoogleCloudAiplatformV1Attribution::class;
  protected $meanAttributionsDataType = 'array';

  /**
   * Output only. Aggregated attributions explaining the Model's prediction
   * outputs over the set of instances. The attributions are grouped by outputs.
   * For Models that predict only one output, such as regression Models that
   * predict only one score, there is only one attibution that explains the
   * predicted output. For Models that predict multiple outputs, such as
   * multiclass Models that predict multiple classes, each element explains one
   * specific item. Attribution.output_index can be used to identify which
   * output this attribution is explaining. The baselineOutputValue,
   * instanceOutputValue and featureAttributions fields are averaged over the
   * test data. NOTE: Currently AutoML tabular classification Models produce
   * only one attribution, which averages attributions over all the classes it
   * predicts. Attribution.approximation_error is not populated.
   *
   * @param GoogleCloudAiplatformV1Attribution[] $meanAttributions
   */
  public function setMeanAttributions($meanAttributions)
  {
    $this->meanAttributions = $meanAttributions;
  }
  /**
   * @return GoogleCloudAiplatformV1Attribution[]
   */
  public function getMeanAttributions()
  {
    return $this->meanAttributions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ModelExplanation::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ModelExplanation');
