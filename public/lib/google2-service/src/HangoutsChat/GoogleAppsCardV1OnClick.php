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

class GoogleAppsCardV1OnClick extends \Google\Model
{
  protected $actionType = GoogleAppsCardV1Action::class;
  protected $actionDataType = '';
  protected $cardType = GoogleAppsCardV1Card::class;
  protected $cardDataType = '';
  protected $openDynamicLinkActionType = GoogleAppsCardV1Action::class;
  protected $openDynamicLinkActionDataType = '';
  protected $openLinkType = GoogleAppsCardV1OpenLink::class;
  protected $openLinkDataType = '';
  protected $overflowMenuType = GoogleAppsCardV1OverflowMenu::class;
  protected $overflowMenuDataType = '';

  /**
   * If specified, an action is triggered by this `onClick`.
   *
   * @param GoogleAppsCardV1Action $action
   */
  public function setAction(GoogleAppsCardV1Action $action)
  {
    $this->action = $action;
  }
  /**
   * @return GoogleAppsCardV1Action
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * A new card is pushed to the card stack after clicking if specified. [Google
   * Workspace add-ons](https://developers.google.com/workspace/add-ons):
   *
   * @param GoogleAppsCardV1Card $card
   */
  public function setCard(GoogleAppsCardV1Card $card)
  {
    $this->card = $card;
  }
  /**
   * @return GoogleAppsCardV1Card
   */
  public function getCard()
  {
    return $this->card;
  }
  /**
   * An add-on triggers this action when the action needs to open a link. This
   * differs from the `open_link` above in that this needs to talk to server to
   * get the link. Thus some preparation work is required for web client to do
   * before the open link action response comes back. [Google Workspace add-
   * ons](https://developers.google.com/workspace/add-ons):
   *
   * @param GoogleAppsCardV1Action $openDynamicLinkAction
   */
  public function setOpenDynamicLinkAction(GoogleAppsCardV1Action $openDynamicLinkAction)
  {
    $this->openDynamicLinkAction = $openDynamicLinkAction;
  }
  /**
   * @return GoogleAppsCardV1Action
   */
  public function getOpenDynamicLinkAction()
  {
    return $this->openDynamicLinkAction;
  }
  /**
   * If specified, this `onClick` triggers an open link action.
   *
   * @param GoogleAppsCardV1OpenLink $openLink
   */
  public function setOpenLink(GoogleAppsCardV1OpenLink $openLink)
  {
    $this->openLink = $openLink;
  }
  /**
   * @return GoogleAppsCardV1OpenLink
   */
  public function getOpenLink()
  {
    return $this->openLink;
  }
  /**
   * If specified, this `onClick` opens an overflow menu.
   *
   * @param GoogleAppsCardV1OverflowMenu $overflowMenu
   */
  public function setOverflowMenu(GoogleAppsCardV1OverflowMenu $overflowMenu)
  {
    $this->overflowMenu = $overflowMenu;
  }
  /**
   * @return GoogleAppsCardV1OverflowMenu
   */
  public function getOverflowMenu()
  {
    return $this->overflowMenu;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCardV1OnClick::class, 'Google_Service_HangoutsChat_GoogleAppsCardV1OnClick');
