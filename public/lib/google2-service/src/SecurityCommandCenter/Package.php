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

namespace Google\Service\SecurityCommandCenter;

class Package extends \Google\Model
{
  /**
   * The CPE URI where the vulnerability was detected.
   *
   * @var string
   */
  public $cpeUri;
  /**
   * The name of the package where the vulnerability was detected.
   *
   * @var string
   */
  public $packageName;
  /**
   * Type of package, for example, os, maven, or go.
   *
   * @var string
   */
  public $packageType;
  /**
   * The version of the package.
   *
   * @var string
   */
  public $packageVersion;

  /**
   * The CPE URI where the vulnerability was detected.
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
   * The name of the package where the vulnerability was detected.
   *
   * @param string $packageName
   */
  public function setPackageName($packageName)
  {
    $this->packageName = $packageName;
  }
  /**
   * @return string
   */
  public function getPackageName()
  {
    return $this->packageName;
  }
  /**
   * Type of package, for example, os, maven, or go.
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
   * The version of the package.
   *
   * @param string $packageVersion
   */
  public function setPackageVersion($packageVersion)
  {
    $this->packageVersion = $packageVersion;
  }
  /**
   * @return string
   */
  public function getPackageVersion()
  {
    return $this->packageVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Package::class, 'Google_Service_SecurityCommandCenter_Package');
