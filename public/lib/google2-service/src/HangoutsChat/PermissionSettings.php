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

class PermissionSettings extends \Google\Model
{
  protected $manageAppsType = PermissionSetting::class;
  protected $manageAppsDataType = '';
  protected $manageMembersAndGroupsType = PermissionSetting::class;
  protected $manageMembersAndGroupsDataType = '';
  protected $manageWebhooksType = PermissionSetting::class;
  protected $manageWebhooksDataType = '';
  protected $modifySpaceDetailsType = PermissionSetting::class;
  protected $modifySpaceDetailsDataType = '';
  protected $postMessagesType = PermissionSetting::class;
  protected $postMessagesDataType = '';
  protected $replyMessagesType = PermissionSetting::class;
  protected $replyMessagesDataType = '';
  protected $toggleHistoryType = PermissionSetting::class;
  protected $toggleHistoryDataType = '';
  protected $useAtMentionAllType = PermissionSetting::class;
  protected $useAtMentionAllDataType = '';

  /**
   * Optional. Setting for managing apps in a space.
   *
   * @param PermissionSetting $manageApps
   */
  public function setManageApps(PermissionSetting $manageApps)
  {
    $this->manageApps = $manageApps;
  }
  /**
   * @return PermissionSetting
   */
  public function getManageApps()
  {
    return $this->manageApps;
  }
  /**
   * Optional. Setting for managing members and groups in a space.
   *
   * @param PermissionSetting $manageMembersAndGroups
   */
  public function setManageMembersAndGroups(PermissionSetting $manageMembersAndGroups)
  {
    $this->manageMembersAndGroups = $manageMembersAndGroups;
  }
  /**
   * @return PermissionSetting
   */
  public function getManageMembersAndGroups()
  {
    return $this->manageMembersAndGroups;
  }
  /**
   * Optional. Setting for managing webhooks in a space.
   *
   * @param PermissionSetting $manageWebhooks
   */
  public function setManageWebhooks(PermissionSetting $manageWebhooks)
  {
    $this->manageWebhooks = $manageWebhooks;
  }
  /**
   * @return PermissionSetting
   */
  public function getManageWebhooks()
  {
    return $this->manageWebhooks;
  }
  /**
   * Optional. Setting for updating space name, avatar, description and
   * guidelines.
   *
   * @param PermissionSetting $modifySpaceDetails
   */
  public function setModifySpaceDetails(PermissionSetting $modifySpaceDetails)
  {
    $this->modifySpaceDetails = $modifySpaceDetails;
  }
  /**
   * @return PermissionSetting
   */
  public function getModifySpaceDetails()
  {
    return $this->modifySpaceDetails;
  }
  /**
   * Output only. Setting for posting messages in a space.
   *
   * @param PermissionSetting $postMessages
   */
  public function setPostMessages(PermissionSetting $postMessages)
  {
    $this->postMessages = $postMessages;
  }
  /**
   * @return PermissionSetting
   */
  public function getPostMessages()
  {
    return $this->postMessages;
  }
  /**
   * Optional. Setting for replying to messages in a space.
   *
   * @param PermissionSetting $replyMessages
   */
  public function setReplyMessages(PermissionSetting $replyMessages)
  {
    $this->replyMessages = $replyMessages;
  }
  /**
   * @return PermissionSetting
   */
  public function getReplyMessages()
  {
    return $this->replyMessages;
  }
  /**
   * Optional. Setting for toggling space history on and off.
   *
   * @param PermissionSetting $toggleHistory
   */
  public function setToggleHistory(PermissionSetting $toggleHistory)
  {
    $this->toggleHistory = $toggleHistory;
  }
  /**
   * @return PermissionSetting
   */
  public function getToggleHistory()
  {
    return $this->toggleHistory;
  }
  /**
   * Optional. Setting for using @all in a space.
   *
   * @param PermissionSetting $useAtMentionAll
   */
  public function setUseAtMentionAll(PermissionSetting $useAtMentionAll)
  {
    $this->useAtMentionAll = $useAtMentionAll;
  }
  /**
   * @return PermissionSetting
   */
  public function getUseAtMentionAll()
  {
    return $this->useAtMentionAll;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PermissionSettings::class, 'Google_Service_HangoutsChat_PermissionSettings');
