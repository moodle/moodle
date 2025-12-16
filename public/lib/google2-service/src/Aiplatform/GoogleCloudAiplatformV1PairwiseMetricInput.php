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

class GoogleCloudAiplatformV1PairwiseMetricInput extends \Google\Model
{
  protected $instanceType = GoogleCloudAiplatformV1PairwiseMetricInstance::class;
  protected $instanceDataType = '';
  protected $metricSpecType = GoogleCloudAiplatformV1PairwiseMetricSpec::class;
  protected $metricSpecDataType = '';

  /**
   * Required. Pairwise metric instance.
   *
   * @param GoogleCloudAiplatformV1PairwiseMetricInstance $instance
   */
  public function setInstance(GoogleCloudAiplatformV1PairwiseMetricInstance $instance)
  {
    $this->instance = $instance;
  }
  /**
   * @return GoogleCloudAiplatformV1PairwiseMetricInstance
   */
  public function getInstance()
  {
    return $this->instance;
  }
  /**
   * Required. Spec for pairwise metric.
   *
   * @param GoogleCloudAiplatformV1PairwiseMetricSpec $metricSpec
   */
  public function setMetricSpec(GoogleCloudAiplatformV1PairwiseMetricSpec $metricSpec)
  {
    $this->metricSpec = $metricSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1PairwiseMetricSpec
   */
  public function getMetricSpec()
  {
    return $this->metricSpec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1PairwiseMetricInput::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PairwiseMetricInput');
