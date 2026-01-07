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

class GoogleCloudDialogflowCxV3beta1TransitionRoute extends \Google\Model
{
  /**
   * The condition to evaluate against form parameters or session parameters.
   * See the [conditions reference](https://cloud.google.com/dialogflow/cx/docs/
   * reference/condition). At least one of `intent` or `condition` must be
   * specified. When both `intent` and `condition` are specified, the transition
   * can only happen when both are fulfilled.
   *
   * @var string
   */
  public $condition;
  /**
   * Optional. The description of the transition route. The maximum length is
   * 500 characters.
   *
   * @var string
   */
  public $description;
  /**
   * The unique identifier of an Intent. Format:
   * `projects//locations//agents//intents/`. Indicates that the transition can
   * only happen when the given intent is matched. At least one of `intent` or
   * `condition` must be specified. When both `intent` and `condition` are
   * specified, the transition can only happen when both are fulfilled.
   *
   * @var string
   */
  public $intent;
  /**
   * Output only. The unique identifier of this transition route.
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
  protected $triggerFulfillmentType = GoogleCloudDialogflowCxV3beta1Fulfillment::class;
  protected $triggerFulfillmentDataType = '';

  /**
   * The condition to evaluate against form parameters or session parameters.
   * See the [conditions reference](https://cloud.google.com/dialogflow/cx/docs/
   * reference/condition). At least one of `intent` or `condition` must be
   * specified. When both `intent` and `condition` are specified, the transition
   * can only happen when both are fulfilled.
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
   * Optional. The description of the transition route. The maximum length is
   * 500 characters.
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
  /**
   * The unique identifier of an Intent. Format:
   * `projects//locations//agents//intents/`. Indicates that the transition can
   * only happen when the given intent is matched. At least one of `intent` or
   * `condition` must be specified. When both `intent` and `condition` are
   * specified, the transition can only happen when both are fulfilled.
   *
   * @param string $intent
   */
  public function setIntent($intent)
  {
    $this->intent = $intent;
  }
  /**
   * @return string
   */
  public function getIntent()
  {
    return $this->intent;
  }
  /**
   * Output only. The unique identifier of this transition route.
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
   * The fulfillment to call when the condition is satisfied. At least one of
   * `trigger_fulfillment` and `target` must be specified. When both are
   * defined, `trigger_fulfillment` is executed first.
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
class_alias(GoogleCloudDialogflowCxV3beta1TransitionRoute::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3beta1TransitionRoute');
