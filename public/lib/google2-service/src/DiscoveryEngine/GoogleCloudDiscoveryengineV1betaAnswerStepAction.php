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

class GoogleCloudDiscoveryengineV1betaAnswerStepAction extends \Google\Model
{
  protected $observationType = GoogleCloudDiscoveryengineV1betaAnswerStepActionObservation::class;
  protected $observationDataType = '';
  protected $searchActionType = GoogleCloudDiscoveryengineV1betaAnswerStepActionSearchAction::class;
  protected $searchActionDataType = '';

  /**
   * @param GoogleCloudDiscoveryengineV1betaAnswerStepActionObservation
   */
  public function setObservation(GoogleCloudDiscoveryengineV1betaAnswerStepActionObservation $observation)
  {
    $this->observation = $observation;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaAnswerStepActionObservation
   */
  public function getObservation()
  {
    return $this->observation;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1betaAnswerStepActionSearchAction
   */
  public function setSearchAction(GoogleCloudDiscoveryengineV1betaAnswerStepActionSearchAction $searchAction)
  {
    $this->searchAction = $searchAction;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaAnswerStepActionSearchAction
   */
  public function getSearchAction()
  {
    return $this->searchAction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaAnswerStepAction::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaAnswerStepAction');
