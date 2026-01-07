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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3HandlerLifecycleHandler extends \Google\Model
{
  /**
   * Optional. The condition that must be satisfied to trigger this handler.
   *
   * @var string
   */
  public $condition;
  protected $fulfillmentType = GoogleCloudDialogflowCxV3Fulfillment::class;
  protected $fulfillmentDataType = '';
  /**
   * Required. The name of the lifecycle stage that triggers this handler.
   * Supported values: * `playbook-start` * `pre-action-selection` * `pre-
   * action-execution`
   *
   * @var string
   */
  public $lifecycleStage;

  /**
   * Optional. The condition that must be satisfied to trigger this handler.
   *
   * @param string $condition
   */
  public function setCondition($condition)
  {
    $this->condition = $condition;
  }
  /**
   * @return string
   */
  public function getCondition()
  {
    return $this->condition;
  }
  /**
   * Required. The fulfillment to call when this handler is triggered.
   *
   * @param GoogleCloudDialogflowCxV3Fulfillment $fulfillment
   */
  public function setFulfillment(GoogleCloudDialogflowCxV3Fulfillment $fulfillment)
  {
    $this->fulfillment = $fulfillment;
  }
  /**
   * @return GoogleCloudDialogflowCxV3Fulfillment
   */
  public function getFulfillment()
  {
    return $this->fulfillment;
  }
  /**
   * Required. The name of the lifecycle stage that triggers this handler.
   * Supported values: * `playbook-start` * `pre-action-selection` * `pre-
   * action-execution`
   *
   * @param string $lifecycleStage
   */
  public function setLifecycleStage($lifecycleStage)
  {
    $this->lifecycleStage = $lifecycleStage;
  }
  /**
   * @return string
   */
  public function getLifecycleStage()
  {
    return $this->lifecycleStage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3HandlerLifecycleHandler::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3HandlerLifecycleHandler');
