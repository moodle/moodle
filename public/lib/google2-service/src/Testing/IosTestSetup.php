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

class IosTestSetup extends \Google\Collection
{
  protected $collection_key = 'pushFiles';
  protected $additionalIpasType = FileReference::class;
  protected $additionalIpasDataType = 'array';
  /**
   * The network traffic profile used for running the test. Available network
   * profiles can be queried by using the NETWORK_CONFIGURATION environment type
   * when calling TestEnvironmentDiscoveryService.GetTestEnvironmentCatalog.
   *
   * @var string
   */
  public $networkProfile;
  protected $pullDirectoriesType = IosDeviceFile::class;
  protected $pullDirectoriesDataType = 'array';
  protected $pushFilesType = IosDeviceFile::class;
  protected $pushFilesDataType = 'array';

  /**
   * iOS apps to install in addition to those being directly tested.
   *
   * @param FileReference[] $additionalIpas
   */
  public function setAdditionalIpas($additionalIpas)
  {
    $this->additionalIpas = $additionalIpas;
  }
  /**
   * @return FileReference[]
   */
  public function getAdditionalIpas()
  {
    return $this->additionalIpas;
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
   * List of directories on the device to upload to Cloud Storage at the end of
   * the test. Directories should either be in a shared directory (such as
   * /private/var/mobile/Media) or within an accessible directory inside the
   * app's filesystem (such as /Documents) by specifying the bundle ID.
   *
   * @param IosDeviceFile[] $pullDirectories
   */
  public function setPullDirectories($pullDirectories)
  {
    $this->pullDirectories = $pullDirectories;
  }
  /**
   * @return IosDeviceFile[]
   */
  public function getPullDirectories()
  {
    return $this->pullDirectories;
  }
  /**
   * List of files to push to the device before starting the test.
   *
   * @param IosDeviceFile[] $pushFiles
   */
  public function setPushFiles($pushFiles)
  {
    $this->pushFiles = $pushFiles;
  }
  /**
   * @return IosDeviceFile[]
   */
  public function getPushFiles()
  {
    return $this->pushFiles;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IosTestSetup::class, 'Google_Service_Testing_IosTestSetup');
