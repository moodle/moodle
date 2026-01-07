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

namespace Google\Service\ShoppingContent;

class RenderAccountIssuesRequestPayload extends \Google\Model
{
  /**
   * Default value. Will never be provided by the API.
   */
  public const CONTENT_OPTION_CONTENT_OPTION_UNSPECIFIED = 'CONTENT_OPTION_UNSPECIFIED';
  /**
   * Returns the detail of the issue as a pre-rendered HTML text.
   */
  public const CONTENT_OPTION_PRE_RENDERED_HTML = 'PRE_RENDERED_HTML';
  /**
   * Default value. Will never be provided by the API.
   */
  public const USER_INPUT_ACTION_OPTION_USER_INPUT_ACTION_RENDERING_OPTION_UNSPECIFIED = 'USER_INPUT_ACTION_RENDERING_OPTION_UNSPECIFIED';
  /**
   * Actions that require user input are represented only as links that points
   * merchant to Merchant Center where they can request the action. Provides
   * easier to implement alternative to `BUILT_IN_USER_INPUT_ACTIONS`.
   */
  public const USER_INPUT_ACTION_OPTION_REDIRECT_TO_MERCHANT_CENTER = 'REDIRECT_TO_MERCHANT_CENTER';
  /**
   * Returns content and input form definition for each complex action. Your
   * application needs to display this content and input form to the merchant
   * before they can request processing of the action. To start the action, your
   * application needs to call the `triggeraction` method.
   */
  public const USER_INPUT_ACTION_OPTION_BUILT_IN_USER_INPUT_ACTIONS = 'BUILT_IN_USER_INPUT_ACTIONS';
  /**
   * Optional. How the detailed content should be returned. Default option is to
   * return the content as a pre-rendered HTML text.
   *
   * @var string
   */
  public $contentOption;
  /**
   * Optional. How actions with user input form should be handled. If not
   * provided, actions will be returned as links that points merchant to
   * Merchant Center where they can request the action.
   *
   * @var string
   */
  public $userInputActionOption;

  /**
   * Optional. How the detailed content should be returned. Default option is to
   * return the content as a pre-rendered HTML text.
   *
   * Accepted values: CONTENT_OPTION_UNSPECIFIED, PRE_RENDERED_HTML
   *
   * @param self::CONTENT_OPTION_* $contentOption
   */
  public function setContentOption($contentOption)
  {
    $this->contentOption = $contentOption;
  }
  /**
   * @return self::CONTENT_OPTION_*
   */
  public function getContentOption()
  {
    return $this->contentOption;
  }
  /**
   * Optional. How actions with user input form should be handled. If not
   * provided, actions will be returned as links that points merchant to
   * Merchant Center where they can request the action.
   *
   * Accepted values: USER_INPUT_ACTION_RENDERING_OPTION_UNSPECIFIED,
   * REDIRECT_TO_MERCHANT_CENTER, BUILT_IN_USER_INPUT_ACTIONS
   *
   * @param self::USER_INPUT_ACTION_OPTION_* $userInputActionOption
   */
  public function setUserInputActionOption($userInputActionOption)
  {
    $this->userInputActionOption = $userInputActionOption;
  }
  /**
   * @return self::USER_INPUT_ACTION_OPTION_*
   */
  public function getUserInputActionOption()
  {
    return $this->userInputActionOption;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RenderAccountIssuesRequestPayload::class, 'Google_Service_ShoppingContent_RenderAccountIssuesRequestPayload');
