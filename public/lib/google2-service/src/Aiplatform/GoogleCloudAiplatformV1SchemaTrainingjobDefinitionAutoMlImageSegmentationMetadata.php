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

class GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlImageSegmentationMetadata extends \Google\Model
{
  /**
   * Should not be set.
   */
  public const SUCCESSFUL_STOP_REASON_SUCCESSFUL_STOP_REASON_UNSPECIFIED = 'SUCCESSFUL_STOP_REASON_UNSPECIFIED';
  /**
   * The inputs.budgetMilliNodeHours had been reached.
   */
  public const SUCCESSFUL_STOP_REASON_BUDGET_REACHED = 'BUDGET_REACHED';
  /**
   * Further training of the Model ceased to increase its quality, since it
   * already has converged.
   */
  public const SUCCESSFUL_STOP_REASON_MODEL_CONVERGED = 'MODEL_CONVERGED';
  /**
   * The actual training cost of creating this model, expressed in milli node
   * hours, i.e. 1,000 value in this field means 1 node hour. Guaranteed to not
   * exceed inputs.budgetMilliNodeHours.
   *
   * @var string
   */
  public $costMilliNodeHours;
  /**
   * For successful job completions, this is the reason why the job has
   * finished.
   *
   * @var string
   */
  public $successfulStopReason;

  /**
   * The actual training cost of creating this model, expressed in milli node
   * hours, i.e. 1,000 value in this field means 1 node hour. Guaranteed to not
   * exceed inputs.budgetMilliNodeHours.
   *
   * @param string $costMilliNodeHours
   */
  public function setCostMilliNodeHours($costMilliNodeHours)
  {
    $this->costMilliNodeHours = $costMilliNodeHours;
  }
  /**
   * @return string
   */
  public function getCostMilliNodeHours()
  {
    return $this->costMilliNodeHours;
  }
  /**
   * For successful job completions, this is the reason why the job has
   * finished.
   *
   * Accepted values: SUCCESSFUL_STOP_REASON_UNSPECIFIED, BUDGET_REACHED,
   * MODEL_CONVERGED
   *
   * @param self::SUCCESSFUL_STOP_REASON_* $successfulStopReason
   */
  public function setSuccessfulStopReason($successfulStopReason)
  {
    $this->successfulStopReason = $successfulStopReason;
  }
  /**
   * @return self::SUCCESSFUL_STOP_REASON_*
   */
  public function getSuccessfulStopReason()
  {
    return $this->successfulStopReason;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlImageSegmentationMetadata::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlImageSegmentationMetadata');
