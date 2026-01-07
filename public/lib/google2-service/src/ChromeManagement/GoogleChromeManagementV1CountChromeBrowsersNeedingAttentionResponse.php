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

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementV1CountChromeBrowsersNeedingAttentionResponse extends \Google\Model
{
  /**
   * Number of browsers that haven’t had any recent activity
   *
   * @var string
   */
  public $noRecentActivityCount;
  /**
   * Number of browsers that are pending an OS update
   *
   * @var string
   */
  public $pendingBrowserUpdateCount;
  /**
   * Number of browsers that have been recently enrolled
   *
   * @var string
   */
  public $recentlyEnrolledCount;

  /**
   * Number of browsers that haven’t had any recent activity
   *
   * @param string $noRecentActivityCount
   */
  public function setNoRecentActivityCount($noRecentActivityCount)
  {
    $this->noRecentActivityCount = $noRecentActivityCount;
  }
  /**
   * @return string
   */
  public function getNoRecentActivityCount()
  {
    return $this->noRecentActivityCount;
  }
  /**
   * Number of browsers that are pending an OS update
   *
   * @param string $pendingBrowserUpdateCount
   */
  public function setPendingBrowserUpdateCount($pendingBrowserUpdateCount)
  {
    $this->pendingBrowserUpdateCount = $pendingBrowserUpdateCount;
  }
  /**
   * @return string
   */
  public function getPendingBrowserUpdateCount()
  {
    return $this->pendingBrowserUpdateCount;
  }
  /**
   * Number of browsers that have been recently enrolled
   *
   * @param string $recentlyEnrolledCount
   */
  public function setRecentlyEnrolledCount($recentlyEnrolledCount)
  {
    $this->recentlyEnrolledCount = $recentlyEnrolledCount;
  }
  /**
   * @return string
   */
  public function getRecentlyEnrolledCount()
  {
    return $this->recentlyEnrolledCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1CountChromeBrowsersNeedingAttentionResponse::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1CountChromeBrowsersNeedingAttentionResponse');
