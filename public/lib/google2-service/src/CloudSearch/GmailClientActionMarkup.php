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

class GmailClientActionMarkup extends \Google\Model
{
  protected $addonComposeUiActionMarkupType = AddonComposeUiActionMarkup::class;
  protected $addonComposeUiActionMarkupDataType = '';
  protected $openCreatedDraftActionMarkupType = OpenCreatedDraftActionMarkup::class;
  protected $openCreatedDraftActionMarkupDataType = '';
  protected $taskActionType = TaskActionMarkup::class;
  protected $taskActionDataType = '';
  protected $updateDraftActionMarkupType = UpdateDraftActionMarkup::class;
  protected $updateDraftActionMarkupDataType = '';

  /**
   * @param AddonComposeUiActionMarkup
   */
  public function setAddonComposeUiActionMarkup(AddonComposeUiActionMarkup $addonComposeUiActionMarkup)
  {
    $this->addonComposeUiActionMarkup = $addonComposeUiActionMarkup;
  }
  /**
   * @return AddonComposeUiActionMarkup
   */
  public function getAddonComposeUiActionMarkup()
  {
    return $this->addonComposeUiActionMarkup;
  }
  /**
   * @param OpenCreatedDraftActionMarkup
   */
  public function setOpenCreatedDraftActionMarkup(OpenCreatedDraftActionMarkup $openCreatedDraftActionMarkup)
  {
    $this->openCreatedDraftActionMarkup = $openCreatedDraftActionMarkup;
  }
  /**
   * @return OpenCreatedDraftActionMarkup
   */
  public function getOpenCreatedDraftActionMarkup()
  {
    return $this->openCreatedDraftActionMarkup;
  }
  /**
   * @param TaskActionMarkup
   */
  public function setTaskAction(TaskActionMarkup $taskAction)
  {
    $this->taskAction = $taskAction;
  }
  /**
   * @return TaskActionMarkup
   */
  public function getTaskAction()
  {
    return $this->taskAction;
  }
  /**
   * @param UpdateDraftActionMarkup
   */
  public function setUpdateDraftActionMarkup(UpdateDraftActionMarkup $updateDraftActionMarkup)
  {
    $this->updateDraftActionMarkup = $updateDraftActionMarkup;
  }
  /**
   * @return UpdateDraftActionMarkup
   */
  public function getUpdateDraftActionMarkup()
  {
    return $this->updateDraftActionMarkup;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GmailClientActionMarkup::class, 'Google_Service_CloudSearch_GmailClientActionMarkup');
