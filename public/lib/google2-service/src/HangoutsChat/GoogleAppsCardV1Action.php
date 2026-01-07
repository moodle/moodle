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

namespace Google\Service\HangoutsChat;

class GoogleAppsCardV1Action extends \Google\Collection
{
  /**
   * Default value. The `action` executes as normal.
   */
  public const INTERACTION_INTERACTION_UNSPECIFIED = 'INTERACTION_UNSPECIFIED';
  /**
   * Opens a [dialog](https://developers.google.com/workspace/chat/dialogs), a
   * windowed, card-based interface that Chat apps use to interact with users.
   * Only supported by Chat apps in response to button-clicks on card messages.
   * If specified for an add-on, the entire card is stripped and nothing is
   * shown in the client. [Google Chat
   * apps](https://developers.google.com/workspace/chat):
   */
  public const INTERACTION_OPEN_DIALOG = 'OPEN_DIALOG';
  /**
   * Displays a spinner to indicate that content is loading.
   */
  public const LOAD_INDICATOR_SPINNER = 'SPINNER';
  /**
   * Nothing is displayed.
   */
  public const LOAD_INDICATOR_NONE = 'NONE';
  protected $collection_key = 'requiredWidgets';
  /**
   * Optional. If this is true, then all widgets are considered required by this
   * action. [Google Workspace add-ons and Chat
   * apps](https://developers.google.com/workspace/extend):
   *
   * @var bool
   */
  public $allWidgetsAreRequired;
  /**
   * A custom function to invoke when the containing element is clicked or
   * otherwise activated. For example usage, see [Read form
   * data](https://developers.google.com/workspace/chat/read-form-data).
   *
   * @var string
   */
  public $function;
  /**
   * Optional. Required when opening a
   * [dialog](https://developers.google.com/workspace/chat/dialogs). What to do
   * in response to an interaction with a user, such as a user clicking a button
   * in a card message. If unspecified, the app responds by executing an
   * `action`—like opening a link or running a function—as normal. By specifying
   * an `interaction`, the app can respond in special interactive ways. For
   * example, by setting `interaction` to `OPEN_DIALOG`, the app can open a
   * [dialog](https://developers.google.com/workspace/chat/dialogs). When
   * specified, a loading indicator isn't shown. If specified for an add-on, the
   * entire card is stripped and nothing is shown in the client. [Google Chat
   * apps](https://developers.google.com/workspace/chat):
   *
   * @var string
   */
  public $interaction;
  /**
   * Specifies the loading indicator that the action displays while making the
   * call to the action.
   *
   * @var string
   */
  public $loadIndicator;
  protected $parametersType = GoogleAppsCardV1ActionParameter::class;
  protected $parametersDataType = 'array';
  /**
   * Indicates whether form values persist after the action. The default value
   * is `false`. If `true`, form values remain after the action is triggered. To
   * let the user make changes while the action is being processed, set
   * [`LoadIndicator`](https://developers.google.com/workspace/add-
   * ons/reference/rpc/google.apps.card.v1#loadindicator) to `NONE`. For [card m
   * essages](https://developers.google.com/workspace/chat/api/guides/v1/message
   * s/create#create) in Chat apps, you must also set the action's [`ResponseTyp
   * e`](https://developers.google.com/workspace/chat/api/reference/rest/v1/spac
   * es.messages#responsetype) to `UPDATE_MESSAGE` and use the same [`card_id`](
   * https://developers.google.com/workspace/chat/api/reference/rest/v1/spaces.m
   * essages#CardWithId) from the card that contained the action. If `false`,
   * the form values are cleared when the action is triggered. To prevent the
   * user from making changes while the action is being processed, set
   * [`LoadIndicator`](https://developers.google.com/workspace/add-
   * ons/reference/rpc/google.apps.card.v1#loadindicator) to `SPINNER`.
   *
   * @var bool
   */
  public $persistValues;
  /**
   * Optional. Fill this list with the names of widgets that this Action needs
   * for a valid submission. If the widgets listed here don't have a value when
   * this Action is invoked, the form submission is aborted. [Google Workspace
   * add-ons and Chat apps](https://developers.google.com/workspace/extend):
   *
   * @var string[]
   */
  public $requiredWidgets;

  /**
   * Optional. If this is true, then all widgets are considered required by this
   * action. [Google Workspace add-ons and Chat
   * apps](https://developers.google.com/workspace/extend):
   *
   * @param bool $allWidgetsAreRequired
   */
  public function setAllWidgetsAreRequired($allWidgetsAreRequired)
  {
    $this->allWidgetsAreRequired = $allWidgetsAreRequired;
  }
  /**
   * @return bool
   */
  public function getAllWidgetsAreRequired()
  {
    return $this->allWidgetsAreRequired;
  }
  /**
   * A custom function to invoke when the containing element is clicked or
   * otherwise activated. For example usage, see [Read form
   * data](https://developers.google.com/workspace/chat/read-form-data).
   *
   * @param string $function
   */
  public function setFunction($function)
  {
    $this->function = $function;
  }
  /**
   * @return string
   */
  public function getFunction()
  {
    return $this->function;
  }
  /**
   * Optional. Required when opening a
   * [dialog](https://developers.google.com/workspace/chat/dialogs). What to do
   * in response to an interaction with a user, such as a user clicking a button
   * in a card message. If unspecified, the app responds by executing an
   * `action`—like opening a link or running a function—as normal. By specifying
   * an `interaction`, the app can respond in special interactive ways. For
   * example, by setting `interaction` to `OPEN_DIALOG`, the app can open a
   * [dialog](https://developers.google.com/workspace/chat/dialogs). When
   * specified, a loading indicator isn't shown. If specified for an add-on, the
   * entire card is stripped and nothing is shown in the client. [Google Chat
   * apps](https://developers.google.com/workspace/chat):
   *
   * Accepted values: INTERACTION_UNSPECIFIED, OPEN_DIALOG
   *
   * @param self::INTERACTION_* $interaction
   */
  public function setInteraction($interaction)
  {
    $this->interaction = $interaction;
  }
  /**
   * @return self::INTERACTION_*
   */
  public function getInteraction()
  {
    return $this->interaction;
  }
  /**
   * Specifies the loading indicator that the action displays while making the
   * call to the action.
   *
   * Accepted values: SPINNER, NONE
   *
   * @param self::LOAD_INDICATOR_* $loadIndicator
   */
  public function setLoadIndicator($loadIndicator)
  {
    $this->loadIndicator = $loadIndicator;
  }
  /**
   * @return self::LOAD_INDICATOR_*
   */
  public function getLoadIndicator()
  {
    return $this->loadIndicator;
  }
  /**
   * List of action parameters.
   *
   * @param GoogleAppsCardV1ActionParameter[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return GoogleAppsCardV1ActionParameter[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * Indicates whether form values persist after the action. The default value
   * is `false`. If `true`, form values remain after the action is triggered. To
   * let the user make changes while the action is being processed, set
   * [`LoadIndicator`](https://developers.google.com/workspace/add-
   * ons/reference/rpc/google.apps.card.v1#loadindicator) to `NONE`. For [card m
   * essages](https://developers.google.com/workspace/chat/api/guides/v1/message
   * s/create#create) in Chat apps, you must also set the action's [`ResponseTyp
   * e`](https://developers.google.com/workspace/chat/api/reference/rest/v1/spac
   * es.messages#responsetype) to `UPDATE_MESSAGE` and use the same [`card_id`](
   * https://developers.google.com/workspace/chat/api/reference/rest/v1/spaces.m
   * essages#CardWithId) from the card that contained the action. If `false`,
   * the form values are cleared when the action is triggered. To prevent the
   * user from making changes while the action is being processed, set
   * [`LoadIndicator`](https://developers.google.com/workspace/add-
   * ons/reference/rpc/google.apps.card.v1#loadindicator) to `SPINNER`.
   *
   * @param bool $persistValues
   */
  public function setPersistValues($persistValues)
  {
    $this->persistValues = $persistValues;
  }
  /**
   * @return bool
   */
  public function getPersistValues()
  {
    return $this->persistValues;
  }
  /**
   * Optional. Fill this list with the names of widgets that this Action needs
   * for a valid submission. If the widgets listed here don't have a value when
   * this Action is invoked, the form submission is aborted. [Google Workspace
   * add-ons and Chat apps](https://developers.google.com/workspace/extend):
   *
   * @param string[] $requiredWidgets
   */
  public function setRequiredWidgets($requiredWidgets)
  {
    $this->requiredWidgets = $requiredWidgets;
  }
  /**
   * @return string[]
   */
  public function getRequiredWidgets()
  {
    return $this->requiredWidgets;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCardV1Action::class, 'Google_Service_HangoutsChat_GoogleAppsCardV1Action');
