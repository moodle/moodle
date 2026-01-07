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

class PackageData extends \Google\Collection
{
  public const PACKAGE_TYPE_PACKAGE_TYPE_UNSPECIFIED = 'PACKAGE_TYPE_UNSPECIFIED';
  /**
   * Operating System
   */
  public const PACKAGE_TYPE_OS = 'OS';
  /**
   * Java packages from Maven.
   */
  public const PACKAGE_TYPE_MAVEN = 'MAVEN';
  /**
   * Go third-party packages.
   */
  public const PACKAGE_TYPE_GO = 'GO';
  /**
   * Go toolchain + standard library packages.
   */
  public const PACKAGE_TYPE_GO_STDLIB = 'GO_STDLIB';
  /**
   * Python packages.
   */
  public const PACKAGE_TYPE_PYPI = 'PYPI';
  /**
   * NPM packages.
   */
  public const PACKAGE_TYPE_NPM = 'NPM';
  /**
   * Nuget (C#/.NET) packages.
   */
  public const PACKAGE_TYPE_NUGET = 'NUGET';
  /**
   * Ruby packges (from RubyGems package manager).
   */
  public const PACKAGE_TYPE_RUBYGEMS = 'RUBYGEMS';
  /**
   * Rust packages from Cargo (GitHub ecosystem is `RUST`).
   */
  public const PACKAGE_TYPE_RUST = 'RUST';
  /**
   * PHP packages from Composer package manager.
   */
  public const PACKAGE_TYPE_COMPOSER = 'COMPOSER';
  /**
   * Swift packages from Swift Package Manager (SwiftPM).
   */
  public const PACKAGE_TYPE_SWIFT = 'SWIFT';
  protected $collection_key = 'patchedCve';
  /**
   * The architecture of the package.
   *
   * @var string
   */
  public $architecture;
  protected $binarySourceInfoType = BinarySourceInfo::class;
  protected $binarySourceInfoDataType = 'array';
  protected $binaryVersionType = PackageVersion::class;
  protected $binaryVersionDataType = '';
  /**
   * The cpe_uri in [cpe format] (https://cpe.mitre.org/specification/) in which
   * the vulnerability may manifest. Examples include distro or storage location
   * for vulnerable jar.
   *
   * @var string
   */
  public $cpeUri;
  protected $dependencyChainType = LanguagePackageDependency::class;
  protected $dependencyChainDataType = 'array';
  protected $fileLocationType = FileLocation::class;
  protected $fileLocationDataType = 'array';
  /**
   * HashDigest stores the SHA512 hash digest of the jar file if the package is
   * of type Maven. This field will be unset for non Maven packages.
   *
   * @var string
   */
  public $hashDigest;
  protected $layerDetailsType = LayerDetails::class;
  protected $layerDetailsDataType = '';
  /**
   * The list of licenses found that are related to a given package. Note that
   * licenses may also be stored on the BinarySourceInfo. If there is no
   * BinarySourceInfo (because there's no concept of source vs binary), then it
   * will be stored here, while if there are BinarySourceInfos, it will be
   * stored there, as one source can have multiple binaries with different
   * licenses.
   *
   * @var string[]
   */
  public $licenses;
  protected $maintainerType = Maintainer::class;
  protected $maintainerDataType = '';
  /**
   * The OS affected by a vulnerability Used to generate the cpe_uri for OS
   * packages
   *
   * @var string
   */
  public $os;
  /**
   * The version of the OS Used to generate the cpe_uri for OS packages
   *
   * @var string
   */
  public $osVersion;
  /**
   * The package being analysed for vulnerabilities
   *
   * @var string
   */
  public $package;
  /**
   * The type of package: os, maven, go, etc.
   *
   * @var string
   */
  public $packageType;
  /**
   * CVEs that this package is no longer vulnerable to
   *
   * @var string[]
   */
  public $patchedCve;
  protected $sourceVersionType = PackageVersion::class;
  protected $sourceVersionDataType = '';
  /**
   * @var string
   */
  public $unused;
  /**
   * The version of the package being analysed
   *
   * @var string
   */
  public $version;

  /**
   * The architecture of the package.
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
   * A bundle containing the binary and source information.
   *
   * @param BinarySourceInfo[] $binarySourceInfo
   */
  public function setBinarySourceInfo($binarySourceInfo)
  {
    $this->binarySourceInfo = $binarySourceInfo;
  }
  /**
   * @return BinarySourceInfo[]
   */
  public function getBinarySourceInfo()
  {
    return $this->binarySourceInfo;
  }
  /**
   * DEPRECATED
   *
   * @param PackageVersion $binaryVersion
   */
  public function setBinaryVersion(PackageVersion $binaryVersion)
  {
    $this->binaryVersion = $binaryVersion;
  }
  /**
   * @return PackageVersion
   */
  public function getBinaryVersion()
  {
    return $this->binaryVersion;
  }
  /**
   * The cpe_uri in [cpe format] (https://cpe.mitre.org/specification/) in which
   * the vulnerability may manifest. Examples include distro or storage location
   * for vulnerable jar.
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
   * The dependency chain between this package and the user's artifact. List in
   * order from the customer's package under review first, to the current
   * package last. Inclusive of the original package and the current package.
   *
   * @param LanguagePackageDependency[] $dependencyChain
   */
  public function setDependencyChain($dependencyChain)
  {
    $this->dependencyChain = $dependencyChain;
  }
  /**
   * @return LanguagePackageDependency[]
   */
  public function getDependencyChain()
  {
    return $this->dependencyChain;
  }
  /**
   * The path to the jar file / go binary file.
   *
   * @param FileLocation[] $fileLocation
   */
  public function setFileLocation($fileLocation)
  {
    $this->fileLocation = $fileLocation;
  }
  /**
   * @return FileLocation[]
   */
  public function getFileLocation()
  {
    return $this->fileLocation;
  }
  /**
   * HashDigest stores the SHA512 hash digest of the jar file if the package is
   * of type Maven. This field will be unset for non Maven packages.
   *
   * @param string $hashDigest
   */
  public function setHashDigest($hashDigest)
  {
    $this->hashDigest = $hashDigest;
  }
  /**
   * @return string
   */
  public function getHashDigest()
  {
    return $this->hashDigest;
  }
  /**
   * @param LayerDetails $layerDetails
   */
  public function setLayerDetails(LayerDetails $layerDetails)
  {
    $this->layerDetails = $layerDetails;
  }
  /**
   * @return LayerDetails
   */
  public function getLayerDetails()
  {
    return $this->layerDetails;
  }
  /**
   * The list of licenses found that are related to a given package. Note that
   * licenses may also be stored on the BinarySourceInfo. If there is no
   * BinarySourceInfo (because there's no concept of source vs binary), then it
   * will be stored here, while if there are BinarySourceInfos, it will be
   * stored there, as one source can have multiple binaries with different
   * licenses.
   *
   * @param string[] $licenses
   */
  public function setLicenses($licenses)
  {
    $this->licenses = $licenses;
  }
  /**
   * @return string[]
   */
  public function getLicenses()
  {
    return $this->licenses;
  }
  /**
   * The maintainer of the package.
   *
   * @param Maintainer $maintainer
   */
  public function setMaintainer(Maintainer $maintainer)
  {
    $this->maintainer = $maintainer;
  }
  /**
   * @return Maintainer
   */
  public function getMaintainer()
  {
    return $this->maintainer;
  }
  /**
   * The OS affected by a vulnerability Used to generate the cpe_uri for OS
   * packages
   *
   * @param string $os
   */
  public function setOs($os)
  {
    $this->os = $os;
  }
  /**
   * @return string
   */
  public function getOs()
  {
    return $this->os;
  }
  /**
   * The version of the OS Used to generate the cpe_uri for OS packages
   *
   * @param string $osVersion
   */
  public function setOsVersion($osVersion)
  {
    $this->osVersion = $osVersion;
  }
  /**
   * @return string
   */
  public function getOsVersion()
  {
    return $this->osVersion;
  }
  /**
   * The package being analysed for vulnerabilities
   *
   * @param string $package
   */
  public function setPackage($package)
  {
    $this->package = $package;
  }
  /**
   * @return string
   */
  public function getPackage()
  {
    return $this->package;
  }
  /**
   * The type of package: os, maven, go, etc.
   *
   * Accepted values: PACKAGE_TYPE_UNSPECIFIED, OS, MAVEN, GO, GO_STDLIB, PYPI,
   * NPM, NUGET, RUBYGEMS, RUST, COMPOSER, SWIFT
   *
   * @param self::PACKAGE_TYPE_* $packageType
   */
  public function setPackageType($packageType)
  {
    $this->packageType = $packageType;
  }
  /**
   * @return self::PACKAGE_TYPE_*
   */
  public function getPackageType()
  {
    return $this->packageType;
  }
  /**
   * CVEs that this package is no longer vulnerable to
   *
   * @param string[] $patchedCve
   */
  public function setPatchedCve($patchedCve)
  {
    $this->patchedCve = $patchedCve;
  }
  /**
   * @return string[]
   */
  public function getPatchedCve()
  {
    return $this->patchedCve;
  }
  /**
   * DEPRECATED
   *
   * @param PackageVersion $sourceVersion
   */
  public function setSourceVersion(PackageVersion $sourceVersion)
  {
    $this->sourceVersion = $sourceVersion;
  }
  /**
   * @return PackageVersion
   */
  public function getSourceVersion()
  {
    return $this->sourceVersion;
  }
  /**
   * @param string $unused
   */
  public function setUnused($unused)
  {
    $this->unused = $unused;
  }
  /**
   * @return string
   */
  public function getUnused()
  {
    return $this->unused;
  }
  /**
   * The version of the package being analysed
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
class_alias(PackageData::class, 'Google_Service_OnDemandScanning_PackageData');
