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

namespace Google\Service\MigrationCenterAPI;

class MigrationInsight extends \Google\Model
{
  protected $computeEngineTargetType = ComputeEngineMigrationTarget::class;
  protected $computeEngineTargetDataType = '';
  protected $fitType = FitDescriptor::class;
  protected $fitDataType = '';

  /**
   * Output only. A Google Compute Engine target.
   *
   * @param ComputeEngineMigrationTarget $computeEngineTarget
   */
  public function setComputeEngineTarget(ComputeEngineMigrationTarget $computeEngineTarget)
  {
    $this->computeEngineTarget = $computeEngineTarget;
  }
  /**
   * @return ComputeEngineMigrationTarget
   */
  public function getComputeEngineTarget()
  {
    return $this->computeEngineTarget;
  }
  /**
   * Output only. Description of how well the asset this insight is associated
   * with fits the proposed migration.
   *
   * @param FitDescriptor $fit
   */
  public function setFit(FitDescriptor $fit)
  {
    $this->fit = $fit;
  }
  /**
   * @return FitDescriptor
   */
  public function getFit()
  {
    return $this->fit;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MigrationInsight::class, 'Google_Service_MigrationCenterAPI_MigrationInsight');
