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

class GoogleCloudDialogflowCxV3beta1FormParameterFillBehavior extends \Google\Collection
{
  protected $collection_key = 'repromptEventHandlers';
  protected $initialPromptFulfillmentType = GoogleCloudDialogflowCxV3beta1Fulfillment::class;
  protected $initialPromptFulfillmentDataType = '';
  protected $repromptEventHandlersType = GoogleCloudDialogflowCxV3beta1EventHandler::class;
  protected $repromptEventHandlersDataType = 'array';

  /**
   * Required. The fulfillment to provide the initial prompt that the agent can
   * present to the user in order to fill the parameter.
   *
   * @param GoogleCloudDialogflowCxV3beta1Fulfillment $initialPromptFulfillment
   */
  public function setInitialPromptFulfillment(GoogleCloudDialogflowCxV3beta1Fulfillment $initialPromptFulfillment)
  {
    $this->initialPromptFulfillment = $initialPromptFulfillment;
  }
  /**
   * @return GoogleCloudDialogflowCxV3beta1Fulfillment
   */
  public function getInitialPromptFulfillment()
  {
    return $this->initialPromptFulfillment;
  }
  /**
   * The handlers for parameter-level events, used to provide reprompt for the
   * parameter or transition to a different page/flow. The supported events are:
   * * `sys.no-match-`, where N can be from 1 to 6 * `sys.no-match-default` *
   * `sys.no-input-`, where N can be from 1 to 6 * `sys.no-input-default` *
   * `sys.invalid-parameter` `initial_prompt_fulfillment` provides the first
   * prompt for the parameter. If the user's response does not fill the
   * parameter, a no-match/no-input event will be triggered, and the fulfillment
   * associated with the `sys.no-match-1`/`sys.no-input-1` handler (if defined)
   * will be called to provide a prompt. The `sys.no-match-2`/`sys.no-input-2`
   * handler (if defined) will respond to the next no-match/no-input event, and
   * so on. A `sys.no-match-default` or `sys.no-input-default` handler will be
   * used to handle all following no-match/no-input events after all numbered
   * no-match/no-input handlers for the parameter are consumed. A `sys.invalid-
   * parameter` handler can be defined to handle the case where the parameter
   * values have been `invalidated` by webhook. For example, if the user's
   * response fill the parameter, however the parameter was invalidated by
   * webhook, the fulfillment associated with the `sys.invalid-parameter`
   * handler (if defined) will be called to provide a prompt. If the event
   * handler for the corresponding event can't be found on the parameter,
   * `initial_prompt_fulfillment` will be re-prompted.
   *
   * @param GoogleCloudDialogflowCxV3beta1EventHandler[] $repromptEventHandlers
   */
  public function setRepromptEventHandlers($repromptEventHandlers)
  {
    $this->repromptEventHandlers = $repromptEventHandlers;
  }
  /**
   * @return GoogleCloudDialogflowCxV3beta1EventHandler[]
   */
  public function getRepromptEventHandlers()
  {
    return $this->repromptEventHandlers;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3beta1FormParameterFillBehavior::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3beta1FormParameterFillBehavior');
