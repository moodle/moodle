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

namespace Google\Service\DataManager;

class RemoveAudienceMembersStatus extends \Google\Model
{
  protected $mobileDataRemovalStatusType = RemoveMobileDataStatus::class;
  protected $mobileDataRemovalStatusDataType = '';
  protected $pairDataRemovalStatusType = RemovePairDataStatus::class;
  protected $pairDataRemovalStatusDataType = '';
  protected $userDataRemovalStatusType = RemoveUserDataStatus::class;
  protected $userDataRemovalStatusDataType = '';

  /**
   * The status of the mobile data removal from the destination.
   *
   * @param RemoveMobileDataStatus $mobileDataRemovalStatus
   */
  public function setMobileDataRemovalStatus(RemoveMobileDataStatus $mobileDataRemovalStatus)
  {
    $this->mobileDataRemovalStatus = $mobileDataRemovalStatus;
  }
  /**
   * @return RemoveMobileDataStatus
   */
  public function getMobileDataRemovalStatus()
  {
    return $this->mobileDataRemovalStatus;
  }
  /**
   * The status of the pair data removal from the destination.
   *
   * @param RemovePairDataStatus $pairDataRemovalStatus
   */
  public function setPairDataRemovalStatus(RemovePairDataStatus $pairDataRemovalStatus)
  {
    $this->pairDataRemovalStatus = $pairDataRemovalStatus;
  }
  /**
   * @return RemovePairDataStatus
   */
  public function getPairDataRemovalStatus()
  {
    return $this->pairDataRemovalStatus;
  }
  /**
   * The status of the user data removal from the destination.
   *
   * @param RemoveUserDataStatus $userDataRemovalStatus
   */
  public function setUserDataRemovalStatus(RemoveUserDataStatus $userDataRemovalStatus)
  {
    $this->userDataRemovalStatus = $userDataRemovalStatus;
  }
  /**
   * @return RemoveUserDataStatus
   */
  public function getUserDataRemovalStatus()
  {
    return $this->userDataRemovalStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RemoveAudienceMembersStatus::class, 'Google_Service_DataManager_RemoveAudienceMembersStatus');
