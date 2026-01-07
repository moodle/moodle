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

class GoogleCloudAiplatformV1TrajectoryInOrderMatchResults extends \Google\Collection
{
  protected $collection_key = 'trajectoryInOrderMatchMetricValues';
  protected $trajectoryInOrderMatchMetricValuesType = GoogleCloudAiplatformV1TrajectoryInOrderMatchMetricValue::class;
  protected $trajectoryInOrderMatchMetricValuesDataType = 'array';

  /**
   * Output only. TrajectoryInOrderMatch metric values.
   *
   * @param GoogleCloudAiplatformV1TrajectoryInOrderMatchMetricValue[] $trajectoryInOrderMatchMetricValues
   */
  public function setTrajectoryInOrderMatchMetricValues($trajectoryInOrderMatchMetricValues)
  {
    $this->trajectoryInOrderMatchMetricValues = $trajectoryInOrderMatchMetricValues;
  }
  /**
   * @return GoogleCloudAiplatformV1TrajectoryInOrderMatchMetricValue[]
   */
  public function getTrajectoryInOrderMatchMetricValues()
  {
    return $this->trajectoryInOrderMatchMetricValues;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1TrajectoryInOrderMatchResults::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1TrajectoryInOrderMatchResults');
