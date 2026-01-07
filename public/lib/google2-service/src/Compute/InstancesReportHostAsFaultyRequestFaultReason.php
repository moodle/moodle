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

class InstancesReportHostAsFaultyRequestFaultReason extends \Google\Model
{
  /**
   * Public reportable behaviors
   */
  public const BEHAVIOR_BEHAVIOR_UNSPECIFIED = 'BEHAVIOR_UNSPECIFIED';
  public const BEHAVIOR_PERFORMANCE = 'PERFORMANCE';
  public const BEHAVIOR_SILENT_DATA_CORRUPTION = 'SILENT_DATA_CORRUPTION';
  public const BEHAVIOR_UNRECOVERABLE_GPU_ERROR = 'UNRECOVERABLE_GPU_ERROR';
  /**
   * @var string
   */
  public $behavior;
  /**
   * @var string
   */
  public $description;

  /**
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
class_alias(InstancesReportHostAsFaultyRequestFaultReason::class, 'Google_Service_Compute_InstancesReportHostAsFaultyRequestFaultReason');
