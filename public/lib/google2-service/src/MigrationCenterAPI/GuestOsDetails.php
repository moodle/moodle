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

namespace Google\Service\MigrationCenterAPI;

class GuestOsDetails extends \Google\Model
{
  public const FAMILY_OS_FAMILY_UNKNOWN = 'OS_FAMILY_UNKNOWN';
  /**
   * Microsoft Windows Server and Desktop.
   */
  public const FAMILY_OS_FAMILY_WINDOWS = 'OS_FAMILY_WINDOWS';
  /**
   * Various Linux flavors.
   */
  public const FAMILY_OS_FAMILY_LINUX = 'OS_FAMILY_LINUX';
  /**
   * Non-Linux Unix flavors.
   */
  public const FAMILY_OS_FAMILY_UNIX = 'OS_FAMILY_UNIX';
  protected $configType = GuestConfigDetails::class;
  protected $configDataType = '';
  /**
   * What family the OS belong to, if known.
   *
   * @var string
   */
  public $family;
  /**
   * The name of the operating system.
   *
   * @var string
   */
  public $osName;
  protected $runtimeType = GuestRuntimeDetails::class;
  protected $runtimeDataType = '';
  /**
   * The version of the operating system.
   *
   * @var string
   */
  public $version;

  /**
   * OS and app configuration.
   *
   * @param GuestConfigDetails $config
   */
  public function setConfig(GuestConfigDetails $config)
  {
    $this->config = $config;
  }
  /**
   * @return GuestConfigDetails
   */
  public function getConfig()
  {
    return $this->config;
  }
  /**
   * What family the OS belong to, if known.
   *
   * Accepted values: OS_FAMILY_UNKNOWN, OS_FAMILY_WINDOWS, OS_FAMILY_LINUX,
   * OS_FAMILY_UNIX
   *
   * @param self::FAMILY_* $family
   */
  public function setFamily($family)
  {
    $this->family = $family;
  }
  /**
   * @return self::FAMILY_*
   */
  public function getFamily()
  {
    return $this->family;
  }
  /**
   * The name of the operating system.
   *
   * @param string $osName
   */
  public function setOsName($osName)
  {
    $this->osName = $osName;
  }
  /**
   * @return string
   */
  public function getOsName()
  {
    return $this->osName;
  }
  /**
   * Runtime information.
   *
   * @param GuestRuntimeDetails $runtime
   */
  public function setRuntime(GuestRuntimeDetails $runtime)
  {
    $this->runtime = $runtime;
  }
  /**
   * @return GuestRuntimeDetails
   */
  public function getRuntime()
  {
    return $this->runtime;
  }
  /**
   * The version of the operating system.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GuestOsDetails::class, 'Google_Service_MigrationCenterAPI_GuestOsDetails');
