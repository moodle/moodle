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

class TriggerActionPayload extends \Google\Model
{
  /**
   * Required. The context from the selected action. The value is obtained from
   * rendered issues and needs to be sent back to identify the action that is
   * being triggered.
   *
   * @var string
   */
  public $actionContext;
  protected $actionInputType = ActionInput::class;
  protected $actionInputDataType = '';

  /**
   * Required. The context from the selected action. The value is obtained from
   * rendered issues and needs to be sent back to identify the action that is
   * being triggered.
   *
   * @param string $actionContext
   */
  public function setActionContext($actionContext)
  {
    $this->actionContext = $actionContext;
  }
  /**
   * @return string
   */
  public function getActionContext()
  {
    return $this->actionContext;
  }
  /**
   * Required. Input provided by the merchant.
   *
   * @param ActionInput $actionInput
   */
  public function setActionInput(ActionInput $actionInput)
  {
    $this->actionInput = $actionInput;
  }
  /**
   * @return ActionInput
   */
  public function getActionInput()
  {
    return $this->actionInput;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TriggerActionPayload::class, 'Google_Service_ShoppingContent_TriggerActionPayload');
