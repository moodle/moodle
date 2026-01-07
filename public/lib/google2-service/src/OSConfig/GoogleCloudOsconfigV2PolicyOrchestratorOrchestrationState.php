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

namespace Google\Service\OSConfig;

class GoogleCloudOsconfigV2PolicyOrchestratorOrchestrationState extends \Google\Model
{
  protected $currentIterationStateType = GoogleCloudOsconfigV2PolicyOrchestratorIterationState::class;
  protected $currentIterationStateDataType = '';
  protected $previousIterationStateType = GoogleCloudOsconfigV2PolicyOrchestratorIterationState::class;
  protected $previousIterationStateDataType = '';

  /**
   * Output only. Current Wave iteration state.
   *
   * @param GoogleCloudOsconfigV2PolicyOrchestratorIterationState $currentIterationState
   */
  public function setCurrentIterationState(GoogleCloudOsconfigV2PolicyOrchestratorIterationState $currentIterationState)
  {
    $this->currentIterationState = $currentIterationState;
  }
  /**
   * @return GoogleCloudOsconfigV2PolicyOrchestratorIterationState
   */
  public function getCurrentIterationState()
  {
    return $this->currentIterationState;
  }
  /**
   * Output only. Previous Wave iteration state.
   *
   * @param GoogleCloudOsconfigV2PolicyOrchestratorIterationState $previousIterationState
   */
  public function setPreviousIterationState(GoogleCloudOsconfigV2PolicyOrchestratorIterationState $previousIterationState)
  {
    $this->previousIterationState = $previousIterationState;
  }
  /**
   * @return GoogleCloudOsconfigV2PolicyOrchestratorIterationState
   */
  public function getPreviousIterationState()
  {
    return $this->previousIterationState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudOsconfigV2PolicyOrchestratorOrchestrationState::class, 'Google_Service_OSConfig_GoogleCloudOsconfigV2PolicyOrchestratorOrchestrationState');
