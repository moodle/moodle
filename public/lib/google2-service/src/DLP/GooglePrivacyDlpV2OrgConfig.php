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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2OrgConfig extends \Google\Model
{
  protected $locationType = GooglePrivacyDlpV2DiscoveryStartingLocation::class;
  protected $locationDataType = '';
  /**
   * The project that will run the scan. The DLP service account that exists
   * within this project must have access to all resources that are profiled,
   * and the DLP API must be enabled.
   *
   * @var string
   */
  public $projectId;

  /**
   * The data to scan: folder, org, or project
   *
   * @param GooglePrivacyDlpV2DiscoveryStartingLocation $location
   */
  public function setLocation(GooglePrivacyDlpV2DiscoveryStartingLocation $location)
  {
    $this->location = $location;
  }
  /**
   * @return GooglePrivacyDlpV2DiscoveryStartingLocation
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * The project that will run the scan. The DLP service account that exists
   * within this project must have access to all resources that are profiled,
   * and the DLP API must be enabled.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2OrgConfig::class, 'Google_Service_DLP_GooglePrivacyDlpV2OrgConfig');
