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

class PackageNote extends \Google\Collection
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
  protected $collection_key = 'distribution';
  /**
   * The CPU architecture for which packages in this distribution channel were
   * built. Architecture will be blank for language packages.
   *
   * @var string
   */
  public $architecture;
  /**
   * The cpe_uri in [CPE format](https://cpe.mitre.org/specification/) denoting
   * the package manager version distributing a package. The cpe_uri will be
   * blank for language packages.
   *
   * @var string
   */
  public $cpeUri;
  /**
   * The description of this package.
   *
   * @var string
   */
  public $description;
  protected $digestType = Digest::class;
  protected $digestDataType = 'array';
  protected $distributionType = Distribution::class;
  protected $distributionDataType = 'array';
  protected $licenseType = License::class;
  protected $licenseDataType = '';
  /**
   * A freeform text denoting the maintainer of this package.
   *
   * @var string
   */
  public $maintainer;
  /**
   * Required. Immutable. The name of the package.
   *
   * @var string
   */
  public $name;
  /**
   * The type of package; whether native or non native (e.g., ruby gems, node.js
   * packages, etc.).
   *
   * @var string
   */
  public $packageType;
  /**
   * The homepage for this package.
   *
   * @var string
   */
  public $url;
  protected $versionType = Version::class;
  protected $versionDataType = '';

  /**
   * The CPU architecture for which packages in this distribution channel were
   * built. Architecture will be blank for language packages.
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
   * The cpe_uri in [CPE format](https://cpe.mitre.org/specification/) denoting
   * the package manager version distributing a package. The cpe_uri will be
   * blank for language packages.
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
   * The description of this package.
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
   * Hash value, typically a file digest, that allows unique identification a
   * specific package.
   *
   * @param Digest[] $digest
   */
  public function setDigest($digest)
  {
    $this->digest = $digest;
  }
  /**
   * @return Digest[]
   */
  public function getDigest()
  {
    return $this->digest;
  }
  /**
   * Deprecated. The various channels by which a package is distributed.
   *
   * @param Distribution[] $distribution
   */
  public function setDistribution($distribution)
  {
    $this->distribution = $distribution;
  }
  /**
   * @return Distribution[]
   */
  public function getDistribution()
  {
    return $this->distribution;
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
   * A freeform text denoting the maintainer of this package.
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
   * Required. Immutable. The name of the package.
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
   * The type of package; whether native or non native (e.g., ruby gems, node.js
   * packages, etc.).
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
   * The homepage for this package.
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
  /**
   * The version of the package.
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
class_alias(PackageNote::class, 'Google_Service_ContainerAnalysis_PackageNote');
