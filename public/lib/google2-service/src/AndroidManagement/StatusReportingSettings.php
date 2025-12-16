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

namespace Google\Service\AndroidManagement;

class StatusReportingSettings extends \Google\Model
{
  protected $applicationReportingSettingsType = ApplicationReportingSettings::class;
  protected $applicationReportingSettingsDataType = '';
  /**
   * Whether app reports are enabled.
   *
   * @var bool
   */
  public $applicationReportsEnabled;
  /**
   * Whether Common Criteria Mode reporting is enabled. This is supported only
   * on company-owned devices.
   *
   * @var bool
   */
  public $commonCriteriaModeEnabled;
  /**
   * Optional. Whether defaultApplicationInfo reporting is enabled.
   *
   * @var bool
   */
  public $defaultApplicationInfoReportingEnabled;
  /**
   * Whether device settings reporting is enabled.
   *
   * @var bool
   */
  public $deviceSettingsEnabled;
  /**
   * Whether displays reporting is enabled. Report data is not available for
   * personally owned devices with work profiles.
   *
   * @var bool
   */
  public $displayInfoEnabled;
  /**
   * Whether hardware status reporting is enabled. Report data is not available
   * for personally owned devices with work profiles.
   *
   * @var bool
   */
  public $hardwareStatusEnabled;
  /**
   * Whether memory event reporting is enabled.
   *
   * @var bool
   */
  public $memoryInfoEnabled;
  /**
   * Whether network info reporting is enabled.
   *
   * @var bool
   */
  public $networkInfoEnabled;
  /**
   * Whether power management event reporting is enabled. Report data is not
   * available for personally owned devices with work profiles.
   *
   * @var bool
   */
  public $powerManagementEventsEnabled;
  /**
   * Whether software info reporting is enabled.
   *
   * @var bool
   */
  public $softwareInfoEnabled;
  /**
   * Whether system properties reporting is enabled.
   *
   * @var bool
   */
  public $systemPropertiesEnabled;

  /**
   * Application reporting settings. Only applicable if
   * application_reports_enabled is true.
   *
   * @param ApplicationReportingSettings $applicationReportingSettings
   */
  public function setApplicationReportingSettings(ApplicationReportingSettings $applicationReportingSettings)
  {
    $this->applicationReportingSettings = $applicationReportingSettings;
  }
  /**
   * @return ApplicationReportingSettings
   */
  public function getApplicationReportingSettings()
  {
    return $this->applicationReportingSettings;
  }
  /**
   * Whether app reports are enabled.
   *
   * @param bool $applicationReportsEnabled
   */
  public function setApplicationReportsEnabled($applicationReportsEnabled)
  {
    $this->applicationReportsEnabled = $applicationReportsEnabled;
  }
  /**
   * @return bool
   */
  public function getApplicationReportsEnabled()
  {
    return $this->applicationReportsEnabled;
  }
  /**
   * Whether Common Criteria Mode reporting is enabled. This is supported only
   * on company-owned devices.
   *
   * @param bool $commonCriteriaModeEnabled
   */
  public function setCommonCriteriaModeEnabled($commonCriteriaModeEnabled)
  {
    $this->commonCriteriaModeEnabled = $commonCriteriaModeEnabled;
  }
  /**
   * @return bool
   */
  public function getCommonCriteriaModeEnabled()
  {
    return $this->commonCriteriaModeEnabled;
  }
  /**
   * Optional. Whether defaultApplicationInfo reporting is enabled.
   *
   * @param bool $defaultApplicationInfoReportingEnabled
   */
  public function setDefaultApplicationInfoReportingEnabled($defaultApplicationInfoReportingEnabled)
  {
    $this->defaultApplicationInfoReportingEnabled = $defaultApplicationInfoReportingEnabled;
  }
  /**
   * @return bool
   */
  public function getDefaultApplicationInfoReportingEnabled()
  {
    return $this->defaultApplicationInfoReportingEnabled;
  }
  /**
   * Whether device settings reporting is enabled.
   *
   * @param bool $deviceSettingsEnabled
   */
  public function setDeviceSettingsEnabled($deviceSettingsEnabled)
  {
    $this->deviceSettingsEnabled = $deviceSettingsEnabled;
  }
  /**
   * @return bool
   */
  public function getDeviceSettingsEnabled()
  {
    return $this->deviceSettingsEnabled;
  }
  /**
   * Whether displays reporting is enabled. Report data is not available for
   * personally owned devices with work profiles.
   *
   * @param bool $displayInfoEnabled
   */
  public function setDisplayInfoEnabled($displayInfoEnabled)
  {
    $this->displayInfoEnabled = $displayInfoEnabled;
  }
  /**
   * @return bool
   */
  public function getDisplayInfoEnabled()
  {
    return $this->displayInfoEnabled;
  }
  /**
   * Whether hardware status reporting is enabled. Report data is not available
   * for personally owned devices with work profiles.
   *
   * @param bool $hardwareStatusEnabled
   */
  public function setHardwareStatusEnabled($hardwareStatusEnabled)
  {
    $this->hardwareStatusEnabled = $hardwareStatusEnabled;
  }
  /**
   * @return bool
   */
  public function getHardwareStatusEnabled()
  {
    return $this->hardwareStatusEnabled;
  }
  /**
   * Whether memory event reporting is enabled.
   *
   * @param bool $memoryInfoEnabled
   */
  public function setMemoryInfoEnabled($memoryInfoEnabled)
  {
    $this->memoryInfoEnabled = $memoryInfoEnabled;
  }
  /**
   * @return bool
   */
  public function getMemoryInfoEnabled()
  {
    return $this->memoryInfoEnabled;
  }
  /**
   * Whether network info reporting is enabled.
   *
   * @param bool $networkInfoEnabled
   */
  public function setNetworkInfoEnabled($networkInfoEnabled)
  {
    $this->networkInfoEnabled = $networkInfoEnabled;
  }
  /**
   * @return bool
   */
  public function getNetworkInfoEnabled()
  {
    return $this->networkInfoEnabled;
  }
  /**
   * Whether power management event reporting is enabled. Report data is not
   * available for personally owned devices with work profiles.
   *
   * @param bool $powerManagementEventsEnabled
   */
  public function setPowerManagementEventsEnabled($powerManagementEventsEnabled)
  {
    $this->powerManagementEventsEnabled = $powerManagementEventsEnabled;
  }
  /**
   * @return bool
   */
  public function getPowerManagementEventsEnabled()
  {
    return $this->powerManagementEventsEnabled;
  }
  /**
   * Whether software info reporting is enabled.
   *
   * @param bool $softwareInfoEnabled
   */
  public function setSoftwareInfoEnabled($softwareInfoEnabled)
  {
    $this->softwareInfoEnabled = $softwareInfoEnabled;
  }
  /**
   * @return bool
   */
  public function getSoftwareInfoEnabled()
  {
    return $this->softwareInfoEnabled;
  }
  /**
   * Whether system properties reporting is enabled.
   *
   * @param bool $systemPropertiesEnabled
   */
  public function setSystemPropertiesEnabled($systemPropertiesEnabled)
  {
    $this->systemPropertiesEnabled = $systemPropertiesEnabled;
  }
  /**
   * @return bool
   */
  public function getSystemPropertiesEnabled()
  {
    return $this->systemPropertiesEnabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StatusReportingSettings::class, 'Google_Service_AndroidManagement_StatusReportingSettings');
