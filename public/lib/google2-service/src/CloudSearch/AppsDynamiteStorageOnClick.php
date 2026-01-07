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

namespace Google\Service\CloudSearch;

class AppsDynamiteStorageOnClick extends \Google\Model
{
  protected $actionType = AppsDynamiteStorageAction::class;
  protected $actionDataType = '';
  protected $hostAppActionType = HostAppActionMarkup::class;
  protected $hostAppActionDataType = '';
  protected $openDynamicLinkActionType = AppsDynamiteStorageAction::class;
  protected $openDynamicLinkActionDataType = '';
  protected $openLinkType = AppsDynamiteStorageOpenLink::class;
  protected $openLinkDataType = '';

  /**
   * @param AppsDynamiteStorageAction
   */
  public function setAction(AppsDynamiteStorageAction $action)
  {
    $this->action = $action;
  }
  /**
   * @return AppsDynamiteStorageAction
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * @param HostAppActionMarkup
   */
  public function setHostAppAction(HostAppActionMarkup $hostAppAction)
  {
    $this->hostAppAction = $hostAppAction;
  }
  /**
   * @return HostAppActionMarkup
   */
  public function getHostAppAction()
  {
    return $this->hostAppAction;
  }
  /**
   * @param AppsDynamiteStorageAction
   */
  public function setOpenDynamicLinkAction(AppsDynamiteStorageAction $openDynamicLinkAction)
  {
    $this->openDynamicLinkAction = $openDynamicLinkAction;
  }
  /**
   * @return AppsDynamiteStorageAction
   */
  public function getOpenDynamicLinkAction()
  {
    return $this->openDynamicLinkAction;
  }
  /**
   * @param AppsDynamiteStorageOpenLink
   */
  public function setOpenLink(AppsDynamiteStorageOpenLink $openLink)
  {
    $this->openLink = $openLink;
  }
  /**
   * @return AppsDynamiteStorageOpenLink
   */
  public function getOpenLink()
  {
    return $this->openLink;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppsDynamiteStorageOnClick::class, 'Google_Service_CloudSearch_AppsDynamiteStorageOnClick');
