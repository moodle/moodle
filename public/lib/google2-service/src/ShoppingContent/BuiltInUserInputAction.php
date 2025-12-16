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

class BuiltInUserInputAction extends \Google\Collection
{
  protected $collection_key = 'flows';
  /**
   * Internal details. Not for display but need to be sent back when triggering
   * the action.
   *
   * @var string
   */
  public $actionContext;
  protected $flowsType = ActionFlow::class;
  protected $flowsDataType = 'array';

  /**
   * Internal details. Not for display but need to be sent back when triggering
   * the action.
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
   * Actions may provide multiple different flows. Merchant selects one that
   * fits best to their intent. Selecting the flow is the first step in user's
   * interaction with the action. It affects what input fields will be available
   * and required and also how the request will be processed.
   *
   * @param ActionFlow[] $flows
   */
  public function setFlows($flows)
  {
    $this->flows = $flows;
  }
  /**
   * @return ActionFlow[]
   */
  public function getFlows()
  {
    return $this->flows;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BuiltInUserInputAction::class, 'Google_Service_ShoppingContent_BuiltInUserInputAction');
