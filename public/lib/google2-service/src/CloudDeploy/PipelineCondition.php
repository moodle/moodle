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

namespace Google\Service\CloudDeploy;

class PipelineCondition extends \Google\Model
{
  protected $pipelineReadyConditionType = PipelineReadyCondition::class;
  protected $pipelineReadyConditionDataType = '';
  protected $targetsPresentConditionType = TargetsPresentCondition::class;
  protected $targetsPresentConditionDataType = '';
  protected $targetsTypeConditionType = TargetsTypeCondition::class;
  protected $targetsTypeConditionDataType = '';

  /**
   * Details around the Pipeline's overall status.
   *
   * @param PipelineReadyCondition $pipelineReadyCondition
   */
  public function setPipelineReadyCondition(PipelineReadyCondition $pipelineReadyCondition)
  {
    $this->pipelineReadyCondition = $pipelineReadyCondition;
  }
  /**
   * @return PipelineReadyCondition
   */
  public function getPipelineReadyCondition()
  {
    return $this->pipelineReadyCondition;
  }
  /**
   * Details around targets enumerated in the pipeline.
   *
   * @param TargetsPresentCondition $targetsPresentCondition
   */
  public function setTargetsPresentCondition(TargetsPresentCondition $targetsPresentCondition)
  {
    $this->targetsPresentCondition = $targetsPresentCondition;
  }
  /**
   * @return TargetsPresentCondition
   */
  public function getTargetsPresentCondition()
  {
    return $this->targetsPresentCondition;
  }
  /**
   * Details on the whether the targets enumerated in the pipeline are of the
   * same type.
   *
   * @param TargetsTypeCondition $targetsTypeCondition
   */
  public function setTargetsTypeCondition(TargetsTypeCondition $targetsTypeCondition)
  {
    $this->targetsTypeCondition = $targetsTypeCondition;
  }
  /**
   * @return TargetsTypeCondition
   */
  public function getTargetsTypeCondition()
  {
    return $this->targetsTypeCondition;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PipelineCondition::class, 'Google_Service_CloudDeploy_PipelineCondition');
