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

class GoogleCloudAiplatformV1QuestionAnsweringRelevanceInput extends \Google\Model
{
  protected $instanceType = GoogleCloudAiplatformV1QuestionAnsweringRelevanceInstance::class;
  protected $instanceDataType = '';
  protected $metricSpecType = GoogleCloudAiplatformV1QuestionAnsweringRelevanceSpec::class;
  protected $metricSpecDataType = '';

  /**
   * Required. Question answering relevance instance.
   *
   * @param GoogleCloudAiplatformV1QuestionAnsweringRelevanceInstance $instance
   */
  public function setInstance(GoogleCloudAiplatformV1QuestionAnsweringRelevanceInstance $instance)
  {
    $this->instance = $instance;
  }
  /**
   * @return GoogleCloudAiplatformV1QuestionAnsweringRelevanceInstance
   */
  public function getInstance()
  {
    return $this->instance;
  }
  /**
   * Required. Spec for question answering relevance score metric.
   *
   * @param GoogleCloudAiplatformV1QuestionAnsweringRelevanceSpec $metricSpec
   */
  public function setMetricSpec(GoogleCloudAiplatformV1QuestionAnsweringRelevanceSpec $metricSpec)
  {
    $this->metricSpec = $metricSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1QuestionAnsweringRelevanceSpec
   */
  public function getMetricSpec()
  {
    return $this->metricSpec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1QuestionAnsweringRelevanceInput::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1QuestionAnsweringRelevanceInput');
