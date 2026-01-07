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

class Detail extends \Google\Model
{
  /**
   * Required. The [CPE URI](https://cpe.mitre.org/specification/) this
   * vulnerability affects.
   *
   * @var string
   */
  public $affectedCpeUri;
  /**
   * Required. The package this vulnerability affects.
   *
   * @var string
   */
  public $affectedPackage;
  protected $affectedVersionEndType = Version::class;
  protected $affectedVersionEndDataType = '';
  protected $affectedVersionStartType = Version::class;
  protected $affectedVersionStartDataType = '';
  /**
   * A vendor-specific description of this vulnerability.
   *
   * @var string
   */
  public $description;
  /**
   * The distro recommended [CPE URI](https://cpe.mitre.org/specification/) to
   * update to that contains a fix for this vulnerability. It is possible for
   * this to be different from the affected_cpe_uri.
   *
   * @var string
   */
  public $fixedCpeUri;
  /**
   * The distro recommended package to update to that contains a fix for this
   * vulnerability. It is possible for this to be different from the
   * affected_package.
   *
   * @var string
   */
  public $fixedPackage;
  protected $fixedVersionType = Version::class;
  protected $fixedVersionDataType = '';
  /**
   * Whether this detail is obsolete. Occurrences are expected not to point to
   * obsolete details.
   *
   * @var bool
   */
  public $isObsolete;
  /**
   * The type of package; whether native or non native (e.g., ruby gems, node.js
   * packages, etc.).
   *
   * @var string
   */
  public $packageType;
  /**
   * The distro assigned severity of this vulnerability.
   *
   * @var string
   */
  public $severityName;
  /**
   * The source from which the information in this Detail was obtained.
   *
   * @var string
   */
  public $source;
  /**
   * The time this information was last changed at the source. This is an
   * upstream timestamp from the underlying information source - e.g. Ubuntu
   * security tracker.
   *
   * @var string
   */
  public $sourceUpdateTime;
  /**
   * The name of the vendor of the product.
   *
   * @var string
   */
  public $vendor;

  /**
   * Required. The [CPE URI](https://cpe.mitre.org/specification/) this
   * vulnerability affects.
   *
   * @param string $affectedCpeUri
   */
  public function setAffectedCpeUri($affectedCpeUri)
  {
    $this->affectedCpeUri = $affectedCpeUri;
  }
  /**
   * @return string
   */
  public function getAffectedCpeUri()
  {
    return $this->affectedCpeUri;
  }
  /**
   * Required. The package this vulnerability affects.
   *
   * @param string $affectedPackage
   */
  public function setAffectedPackage($affectedPackage)
  {
    $this->affectedPackage = $affectedPackage;
  }
  /**
   * @return string
   */
  public function getAffectedPackage()
  {
    return $this->affectedPackage;
  }
  /**
   * The version number at the end of an interval in which this vulnerability
   * exists. A vulnerability can affect a package between version numbers that
   * are disjoint sets of intervals (example: [1.0.0-1.1.0], [2.4.6-2.4.8] and
   * [4.5.6-4.6.8]) each of which will be represented in its own Detail. If a
   * specific affected version is provided by a vulnerability database,
   * affected_version_start and affected_version_end will be the same in that
   * Detail.
   *
   * @param Version $affectedVersionEnd
   */
  public function setAffectedVersionEnd(Version $affectedVersionEnd)
  {
    $this->affectedVersionEnd = $affectedVersionEnd;
  }
  /**
   * @return Version
   */
  public function getAffectedVersionEnd()
  {
    return $this->affectedVersionEnd;
  }
  /**
   * The version number at the start of an interval in which this vulnerability
   * exists. A vulnerability can affect a package between version numbers that
   * are disjoint sets of intervals (example: [1.0.0-1.1.0], [2.4.6-2.4.8] and
   * [4.5.6-4.6.8]) each of which will be represented in its own Detail. If a
   * specific affected version is provided by a vulnerability database,
   * affected_version_start and affected_version_end will be the same in that
   * Detail.
   *
   * @param Version $affectedVersionStart
   */
  public function setAffectedVersionStart(Version $affectedVersionStart)
  {
    $this->affectedVersionStart = $affectedVersionStart;
  }
  /**
   * @return Version
   */
  public function getAffectedVersionStart()
  {
    return $this->affectedVersionStart;
  }
  /**
   * A vendor-specific description of this vulnerability.
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
   * The distro recommended [CPE URI](https://cpe.mitre.org/specification/) to
   * update to that contains a fix for this vulnerability. It is possible for
   * this to be different from the affected_cpe_uri.
   *
   * @param string $fixedCpeUri
   */
  public function setFixedCpeUri($fixedCpeUri)
  {
    $this->fixedCpeUri = $fixedCpeUri;
  }
  /**
   * @return string
   */
  public function getFixedCpeUri()
  {
    return $this->fixedCpeUri;
  }
  /**
   * The distro recommended package to update to that contains a fix for this
   * vulnerability. It is possible for this to be different from the
   * affected_package.
   *
   * @param string $fixedPackage
   */
  public function setFixedPackage($fixedPackage)
  {
    $this->fixedPackage = $fixedPackage;
  }
  /**
   * @return string
   */
  public function getFixedPackage()
  {
    return $this->fixedPackage;
  }
  /**
   * The distro recommended version to update to that contains a fix for this
   * vulnerability. Setting this to VersionKind.MAXIMUM means no such version is
   * yet available.
   *
   * @param Version $fixedVersion
   */
  public function setFixedVersion(Version $fixedVersion)
  {
    $this->fixedVersion = $fixedVersion;
  }
  /**
   * @return Version
   */
  public function getFixedVersion()
  {
    return $this->fixedVersion;
  }
  /**
   * Whether this detail is obsolete. Occurrences are expected not to point to
   * obsolete details.
   *
   * @param bool $isObsolete
   */
  public function setIsObsolete($isObsolete)
  {
    $this->isObsolete = $isObsolete;
  }
  /**
   * @return bool
   */
  public function getIsObsolete()
  {
    return $this->isObsolete;
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
   * The distro assigned severity of this vulnerability.
   *
   * @param string $severityName
   */
  public function setSeverityName($severityName)
  {
    $this->severityName = $severityName;
  }
  /**
   * @return string
   */
  public function getSeverityName()
  {
    return $this->severityName;
  }
  /**
   * The source from which the information in this Detail was obtained.
   *
   * @param string $source
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return string
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * The time this information was last changed at the source. This is an
   * upstream timestamp from the underlying information source - e.g. Ubuntu
   * security tracker.
   *
   * @param string $sourceUpdateTime
   */
  public function setSourceUpdateTime($sourceUpdateTime)
  {
    $this->sourceUpdateTime = $sourceUpdateTime;
  }
  /**
   * @return string
   */
  public function getSourceUpdateTime()
  {
    return $this->sourceUpdateTime;
  }
  /**
   * The name of the vendor of the product.
   *
   * @param string $vendor
   */
  public function setVendor($vendor)
  {
    $this->vendor = $vendor;
  }
  /**
   * @return string
   */
  public function getVendor()
  {
    return $this->vendor;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Detail::class, 'Google_Service_ContainerAnalysis_Detail');
