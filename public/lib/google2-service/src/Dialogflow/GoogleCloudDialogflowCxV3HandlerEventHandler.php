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

class GoogleCloudDialogflowCxV3HandlerEventHandler extends \Google\Model
{
  /**
   * Optional. The condition that must be satisfied to trigger this handler.
   *
   * @var string
   */
  public $condition;
  /**
   * Required. The name of the event that triggers this handler.
   *
   * @var string
   */
  public $event;
  protected $fulfillmentType = GoogleCloudDialogflowCxV3Fulfillment::class;
  protected $fulfillmentDataType = '';

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
   * Required. The name of the event that triggers this handler.
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
   * Required. The fulfillment to call when the event occurs.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3HandlerEventHandler::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3HandlerEventHandler');
