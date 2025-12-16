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

class GoogleChromeManagementVersionsV1ReportingData extends \Google\Collection
{
  protected $collection_key = 'policyData';
  /**
   * Output only. Executable path of the installed Chrome browser. A valid path
   * is included only in affiliated profiles.
   *
   * @var string
   */
  public $browserExecutablePath;
  protected $extensionDataType = GoogleChromeManagementVersionsV1ReportingDataExtensionData::class;
  protected $extensionDataDataType = 'array';
  protected $extensionPolicyDataType = GoogleChromeManagementVersionsV1ReportingDataExtensionPolicyData::class;
  protected $extensionPolicyDataDataType = 'array';
  /**
   * Output only. Updated version of a browser, if it is different from the
   * active browser version.
   *
   * @var string
   */
  public $installedBrowserVersion;
  protected $policyDataType = GoogleChromeManagementVersionsV1ReportingDataPolicyData::class;
  protected $policyDataDataType = 'array';
  /**
   * Output only. Path of the profile. A valid path is included only in
   * affiliated profiles.
   *
   * @var string
   */
  public $profilePath;

  /**
   * Output only. Executable path of the installed Chrome browser. A valid path
   * is included only in affiliated profiles.
   *
   * @param string $browserExecutablePath
   */
  public function setBrowserExecutablePath($browserExecutablePath)
  {
    $this->browserExecutablePath = $browserExecutablePath;
  }
  /**
   * @return string
   */
  public function getBrowserExecutablePath()
  {
    return $this->browserExecutablePath;
  }
  /**
   * Output only. Information of the extensions installed on the profile.
   *
   * @param GoogleChromeManagementVersionsV1ReportingDataExtensionData[] $extensionData
   */
  public function setExtensionData($extensionData)
  {
    $this->extensionData = $extensionData;
  }
  /**
   * @return GoogleChromeManagementVersionsV1ReportingDataExtensionData[]
   */
  public function getExtensionData()
  {
    return $this->extensionData;
  }
  /**
   * Output only. Information of the policies applied on the extensions.
   *
   * @param GoogleChromeManagementVersionsV1ReportingDataExtensionPolicyData[] $extensionPolicyData
   */
  public function setExtensionPolicyData($extensionPolicyData)
  {
    $this->extensionPolicyData = $extensionPolicyData;
  }
  /**
   * @return GoogleChromeManagementVersionsV1ReportingDataExtensionPolicyData[]
   */
  public function getExtensionPolicyData()
  {
    return $this->extensionPolicyData;
  }
  /**
   * Output only. Updated version of a browser, if it is different from the
   * active browser version.
   *
   * @param string $installedBrowserVersion
   */
  public function setInstalledBrowserVersion($installedBrowserVersion)
  {
    $this->installedBrowserVersion = $installedBrowserVersion;
  }
  /**
   * @return string
   */
  public function getInstalledBrowserVersion()
  {
    return $this->installedBrowserVersion;
  }
  /**
   * Output only. Information of the policies applied on the profile.
   *
   * @param GoogleChromeManagementVersionsV1ReportingDataPolicyData[] $policyData
   */
  public function setPolicyData($policyData)
  {
    $this->policyData = $policyData;
  }
  /**
   * @return GoogleChromeManagementVersionsV1ReportingDataPolicyData[]
   */
  public function getPolicyData()
  {
    return $this->policyData;
  }
  /**
   * Output only. Path of the profile. A valid path is included only in
   * affiliated profiles.
   *
   * @param string $profilePath
   */
  public function setProfilePath($profilePath)
  {
    $this->profilePath = $profilePath;
  }
  /**
   * @return string
   */
  public function getProfilePath()
  {
    return $this->profilePath;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementVersionsV1ReportingData::class, 'Google_Service_ChromeManagement_GoogleChromeManagementVersionsV1ReportingData');
