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

class GoogleCloudAiplatformV1EvaluationRunMetricPredefinedMetricSpec extends \Google\Model
{
  /**
   * Required. The name of a pre-defined metric, such as
   * "instruction_following_v1" or "text_quality_v1".
   *
   * @var string
   */
  public $metricSpecName;
  /**
   * Optional. The parameters needed to run the pre-defined metric.
   *
   * @var array[]
   */
  public $parameters;

  /**
   * Required. The name of a pre-defined metric, such as
   * "instruction_following_v1" or "text_quality_v1".
   *
   * @param string $metricSpecName
   */
  public function setMetricSpecName($metricSpecName)
  {
    $this->metricSpecName = $metricSpecName;
  }
  /**
   * @return string
   */
  public function getMetricSpecName()
  {
    return $this->metricSpecName;
  }
  /**
   * Optional. The parameters needed to run the pre-defined metric.
   *
   * @param array[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return array[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1EvaluationRunMetricPredefinedMetricSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1EvaluationRunMetricPredefinedMetricSpec');
