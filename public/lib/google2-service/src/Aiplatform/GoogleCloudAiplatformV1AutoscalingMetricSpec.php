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

class GoogleCloudAiplatformV1AutoscalingMetricSpec extends \Google\Model
{
  /**
   * Required. The resource metric name. Supported metrics: * For Online
   * Prediction: *
   * `aiplatform.googleapis.com/prediction/online/accelerator/duty_cycle` *
   * `aiplatform.googleapis.com/prediction/online/cpu/utilization` *
   * `aiplatform.googleapis.com/prediction/online/request_count` *
   * `pubsub.googleapis.com/subscription/num_undelivered_messages`
   *
   * @var string
   */
  public $metricName;
  /**
   * The target resource utilization in percentage (1% - 100%) for the given
   * metric; once the real usage deviates from the target by a certain
   * percentage, the machine replicas change. The default value is 60
   * (representing 60%) if not provided.
   *
   * @var int
   */
  public $target;

  /**
   * Required. The resource metric name. Supported metrics: * For Online
   * Prediction: *
   * `aiplatform.googleapis.com/prediction/online/accelerator/duty_cycle` *
   * `aiplatform.googleapis.com/prediction/online/cpu/utilization` *
   * `aiplatform.googleapis.com/prediction/online/request_count` *
   * `pubsub.googleapis.com/subscription/num_undelivered_messages`
   *
   * @param string $metricName
   */
  public function setMetricName($metricName)
  {
    $this->metricName = $metricName;
  }
  /**
   * @return string
   */
  public function getMetricName()
  {
    return $this->metricName;
  }
  /**
   * The target resource utilization in percentage (1% - 100%) for the given
   * metric; once the real usage deviates from the target by a certain
   * percentage, the machine replicas change. The default value is 60
   * (representing 60%) if not provided.
   *
   * @param int $target
   */
  public function setTarget($target)
  {
    $this->target = $target;
  }
  /**
   * @return int
   */
  public function getTarget()
  {
    return $this->target;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1AutoscalingMetricSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1AutoscalingMetricSpec');
