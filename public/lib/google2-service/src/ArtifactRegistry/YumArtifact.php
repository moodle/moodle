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

namespace Google\Service\ArtifactRegistry;

class YumArtifact extends \Google\Model
{
  /**
   * Package type is not specified.
   */
  public const PACKAGE_TYPE_PACKAGE_TYPE_UNSPECIFIED = 'PACKAGE_TYPE_UNSPECIFIED';
  /**
   * Binary package (.rpm).
   */
  public const PACKAGE_TYPE_BINARY = 'BINARY';
  /**
   * Source package (.srpm).
   */
  public const PACKAGE_TYPE_SOURCE = 'SOURCE';
  /**
   * Output only. Operating system architecture of the artifact.
   *
   * @var string
   */
  public $architecture;
  /**
   * Output only. The Artifact Registry resource name of the artifact.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The yum package name of the artifact.
   *
   * @var string
   */
  public $packageName;
  /**
   * Output only. An artifact is a binary or source package.
   *
   * @var string
   */
  public $packageType;

  /**
   * Output only. Operating system architecture of the artifact.
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
   * Output only. The Artifact Registry resource name of the artifact.
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
   * Output only. The yum package name of the artifact.
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
   * Output only. An artifact is a binary or source package.
   *
   * Accepted values: PACKAGE_TYPE_UNSPECIFIED, BINARY, SOURCE
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(YumArtifact::class, 'Google_Service_ArtifactRegistry_YumArtifact');
