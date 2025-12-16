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

namespace Google\Service\Testing;

class TestSetup extends \Google\Collection
{
  protected $collection_key = 'initialSetupApks';
  protected $accountType = Account::class;
  protected $accountDataType = '';
  protected $additionalApksType = Apk::class;
  protected $additionalApksDataType = 'array';
  /**
   * List of directories on the device to upload to GCS at the end of the test;
   * they must be absolute paths under /sdcard, /storage or /data/local/tmp.
   * Path names are restricted to characters a-z A-Z 0-9 _ - . + and / Note: The
   * paths /sdcard and /data will be made available and treated as implicit path
   * substitutions. E.g. if /sdcard on a particular device does not map to
   * external storage, the system will replace it with the external storage path
   * prefix for that device.
   *
   * @var string[]
   */
  public $directoriesToPull;
  /**
   * Whether to prevent all runtime permissions to be granted at app install
   *
   * @var bool
   */
  public $dontAutograntPermissions;
  protected $environmentVariablesType = EnvironmentVariable::class;
  protected $environmentVariablesDataType = 'array';
  protected $filesToPushType = DeviceFile::class;
  protected $filesToPushDataType = 'array';
  protected $initialSetupApksType = Apk::class;
  protected $initialSetupApksDataType = 'array';
  /**
   * The network traffic profile used for running the test. Available network
   * profiles can be queried by using the NETWORK_CONFIGURATION environment type
   * when calling TestEnvironmentDiscoveryService.GetTestEnvironmentCatalog.
   *
   * @var string
   */
  public $networkProfile;
  protected $systraceType = SystraceSetup::class;
  protected $systraceDataType = '';

  /**
   * The device will be logged in on this account for the duration of the test.
   *
   * @param Account $account
   */
  public function setAccount(Account $account)
  {
    $this->account = $account;
  }
  /**
   * @return Account
   */
  public function getAccount()
  {
    return $this->account;
  }
  /**
   * APKs to install in addition to those being directly tested. These will be
   * installed after the app under test. Limited to a combined total of 100
   * initial setup and additional files.
   *
   * @param Apk[] $additionalApks
   */
  public function setAdditionalApks($additionalApks)
  {
    $this->additionalApks = $additionalApks;
  }
  /**
   * @return Apk[]
   */
  public function getAdditionalApks()
  {
    return $this->additionalApks;
  }
  /**
   * List of directories on the device to upload to GCS at the end of the test;
   * they must be absolute paths under /sdcard, /storage or /data/local/tmp.
   * Path names are restricted to characters a-z A-Z 0-9 _ - . + and / Note: The
   * paths /sdcard and /data will be made available and treated as implicit path
   * substitutions. E.g. if /sdcard on a particular device does not map to
   * external storage, the system will replace it with the external storage path
   * prefix for that device.
   *
   * @param string[] $directoriesToPull
   */
  public function setDirectoriesToPull($directoriesToPull)
  {
    $this->directoriesToPull = $directoriesToPull;
  }
  /**
   * @return string[]
   */
  public function getDirectoriesToPull()
  {
    return $this->directoriesToPull;
  }
  /**
   * Whether to prevent all runtime permissions to be granted at app install
   *
   * @param bool $dontAutograntPermissions
   */
  public function setDontAutograntPermissions($dontAutograntPermissions)
  {
    $this->dontAutograntPermissions = $dontAutograntPermissions;
  }
  /**
   * @return bool
   */
  public function getDontAutograntPermissions()
  {
    return $this->dontAutograntPermissions;
  }
  /**
   * Environment variables to set for the test (only applicable for
   * instrumentation tests).
   *
   * @param EnvironmentVariable[] $environmentVariables
   */
  public function setEnvironmentVariables($environmentVariables)
  {
    $this->environmentVariables = $environmentVariables;
  }
  /**
   * @return EnvironmentVariable[]
   */
  public function getEnvironmentVariables()
  {
    return $this->environmentVariables;
  }
  /**
   * List of files to push to the device before starting the test.
   *
   * @param DeviceFile[] $filesToPush
   */
  public function setFilesToPush($filesToPush)
  {
    $this->filesToPush = $filesToPush;
  }
  /**
   * @return DeviceFile[]
   */
  public function getFilesToPush()
  {
    return $this->filesToPush;
  }
  /**
   * Optional. Initial setup APKs to install before the app under test is
   * installed. Limited to a combined total of 100 initial setup and additional
   * files.
   *
   * @param Apk[] $initialSetupApks
   */
  public function setInitialSetupApks($initialSetupApks)
  {
    $this->initialSetupApks = $initialSetupApks;
  }
  /**
   * @return Apk[]
   */
  public function getInitialSetupApks()
  {
    return $this->initialSetupApks;
  }
  /**
   * The network traffic profile used for running the test. Available network
   * profiles can be queried by using the NETWORK_CONFIGURATION environment type
   * when calling TestEnvironmentDiscoveryService.GetTestEnvironmentCatalog.
   *
   * @param string $networkProfile
   */
  public function setNetworkProfile($networkProfile)
  {
    $this->networkProfile = $networkProfile;
  }
  /**
   * @return string
   */
  public function getNetworkProfile()
  {
    return $this->networkProfile;
  }
  /**
   * Systrace configuration for the run. Deprecated: Systrace used Python 2
   * which was sunsetted on 2020-01-01. Systrace is no longer supported in the
   * Cloud Testing API, and no Systrace file will be provided in the results.
   *
   * @deprecated
   * @param SystraceSetup $systrace
   */
  public function setSystrace(SystraceSetup $systrace)
  {
    $this->systrace = $systrace;
  }
  /**
   * @deprecated
   * @return SystraceSetup
   */
  public function getSystrace()
  {
    return $this->systrace;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TestSetup::class, 'Google_Service_Testing_TestSetup');
