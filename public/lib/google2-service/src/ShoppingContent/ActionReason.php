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

class ActionReason extends \Google\Model
{
  protected $actionType = Action::class;
  protected $actionDataType = '';
  /**
   * Detailed explanation of the reason. Should be displayed as a hint if
   * present.
   *
   * @var string
   */
  public $detail;
  /**
   * Messages summarizing the reason, why the action is not available. For
   * example: "Review requested on Jan 03. Review requests can take a few days
   * to complete."
   *
   * @var string
   */
  public $message;

  /**
   * Optional. An action that needs to be performed to solve the problem
   * represented by this reason. This action will always be available. Should be
   * rendered as a link or button next to the summarizing message. For example,
   * the review may be available only once merchant configure all required
   * attributes. In such a situation this action can be a link to the form,
   * where they can fill the missing attribute to unblock the main action.
   *
   * @param Action $action
   */
  public function setAction(Action $action)
  {
    $this->action = $action;
  }
  /**
   * @return Action
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * Detailed explanation of the reason. Should be displayed as a hint if
   * present.
   *
   * @param string $detail
   */
  public function setDetail($detail)
  {
    $this->detail = $detail;
  }
  /**
   * @return string
   */
  public function getDetail()
  {
    return $this->detail;
  }
  /**
   * Messages summarizing the reason, why the action is not available. For
   * example: "Review requested on Jan 03. Review requests can take a few days
   * to complete."
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ActionReason::class, 'Google_Service_ShoppingContent_ActionReason');
