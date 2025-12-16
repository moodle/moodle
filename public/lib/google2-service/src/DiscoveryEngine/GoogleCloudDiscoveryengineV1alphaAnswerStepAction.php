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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1alphaAnswerStepAction extends \Google\Model
{
  protected $observationType = GoogleCloudDiscoveryengineV1alphaAnswerStepActionObservation::class;
  protected $observationDataType = '';
  protected $searchActionType = GoogleCloudDiscoveryengineV1alphaAnswerStepActionSearchAction::class;
  protected $searchActionDataType = '';

  /**
   * Observation.
   *
   * @param GoogleCloudDiscoveryengineV1alphaAnswerStepActionObservation $observation
   */
  public function setObservation(GoogleCloudDiscoveryengineV1alphaAnswerStepActionObservation $observation)
  {
    $this->observation = $observation;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaAnswerStepActionObservation
   */
  public function getObservation()
  {
    return $this->observation;
  }
  /**
   * Search action.
   *
   * @param GoogleCloudDiscoveryengineV1alphaAnswerStepActionSearchAction $searchAction
   */
  public function setSearchAction(GoogleCloudDiscoveryengineV1alphaAnswerStepActionSearchAction $searchAction)
  {
    $this->searchAction = $searchAction;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaAnswerStepActionSearchAction
   */
  public function getSearchAction()
  {
    return $this->searchAction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaAnswerStepAction::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaAnswerStepAction');
