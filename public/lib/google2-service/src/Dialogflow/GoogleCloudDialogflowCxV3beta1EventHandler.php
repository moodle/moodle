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

class GoogleCloudDialogflowCxV3beta1EventHandler extends \Google\Model
{
  /**
   * Required. The name of the event to handle.
   *
   * @var string
   */
  public $event;
  /**
   * Output only. The unique identifier of this event handler.
   *
   * @var string
   */
  public $name;
  /**
   * The target flow to transition to. Format:
   * `projects//locations//agents//flows/`.
   *
   * @var string
   */
  public $targetFlow;
  /**
   * The target page to transition to. Format:
   * `projects//locations//agents//flows//pages/`.
   *
   * @var string
   */
  public $targetPage;
  /**
   * The target playbook to transition to. Format:
   * `projects//locations//agents//playbooks/`.
   *
   * @var string
   */
  public $targetPlaybook;
  protected $triggerFulfillmentType = GoogleCloudDialogflowCxV3beta1Fulfillment::class;
  protected $triggerFulfillmentDataType = '';

  /**
   * Required. The name of the event to handle.
   *
   * @param string $event
   */
  public function setEvent($event)
  {
    $this->event = $event;
  }
  /**
   * @return string
   */
  public function getEvent()
  {
    return $this->event;
  }
  /**
   * Output only. The unique identifier of this event handler.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The target flow to transition to. Format:
   * `projects//locations//agents//flows/`.
   *
   * @param string $targetFlow
   */
  public function setTargetFlow($targetFlow)
  {
    $this->targetFlow = $targetFlow;
  }
  /**
   * @return string
   */
  public function getTargetFlow()
  {
    return $this->targetFlow;
  }
  /**
   * The target page to transition to. Format:
   * `projects//locations//agents//flows//pages/`.
   *
   * @param string $targetPage
   */
  public function setTargetPage($targetPage)
  {
    $this->targetPage = $targetPage;
  }
  /**
   * @return string
   */
  public function getTargetPage()
  {
    return $this->targetPage;
  }
  /**
   * The target playbook to transition to. Format:
   * `projects//locations//agents//playbooks/`.
   *
   * @param string $targetPlaybook
   */
  public function setTargetPlaybook($targetPlaybook)
  {
    $this->targetPlaybook = $targetPlaybook;
  }
  /**
   * @return string
   */
  public function getTargetPlaybook()
  {
    return $this->targetPlaybook;
  }
  /**
   * The fulfillment to call when the event occurs. Handling webhook errors with
   * a fulfillment enabled with webhook could cause infinite loop. It is invalid
   * to specify such fulfillment for a handler handling webhooks.
   *
   * @param GoogleCloudDialogflowCxV3beta1Fulfillment $triggerFulfillment
   */
  public function setTriggerFulfillment(GoogleCloudDialogflowCxV3beta1Fulfillment $triggerFulfillment)
  {
    $this->triggerFulfillment = $triggerFulfillment;
  }
  /**
   * @return GoogleCloudDialogflowCxV3beta1Fulfillment
   */
  public function getTriggerFulfillment()
  {
    return $this->triggerFulfillment;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3beta1EventHandler::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3beta1EventHandler');
