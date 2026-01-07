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

namespace Google\Service\Compute;

class ReservationSubBlocksReportFaultyRequestFaultReason extends \Google\Model
{
  public const BEHAVIOR_FAULT_BEHAVIOR_UNSPECIFIED = 'FAULT_BEHAVIOR_UNSPECIFIED';
  /**
   * The subBlock experienced a GPU error.
   */
  public const BEHAVIOR_GPU_ERROR = 'GPU_ERROR';
  /**
   * The subBlock experienced performance issues.
   */
  public const BEHAVIOR_PERFORMANCE = 'PERFORMANCE';
  /**
   * The subBlock experienced silent data corruption.
   */
  public const BEHAVIOR_SILENT_DATA_CORRUPTION = 'SILENT_DATA_CORRUPTION';
  /**
   * The subBlock experienced a switch failure.
   */
  public const BEHAVIOR_SWITCH_FAILURE = 'SWITCH_FAILURE';
  /**
   * The behavior of the fault experienced with the subBlock.
   *
   * @var string
   */
  public $behavior;
  /**
   * The description of the fault experienced with the subBlock.
   *
   * @var string
   */
  public $description;

  /**
   * The behavior of the fault experienced with the subBlock.
   *
   * Accepted values: FAULT_BEHAVIOR_UNSPECIFIED, GPU_ERROR, PERFORMANCE,
   * SILENT_DATA_CORRUPTION, SWITCH_FAILURE
   *
   * @param self::BEHAVIOR_* $behavior
   */
  public function setBehavior($behavior)
  {
    $this->behavior = $behavior;
  }
  /**
   * @return self::BEHAVIOR_*
   */
  public function getBehavior()
  {
    return $this->behavior;
  }
  /**
   * The description of the fault experienced with the subBlock.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReservationSubBlocksReportFaultyRequestFaultReason::class, 'Google_Service_Compute_ReservationSubBlocksReportFaultyRequestFaultReason');
