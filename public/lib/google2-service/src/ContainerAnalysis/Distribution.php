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

namespace Google\Service\ContainerAnalysis;

class Distribution extends \Google\Model
{
  /**
   * Unknown architecture.
   */
  public const ARCHITECTURE_ARCHITECTURE_UNSPECIFIED = 'ARCHITECTURE_UNSPECIFIED';
  /**
   * X86 architecture.
   */
  public const ARCHITECTURE_X86 = 'X86';
  /**
   * X64 architecture.
   */
  public const ARCHITECTURE_X64 = 'X64';
  /**
   * The CPU architecture for which packages in this distribution channel were
   * built.
   *
   * @var string
   */
  public $architecture;
  /**
   * Required. The cpe_uri in [CPE format](https://cpe.mitre.org/specification/)
   * denoting the package manager version distributing a package.
   *
   * @var string
   */
  public $cpeUri;
  /**
   * The distribution channel-specific description of this package.
   *
   * @var string
   */
  public $description;
  protected $latestVersionType = Version::class;
  protected $latestVersionDataType = '';
  /**
   * A freeform string denoting the maintainer of this package.
   *
   * @var string
   */
  public $maintainer;
  /**
   * The distribution channel-specific homepage for this package.
   *
   * @var string
   */
  public $url;

  /**
   * The CPU architecture for which packages in this distribution channel were
   * built.
   *
   * Accepted values: ARCHITECTURE_UNSPECIFIED, X86, X64
   *
   * @param self::ARCHITECTURE_* $architecture
   */
  public function setArchitecture($architecture)
  {
    $this->architecture = $architecture;
  }
  /**
   * @return self::ARCHITECTURE_*
   */
  public function getArchitecture()
  {
    return $this->architecture;
  }
  /**
   * Required. The cpe_uri in [CPE format](https://cpe.mitre.org/specification/)
   * denoting the package manager version distributing a package.
   *
   * @param string $cpeUri
   */
  public function setCpeUri($cpeUri)
  {
    $this->cpeUri = $cpeUri;
  }
  /**
   * @return string
   */
  public function getCpeUri()
  {
    return $this->cpeUri;
  }
  /**
   * The distribution channel-specific description of this package.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The latest available version of this package in this distribution channel.
   *
   * @param Version $latestVersion
   */
  public function setLatestVersion(Version $latestVersion)
  {
    $this->latestVersion = $latestVersion;
  }
  /**
   * @return Version
   */
  public function getLatestVersion()
  {
    return $this->latestVersion;
  }
  /**
   * A freeform string denoting the maintainer of this package.
   *
   * @param string $maintainer
   */
  public function setMaintainer($maintainer)
  {
    $this->maintainer = $maintainer;
  }
  /**
   * @return string
   */
  public function getMaintainer()
  {
    return $this->maintainer;
  }
  /**
   * The distribution channel-specific homepage for this package.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Distribution::class, 'Google_Service_ContainerAnalysis_Distribution');
