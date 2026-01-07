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

class GoogleCloudAiplatformV1SummarizationHelpfulnessInput extends \Google\Model
{
  protected $instanceType = GoogleCloudAiplatformV1SummarizationHelpfulnessInstance::class;
  protected $instanceDataType = '';
  protected $metricSpecType = GoogleCloudAiplatformV1SummarizationHelpfulnessSpec::class;
  protected $metricSpecDataType = '';

  /**
   * Required. Summarization helpfulness instance.
   *
   * @param GoogleCloudAiplatformV1SummarizationHelpfulnessInstance $instance
   */
  public function setInstance(GoogleCloudAiplatformV1SummarizationHelpfulnessInstance $instance)
  {
    $this->instance = $instance;
  }
  /**
   * @return GoogleCloudAiplatformV1SummarizationHelpfulnessInstance
   */
  public function getInstance()
  {
    return $this->instance;
  }
  /**
   * Required. Spec for summarization helpfulness score metric.
   *
   * @param GoogleCloudAiplatformV1SummarizationHelpfulnessSpec $metricSpec
   */
  public function setMetricSpec(GoogleCloudAiplatformV1SummarizationHelpfulnessSpec $metricSpec)
  {
    $this->metricSpec = $metricSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1SummarizationHelpfulnessSpec
   */
  public function getMetricSpec()
  {
    return $this->metricSpec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SummarizationHelpfulnessInput::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SummarizationHelpfulnessInput');
