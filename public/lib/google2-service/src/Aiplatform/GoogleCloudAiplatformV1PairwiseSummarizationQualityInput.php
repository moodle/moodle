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

class GoogleCloudAiplatformV1PairwiseSummarizationQualityInput extends \Google\Model
{
  protected $instanceType = GoogleCloudAiplatformV1PairwiseSummarizationQualityInstance::class;
  protected $instanceDataType = '';
  protected $metricSpecType = GoogleCloudAiplatformV1PairwiseSummarizationQualitySpec::class;
  protected $metricSpecDataType = '';

  /**
   * Required. Pairwise summarization quality instance.
   *
   * @param GoogleCloudAiplatformV1PairwiseSummarizationQualityInstance $instance
   */
  public function setInstance(GoogleCloudAiplatformV1PairwiseSummarizationQualityInstance $instance)
  {
    $this->instance = $instance;
  }
  /**
   * @return GoogleCloudAiplatformV1PairwiseSummarizationQualityInstance
   */
  public function getInstance()
  {
    return $this->instance;
  }
  /**
   * Required. Spec for pairwise summarization quality score metric.
   *
   * @param GoogleCloudAiplatformV1PairwiseSummarizationQualitySpec $metricSpec
   */
  public function setMetricSpec(GoogleCloudAiplatformV1PairwiseSummarizationQualitySpec $metricSpec)
  {
    $this->metricSpec = $metricSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1PairwiseSummarizationQualitySpec
   */
  public function getMetricSpec()
  {
    return $this->metricSpec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1PairwiseSummarizationQualityInput::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PairwiseSummarizationQualityInput');
