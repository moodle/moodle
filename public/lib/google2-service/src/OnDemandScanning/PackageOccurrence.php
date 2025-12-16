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

namespace Google\Service\OnDemandScanning;

class PackageOccurrence extends \Google\Collection
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
  protected $collection_key = 'location';
  /**
   * Output only. The CPU architecture for which packages in this distribution
   * channel were built. Architecture will be blank for language packages.
   *
   * @var string
   */
  public $architecture;
  /**
   * Output only. The cpe_uri in [CPE
   * format](https://cpe.mitre.org/specification/) denoting the package manager
   * version distributing a package. The cpe_uri will be blank for language
   * packages.
   *
   * @var string
   */
  public $cpeUri;
  protected $licenseType = License::class;
  protected $licenseDataType = '';
  protected $locationType = Location::class;
  protected $locationDataType = 'array';
  /**
   * Required. Output only. The name of the installed package.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The type of package; whether native or non native (e.g., ruby
   * gems, node.js packages, etc.).
   *
   * @var string
   */
  public $packageType;
  protected $versionType = Version::class;
  protected $versionDataType = '';

  /**
   * Output only. The CPU architecture for which packages in this distribution
   * channel were built. Architecture will be blank for language packages.
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
   * Output only. The cpe_uri in [CPE
   * format](https://cpe.mitre.org/specification/) denoting the package manager
   * version distributing a package. The cpe_uri will be blank for language
   * packages.
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
   * Licenses that have been declared by the authors of the package.
   *
   * @param License $license
   */
  public function setLicense(License $license)
  {
    $this->license = $license;
  }
  /**
   * @return License
   */
  public function getLicense()
  {
    return $this->license;
  }
  /**
   * All of the places within the filesystem versions of this package have been
   * found.
   *
   * @param Location[] $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return Location[]
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Required. Output only. The name of the installed package.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. The type of package; whether native or non native (e.g., ruby
   * gems, node.js packages, etc.).
   *
   * @param string $packageType
   */
  public function setPackageType($packageType)
  {
    $this->packageType = $packageType;
  }
  /**
   * @return string
   */
  public function getPackageType()
  {
    return $this->packageType;
  }
  /**
   * Output only. The version of the package.
   *
   * @param Version $version
   */
  public function setVersion(Version $version)
  {
    $this->version = $version;
  }
  /**
   * @return Version
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PackageOccurrence::class, 'Google_Service_OnDemandScanning_PackageOccurrence');
