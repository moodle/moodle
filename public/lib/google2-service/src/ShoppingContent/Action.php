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

class Action extends \Google\Collection
{
  protected $collection_key = 'reasons';
  protected $builtinSimpleActionType = BuiltInSimpleAction::class;
  protected $builtinSimpleActionDataType = '';
  protected $builtinUserInputActionType = BuiltInUserInputAction::class;
  protected $builtinUserInputActionDataType = '';
  /**
   * Label of the action button.
   *
   * @var string
   */
  public $buttonLabel;
  protected $externalActionType = ExternalAction::class;
  protected $externalActionDataType = '';
  /**
   * Controlling whether the button is active or disabled. The value is 'false'
   * when the action was already requested or is not available. If the action is
   * not available then a reason will be present. If (your) third-party
   * application shows a disabled button for action that is not available, then
   * it should also show reasons.
   *
   * @var bool
   */
  public $isAvailable;
  protected $reasonsType = ActionReason::class;
  protected $reasonsDataType = 'array';

  /**
   * Action implemented and performed in (your) third-party application. The
   * application should point the merchant to the place, where they can access
   * the corresponding functionality or provide instructions, if the specific
   * functionality is not available.
   *
   * @param BuiltInSimpleAction $builtinSimpleAction
   */
  public function setBuiltinSimpleAction(BuiltInSimpleAction $builtinSimpleAction)
  {
    $this->builtinSimpleAction = $builtinSimpleAction;
  }
  /**
   * @return BuiltInSimpleAction
   */
  public function getBuiltinSimpleAction()
  {
    return $this->builtinSimpleAction;
  }
  /**
   * Action implemented and performed in (your) third-party application. The
   * application needs to show an additional content and input form to the
   * merchant as specified for given action. They can trigger the action only
   * when they provided all required inputs.
   *
   * @param BuiltInUserInputAction $builtinUserInputAction
   */
  public function setBuiltinUserInputAction(BuiltInUserInputAction $builtinUserInputAction)
  {
    $this->builtinUserInputAction = $builtinUserInputAction;
  }
  /**
   * @return BuiltInUserInputAction
   */
  public function getBuiltinUserInputAction()
  {
    return $this->builtinUserInputAction;
  }
  /**
   * Label of the action button.
   *
   * @param string $buttonLabel
   */
  public function setButtonLabel($buttonLabel)
  {
    $this->buttonLabel = $buttonLabel;
  }
  /**
   * @return string
   */
  public function getButtonLabel()
  {
    return $this->buttonLabel;
  }
  /**
   * Action that is implemented and performed outside of (your) third-party
   * application. The application needs to redirect the merchant to the external
   * location where they can perform the action.
   *
   * @param ExternalAction $externalAction
   */
  public function setExternalAction(ExternalAction $externalAction)
  {
    $this->externalAction = $externalAction;
  }
  /**
   * @return ExternalAction
   */
  public function getExternalAction()
  {
    return $this->externalAction;
  }
  /**
   * Controlling whether the button is active or disabled. The value is 'false'
   * when the action was already requested or is not available. If the action is
   * not available then a reason will be present. If (your) third-party
   * application shows a disabled button for action that is not available, then
   * it should also show reasons.
   *
   * @param bool $isAvailable
   */
  public function setIsAvailable($isAvailable)
  {
    $this->isAvailable = $isAvailable;
  }
  /**
   * @return bool
   */
  public function getIsAvailable()
  {
    return $this->isAvailable;
  }
  /**
   * List of reasons why the action is not available. The list of reasons is
   * empty if the action is available. If there is only one reason, it can be
   * displayed next to the disabled button. If there are more reasons, all of
   * them should be displayed, for example in a pop-up dialog.
   *
   * @param ActionReason[] $reasons
   */
  public function setReasons($reasons)
  {
    $this->reasons = $reasons;
  }
  /**
   * @return ActionReason[]
   */
  public function getReasons()
  {
    return $this->reasons;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Action::class, 'Google_Service_ShoppingContent_Action');
