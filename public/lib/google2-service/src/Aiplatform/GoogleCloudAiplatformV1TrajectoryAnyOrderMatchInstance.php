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

class GoogleCloudAiplatformV1TrajectoryAnyOrderMatchInstance extends \Google\Model
{
  protected $predictedTrajectoryType = GoogleCloudAiplatformV1Trajectory::class;
  protected $predictedTrajectoryDataType = '';
  protected $referenceTrajectoryType = GoogleCloudAiplatformV1Trajectory::class;
  protected $referenceTrajectoryDataType = '';

  /**
   * Required. Spec for predicted tool call trajectory.
   *
   * @param GoogleCloudAiplatformV1Trajectory $predictedTrajectory
   */
  public function setPredictedTrajectory(GoogleCloudAiplatformV1Trajectory $predictedTrajectory)
  {
    $this->predictedTrajectory = $predictedTrajectory;
  }
  /**
   * @return GoogleCloudAiplatformV1Trajectory
   */
  public function getPredictedTrajectory()
  {
    return $this->predictedTrajectory;
  }
  /**
   * Required. Spec for reference tool call trajectory.
   *
   * @param GoogleCloudAiplatformV1Trajectory $referenceTrajectory
   */
  public function setReferenceTrajectory(GoogleCloudAiplatformV1Trajectory $referenceTrajectory)
  {
    $this->referenceTrajectory = $referenceTrajectory;
  }
  /**
   * @return GoogleCloudAiplatformV1Trajectory
   */
  public function getReferenceTrajectory()
  {
    return $this->referenceTrajectory;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1TrajectoryAnyOrderMatchInstance::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1TrajectoryAnyOrderMatchInstance');
