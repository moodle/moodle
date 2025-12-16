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

namespace Google\Service\SecurityCommandCenter;

class SecurityBulletin extends \Google\Model
{
  /**
   * ID of the bulletin corresponding to the vulnerability.
   *
   * @var string
   */
  public $bulletinId;
  /**
   * Submission time of this Security Bulletin.
   *
   * @var string
   */
  public $submissionTime;
  /**
   * This represents a version that the cluster receiving this notification
   * should be upgraded to, based on its current version. For example, 1.15.0
   *
   * @var string
   */
  public $suggestedUpgradeVersion;

  /**
   * ID of the bulletin corresponding to the vulnerability.
   *
   * @param string $bulletinId
   */
  public function setBulletinId($bulletinId)
  {
    $this->bulletinId = $bulletinId;
  }
  /**
   * @return string
   */
  public function getBulletinId()
  {
    return $this->bulletinId;
  }
  /**
   * Submission time of this Security Bulletin.
   *
   * @param string $submissionTime
   */
  public function setSubmissionTime($submissionTime)
  {
    $this->submissionTime = $submissionTime;
  }
  /**
   * @return string
   */
  public function getSubmissionTime()
  {
    return $this->submissionTime;
  }
  /**
   * This represents a version that the cluster receiving this notification
   * should be upgraded to, based on its current version. For example, 1.15.0
   *
   * @param string $suggestedUpgradeVersion
   */
  public function setSuggestedUpgradeVersion($suggestedUpgradeVersion)
  {
    $this->suggestedUpgradeVersion = $suggestedUpgradeVersion;
  }
  /**
   * @return string
   */
  public function getSuggestedUpgradeVersion()
  {
    return $this->suggestedUpgradeVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecurityBulletin::class, 'Google_Service_SecurityCommandCenter_SecurityBulletin');
