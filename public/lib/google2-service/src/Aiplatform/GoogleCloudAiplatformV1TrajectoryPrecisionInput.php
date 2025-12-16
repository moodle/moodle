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

class GoogleCloudAiplatformV1TrajectoryPrecisionInput extends \Google\Collection
{
  protected $collection_key = 'instances';
  protected $instancesType = GoogleCloudAiplatformV1TrajectoryPrecisionInstance::class;
  protected $instancesDataType = 'array';
  protected $metricSpecType = GoogleCloudAiplatformV1TrajectoryPrecisionSpec::class;
  protected $metricSpecDataType = '';

  /**
   * Required. Repeated TrajectoryPrecision instance.
   *
   * @param GoogleCloudAiplatformV1TrajectoryPrecisionInstance[] $instances
   */
  public function setInstances($instances)
  {
    $this->instances = $instances;
  }
  /**
   * @return GoogleCloudAiplatformV1TrajectoryPrecisionInstance[]
   */
  public function getInstances()
  {
    return $this->instances;
  }
  /**
   * Required. Spec for TrajectoryPrecision metric.
   *
   * @param GoogleCloudAiplatformV1TrajectoryPrecisionSpec $metricSpec
   */
  public function setMetricSpec(GoogleCloudAiplatformV1TrajectoryPrecisionSpec $metricSpec)
  {
    $this->metricSpec = $metricSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1TrajectoryPrecisionSpec
   */
  public function getMetricSpec()
  {
    return $this->metricSpec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1TrajectoryPrecisionInput::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1TrajectoryPrecisionInput');
