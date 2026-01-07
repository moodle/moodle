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

namespace Google\Service\CloudAsset;

class OsInfo extends \Google\Model
{
  /**
   * The system architecture of the operating system.
   *
   * @var string
   */
  public $architecture;
  /**
   * The VM hostname.
   *
   * @var string
   */
  public $hostname;
  /**
   * The kernel release of the operating system.
   *
   * @var string
   */
  public $kernelRelease;
  /**
   * The kernel version of the operating system.
   *
   * @var string
   */
  public $kernelVersion;
  /**
   * The operating system long name. For example 'Debian GNU/Linux 9' or
   * 'Microsoft Window Server 2019 Datacenter'.
   *
   * @var string
   */
  public $longName;
  /**
   * The current version of the OS Config agent running on the VM.
   *
   * @var string
   */
  public $osconfigAgentVersion;
  /**
   * The operating system short name. For example, 'windows' or 'debian'.
   *
   * @var string
   */
  public $shortName;
  /**
   * The version of the operating system.
   *
   * @var string
   */
  public $version;

  /**
   * The system architecture of the operating system.
   *
   * @param string $architecture
   */
  public function setArchitecture($architecture)
  {
    $this->architecture = $architecture;
  }
  /**
   * @return string
   */
  public function getArchitecture()
  {
    return $this->architecture;
  }
  /**
   * The VM hostname.
   *
   * @param string $hostname
   */
  public function setHostname($hostname)
  {
    $this->hostname = $hostname;
  }
  /**
   * @return string
   */
  public function getHostname()
  {
    return $this->hostname;
  }
  /**
   * The kernel release of the operating system.
   *
   * @param string $kernelRelease
   */
  public function setKernelRelease($kernelRelease)
  {
    $this->kernelRelease = $kernelRelease;
  }
  /**
   * @return string
   */
  public function getKernelRelease()
  {
    return $this->kernelRelease;
  }
  /**
   * The kernel version of the operating system.
   *
   * @param string $kernelVersion
   */
  public function setKernelVersion($kernelVersion)
  {
    $this->kernelVersion = $kernelVersion;
  }
  /**
   * @return string
   */
  public function getKernelVersion()
  {
    return $this->kernelVersion;
  }
  /**
   * The operating system long name. For example 'Debian GNU/Linux 9' or
   * 'Microsoft Window Server 2019 Datacenter'.
   *
   * @param string $longName
   */
  public function setLongName($longName)
  {
    $this->longName = $longName;
  }
  /**
   * @return string
   */
  public function getLongName()
  {
    return $this->longName;
  }
  /**
   * The current version of the OS Config agent running on the VM.
   *
   * @param string $osconfigAgentVersion
   */
  public function setOsconfigAgentVersion($osconfigAgentVersion)
  {
    $this->osconfigAgentVersion = $osconfigAgentVersion;
  }
  /**
   * @return string
   */
  public function getOsconfigAgentVersion()
  {
    return $this->osconfigAgentVersion;
  }
  /**
   * The operating system short name. For example, 'windows' or 'debian'.
   *
   * @param string $shortName
   */
  public function setShortName($shortName)
  {
    $this->shortName = $shortName;
  }
  /**
   * @return string
   */
  public function getShortName()
  {
    return $this->shortName;
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
class_alias(OsInfo::class, 'Google_Service_CloudAsset_OsInfo');
