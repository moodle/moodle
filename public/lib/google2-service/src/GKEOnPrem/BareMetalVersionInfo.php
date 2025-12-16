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

namespace Google\Service\GKEOnPrem;

class BareMetalVersionInfo extends \Google\Collection
{
  protected $collection_key = 'dependencies';
  protected $dependenciesType = UpgradeDependency::class;
  protected $dependenciesDataType = 'array';
  /**
   * If set, the cluster dependencies (e.g. the admin cluster, other user
   * clusters managed by the same admin cluster, version skew policy, etc) must
   * be upgraded before this version can be installed or upgraded to.
   *
   * @var bool
   */
  public $hasDependencies;
  /**
   * Version number e.g. 1.13.1.
   *
   * @var string
   */
  public $version;

  /**
   * The list of upgrade dependencies for this version.
   *
   * @param UpgradeDependency[] $dependencies
   */
  public function setDependencies($dependencies)
  {
    $this->dependencies = $dependencies;
  }
  /**
   * @return UpgradeDependency[]
   */
  public function getDependencies()
  {
    return $this->dependencies;
  }
  /**
   * If set, the cluster dependencies (e.g. the admin cluster, other user
   * clusters managed by the same admin cluster, version skew policy, etc) must
   * be upgraded before this version can be installed or upgraded to.
   *
   * @param bool $hasDependencies
   */
  public function setHasDependencies($hasDependencies)
  {
    $this->hasDependencies = $hasDependencies;
  }
  /**
   * @return bool
   */
  public function getHasDependencies()
  {
    return $this->hasDependencies;
  }
  /**
   * Version number e.g. 1.13.1.
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
class_alias(BareMetalVersionInfo::class, 'Google_Service_GKEOnPrem_BareMetalVersionInfo');
